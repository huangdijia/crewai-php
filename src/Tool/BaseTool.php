<?php

declare(strict_types=1);

namespace CrewAI\PHP\Tool;

use CrewAI\PHP\Core\Interfaces\ToolInterface;

abstract class BaseTool implements ToolInterface
{
    protected string $name;
    protected string $description;

    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    abstract public function execute(string $input): string;
}
