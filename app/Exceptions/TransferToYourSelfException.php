<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TransferToYourSelfException extends AbstractException
{
    public function __construct(
        array $contextualData,
        ?int $code = ResponseAlias::HTTP_BAD_REQUEST,
    ) {
        $message      = 'Não é possível realizar transferência para si mesmo.';
        $debugMessage = "Não é possível realizar transferência para si mesmo.";
        $type         = 'TransferToYourSelfException';
        parent::__construct(
            $type,
            $contextualData,
            $message,
            $debugMessage,
            $code,
        );
    }
}
