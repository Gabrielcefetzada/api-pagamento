<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TransferActingAsAnotherUserException extends AbstractException
{
    public function __construct(
        array $contextualData,
        ?int $code = ResponseAlias::HTTP_BAD_REQUEST,
    ) {
        $message      = 'Você deve efetuar transferências com sua própria conta.';
        $debugMessage = "Não é possível realizar transferência para outros usuários com a conta de outro usuário.";
        $type         = 'TransferActingAsAnotherUserException';
        parent::__construct(
            $type,
            $contextualData,
            $message,
            $debugMessage,
            $code,
        );
    }
}
