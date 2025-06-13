<?php

declare(strict_types=1);

namespace CrewAI\PHP\Agent;

use CrewAI\PHP\Core\Exceptions\CrewAIException;
use CrewAI\PHP\Core\Interfaces\ToolInterface;

class ToolsHandler
{
    private array $tools;

    public function __construct(array $tools)
    {
        $this->tools = [];
        foreach ($tools as $tool) {
            if ($tool instanceof ToolInterface) {
                $this->tools[$tool->getName()] = $tool;
            } else {
                throw new CrewAIException('Invalid tool provided. Must implement ToolInterface.');
            }
        }
    }

    public function executeTool(string $toolName, string $toolInput): string
    {
        if (! isset($this->tools[$toolName])) {
            throw new CrewAIException(sprintf('Tool %s not found.', $toolName));
        }

        $tool = $this->tools[$toolName];

        return $tool->execute($toolInput);
    }
}
