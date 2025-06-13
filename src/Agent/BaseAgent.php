<?php

declare(strict_types=1);

namespace CrewAI\PHP\Agent;

use CrewAI\PHP\Core\Interfaces\AgentInterface;
use CrewAI\PHP\Core\Interfaces\LLMInterface;
use CrewAI\PHP\Core\Interfaces\TaskInterface;

abstract class BaseAgent implements AgentInterface
{
    protected string $role;
    protected string $goal;
    protected string $backstory;
    protected array $tools;
    protected LLMInterface $llm;
    protected bool $allowDelegation;
    protected bool $verbose;

    public function __construct(
        string $role,
        string $goal,
        string $backstory,
        LLMInterface $llm,
        array $tools = [],
        bool $allowDelegation = true,
        bool $verbose = false
    ) {
        $this->role = $role;
        $this->goal = $goal;
        $this->backstory = $backstory;
        $this->llm = $llm;
        $this->tools = $tools;
        $this->allowDelegation = $allowDelegation;
        $this->verbose = $verbose;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getName(): string
    {
        return $this->role; // Use role as name for now
    }

    public function getGoal(): string
    {
        return $this->goal;
    }

    public function getBackstory(): string
    {
        return $this->backstory;
    }

    public function getTools(): array
    {
        return $this->tools;
    }

    public function getLLM(): LLMInterface
    {
        return $this->llm;
    }

    public function allowDelegation(): bool
    {
        return $this->allowDelegation;
    }

    public function verbose(): bool
    {
        return $this->verbose;
    }

    abstract public function executeTask(TaskInterface $task): string;
}
