<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Mails;

use App\Domain\Notification\ValueObjects\NotificationContent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class BookFinishedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    private const string QUEUE = 'notifications';

    public function __construct(
        private readonly NotificationContent $content,
    ) {
        $this->onQueue(self::QUEUE);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->content->title);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notification.book-finished',
            with: [
                'title'  => $this->content->title,
                'body'   => $this->content->body,
                'bookId' => $this->content->data['book_id'] ?? null,
            ],
        );
    }
}
