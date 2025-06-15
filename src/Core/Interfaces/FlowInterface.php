<?php

declare(strict_types=1);

namespace CrewAI\PHP\Core\Interfaces;

interface FlowInterface
{
    public function run(): string;

    public function getCrews(): array;

    public function getProcess(): string;

    public function getVerbose(): bool;
}
