<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notify = config('devi_tools.notify');

        if ($notify) {
            $notificationResponse = Http::post($notify, [
                'transaction_id' => $this->transaction->id,
                'message' => 'Você recebeu uma transferência de ' . $this->transaction->payer->name,
            ]);

            if ($notificationResponse->failed()) {
                Log::error('Error sending notification for transaction ' . $this->transaction->id);
                $this->release(60);
            }
        }
    }
}
