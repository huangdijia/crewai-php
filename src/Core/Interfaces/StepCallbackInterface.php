<?php

declare(strict_types=1);

namespace CrewAI\PHP\Core\Interfaces;

interface StepCallbackInterface
{
    public function __invoke(array $step): void;
}
