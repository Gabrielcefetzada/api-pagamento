<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PayeeNotFoundException extends AbstractException
{
    public function __construct(
        array $contextualData,
        ?int $code = ResponseAlias::HTTP_NOT_FOUND,
    ) {
        $message      = 'Destinatário não existe';
        $debugMessage = "Destinatário não existe. ID de usuário destinatário passado " . $contextualData['payee'];
        $type         = 'PayeeNotFoundException';
        parent::__construct(
            $type,
            $contextualData,
            $message,
            $debugMessage,
            $code,
        );
    }
}
