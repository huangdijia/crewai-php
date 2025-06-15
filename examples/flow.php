<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use CrewAI\PHP\Agent\Agent;
use CrewAI\PHP\Crew\Crew;
use CrewAI\PHP\Flow\Flow;
use CrewAI\PHP\Knowledge\KnowledgeBase;
use CrewAI\PHP\LLM\BaseLLM;
use CrewAI\PHP\Task\Task;
use CrewAI\PHP\Tool\BaseTool;

class MockLLM extends BaseLLM
{
    public function call(array $messages, array $options = []): string
    {
        $lastMessage = end($messages);
        $content = $lastMessage['content'];

        if (str_contains($content, 'research')) {
            return "Thought: I need to find information about the topic.\nTool:search_tool\nTool Input: ".substr($content, strpos($content, 'research') + 9);
        } elseif (str_contains($content, 'write a report')) {
            return "Thought: I have gathered enough information. Now I will write the report.\nFinal Answer: This is a mock report about the requested topic.";
        }

        return 'Final Answer: '.$content;
    }
}

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

$llm = new MockLLM('mock-model');
$searchTool = new SearchTool();

$knowledge = new KnowledgeBase();
$knowledge->addDocument('doc1', 'AI ethics is about moral considerations of artificial intelligence.');
$knowledge->addDocument('doc2', 'Responsible AI includes fairness and transparency.');

$researcher = new Agent(
    role: 'Researcher',
    goal: 'Find information about AI ethics',
    backstory: 'An expert in gathering data.',
    llm: $llm,
    tools: [$searchTool],
    verbose: true
);

$writer = new Agent(
    role: 'Writer',
    goal: 'Write reports',
    backstory: 'Experienced technical writer.',
    llm: $llm,
    verbose: true
);

$researchTask = new Task(
    description: 'research AI ethics',
    expectedOutput: 'key facts about AI ethics',
    agent: $researcher,
    verbose: true
);

$writeTask = new Task(
    description: 'write a report on AI ethics',
    expectedOutput: 'detailed report',
    agent: $writer,
    context: [$researchTask],
    verbose: true
);

$researchCrew = new Crew(agents: [$researcher], tasks: [$researchTask], verbose: true);
$writeCrew = new Crew(agents: [$writer], tasks: [$writeTask], verbose: true);

$flow = new Flow(crews: [$researchCrew, $writeCrew], process: 'sequential', verbose: true);

echo "\n--- Flow Execution Started ---\n";
$result = $flow->run();

print_r($knowledge->search('AI'));

echo "\n--- Flow Execution Finished ---\n";

echo "\nFlow Result:\n".$result."\n";
