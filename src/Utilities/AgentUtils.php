<?php

declare(strict_types=1);

namespace CrewAI\PHP\Utilities;

use CrewAI\PHP\Agent\Parser\AgentAction;
use CrewAI\PHP\Agent\Parser\AgentFinish;
use CrewAI\PHP\Agent\Parser\OutputParserException;
use CrewAI\PHP\Core\Interfaces\LLMInterface;

class AgentUtils
{
    public static function formatMessageForLLM(string $content, string $role): array
    {
        return [
            'role' => $role,
            'content' => $content,
        ];
    }

    public static function hasReachedMaxIterations(int $currentIterations, int $maxIterations): bool
    {
        return $currentIterations >= $maxIterations;
    }

    public static function handleMaxIterationsExceeded(
        mixed $formattedAnswer,
        Printer $printer,
        I18N $i18n,
        array $messages,
        LLMInterface $llm
    ): AgentFinish {
        $printer->print(
            content: $i18n->errors('max_iterations_exceeded'),
            color: 'red'
        );

        // In a real scenario, you might want to summarize or return partial results
        return new AgentFinish('Max iterations exceeded.', '');
    }

    public static function processLLMResponse(string $response, array $stopWords): AgentAction|AgentFinish
    {
        // This is a simplified parser. In a real scenario, this would be more robust
        // and handle different LLM output formats (e.g., JSON, XML, plain text).
        // It should try to identify if the LLM wants to use a tool or provide a final answer.

        // Example: If response contains "Thought:" and "Tool:", it's an AgentAction
        if (str_contains($response, 'Thought:') && str_contains($response, 'Tool:')) {
            preg_match("/Tool:\s*(.*?)\n/", $response, $toolMatch);
            preg_match("/Tool Input:\s*(.*?)\n/", $response, $toolInputMatch);
            preg_match("/Thought:\s*(.*?)\n/s", $response, $logMatch);

            if (isset($toolMatch[1]) && isset($toolInputMatch[1])) {
                return new AgentAction(
                    trim($toolMatch[1]),
                    trim($toolInputMatch[1]),
                    trim($logMatch[1] ?? '')
                );
            }
        }

        // Otherwise, assume it's a final answer
        return new AgentFinish($response, '');
    }

    public static function handleOutputParserException(
        OutputParserException $e,
        array $messages,
        int $iterations,
        int $logErrorAfter,
        Printer $printer
    ): AgentFinish {
        $printer->print(
            content: 'Could not parse LLM output: '.$e->getMessage(),
            color: 'red'
        );

        // In a real scenario, you might want to retry or ask the LLM to rephrase
        return new AgentFinish('Error parsing LLM output.', '');
    }

    public static function handleUnknownError(Printer $printer, \Exception $e): void
    {
        $printer->print(
            content: 'An unknown error occurred: '.$e->getMessage(),
            color: 'red'
        );
    }
}
