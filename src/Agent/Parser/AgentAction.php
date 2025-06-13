<?php

declare(strict_types=1);

namespace CrewAI\PHP\Agent\Parser;

class AgentAction
{
    public string $tool;
    public string $toolInput;
    public string $log;

    public function __construct(string $tool, string $toolInput, string $log)
    {
        $this->tool = $tool;
        $this->toolInput = $toolInput;
        $this->log = $log;
    }
}
