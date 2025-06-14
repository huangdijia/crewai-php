<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use CrewAI\PHP\Agent\Agent;
use CrewAI\PHP\Crew\Crew;
use CrewAI\PHP\LLM\BaseLLM;
use CrewAI\PHP\Task\Task;
use CrewAI\PHP\Tool\BaseTool;

// Mock LLM Implementation
class MockLLM extends BaseLLM
{
    public function call(array $messages, array $options = []): string
    {
        // Simulate LLM response based on the last message
        $lastMessage = end($messages);
        $content = $lastMessage['content'];

        if (str_contains($content, 'research')) {
            return "Thought: I need to find information about the topic.\nTool: search_tool\nTool Input: ".substr($content, strpos($content, 'research') + 9);
        } elseif (str_contains($content, 'write a report')) {
            return "Thought: I have gathered enough information. Now I will write the report.\nFinal Answer: This is a mock report about the requested topic.";
        } else {
            return 'Final Answer: I am a mock LLM and I received: '.$content;
        }
    }
}

// Mock Search Tool Implementation
class SearchTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('search_tool', 'A tool to perform web searches.');
    }

    public function execute(string $input): string
    {
        return "Search results for '".$input."': Mock data from web search.";
    }
}

// Initialize LLM and Tools
$mockLLM = new MockLLM('mock-model');
$searchTool = new SearchTool();

// Create Agents
$researcher = new Agent(
    role: 'Senior Researcher',
    goal: 'Uncover groundbreaking technologies',
    backstory: 'A seasoned researcher with a knack for finding hidden gems.',
    llm: $mockLLM,
    tools: [$searchTool],
    verbose: true
);

$writer = new Agent(
    role: 'Content Writer',
    goal: 'Craft compelling narratives',
    backstory: 'A creative writer who can turn complex ideas into engaging stories.',
    llm: $mockLLM,
    verbose: true
);

// Create Tasks
$researchTask = new Task(
    description: 'Research the latest advancements in AI ethics.',
    expectedOutput: 'A comprehensive summary of current AI ethics trends and challenges.',
    agent: $researcher,
    verbose: true,
    async: true
);

$writeReportTask = new Task(
    description: 'Write a detailed report based on the research findings.',
    expectedOutput: 'A well-structured report on AI ethics, including key findings and recommendations.',
    agent: $writer,
    context: [$researchTask],
    verbose: true,
    async: true
);

// Create Crew
$crew = new Crew(
    agents: [$researcher, $writer],
    tasks: [$researchTask, $writeReportTask],
    process: 'parallel',
    verbose: true
);

// Kickoff the Crew
echo "\n--- CrewAI PHP Execution Started ---\n";
$result = $crew->kickoff();
echo "\n--- CrewAI PHP Execution Finished ---\n";
echo "\nFinal Result:\n".$result."\n";
