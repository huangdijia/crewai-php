<?php

declare(strict_types=1);

namespace CrewAI\PHP\Core\Interfaces;

interface KnowledgeInterface
{
    public function addDocument(string $id, string $content): void;

    public function getDocument(string $id): ?string;

    public function removeDocument(string $id): void;

    public function search(string $keyword): array;

    public function all(): array;
}
