<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NoBalanceException extends AbstractException
{
    public function __construct(
        array $contextualData,
        ?int $code = ResponseAlias::HTTP_BAD_REQUEST,
    ) {
        $message      = 'Saldo insuficiente';
        $debugMessage = "Saldo insuficiente. Você tentou realizar uma transferência de R$ " . $contextualData['transferenceAmount'] . ", mas possui na carteira R$ " . $contextualData['payerWallet'];
        $type         = 'NoBalanceException';
        parent::__construct(
            $type,
            $contextualData,
            $message,
            $debugMessage,
            $code,
        );
    }
}
