<?php

declare(strict_types=1);

namespace CrewAI\PHP\Memory\Contextual;

use CrewAI\PHP\Memory\Interfaces\MemoryInterface;

class ContextualMemory implements MemoryInterface
{
    private array $memory = [];

    public function add(string $key, string $value): void
    {
        $this->memory[$key] = $value;
    }

    public function get(string $key): ?string
    {
        return $this->memory[$key] ?? null;
    }

    public function delete(string $key): void
    {
        unset($this->memory[$key]);
    }

    public function clear(): void
    {
        $this->memory = [];
    }
}
