<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AuthorizationService
{
    public function isAuthorized(): bool
    {
        $authorize = config('devi_tools.authorize');

        if (!$authorize) {
            return false;
        }

        $response = Http::get($authorize);

        return !$response->failed();
    }
}
