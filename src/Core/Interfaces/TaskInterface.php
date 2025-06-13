<?php

declare(strict_types=1);

namespace CrewAI\PHP\Core\Interfaces;

interface TaskInterface
{
    public function getDescription(): string;

    public function getExpectedOutput(): string;

    public function getAgent(): ?AgentInterface;

    public function getOutputJson(): ?array;

    public function getOutputPydantic(): ?string;

    public function getCallback(): ?callable;

    public function getHumanInput(): ?bool;

    public function getContext(): array;

    public function getTools(): array;

    public function getAsync(): bool;

    public function getVerbose(): bool;

    public function getOutputFile(): ?string;

    public function getAgentExecutor(): ?object;

    public function getOutputFormat(): ?string;

    public function getPromptTemplate(): ?string;

    public function getStepCallback(): ?callable;

    public function getConfig(): array;
}
