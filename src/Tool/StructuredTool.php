<?php

declare(strict_types=1);

namespace CrewAI\PHP\Tool;

class StructuredTool extends BaseTool
{
    private array $parameters;

    public function __construct(string $name, string $description, array $parameters)
    {
        parent::__construct($name, $description);
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function execute(string $input): string
    {
        // This is a placeholder. In a real scenario, this would parse the input
        // based on $this->parameters and call the actual tool logic.
        return 'Executing structured tool: '.$this->name.' with input: '.$input;
    }
}
