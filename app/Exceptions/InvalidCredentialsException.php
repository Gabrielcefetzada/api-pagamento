<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class InvalidCredentialsException extends AbstractException
{
    public function __construct(
        array $contextualData,
        ?int $code = ResponseAlias::HTTP_BAD_REQUEST,
    ) {
        $message      = 'Credenciais inválidas';
        $debugMessage = "Credenciais inválidas ao logar";
        $type         = 'InvalidCredentialsException';
        parent::__construct(
            $type,
            $contextualData,
            $message,
            $debugMessage,
            $code,
        );
    }
}
