<?php

declare(strict_types=1);

namespace CrewAI\PHP\Task;

use CrewAI\PHP\Core\Interfaces\AgentInterface;

class Task extends BaseTask
{
    public function __construct(
        string $description,
        string $expectedOutput,
        ?AgentInterface $agent = null,
        ?array $outputJson = null,
        ?string $outputPydantic = null,
        ?callable $callback = null,
        ?bool $humanInput = null,
        array $context = [],
        array $tools = [],
        bool $async = false,
        bool $verbose = false,
        ?string $outputFile = null,
        ?object $agentExecutor = null,
        ?string $outputFormat = null,
        ?string $promptTemplate = null,
        ?callable $stepCallback = null,
        array $config = []
    ) {
        parent::__construct(
            $description,
            $expectedOutput,
            $agent,
            $outputJson,
            $outputPydantic,
            $callback,
            $humanInput,
            $context,
            $tools,
            $async,
            $verbose,
            $outputFile,
            $agentExecutor,
            $outputFormat,
            $promptTemplate,
            $stepCallback,
            $config
        );
    }
}
