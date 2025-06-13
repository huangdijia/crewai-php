
<?php

declare(strict_types=1);

namespace CrewAI\PHP\Memory;

use CrewAI\PHP\Memory\Contextual\ContextualMemory;
use CrewAI\PHP\Memory\Entity\EntityMemory;
use CrewAI\PHP\Memory\External\ExternalMemory;
use CrewAI\PHP\Memory\Interfaces\MemoryInterface;
use CrewAI\PHP\Memory\LongTerm\LongTermMemory;
use CrewAI\PHP\Memory\ShortTerm\ShortTermMemory;

class MemoryManager
{
    private ShortTermMemory $shortTermMemory;
    private LongTermMemory $longTermMemory;
    private EntityMemory $entityMemory;
    private ContextualMemory $contextualMemory;
    private ExternalMemory $externalMemory;

    public function __construct()
    {
        $this->shortTermMemory = new ShortTermMemory();
        $this->longTermMemory = new LongTermMemory();
        $this->entityMemory = new EntityMemory();
        $this->contextualMemory = new ContextualMemory();
        $this->externalMemory = new ExternalMemory();
    }

    public function getShortTermMemory(): MemoryInterface
    {
        return $this->shortTermMemory;
    }

    public function getLongTermMemory(): MemoryInterface
    {
        return $this->longTermMemory;
    }

    public function getEntityMemory(): MemoryInterface
    {
        return $this->entityMemory;
    }

    public function getContextualMemory(): MemoryInterface
    {
        return $this->contextualMemory;
    }

    public function getExternalMemory(): MemoryInterface
    {
        return $this->externalMemory;
    }

    public function clearAll(): void
    {
        $this->shortTermMemory->clear();
        $this->longTermMemory->clear();
        $this->entityMemory->clear();
        $this->contextualMemory->clear();
        $this->externalMemory->clear();
    }
}
