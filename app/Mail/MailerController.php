<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailerController extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $mail_data)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->mail_data['type']) {
            'Login' => 'Login Request',
            'Login_Auth' => 'Login Authentication',
            'Registration' => 'Account Registration',
            'Forgot_Password' => 'Forgot Password?',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = match($this->mail_data['type']) {
            'Login' => 'emails.login',
            'Login_Auth' => 'emails.login_auth',
            'Registration' => 'emails.register',
            'Forgot_Password' => 'emails.forgot_password',
        };

        return new Content(
            view: $view,
            with: [
                'mail_data' => $this->mail_data,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
