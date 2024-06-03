<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NotificationServiceUnavailable extends AbstractException
{
    public function __construct(
        array $contextualData,
        ?int  $code = ResponseAlias::HTTP_SERVICE_UNAVAILABLE,
    ) {
        $message      = 'Serviço de notificação indisponível';
        $debugMessage = "O provedor de E-mail e/ou provedor de SMS está(ão) indisponíveis";
        $type         = 'NotificationServiceUnavailable';
        parent::__construct(
            $type,
            $contextualData,
            $message,
            $debugMessage,
            $code,
        );
    }
}
