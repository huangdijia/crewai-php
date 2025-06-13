<?php

declare(strict_types=1);

namespace CrewAI\PHP\Core\Interfaces;

interface CrewInterface
{
    public function kickoff(): string;

    public function getAgents(): array;

    public function getTasks(): array;

    public function getProcess(): string;

    public function getVerbose(): bool;

    public function getFullOutput(): bool;

    public function getStepCallback(): ?callable;

    public function getShareCrew(): bool;

    public function getManagerLLM(): ?LLMInterface;
}
