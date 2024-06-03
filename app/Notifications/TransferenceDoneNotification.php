<?php

namespace App\Notifications;

use App\Exceptions\NotificationServiceUnavailable;
use App\Interfaces\NotificationServiceInterface;
use App\Models\Transference;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TransferenceDoneNotification extends Notification implements NotificationServiceInterface
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Transference  $transference)
    {

    }

    private function healthy(): bool
    {
        $response = Http::post(env('NOTIFICATION_SERVICE_BASE_URL').'/api/v2/notify');

        return $response->status() === ResponseAlias::HTTP_NO_CONTENT;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->healthy()) {
            return (new MailMessage())
                ->subject('Transferência realizada com sucesso')
                ->view(
                    'mail.transference_done',
                    [
                        'transference' => $this->transference,
                    ]
                );
        }
        throw new NotificationServiceUnavailable(['id' => $this->transference]);
    }
}
