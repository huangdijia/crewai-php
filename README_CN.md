# CrewAI PHP

[![PHP 版本](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg)](https://www.php.net/)
[![许可证](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)

CrewAI 框架的 PHP 实现，用于构建和编排 AI 智能体团队。CrewAI PHP 允许您创建能够通过角色扮演、协作和委派来共同完成复杂任务的自主 AI 智能体。

## 特性

- 🤖 **多智能体系统**: 创建和管理具有不同角色的多个 AI 智能体
- 🔧 **工具集成**: 为智能体配备特定任务的自定义工具
- 📋 **任务管理**: 定义具有任务依赖关系的复杂工作流
- 🎭 **角色扮演**: 智能体具有明确的角色、目标和背景故事
- 🔄 **流程编排**: 顺序、并行和层次化任务执行
- 🔀 **流程组合**: 将多个 Crew 组合成更高级的流程
- 📚 **知识库**: 简单的内存级事实存储与检索
- 📊 **内存管理**: 短期、长期和上下文内存
- 🎯 **任务委派**: 智能体可以将任务委派给其他专业智能体
- 📝 **全面日志**: 内置日志记录和监控功能

## 系统要求

- PHP 8.2 或更高版本
- Swoole 扩展
- Composer

## 安装

通过 Composer 安装：

```bash
composer require huangdijia/crewai-php
```

## 快速开始

以下是创建研究团队的简单示例：

```php
<?php

require_once 'vendor/autoload.php';

use CrewAI\PHP\Agent\Agent;
use CrewAI\PHP\Crew\Crew;
use CrewAI\PHP\Task\Task;
use CrewAI\PHP\LLM\BaseLLM;
use CrewAI\PHP\Tool\BaseTool;

// 自定义 LLM 实现
class YourLLM extends BaseLLM
{
    public function call(array $messages, array $options = []): string
    {
        // 在这里实现您的 LLM 集成
        // 可以是 OpenAI、Claude 或任何其他 LLM 服务
        return "您的 LLM 响应";
    }
}

// 自定义工具实现
class SearchTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('search_tool', '执行网络搜索的工具');
    }

    public function execute(string $input): string
    {
        // 在这里实现您的搜索逻辑
        return "搜索结果：" . $input;
    }
}

// 初始化组件
$llm = new YourLLM('your-model');
$searchTool = new SearchTool();

// 创建智能体
$researcher = new Agent(
    role: '高级研究员',
    goal: '发现突破性技术',
    backstory: '一位经验丰富的研究员，擅长发现隐藏的宝藏。',
    llm: $llm,
    tools: [$searchTool],
    verbose: true
);

$writer = new Agent(
    role: '内容写手',
    goal: '撰写引人入胜的叙述',
    backstory: '一位创意写手，能够将复杂想法转化为引人入胜的故事。',
    llm: $llm,
    verbose: true
);

// 创建任务
$researchTask = new Task(
    description: '研究 AI 伦理学的最新进展。',
    expectedOutput: '当前 AI 伦理趋势的全面总结。',
    agent: $researcher
);

$writeTask = new Task(
    description: '基于研究结果撰写详细报告。',
    expectedOutput: '关于 AI 伦理的结构良好的报告。',
    agent: $writer,
    context: [$researchTask]
);

// 创建并运行团队
$crew = new Crew(
    agents: [$researcher, $writer],
    tasks: [$researchTask, $writeTask],
    process: 'sequential',
    verbose: true
);

$result = $crew->kickoff();
echo $result;
```

## 核心概念

### 智能体（Agents）

智能体是具有特定角色、目标和能力的自主 AI 实体：

```php
$agent = new Agent(
    role: '数据分析师',
    goal: '从复杂数据集中提取见解',
    backstory: '一位具有统计建模专业知识的经验丰富的分析师。',
    llm: $llm,
    tools: [$analysisTools],
    allowDelegation: true,
    verbose: true
);
```

### 任务（Tasks）

任务定义需要完成的工作：

```php
$task = new Task(
    description: '分析第四季度销售数据趋势',
    expectedOutput: '包含可视化图表的详细报告',
    agent: $analyst,
    context: [$dataCollectionTask], // 依赖关系
    verbose: true
);
```

### 工具（Tools）

工具扩展智能体的能力：

```php
class DatabaseTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('database_query', '执行数据库查询');
    }

    public function execute(string $input): string
    {
        // 您的数据库查询逻辑
        return $results;
    }
}
```

### 团队（Crews）

团队编排多个智能体和任务：

```php
$crew = new Crew(
    agents: [$agent1, $agent2],
    tasks: [$task1, $task2],
    process: 'sequential', // 'parallel' 或 'hierarchical'
    verbose: true,
    stepCallback: $stepCallback // 可选的监控回调
);
```

## 高级功能

### 内存管理

CrewAI PHP 包含复杂的内存管理系统：

- **短期内存**: 最近的交互和上下文
- **长期内存**: 持久的知识和学习
- **实体内存**: 关于特定实体的信息
- **上下文内存**: 任务和对话上下文

### 流程类型

1. **顺序流程**: 任务按顺序执行
2. **层次流程**: 管理者智能体委派和协调任务

### 自定义 LLM 集成

通过继承 `BaseLLM` 实现您自己的 LLM：

```php
class OpenAILLM extends BaseLLM
{
    private string $apiKey;
    
    public function __construct(string $model, string $apiKey)
    {
        parent::__construct($model);
        $this->apiKey = $apiKey;
    }
    
    public function call(array $messages, array $options = []): string
    {
        // OpenAI API 集成
        // 处理身份验证、速率限制等
        return $response;
    }
}
```

## 开发

### 运行测试

```bash
composer test
```

### 代码风格

```bash
composer cs-fix
```

### 静态分析

```bash
composer analyse
```

### 全面检查

```bash
composer check
```

## 项目结构

```text
src/
├── Agent/           # 智能体实现和执行器
├── Core/           # 核心接口和异常
├── Crew/           # 团队编排
├── LLM/            # 语言模型抽象
├── Memory/         # 内存管理系统
├── Task/           # 任务定义和管理
├── Tool/           # 工具实现
└── Utilities/      # 辅助类和工具

tests/              # 单元测试
examples/           # 示例实现
```

## 贡献

我们欢迎贡献！请查看我们的[贡献指南](CONTRIBUTING.md)了解详情。

1. Fork 仓库
2. 创建功能分支
3. 进行更改
4. 为新功能添加测试
5. 确保所有测试通过
6. 提交 Pull Request

### 开发环境设置

```bash
git clone https://github.com/huangdijia/crewai-php.git
cd crewai-php
composer install
composer check
```

## 示例

查看 `examples/` 目录获取更多综合示例：

- `simple_crew.php` - 基本团队设置和执行
- `parallel_crew.php` - 展示并行任务执行
- `flow.php` - 演示如何将多个 Crew 组合成流程并使用知识库
- 更多示例即将推出！

## 路线图

- [ ] 增强的层次流程支持
- [ ] 更多内置工具（网络爬虫、文件操作等）
- [ ] 与流行 LLM 提供商的集成
- [ ] 高级内存持久化选项
- [ ] 实时协作功能
- [ ] 团队管理的 Web 界面
- [ ] 性能优化

## 许可证

此项目根据 MIT 许可证授权 - 详情请参阅 [LICENSE](LICENSE) 文件。

## 致谢

- 受原始 [CrewAI](https://github.com/joaomdmoura/crewAI) Python 框架启发
- 采用现代 PHP 实践和强类型构建
- 专为生产使用而设计，具有全面的错误处理

## 支持

- 📧 邮箱: [huangdijia@gmail.com](mailto:huangdijia@gmail.com)
- 🐛 问题: [GitHub Issues](https://github.com/huangdijia/crewai-php/issues)
- 💬 讨论: [GitHub Discussions](https://github.com/huangdijia/crewai-php/discussions)

---

用 ❤️ 由 [Huangdijia](https://github.com/huangdijia) 制作
