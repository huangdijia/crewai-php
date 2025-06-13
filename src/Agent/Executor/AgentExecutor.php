<?php

declare(strict_types=1);

namespace CrewAI\PHP\Agent\Executor;

use CrewAI\PHP\Agent\Parser\AgentAction;
use CrewAI\PHP\Agent\Parser\AgentFinish;
use CrewAI\PHP\Agent\Parser\OutputParserException;
use CrewAI\PHP\Agent\ToolsHandler;
use CrewAI\PHP\Core\Interfaces\AgentInterface;
use CrewAI\PHP\Core\Interfaces\LLMInterface;
use CrewAI\PHP\Core\Interfaces\TaskInterface;
use CrewAI\PHP\Utilities\AgentUtils;
use CrewAI\PHP\Utilities\I18N;
use CrewAI\PHP\Utilities\Logger;
use CrewAI\PHP\Utilities\Printer;

class AgentExecutor
{
    private Logger $logger;
    private I18N $i18n;
    private LLMInterface $llm;
    private TaskInterface $task;
    private AgentInterface $agent;
    private array $tools;
    private bool $verbose;
    private Printer $printer;
    private ToolsHandler $toolsHandler;
    private array $messages = [];
    private int $iterations = 0;
    private int $maxIterations = 15; // Default max iterations
    private int $logErrorAfter = 3;

    public function __construct(
        LLMInterface $llm,
        TaskInterface $task,
        AgentInterface $agent,
        array $tools,
        bool $verbose = false
    ) {
        $this->logger = new Logger();
        $this->i18n = new I18N();
        $this->llm = $llm;
        $this->task = $task;
        $this->agent = $agent;
        $this->tools = $tools;
        $this->verbose = $verbose;
        $this->printer = new Printer();
        $this->toolsHandler = new ToolsHandler($tools);

        // Initialize messages with system and user prompts
        $systemPrompt = 'You are a helpful AI assistant.'; // Placeholder, will be dynamic
        $userPrompt = $this->task->getDescription(); // Placeholder, will be dynamic

        $this->messages[] = AgentUtils::formatMessageForLLM($systemPrompt, 'system');
        $this->messages[] = AgentUtils::formatMessageForLLM($userPrompt, 'user');
    }

    public function execute(): string
    {
        $this->logger->info("Starting agent execution", [
            'agent' => $this->agent->getName(),
            'task' => $this->task->getDescription(),
            'tools' => array_keys($this->tools),
            'verbose' => $this->verbose
        ]);

        try {
            $formattedAnswer = $this->invokeLoop();
        } catch (\Exception $e) {
            $this->logger->error("Agent execution failed", [
                'error' => $e->getMessage(),
                'agent' => $this->agent->getName()
            ]);
            AgentUtils::handleUnknownError($this->printer, $e);

            throw $e;
        }

        $this->logger->info("Agent execution completed successfully", [
            'agent' => $this->agent->getName(),
            'output_length' => strlen($formattedAnswer->output)
        ]);

        // TODO: Implement human feedback and memory creation
        // if ($this->task->getHumanInput()) {
        //     $formattedAnswer = $this->handleHumanFeedback($formattedAnswer);
        // }
        // $this->createShortTermMemory($formattedAnswer);
        // $this->createLongTermMemory($formattedAnswer);
        // $this->createExternalMemory($formattedAnswer);

        return $formattedAnswer->output;
    }

    private function invokeLoop(): AgentFinish
    {
        $formattedAnswer = null;
        while (! ($formattedAnswer instanceof AgentFinish)) {
            if (AgentUtils::hasReachedMaxIterations($this->iterations, $this->maxIterations)) {
                if ($this->verbose) {
                    $this->logger->warning("Max iterations reached", [
                        'iterations' => $this->iterations,
                        'max_iterations' => $this->maxIterations
                    ]);
                }
                
                $formattedAnswer = AgentUtils::handleMaxIterationsExceeded(
                    $formattedAnswer,
                    $this->printer,
                    $this->i18n,
                    $this->messages,
                    $this->llm
                );

                break;
            }

            try {
                if ($this->verbose) {
                    $this->logger->debug("Calling LLM", ['iteration' => $this->iterations]);
                }
                
                $llmResponse = $this->llm->call($this->messages);
                $formattedAnswer = AgentUtils::processLLMResponse($llmResponse, $this->llm->getStopWords());

                if ($formattedAnswer instanceof AgentAction) {
                    if ($this->verbose) {
                        $this->logger->debug("Executing tool", [
                            'tool' => $formattedAnswer->tool,
                            'input' => $formattedAnswer->toolInput
                        ]);
                    }
                    
                    $toolResult = $this->toolsHandler->executeTool(
                        $formattedAnswer->tool,
                        $formattedAnswer->toolInput
                    );
                    $formattedAnswer = $this->handleAgentAction($formattedAnswer, $toolResult);
                    $this->messages[] = AgentUtils::formatMessageForLLM($formattedAnswer->log, 'assistant');
                } elseif ($formattedAnswer instanceof AgentFinish) {
                    if ($this->verbose) {
                        $this->logger->info("Agent finished", ['output' => $formattedAnswer->output]);
                    }
                    $this->messages[] = AgentUtils::formatMessageForLLM($formattedAnswer->output, 'assistant');
                }
            } catch (OutputParserException $e) {
                if ($this->verbose) {
                    $this->logger->warning("Output parser exception", ['error' => $e->getMessage()]);
                }
                
                $formattedAnswer = AgentUtils::handleOutputParserException(
                    $e,
                    $this->messages,
                    $this->iterations,
                    $this->logErrorAfter,
                    $this->printer
                );
            } catch (\Exception $e) {
                // TODO: Implement context length handling
                // if (AgentUtils::isContextLengthExceeded($e)) {
                //     AgentUtils::handleContextLength(
                //         true, // respect_context_window
                //         $this->printer,
                //         $this->messages,
                //         $this->llm,
                //         $this->i18n
                //     );
                //     continue;
                // } else {
                AgentUtils::handleUnknownError($this->printer, $e);

                throw $e;
                // }
            }
            ++$this->iterations;
        }

        return $formattedAnswer;
    }

    private function handleAgentAction(AgentAction $action, string $toolResult): AgentAction
    {
        // This is a simplified version. In the original, it also handles add_image_tool
        // and other complex logic. For now, we just append the tool result.
        $this->messages[] = AgentUtils::formatMessageForLLM($toolResult, 'tool_output');

        return $action; // Return the action to continue the loop
    }
}
