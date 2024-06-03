<?php

namespace App\Interfaces;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

interface NotificationServiceInterface extends ShouldQueue
{
    public function toMail(object $notifiable): MailMessage|\Exception;
    public function via(object $notifiable): array;
}
