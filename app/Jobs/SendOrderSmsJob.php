<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Sms\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;
    public string $event;

    /**
     * Number of times to attempt the job.
     */
    public int $tries = 3;

    /**
     * Timeout in seconds before failing.
     */
    public int $timeout = 30;

    /**
     * Backoff delay between retries in seconds.
     */
    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, string $event = 'order_placed')
    {
        $this->order = $order;
        $this->event = $event;
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        try {
            if ($this->event === 'order_placed') {
                $smsService->sendOrderPlaced($this->order);
                $smsService->sendAdminNewOrderAlert($this->order);
            } elseif ($this->event === 'order_shipped') {
                $smsService->sendOrderShipped($this->order);
            } else {
                Log::warning("SendOrderSmsJob: Unknown event '{$this->event}' for order #{$this->order->order_number}");
            }
        } catch (\Throwable $e) {
            Log::error("SendOrderSmsJob failed for order #{$this->order->order_number} ({$this->event}): " . $e->getMessage());
            throw $e;
        }
    }
}
