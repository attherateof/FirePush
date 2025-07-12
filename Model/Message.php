<?php

declare(strict_types=1);

namespace MageStack\FirePush\Model;

use MageStack\FirePush\Api\MessageInterface;

class Message implements MessageInterface
{
    private ?string $title = null;
    private ?string $body = null;
    private array $data = [];

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function resetTitle(): self
    {
        $this->title = null;
        return $this;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;
        return $this;
    }
    public function getBody(): ?string
    {
        return $this->body;
    }
    public function resetBody(): self
    {
        $this->body = null;
        return $this;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }
    public function getData(): array
    {
        return $this->data;
    }
    public function resetData(): self
    {
        $this->data = [];
        return $this;
    }
}
