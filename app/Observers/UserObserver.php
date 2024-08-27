<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function saving(User $user): void
    {
        if (!$user->password) {
            unset($user->password);
        }

        if ($user->isDirty('document_number')) {
            $user->document_number = preg_replace("/[^0-9]/", "", $user->document_number);
        }
    }
}
