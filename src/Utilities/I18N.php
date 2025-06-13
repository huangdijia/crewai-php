<?php

declare(strict_types=1);

namespace CrewAI\PHP\Utilities;

class I18N
{
    private array $messages = [
        'errors' => [
            'max_iterations_exceeded' => '最大迭代次数已超出。',
            'invalid_tool_provided' => '提供了无效的工具。',
            'tool_not_found' => '未找到工具：',
        ],
        'tools' => [
            'add_image' => [
                'name' => 'add_image',
                'description' => '添加图片到当前上下文。',
            ],
        ],
    ];

    public function errors(string $key): string
    {
        return $this->messages['errors'][$key] ?? '未知错误';
    }

    public function tools(string $key): array|string
    {
        return $this->messages['tools'][$key] ?? '';
    }
}
