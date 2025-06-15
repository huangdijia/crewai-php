<?php

declare(strict_types=1);

namespace CrewAI\PHP\Knowledge;

use CrewAI\PHP\Core\Interfaces\KnowledgeInterface;

class KnowledgeBase implements KnowledgeInterface
{
    private array $documents = [];

    public function addDocument(string $id, string $content): void
    {
        $this->documents[$id] = $content;
    }

    public function getDocument(string $id): ?string
    {
        return $this->documents[$id] ?? null;
    }

    public function removeDocument(string $id): void
    {
        unset($this->documents[$id]);
    }

    public function search(string $keyword): array
    {
        $results = [];
        foreach ($this->documents as $id => $content) {
            if (false !== stripos($content, $keyword)) {
                $results[$id] = $content;
            }
        }

        return $results;
    }

    public function all(): array
    {
        return $this->documents;
    }
}
