<?php
declare(strict_types=1);

namespace CrewAI\PHP\Task;

use CrewAI\PHP\Core\Interfaces\AgentInterface;
use CrewAI\PHP\Core\Interfaces\TaskInterface;

abstract class BaseTask implements TaskInterface
{
    protected string $description;
    protected string $expectedOutput;
    protected ?AgentInterface $agent;
    protected ?array $outputJson;
    protected ?string $outputPydantic;
    protected callable|null $callback;
    protected ?bool $humanInput;
    protected array $context;
    protected array $tools;
    protected bool $async;
    protected bool $verbose;
    protected ?string $outputFile;
    protected ?object $agentExecutor;
    protected ?string $outputFormat;
    protected ?string $promptTemplate;
    protected mixed $stepCallback;
    protected array $config;

    public function __construct(
        string $description,
        string $expectedOutput,
        ?AgentInterface $agent = null,
        ?array $outputJson = null,
        ?string $outputPydantic = null,
        mixed $callback = null,
        ?bool $humanInput = null,
        array $context = [],
        array $tools = [],
        bool $async = false,
        bool $verbose = false,
        ?string $outputFile = null,
        ?object $agentExecutor = null,
        ?string $outputFormat = null,
        ?string $promptTemplate = null,
           callable|null $callback = null,,
        array $config = []
    )
    {
        $this->description = $description;
        $this->expectedOutput = $expectedOutput;
        $this->agent = $agent;
        $this->outputJson = $outputJson;
        $this->outputPydantic = $outputPydantic;
        $this->callback = $callback;
        $this->humanInput = $humanInput;
        $this->context = $context;
        $this->tools = $tools;
        $this->async = $async;
        $this->verbose = $verbose;
        $this->outputFile = $outputFile;
        $this->agentExecutor = $agentExecutor;
        $this->outputFormat = $outputFormat;
        $this->promptTemplate = $promptTemplate;
        $this->stepCallback = $stepCallback;
        $this->config = $config;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getExpectedOutput(): string
    {
        return $this->expectedOutput;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function getOutputJson(): ?array
    {
        return $this->outputJson;
    }

    public function getOutputPydantic(): ?string
    {
        return $this->outputPydantic;
    }

    public function getCallback(): \Closure|null
    {
        return $this->callback;
    }

    public function getHumanInput(): ?bool
    {
        return $this->humanInput;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getTools(): array
    {
        return $this->tools;
    }

    public function getAsync(): bool
    {
        return $this->async;
    }

    public function getVerbose(): bool
    {
        return $this->verbose;
    }

    public function getOutputFile(): ?string
    {
        return $this->outputFile;
    }

    public function getAgentExecutor(): ?object
    {
        return $this->agentExecutor;
    }

    public function getOutputFormat(): ?string
    {
        return $this->outputFormat;
    }

    public function getPromptTemplate(): ?string
    {
        return $this->promptTemplate;
    }

    public function getStepCallback(): \Closure|null
    {
        return $this->stepCallback;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}


