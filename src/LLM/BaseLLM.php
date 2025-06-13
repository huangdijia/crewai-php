<?php

declare(strict_types=1);

namespace CrewAI\PHP\LLM;

use CrewAI\PHP\Core\Interfaces\LLMInterface;

abstract class BaseLLM implements LLMInterface
{
    protected string $model;
    protected ?float $temperature;
    protected array $stop;

    public function __construct(string $model, ?float $temperature = null, array $stop = [])
    {
        $this->model = $model;
        $this->temperature = $temperature;
        $this->stop = $stop;
    }

    public function supportsStopWords(): bool
    {
        return ! empty($this->stop);
    }

    public function getStopWords(): array
    {
        return $this->stop;
    }

    abstract public function call(array $messages, array $options = []): string;

    protected function constructTextPrompt(string $prompt): array
    {
        return [['role' => 'user', 'content' => $prompt]];
    }

    protected function constructMessagesPrompt(array $messages): array
    {
        return $messages;
    }
}
