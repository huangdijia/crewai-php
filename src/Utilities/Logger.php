<?php

declare(strict_types=1);

namespace CrewAI\PHP\Utilities;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
    private bool $enabled = true;

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (! $this->enabled) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = sprintf('[%s] %s: %s', $timestamp, strtoupper($level), $message);

        if (! empty($context)) {
            $formattedMessage .= ' '.json_encode($context);
        }

        error_log($formattedMessage);
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
