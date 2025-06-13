<?php

declare(strict_types=1);

namespace CrewAI\PHP\Agent\Parser;

class AgentFinish
{
    public string $output;
    public string $log;

    public function __construct(string $output, string $log)
    {
        $this->output = $output;
        $this->log = $log;
    }
}
