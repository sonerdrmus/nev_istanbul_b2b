<?php

namespace App\Mail;

use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactMessageMail extends Mailable
{
    /**
     * @param  list<UploadedFile>  $files
     */
    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public ?string $phone,
        public ?string $companyName,
        public ?string $topic,
        public string $bodyText,
        public array $files = [],
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->topic
            ? __('store.contact.mail_subject_prefixed', ['subject' => $this->topic])
            : __('store.contact.mail_subject_default', ['name' => $this->senderName]);

        return new Envelope(
            subject: $subject,
            replyTo: [
                new Address($this->senderEmail, $this->senderName),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
        );
    }

    /**
     * @return list<Attachment>
     */
    public function attachments(): array
    {
        $attached = [];

        foreach ($this->files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $path = $file->getRealPath();

            if ($path === false) {
                continue;
            }

            $attachment = Attachment::fromPath($path)
                ->as(basename($file->getClientOriginalName()));

            if ($file->getMimeType()) {
                $attachment = $attachment->withMime($file->getMimeType());
            }

            $attached[] = $attachment;
        }

        return $attached;
    }
}
