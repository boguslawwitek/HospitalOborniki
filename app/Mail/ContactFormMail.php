<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Data from the contact form
     */
    public $name;
    public $email;
    public $userSubject;
    public $messageContent;
    public $phone;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $name,
        string $email,
        string $subject,
        string $message,
        ?string $phone = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->userSubject = $subject;
        $this->messageContent = $message;
        $this->phone = $phone;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wiadomość z formularza kontaktowego',
            replyTo: $this->email,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'subject' => $this->userSubject,
                'messageContent' => $this->messageContent,
                'phone' => $this->phone,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
