<?php

declare(strict_types=1);

namespace CrewAI\PHP\Core\Interfaces;

interface AgentInterface
{
    public function executeTask(TaskInterface $task): string;

    public function getName(): string;

    public function getRole(): string;

    public function getGoal(): string;

    public function getBackstory(): string;

    public function getTools(): array;

    public function getLLM(): LLMInterface;

    public function allowDelegation(): bool;

    public function verbose(): bool;
}
