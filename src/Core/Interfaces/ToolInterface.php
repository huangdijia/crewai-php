<?php

declare(strict_types=1);

namespace CrewAI\PHP\Core\Interfaces;

interface ToolInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function execute(string $input): string;
}
