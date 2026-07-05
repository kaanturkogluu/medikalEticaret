<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NetgsmService
{
    private string $usercode;
    private string $password;
    private string $header;

    public function __construct()
    {
        $this->usercode = config('services.netgsm.usercode');
        $this->password = config('services.netgsm.password');
        $this->header   = config('services.netgsm.header');
    }

    public function sendSms(string|array $phoneNumbers, string $message, string $type = 'Diğer', string|array|null $customerNames = null): array
    {
        if (!$this->usercode || !$this->password || !$this->header) {
            Log::error('Netgsm: Eksik config bilgisi (usercode/password/header).');

            return [
                'status' => false,
                'message' => 'Netgsm config eksik.'
            ];
        }

        $numbers = $this->normalizeNumbers($phoneNumbers);
        
        $messagesArray = [];
        foreach ($numbers as $number) {
            $messagesArray[] = [
                'msg' => $message,
                'no' => $number
            ];
        }

        try {
            $payload = [
                'msgheader' => $this->header,
                'messages'  => $messagesArray,
                'encoding'  => 'TR',
                'iysfilter' => '0',
                'appname'   => config('app.name', 'Laravel')
            ];

            $response = Http::withBasicAuth($this->usercode, $this->password)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])->timeout(10)->post('https://api.netgsm.com.tr/sms/rest/v2/send', $payload);

            $result = $response->json();

            // Default status and error info
            $statusCode = $result['code'] ?? 'Bilinmiyor';
            $jobId = $result['jobid'] ?? null;
            $statusMessage = 'Bilinmiyor';

            // Determine if success or not
            $isSuccess = false;
            if (isset($result['code']) && $result['code'] === '00') {
                $isSuccess = true;
                $statusMessage = 'Başarılı';
            } else {
                $statusMessage = $result['description'] ?? 'Hata açıklaması dönmedi.';
            }

            // Log to database
            foreach ($numbers as $index => $number) {
                // Determine customer name if provided
                $cName = null;
                if (is_array($customerNames) && isset($customerNames[$index])) {
                    $cName = $customerNames[$index];
                } elseif (is_string($customerNames)) {
                    $cName = $customerNames;
                }

                \App\Models\SmsLog::create([
                    'customer_name' => $cName,
                    'phone' => $number,
                    'message' => $message,
                    'type' => $type,
                    'job_id' => $jobId,
                    'status_code' => $statusCode,
                    'status_message' => $statusMessage,
                ]);
            }

            if ($isSuccess) {
                return [
                    'status' => true,
                    'message' => 'SMS başarıyla gönderildi.',
                    'bulk_id' => $jobId
                ];
            }

            // ERROR MAP
            $errorMessage = "Netgsm Hatası [{$statusCode}]: {$statusMessage}";

            Log::error('Netgsm Error', [
                'code' => $statusCode,
                'description' => $statusMessage
            ]);

            return [
                'status' => false,
                'message' => $errorMessage
            ];

        } catch (\Throwable $e) {
            Log::error('Netgsm Exception', [
                'error' => $e->getMessage()
            ]);

            // Save exception to logs if requested
            foreach ($numbers as $index => $number) {
                $cName = is_array($customerNames) ? ($customerNames[$index] ?? null) : (is_string($customerNames) ? $customerNames : null);
                \App\Models\SmsLog::create([
                    'customer_name' => $cName,
                    'phone' => $number,
                    'message' => $message,
                    'type' => $type,
                    'job_id' => null,
                    'status_code' => 'Exception',
                    'status_message' => substr($e->getMessage(), 0, 250),
                ]);
            }

            return [
                'status' => false,
                'message' => 'SMS gönderim hatası: ' . $e->getMessage()
            ];
        }
    }

    private function normalizeNumbers(string|array $numbers): array
    {
        if (is_array($numbers)) {
            $numbers = implode(',', $numbers);
        }

        $numbers = preg_replace('/[^0-9,]/', '', $numbers);

        return collect(explode(',', $numbers))
            ->filter()
            ->map(function ($number) {
                $number = ltrim($number, '0');

                if (!str_starts_with($number, '90')) {
                    $number = '90' . $number;
                }

                return $number;
            })
            ->values()
            ->toArray();
    }

    public function checkReport(array $jobIds = [], string $startDate = null, string $endDate = null): array
    {
        if (!$this->usercode || !$this->password) {
            return [
                'status' => false,
                'message' => 'Netgsm config eksik.'
            ];
        }

        $payload = [];

        if (!empty($jobIds)) {
            $payload['jobids'] = array_slice($jobIds, 0, 50); // Maksimum 50 jobid
        }

        if ($startDate && $endDate) {
            $payload['startdate'] = $startDate;
            $payload['stopdate'] = $endDate;
        }

        if (empty($payload)) {
            return [
                'status' => false,
                'message' => 'Sorgulama için JobID veya Tarih aralığı girmelisiniz.'
            ];
        }

        try {
            $response = Http::withBasicAuth($this->usercode, $this->password)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])->timeout(10)->post('https://api.netgsm.com.tr/sms/rest/v2/report', $payload);

            $result = $response->json();

            if (isset($result['code']) && $result['code'] === '00') {
                return [
                    'status' => true,
                    'jobs' => $result['jobs'] ?? [],
                    'message' => 'Sorgulama başarılı.'
                ];
            }

            // ERROR
            $errorCode = $result['code'] ?? 'Bilinmiyor';
            $errorDesc = $result['description'] ?? 'Hata açıklaması dönmedi.';
            $errorMessage = "Netgsm Sorgu Hatası [{$errorCode}]: {$errorDesc}";

            Log::error('Netgsm Report Error', [
                'code' => $errorCode,
                'description' => $errorDesc
            ]);

            return [
                'status' => false,
                'message' => $errorMessage
            ];

        } catch (\Throwable $e) {
            Log::error('Netgsm Report Exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => false,
                'message' => 'Rapor sorgulama hatası: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check Netgsm balance/credits
     */
    public function checkBalance(int $stip = 3): array
    {
        if (!$this->usercode || !$this->password) {
            return [
                'status' => false,
                'message' => 'Netgsm config eksik.',
                'data' => []
            ];
        }

        try {
            $payload = [
                'usercode' => $this->usercode,
                'password' => $this->password,
                'stip' => $stip
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->timeout(10)->post('https://api.netgsm.com.tr/balance', $payload);

            $result = $response->json();

            // Check for errors
            if (isset($result['code']) && $result['code'] !== '00') {
                return [
                    'status' => false,
                    'message' => 'Netgsm Bakiye Hatası: ' . $result['code'],
                    'data' => []
                ];
            }

            return [
                'status' => true,
                'message' => 'Bakiye başarıyla çekildi.',
                'data' => $result['balance'] ?? []
            ];

        } catch (\Throwable $e) {
            Log::error('Netgsm Balance Exception', ['error' => $e->getMessage()]);

            return [
                'status' => false,
                'message' => 'Bakiye sorgulama hatası: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }
}