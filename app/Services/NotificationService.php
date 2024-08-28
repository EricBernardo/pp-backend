<?php

namespace App\Services;

use App\Jobs\SendNotificationJob;
use App\Models\Transaction;

class NotificationService
{
    public function send(Transaction $transaction): void
    {
        SendNotificationJob::dispatch($transaction);
    }
}
