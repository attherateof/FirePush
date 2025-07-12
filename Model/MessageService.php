<?php

declare(strict_types=1);

namespace MageStack\FirePush\Model;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use MageStack\FirePush\Api\MessageInterface;
use MageStack\FirePush\Api\MessageServiceInterface;
use Psr\Log\LoggerInterface;

class MessageService implements MessageServiceInterface
{
    public function __construct(
        private Client $client,
        private LoggerInterface $logger
    ) {
    }

    public function send(MessageInterface $message, string $topic, $validateOnly = false): bool
    {
        try {
            $cloudMessage = CloudMessage::withTarget('topic', $topic);
            $canSend = false;

            if ($message->getTitle() && $message->getBody()) {
                $notification = Notification::create($message->getTitle(), $message->getBody());
                $cloudMessage = $cloudMessage->withNotification($notification);
                $canSend = true;
            }

            if (!empty($message->getData())) {
                $cloudMessage = $cloudMessage->withData($message->getData());
                $canSend = true;
            }

            if (!$canSend) {
                throw new \InvalidArgumentException('Either title/body or data must be set.');
            }

            $this->client->get()->send($cloudMessage, $validateOnly);

            return true;
        } catch (\Throwable $th) {
            $this->logger->error('FCM topic message error', [
                'error_message' => $th->getMessage(),
                'topic' => $topic,
                'title' => $message->getTitle(),
                'body' => $message->getBody(),
                'data' => $message->getData(),
                'stack_trace' => $th->getTraceAsString(),
            ]);

            return false;
        } finally {
            $message->resetTitle()->resetBody()->resetData();
        }
    }
}
