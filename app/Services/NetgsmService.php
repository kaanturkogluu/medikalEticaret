<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NetgsmService
{
    protected string $usercode;
    protected string $password;
    protected string $header;

    public function __construct()
    {
        $this->usercode = config('services.netgsm.usercode', '');
        $this->password = config('services.netgsm.password', '');
        $this->header = config('services.netgsm.header', '');
    }

    /**
     * Send 1:N SMS using Netgsm GET API
     *
     * @param string|array $phoneNumbers Target phone number(s)
     * @param string $message The message content
     * @return array
     */
    public function sendSms($phoneNumbers, string $message): array
    {
        if (empty($this->usercode) || empty($this->password)) {
            Log::error('Netgsm: Kullanıcı bilgileri eksik (services.php veya .env kontrol edin).');
            return ['status' => false, 'message' => 'Netgsm API bilgileri tanımlanmamış.'];
        }

        // Format numbers to string
        if (is_array($phoneNumbers)) {
            $phoneNumbers = implode(',', $phoneNumbers);
        }

        // Clean numbers, remove spaces, leading zero, etc if needed. 
        // Assuming user sends a clean number or we clean it slightly.
        $phoneNumbers = preg_replace('/[^0-9,]/', '', $phoneNumbers);

        $params = [
            'usercode' => $this->usercode,
            'password' => $this->password,
            'gsmno' => $phoneNumbers,
            'message' => $message,
            'msgheader' => $this->header,
            'filter' => '0' // Gönderilemeyenleri filtrelemez
        ];

        try {
            $response = Http::get('https://api.netgsm.com.tr/sms/send/get', $params);
            
            $result = $response->body();

            // Netgsm GET API success response format usually starts with "00 "
            if (str_starts_with($result, '00 ')) {
                $bulkId = trim(substr($result, 3));
                return [
                    'status' => true,
                    'message' => 'Mesaj başarıyla gönderildi.',
                    'bulk_id' => $bulkId
                ];
            }

            // Error codes mapping
            $errorMap = [
                '20' => 'Mesaj metni veya mesaj boyutu hatalı.',
                '30' => 'Geçersiz kullanıcı adı , şifre veya apierişim izni yok.',
                '40' => 'Mesaj başlığı (Gönderici Adı) sistemde tanımlı değil.',
                '50' => 'Gönderilen numaralar hatalı.',
                '60' => 'Hesabınızda yeterli kredi yok.',
                '70' => 'Hatalı sorgulama.',
                '80' => 'Gönderim sınır aşımı.'
            ];

            $errorCode = trim($result);
            $errorMessage = $errorMap[$errorCode] ?? "Bilinmeyen hata kodu: {$errorCode}";

            Log::error("Netgsm Error: {$errorMessage}");

            return [
                'status' => false,
                'message' => $errorMessage
            ];

        } catch (\Exception $e) {
            Log::error("Netgsm Exception: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'SMS gönderilirken bağlantı hatası oluştu: ' . $e->getMessage()
            ];
        }
    }
}
