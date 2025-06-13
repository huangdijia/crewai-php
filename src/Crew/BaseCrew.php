<?php

declare(strict_types=1);

namespace CrewAI\PHP\Crew;

use CrewAI\PHP\Core\Interfaces\CrewInterface;
use CrewAI\PHP\Core\Interfaces\LLMInterface;
use CrewAI\PHP\Core\Interfaces\StepCallbackInterface;

abstract class BaseCrew implements CrewInterface
{
    protected array $agents;
    protected array $tasks;
    protected string $process;
    protected bool $verbose;
    protected bool $fullOutput;
    protected ?StepCallbackInterface $stepCallback;
    protected bool $shareCrew;
    protected ?LLMInterface $managerLLM;

    public function __construct(
        array $agents,
        array $tasks,
        string $process = 'sequential',
        bool $verbose = false,
        bool $fullOutput = false,
        ?StepCallbackInterface $stepCallback = null,
        bool $shareCrew = false,
        ?LLMInterface $managerLLM = null
    ) {
        $this->agents = $agents;
        $this->tasks = $tasks;
        $this->process = $process;
        $this->verbose = $verbose;
        $this->fullOutput = $fullOutput;
        $this->stepCallback = $stepCallback;
        $this->shareCrew = $shareCrew;
        $this->managerLLM = $managerLLM;
    }

    public function getAgents(): array
    {
        return $this->agents;
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function getProcess(): string
    {
        return $this->process;
    }

    public function getVerbose(): bool
    {
        return $this->verbose;
    }

    public function getFullOutput(): bool
    {
        return $this->fullOutput;
    }

    public function getStepCallback(): ?callable
    {
        return $this->stepCallback;
    }

    public function getShareCrew(): bool
    {
        return $this->shareCrew;
    }

    public function getManagerLLM(): ?LLMInterface
    {
        return $this->managerLLM;
    }

    abstract public function kickoff(): string;
}
