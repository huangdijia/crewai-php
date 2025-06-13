
<?php

declare(strict_types=1);

namespace CrewAI\PHP\Memory\Entity;

use CrewAI\PHP\Memory\Interfaces\MemoryInterface;

class EntityMemory implements MemoryInterface
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
