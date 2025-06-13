<?php

declare(strict_types=1);

namespace CrewAI\PHP\Agent;

use CrewAI\PHP\Agent\Executor\AgentExecutor;
use CrewAI\PHP\Core\Interfaces\LLMInterface;
use CrewAI\PHP\Core\Interfaces\TaskInterface;

class Agent extends BaseAgent
{
    public function __construct(
        string $role,
        string $goal,
        string $backstory,
        LLMInterface $llm,
        array $tools = [],
        bool $allowDelegation = true,
        bool $verbose = false
    ) {
        parent::__construct($role, $goal, $backstory, $llm, $tools, $allowDelegation, $verbose);
    }

    public function executeTask(TaskInterface $task): string
    {
        $executor = new AgentExecutor(
            llm: $this->llm,
            task: $task,
            agent: $this,
            tools: $this->tools,
            verbose: $this->verbose
        );

        return $executor->execute();
    }
}
