<?php

namespace App\Http\Services;

use App\Interfaces\AntiFraudInterface;
use Illuminate\Support\Facades\Http;

class AntiFraudService implements AntiFraudInterface
{
    public function authorize(): bool
    {
        if (env('ANTIFRAUD_ACTIVE')) {
            $response = Http::get(env('ANTIFRAUD_BASE_URL').'/api/v2/authorize');

            return $response->object()->data->authorization;
        }

        return true;
    }
}
