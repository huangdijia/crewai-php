<?php

declare(strict_types=1);

namespace CrewAI\PHP\Core\Interfaces;

interface LLMInterface
{
    public function call(array $messages, array $options = []): string;

    public function supportsStopWords(): bool;

    public function getStopWords(): array;
}
