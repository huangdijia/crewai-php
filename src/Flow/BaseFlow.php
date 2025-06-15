<?php

declare(strict_types=1);

namespace CrewAI\PHP\Flow;

use CrewAI\PHP\Core\Interfaces\FlowInterface;

abstract class BaseFlow implements FlowInterface
{
    protected array $crews;
    protected string $process;
    protected bool $verbose;

    public function __construct(array $crews, string $process = 'sequential', bool $verbose = false)
    {
        $this->crews = $crews;
        $this->process = $process;
        $this->verbose = $verbose;
    }

    public function getCrews(): array
    {
        return $this->crews;
    }

    public function getProcess(): string
    {
        return $this->process;
    }

    public function getVerbose(): bool
    {
        return $this->verbose;
    }

    abstract public function run(): string;
}
