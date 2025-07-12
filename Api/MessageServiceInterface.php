<?php

namespace MageStack\FirePush\Api;

interface MessageServiceInterface
{
    public function send(MessageInterface $message, string $topic, $validateOnly = false): bool;
}
