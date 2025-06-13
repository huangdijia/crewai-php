<?php

declare(strict_types=1);

namespace Tests\Unit\LLM;

use CrewAI\PHP\Core\Interfaces\LLMInterface;
use CrewAI\PHP\LLM\BaseLLM;
use PHPUnit\Framework\TestCase;

class MockLLM extends BaseLLM
{
    public function call(array $messages, array $options = []): string
    {
        return 'Mock LLM Response';
    }

    // 添加getter方法用于测试
    public function getModel(): string
    {
        return $this->model;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function getStop(): array
    {
        return $this->stop;
    }
}

class BaseLLMTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $llm = new MockLLM('test-model', 0.7, ['stop1', 'stop2']);

        $this->assertInstanceOf(LLMInterface::class, $llm);
        $this->assertEquals('test-model', $llm->getModel());
        $this->assertEquals(0.7, $llm->getTemperature());
        $this->assertEquals(['stop1', 'stop2'], $llm->getStop());
    }

    public function testSupportsStopWords(): void
    {
        $llm = new MockLLM('test-model', 0.7, ['stop1']);
        $this->assertTrue($llm->supportsStopWords());

        $llm = new MockLLM('test-model', 0.7, []);
        $this->assertFalse($llm->supportsStopWords());
    }

    public function testGetStopWords(): void
    {
        $llm = new MockLLM('test-model', 0.7, ['stop1', 'stop2']);
        $this->assertEquals(['stop1', 'stop2'], $llm->getStopWords());
    }

    public function testCallMethod(): void
    {
        $llm = new MockLLM('test-model');
        $response = $llm->call([['role' => 'user', 'content' => 'Hello']]);
        $this->assertEquals('Mock LLM Response', $response);
    }

    public function testConstructTextPrompt(): void
    {
        $llm = new MockLLM('test-model');
        $reflection = new \ReflectionClass($llm);
        $method = $reflection->getMethod('constructTextPrompt');
        $method->setAccessible(true);

        $prompt = 'Test prompt';
        $expected = [['role' => 'user', 'content' => 'Test prompt']];
        $this->assertEquals($expected, $method->invokeArgs($llm, [$prompt]));
    }

    public function testConstructMessagesPrompt(): void
    {
        $llm = new MockLLM('test-model');
        $reflection = new \ReflectionClass($llm);
        $method = $reflection->getMethod('constructMessagesPrompt');
        $method->setAccessible(true);

        $messages = [['role' => 'user', 'content' => 'Hello'], ['role' => 'assistant', 'content' => 'Hi']];
        $this->assertEquals($messages, $method->invokeArgs($llm, [$messages]));
    }
}
