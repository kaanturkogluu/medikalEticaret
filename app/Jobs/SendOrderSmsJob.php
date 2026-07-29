<?php

namespace App\Jobs;

use App\Services\NetgsmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $phone;
    protected string $message;
    protected string $header;
    protected string $customerName;

    /**
     * Create a new job instance.
     */
    public function __construct(string $phone, string $message, string $header = 'Sipariş Bildirimi', string $customerName = '')
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->header = $header;
        $this->customerName = $customerName;
    }

    /**
     * Execute the job.
     */
    public function handle(NetgsmService $netgsmService): void
    {
        try {
            $netgsmService->sendSms($this->phone, $this->message, $this->header, $this->customerName);
        } catch (\Exception $e) {
            Log::error("SendOrderSmsJob failed: " . $e->getMessage());
        }
    }
}
