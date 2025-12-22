<?php

namespace App\Mail;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Email;
use App\Services\MicrosoftGraphMailService;

class GraphTransport extends AbstractTransport
{
    protected $graph;

    public function __construct(MicrosoftGraphMailService $graph)
    {
        // No dispatcher/logger needed
        parent::__construct(null, null);
        $this->graph = $graph;
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $email */
        $email = $message->getOriginalMessage();

        $subject = $email->getSubject();
        $body = $email->getHtmlBody() ?? $email->getTextBody();
        $from = config('graph.user_email');

        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = [
                '@odata.type' => '#microsoft.graph.fileAttachment',
                'name' => $attachment->getFilename() ?? 'attachment',
                'contentType' => $attachment->getContentType(),
                'contentBytes' => base64_encode($attachment->getBody())
            ];
        }

        foreach ($email->getTo() as $to) {
            $this->graph->sendMail(
                $from,
                $to->getAddress(),
                $subject,
                $body,
                $attachments
            );
        }
    }

    public function __toString(): string
    {
        return 'graph';
    }
}
