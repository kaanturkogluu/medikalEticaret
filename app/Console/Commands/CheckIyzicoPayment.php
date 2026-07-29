<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\IyzicoService;
use App\Http\Controllers\IyzicoController;

class CheckIyzicoPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iyzico:check {identifier : Order ID or Payment Token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Query Iyzico payment status for an Order ID or Token and process payment completion if successful.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $identifier = trim($this->argument('identifier'));
        $iyzicoService = app(IyzicoService::class);
        $iyzicoController = app(IyzicoController::class);

        $order = null;
        $token = null;

        if (is_numeric($identifier)) {
            $order = Order::find((int) $identifier);
            if ($order) {
                $token = $order->payment_token;
            }
        }

        if (!$order) {
            $order = Order::where('payment_token', $identifier)->first();
            if ($order) {
                $token = $identifier;
            }
        }

        if (!$token && is_numeric($identifier)) {
            $token = $this->ask('Order #' . $identifier . ' found, but has no payment_token. Please enter token from Iyzico dashboard/logs (or press Enter to skip):');
            if ($token) {
                $token = trim($token);
                $order->update(['payment_token' => $token]);
            }
        }

        if (!$token) {
            $token = $identifier; // Try using identifier directly as token if order was not found in local DB yet
        }

        if (empty($token)) {
            $this->error('No valid token or order found.');
            return 1;
        }

        $this->info("Querying Iyzico status for token: {$token}...");

        try {
            $payment = $iyzicoService->getPaymentStatus($token);

            $this->info("Iyzico API Response Status: " . $payment->getStatus());
            $this->info("Payment Status: " . $payment->getPaymentStatus());
            $this->info("Payment ID: " . $payment->getPaymentId());
            $this->info("Conversation ID: " . ($payment->getConversationId() ?: 'NULL'));

            if ($payment->getStatus() === 'success' && $payment->getPaymentStatus() === 'SUCCESS') {
                if (!$order) {
                    $req = request();
                    $order = $iyzicoController->findOrderForPayment($req, $payment, $token);
                }

                if (!$order) {
                    $this->error("Payment is SUCCESS on Iyzico, but Order could not be resolved in database!");
                    return 1;
                }

                $processed = $iyzicoController->completeOrder($order, $payment);
                if ($processed) {
                    $this->info("SUCCESS: Order #{$order->id} payment processed and status updated to 'Created'!");
                } else {
                    $this->info("INFO: Order #{$order->id} was already marked as paid.");
                }
                return 0;
            } else {
                $this->error("Payment failed or incomplete on Iyzico. Error: " . $payment->getErrorMessage());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Exception occurred: " . $e->getMessage());
            return 1;
        }
    }
}
