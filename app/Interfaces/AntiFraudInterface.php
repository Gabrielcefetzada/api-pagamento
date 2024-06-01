<?php

namespace App\Interfaces;

interface AntiFraudInterface
{
    public function authorize(): bool;
}
