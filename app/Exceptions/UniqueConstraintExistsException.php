<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UniqueConstraintExistsException extends AbstractException
{
    public function __construct(
        string $message,
        array $contextualData,
        ?int $code = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
    ) {
        $debugMessage = $message;
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
