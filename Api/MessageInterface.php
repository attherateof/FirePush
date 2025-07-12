<?php

namespace MageStack\FirePush\Api;

interface MessageInterface
{
    public function setTitle(?string $title): self;
    public function getTitle(): ?string;
    public function resetTitle(): self;

    public function setBody(?string $body): self;
    public function getBody(): ?string;
    public function resetBody(): self;

    public function setData(array $data): self;
    public function getData(): array;
    public function resetData(): self;
}
