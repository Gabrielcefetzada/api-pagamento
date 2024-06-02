<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AbstractException extends Exception
{
    public function __construct(
        protected string $type,
        protected array $contextualData = [],
        string $message = 'Houve um erro na aplicação',
        protected string $debugMessage = 'Erro genérico',
        int $code = 500,
        protected ?\Throwable $previous = null,
    ) {
        $this->mergeContextualData();
        parent::__construct($message, $code, $previous);
    }

    public function report(): void
    {
        $previous = $this->getPrevious();
        if (!is_null($previous) && method_exists($previous, 'report')) {
            $previous->report();
        }

        $this->sendReport('error');
    }

    private function sendReport(): void
    {
        if (app()->environment('local')) {
            Log::error($this->debugMessage, $this->contextualData);
        }
    }

    public function render(): Response
    {
        $this->report();
        return response(
            [
                'error' => [
                    'httpCode' => $this->code,
                    'type'     => $this->type,
                    'message'  => $this->message,
                ],
            ],
            $this->code
        );
    }

    private function mergeContextualData(): void
    {
        $this->contextualData = array_merge($this->contextualData, [
            'userId'           => Auth::user()->id ?? -1,
            'requestOrigin'    => request()->header('Referer'),
            'previousErrorMsg' => $this->previous?->getMessage(),
            'where'            => "{$this->getFile()}:{$this->getLine()}",
            'trace'            => $this->getTraceAsString(),
        ]);
    }
}
