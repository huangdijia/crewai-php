<?php

declare(strict_types=1);

namespace CrewAI\PHP\Memory\Interfaces;

interface MemoryInterface
{
    public function add(string $key, string $value): void;

    public function get(string $key): ?string;

    public function delete(string $key): void;

    public function clear(): void;
}
