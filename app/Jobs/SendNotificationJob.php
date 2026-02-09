<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\SendNotification\SendNotificationRepository;
use Illuminate\Bus\Batchable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable,Batchable;
    protected $user;
    protected $data;
    protected $messageFile;
    protected $subject;


    // Di-retry sampai 3 kali jika gagal
    public $tries = 3;

    // Jeda antar retry (dalam detik)
    public $backoff = 10;


    
    /**
     * Create a new job instance.
     */
    public function __construct($user, array $data, string $messageFile, string $subject)
    {
        $this->user = $user;
        $this->data = $data;
        $this->messageFile = $messageFile;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     */
    public function handle(SendNotificationRepository $sendNotificationRepository): void
    {
        if (!$this->user || !$this->user->employee) {
            return;
        }
        // // Panggil repository untuk kirim email
        // $sendNotificationRepository->sendEmailMessageWithAttachment(
        //     $this->user,
        //     $this->data,
        //     $this->messageFile,
        //     $this->subject
        // );

        // Kirim WhatsApp juga
        $sendNotificationRepository->sendWhatsappMessage(
            $this->user->employee->phone ?? null,
            $this->data,
            $this->messageFile
        );
    }

    public function failed(\Throwable $exception)
    {
        Log::error("SendNotificationJob gagal untuk user {$this?->user?->name}: {$exception->getMessage()}");
    }
}
