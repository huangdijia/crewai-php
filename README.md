# CrewAI PHP

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)

A PHP implementation of the CrewAI framework for building and orchestrating AI agent crews. CrewAI PHP allows you to create autonomous AI agents that can work together to complete complex tasks through role-playing, collaboration, and delegation.

## Features

- 🤖 **Multi-Agent System**: Create and manage multiple AI agents with distinct roles
- 🔧 **Tool Integration**: Equip agents with custom tools for specific tasks
- 📋 **Task Management**: Define complex workflows with task dependencies
- 🎭 **Role-Playing**: Agents have defined roles, goals, and backstories
- 🔄 **Process Orchestration**: Sequential, parallel, and hierarchical task execution
- 📊 **Memory Management**: Short-term, long-term, and contextual memory
- 🎯 **Delegation**: Agents can delegate tasks to other specialized agents
- 📝 **Comprehensive Logging**: Built-in logging and monitoring capabilities

## Requirements

- PHP 8.2 or higher
- Swoole extension
- Composer

## Installation

Install via Composer:

```bash
composer require huangdijia/crewai-php
```

## Quick Start

Here's a simple example of creating a research crew:

```php
<?php

require_once 'vendor/autoload.php';

use CrewAI\PHP\Agent\Agent;
use CrewAI\PHP\Crew\Crew;
use CrewAI\PHP\Task\Task;
use CrewAI\PHP\LLM\BaseLLM;
use CrewAI\PHP\Tool\BaseTool;

// Custom LLM implementation
class YourLLM extends BaseLLM
{
    public function call(array $messages, array $options = []): string
    {
        // Implement your LLM integration here
        // This could be OpenAI, Claude, or any other LLM service
        return "Your LLM response";
    }
}

// Custom tool implementation
class SearchTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('search_tool', 'A tool to perform web searches.');
    }

    public function execute(string $input): string
    {
        // Implement your search logic here
        return "Search results for: " . $input;
    }
}

// Initialize components
$llm = new YourLLM('your-model');
$searchTool = new SearchTool();

// Create agents
$researcher = new Agent(
    role: 'Senior Researcher',
    goal: 'Uncover groundbreaking technologies',
    backstory: 'A seasoned researcher with a knack for finding hidden gems.',
    llm: $llm,
    tools: [$searchTool],
    verbose: true
);

$writer = new Agent(
    role: 'Content Writer',
    goal: 'Craft compelling narratives',
    backstory: 'A creative writer who can turn complex ideas into engaging stories.',
    llm: $llm,
    verbose: true
);

// Create tasks
$researchTask = new Task(
    description: 'Research the latest advancements in AI ethics.',
    expectedOutput: 'A comprehensive summary of current AI ethics trends.',
    agent: $researcher
);

$writeTask = new Task(
    description: 'Write a detailed report based on the research findings.',
    expectedOutput: 'A well-structured report on AI ethics.',
    agent: $writer,
    context: [$researchTask]
);

// Create and run crew
$crew = new Crew(
    agents: [$researcher, $writer],
    tasks: [$researchTask, $writeTask],
    process: 'sequential',
    verbose: true
);

$result = $crew->kickoff();
echo $result;
```

## Core Concepts

### Agents

Agents are autonomous AI entities with specific roles, goals, and capabilities:

```php
$agent = new Agent(
    role: 'Data Analyst',
    goal: 'Extract insights from complex datasets',
    backstory: 'An experienced analyst with expertise in statistical modeling.',
    llm: $llm,
    tools: [$analysisTools],
    allowDelegation: true,
    verbose: true
);
```

### Tasks

Tasks define what needs to be accomplished:

```php
$task = new Task(
    description: 'Analyze sales data for Q4 trends',
    expectedOutput: 'A detailed report with visualizations',
    agent: $analyst,
    context: [$dataCollectionTask], // Dependencies
    verbose: true
);
```

### Tools

Tools extend agent capabilities:

```php
class DatabaseTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('database_query', 'Execute database queries');
    }

    public function execute(string $input): string
    {
        // Your database query logic
        return $results;
    }
}
```

### Crews

Crews orchestrate multiple agents and tasks:

```php
$crew = new Crew(
    agents: [$agent1, $agent2],
    tasks: [$task1, $task2],
    process: 'sequential', // 'parallel' or 'hierarchical'
    verbose: true,
    stepCallback: $stepCallback // Optional callback for monitoring
);
```

## Advanced Features

### Memory Management

CrewAI PHP includes sophisticated memory management:

- **Short-term Memory**: Recent interactions and context
- **Long-term Memory**: Persistent knowledge and learnings
- **Entity Memory**: Information about specific entities
- **Contextual Memory**: Task and conversation context

### Process Types

1. **Sequential**: Tasks executed one after another
2. **Hierarchical**: Manager agent delegates and coordinates tasks

### Custom LLM Integration

Implement your own LLM by extending `BaseLLM`:

```php
class OpenAILLM extends BaseLLM
{
    private string $apiKey;
    
    public function __construct(string $model, string $apiKey)
    {
        parent::__construct($model);
        $this->apiKey = $apiKey;
    }
    
    public function call(array $messages, array $options = []): string
    {
        // OpenAI API integration
        // Handle authentication, rate limiting, etc.
        return $response;
    }
}
```

## Development

### Running Tests

```bash
composer test
```

### Code Style

```bash
composer cs-fix
```

### Static Analysis

```bash
composer analyse
```

### All Checks

```bash
composer check
```

## Project Structure

```text
src/
├── Agent/           # Agent implementations and executors
├── Core/           # Core interfaces and exceptions
├── Crew/           # Crew orchestration
├── LLM/            # Language model abstractions
├── Memory/         # Memory management systems
├── Task/           # Task definitions and management
├── Tool/           # Tool implementations
└── Utilities/      # Helper classes and utilities

tests/              # Unit tests
examples/           # Example implementations
```

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

### Development Setup

```bash
git clone https://github.com/huangdijia/crewai-php.git
cd crewai-php
composer install
composer check
```

## Examples

Check out the `examples/` directory for more comprehensive examples:

- `simple_crew.php` - Basic crew setup and execution
- `parallel_crew.php` - Demonstrates parallel task execution
- More examples coming soon!

## Roadmap

- [ ] Enhanced hierarchical process support
- [ ] More built-in tools (web scraping, file operations, etc.)
- [ ] Integration with popular LLM providers
- [ ] Advanced memory persistence options
- [ ] Real-time collaboration features
- [ ] Web interface for crew management
- [ ] Performance optimizations

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Inspired by the original [CrewAI](https://github.com/joaomdmoura/crewAI) Python framework
- Built with modern PHP practices and strong typing
- Designed for production use with comprehensive error handling

## Support

- 📧 Email: [huangdijia@gmail.com](mailto:huangdijia@gmail.com)
- 🐛 Issues: [GitHub Issues](https://github.com/huangdijia/crewai-php/issues)
- 💬 Discussions: [GitHub Discussions](https://github.com/huangdijia/crewai-php/discussions)

---

Made with ❤️ by [Huangdijia](https://github.com/huangdijia)
