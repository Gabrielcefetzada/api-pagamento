<?php

namespace App\Jobs;

use App\Models\Transference;
use App\Notifications\TransferenceDoneNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTransferenceDoneNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Transference $transference)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->transference->payee->notify(new TransferenceDoneNotification($this->transference));
    }
}
