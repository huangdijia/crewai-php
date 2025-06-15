<?php

declare(strict_types=1);

namespace CrewAI\PHP\Crew;

use CrewAI\PHP\Core\Exceptions\CrewAIException;
use CrewAI\PHP\Core\Interfaces\AgentInterface;
use CrewAI\PHP\Core\Interfaces\LLMInterface;
use CrewAI\PHP\Core\Interfaces\StepCallbackInterface;
use CrewAI\PHP\Core\Interfaces\TaskInterface;
use Swoole\Coroutine\WaitGroup;

class Crew extends BaseCrew
{
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
                        ($this->stepCallback)(['result' => $taskResult, 'task' => $task->getDescription()]);
                    }
                }

                break;
            case 'parallel':
                \Swoole\Coroutine\run(function () use (&$output) {
                    $wg = new WaitGroup();

                    foreach ($this->tasks as $task) {
                        if (! $task instanceof TaskInterface) {
                            throw new CrewAIException('Invalid task provided. Must implement TaskInterface.');
                        }

                        $assignedAgent = $task->getAgent();
                        if (null === $assignedAgent) {
                            $assignedAgent = $this->agents[0];
                        }

                        if (! $assignedAgent instanceof AgentInterface) {
                            throw new CrewAIException('Invalid agent assigned to task. Must implement AgentInterface.');
                        }

                        $wg->add();

                        \Swoole\Coroutine\go(function () use ($task, $assignedAgent, $wg, &$output) {
                            if ($this->verbose) {
                                echo "\nExecuting Task: ".$task->getDescription().' with Agent: '.$assignedAgent->getRole()."\n";
                            }

                            $taskResult = $assignedAgent->executeTask($task);
                            $output[] = $taskResult;

                            if ($this->stepCallback) {
                                ($this->stepCallback)(['result' => $taskResult, 'task' => $task->getDescription()]);
                            }

                            $wg->done();
                        });
                    }

                    $wg->wait();
                });

                break;
            case 'hierarchical':
                throw new CrewAIException('Hierarchical process not yet implemented.');
            default:
                throw new CrewAIException('Unknown process type: '.$this->process);
        }

        return implode("\n\n", $output);
    }
}
