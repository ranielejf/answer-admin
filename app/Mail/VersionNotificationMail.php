<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Version;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VersionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Version $version,
        public User $user,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Platform Update v'.$this->version->version_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.version-notification',
            with: [
                'version' => $this->version,
                'user' => $this->user,
                'appName' => config('app.name'),
                'appUrl' => config('app.url'),
            ],
        );
    }
}
