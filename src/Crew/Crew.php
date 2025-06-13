<?php

declare(strict_types=1);

namespace CrewAI\PHP\Crew;

use CrewAI\PHP\Core\Exceptions\CrewAIException;
use CrewAI\PHP\Core\Interfaces\AgentInterface;
use CrewAI\PHP\Core\Interfaces\LLMInterface;
use CrewAI\PHP\Core\Interfaces\TaskInterface;

class Crew extends BaseCrew
{
    public function __construct(
        array $agents,
        array $tasks,
        string $process = 'sequential',
        bool $verbose = false,
        bool $fullOutput = false,
        ?callable $stepCallback = null,
        bool $shareCrew = false,
        ?LLMInterface $managerLLM = null
    ) {
        parent::__construct(
            $agents,
            $tasks,
            $process,
            $verbose,
            $fullOutput,
            $stepCallback,
            $shareCrew,
            $managerLLM
        );
    }

    public function kickoff(): string
    {
        if (empty($this->agents)) {
            throw new CrewAIException('Crew must have at least one agent.');
        }

        if (empty($this->tasks)) {
            throw new CrewAIException('Crew must have at least one task.');
        }

        $output = [];

        switch ($this->process) {
            case 'sequential':
                foreach ($this->tasks as $task) {
                    if (! $task instanceof TaskInterface) {
                        throw new CrewAIException('Invalid task provided. Must implement TaskInterface.');
                    }

                    $assignedAgent = $task->getAgent();
                    if (null === $assignedAgent) {
                        // If no agent is assigned to the task, assign the first agent in the crew
                        $assignedAgent = $this->agents[0];
                    }

                    if (! $assignedAgent instanceof AgentInterface) {
                        throw new CrewAIException('Invalid agent assigned to task. Must implement AgentInterface.');
                    }

                    if ($this->verbose) {
                        echo "\nExecuting Task: ".$task->getDescription().' with Agent: '.$assignedAgent->getRole()."\n";
                    }

                    $taskResult = $assignedAgent->executeTask($task);
                    $output[] = $taskResult;

                    if ($this->stepCallback) {
                        ($this->stepCallback)($taskResult);
                    }
                }

                break;
            case 'hierarchical':
                throw new CrewAIException('Hierarchical process not yet implemented.');
            default:
                throw new CrewAIException('Unknown process type: '.$this->process);
        }

        return implode("\n\n", $output);
    }
}
