<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NoBalanceException extends AbstractException
{
    protected array $fareData = [];

    public function __construct(
        array $contextualData,
        ?int $code = ResponseAlias::HTTP_BAD_REQUEST,
    ) {
        $this->contextualData = $contextualData;
        $message              = 'Saldo insuficiente';
        $debugMessage         = "Saldo insuficiente. Você tentou realizar uma transferência de R$ " . $contextualData['transferenceAmount'] . ", mas possui na carteira R$ " . $contextualData['payerWallet'];
        $type                 = 'NoBalanceException';
        parent::__construct(
            $type,
            $contextualData,
            $message,
            $debugMessage,
            $code,
        );
    }

    public function render(Request $request): Response
    {
        $response = [
            'error' => array_merge([
                'httpCode' => $this->code,
                'type'     => $this->type,
                'message'  => $this->message,
            ], isset($this->fareData) ? $this->contextualData : [])
        ];

        return response(
            $response,
            $this->code
        );
    }
}
