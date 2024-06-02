<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AntiFraudException extends AbstractException
{
    public function __construct(
        array $contextualData,
        ?int $code = ResponseAlias::HTTP_FORBIDDEN,
    ) {
        $message      = 'Transação não autorizada.';
        $debugMessage = "Transação não autorizada pelo serviço de Anti Fraude";
        $type         = 'AntiFraudException';
        parent::__construct(
            $type,
            $contextualData,
            $message,
            $debugMessage,
            $code,
        );
    }
}
