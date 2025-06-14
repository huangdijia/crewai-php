<?php

declare(strict_types=1);

namespace CrewAI\PHP\Flow;

use CrewAI\PHP\Core\Exceptions\CrewAIException;
use CrewAI\PHP\Core\Interfaces\CrewInterface;
use Swoole\Coroutine\WaitGroup;

class Flow extends BaseFlow
{
    public function __construct(array $crews, string $process = 'sequential', bool $verbose = false)
    {
        parent::__construct($crews, $process, $verbose);
    }

    public function run(): string
    {
        if (empty($this->crews)) {
            throw new CrewAIException('Flow must have at least one crew.');
        }

        $output = [];

        switch ($this->process) {
            case 'sequential':
                foreach ($this->crews as $crew) {
                    if (! $crew instanceof CrewInterface) {
                        throw new CrewAIException('Invalid crew provided. Must implement CrewInterface.');
                    }

                    if ($this->verbose) {
                        echo "\nRunning Crew flow step\n";
                    }

                    $output[] = $crew->kickoff();
                }

                break;
            case 'parallel':
                \Swoole\Coroutine\run(function () use (&$output) {
                    $wg = new WaitGroup();

                    foreach ($this->crews as $crew) {
                        if (! $crew instanceof CrewInterface) {
                            throw new CrewAIException('Invalid crew provided. Must implement CrewInterface.');
                        }

                        $wg->add();

                        \Swoole\Coroutine\go(function () use ($crew, $wg, &$output) {
                            if ($this->verbose) {
                                echo "\nRunning Crew flow step\n";
                            }

                            $output[] = $crew->kickoff();
                            $wg->done();
                        });
                    }

                    $wg->wait();
                });

                break;
            default:
                throw new CrewAIException('Unknown process type: '.$this->process);
        }

        return implode("\n\n", $output);
    }
}
