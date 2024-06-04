<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = $this->extractNumbers($value);

        if (!$this->hasValidLength($cpf)) {
            $fail('CPF inválido');
            return;
        }

        if ($this->hasRepeatedDigits($cpf)) {
            $fail('CPF inválido');
            return;
        }

        if (!$this->hasValidCheckDigits($cpf)) {
            $fail('CPF inválido');
        }
    }

    private function extractNumbers(string $value): string
    {
        return preg_replace('/[^0-9]/is', '', $value);
    }

    private function hasValidLength(string $cpf): bool
    {
        return strlen($cpf) == 11;
    }

    private function hasRepeatedDigits(string $cpf): bool
    {
        return preg_match('/(\d)\1{10}/', $cpf);
    }

    private function hasValidCheckDigits(string $cpf): bool
    {
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }
}
