# CrewAI PHP

[![PHP 版本](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg)](https://www.php.net/)
[![許可證](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)

CrewAI 框架的 PHP 實現，用於構建和編排 AI 智能體團隊。CrewAI PHP 允許您創建能夠通過角色扮演、協作和委派来共同完成復雜任務的自主 AI 智能體。

## 特性

- 🤖 **多智能體系统**: 創建和管理具有不同角色的多个 AI 智能體
- 🔧 **工具集成**: 為智能體配备特定任務的自定义工具
- 📋 **任務管理**: 定义具有任務依赖關系的復雜工作流
- 🎭 **角色扮演**: 智能體具有明确的角色、目標和背景故事
- 🔄 **流程編排**: 順序、并行和层次化任務執行
- 🔀 **流程組合**: 将多个 Crew 組合成更高級的流程
- 📚 **知识庫**: 簡单的内存級事實存储与檢索
- 📊 **内存管理**: 短期、長期和上下文内存
- 🎯 **任務委派**: 智能體可以将任務委派給其他专業智能體
- 📝 **全面日志**: 内置日志记錄和監控功能

## 系统要求

- PHP 8.2 或更高版本
- Swoole 扩展
- Composer

## 安裝

通過 Composer 安裝：

```bash
composer require huangdijia/crewai-php
```

## 快速開始

以下是創建研究團隊的簡单示例：

```php
<?php

require_once 'vendor/autoload.php';

use CrewAI\PHP\Agent\Agent;
use CrewAI\PHP\Crew\Crew;
use CrewAI\PHP\Task\Task;
use CrewAI\PHP\LLM\BaseLLM;
use CrewAI\PHP\Tool\BaseTool;

// 自定义 LLM 實現
class YourLLM extends BaseLLM
{
    public function call(array $messages, array $options = []): string
    {
        // 在這里實現您的 LLM 集成
        // 可以是 OpenAI、Claude 或任何其他 LLM 服務
        return "您的 LLM 响应";
    }
}

// 自定义工具實現
class SearchTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('search_tool', '執行網络搜索的工具');
    }

    public function execute(string $input): string
    {
        // 在這里實現您的搜索逻辑
        return "搜索結果：" . $input;
    }
}

// 初始化組件
$llm = new YourLLM('your-model');
$searchTool = new SearchTool();

// 創建智能體
$researcher = new Agent(
    role: '高級研究员',
    goal: '發現突破性技術',
    backstory: '一位经驗丰富的研究员，擅長發現隐藏的寶藏。',
    llm: $llm,
    tools: [$searchTool],
    verbose: true
);

$writer = new Agent(
    role: '内容寫手',
    goal: '撰寫引人入胜的叙述',
    backstory: '一位創意寫手，能夠将復雜想法轉化為引人入胜的故事。',
    llm: $llm,
    verbose: true
);

// 創建任務
$researchTask = new Task(
    description: '研究 AI 伦理學的最新進展。',
    expectedOutput: '當前 AI 伦理趋势的全面总結。',
    agent: $researcher
);

$writeTask = new Task(
    description: '基於研究結果撰寫详细報告。',
    expectedOutput: '關於 AI 伦理的結構良好的報告。',
    agent: $writer,
    context: [$researchTask]
);

// 創建并運行團隊
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

### 智能體（Agents）

智能體是具有特定角色、目標和能力的自主 AI 實體：

```php
$agent = new Agent(
    role: '数據分析師',
    goal: '从復雜数據集中提取見解',
    backstory: '一位具有统计建模专業知识的经驗丰富的分析師。',
    llm: $llm,
    tools: [$analysisTools],
    allowDelegation: true,
    verbose: true
);
```

### 任務（Tasks）

任務定义需要完成的工作：

```php
$task = new Task(
    description: '分析第四季度销售数據趋势',
    expectedOutput: '包含可视化图表的详细報告',
    agent: $analyst,
    context: [$dataCollectionTask], // 依赖關系
    verbose: true
);
```

### 工具（Tools）

工具扩展智能體的能力：

```php
class DatabaseTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('database_query', '執行数據庫查询');
    }

    public function execute(string $input): string
    {
        // 您的数據庫查询逻辑
        return $results;
    }
}
```

### 團隊（Crews）

團隊編排多个智能體和任務：

```php
$crew = new Crew(
    agents: [$agent1, $agent2],
    tasks: [$task1, $task2],
    process: 'sequential', // 'parallel' 或 'hierarchical'
    verbose: true,
    stepCallback: $stepCallback // 可选的監控回调
);
```

## 高級功能

### 内存管理

CrewAI PHP 包含復雜的内存管理系统：

- **短期内存**: 最近的交互和上下文
- **長期内存**: 持久的知识和學习
- **實體内存**: 關於特定實體的信息
- **上下文内存**: 任務和对话上下文

### 流程类型

1. **順序流程**: 任務按順序執行
2. **层次流程**: 管理者智能體委派和協调任務

### 自定义 LLM 集成

通過继承 `BaseLLM` 實現您自己的 LLM：

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
        // 處理身份驗證、速率限制等
        return $response;
    }
}
```

## 開發

### 運行测試

```bash
composer test
```

### 代码風格

```bash
composer cs-fix
```

### 静態分析

```bash
composer analyse
```

### 全面檢查

```bash
composer check
```

## 项目結構

```text
src/
├── Agent/           # 智能體實現和執行器
├── Core/           # 核心接口和異常
├── Crew/           # 團隊編排
├── LLM/            # 語言模型抽象
├── Memory/         # 内存管理系统
├── Task/           # 任務定义和管理
├── Tool/           # 工具實現
└── Utilities/      # 辅助类和工具

tests/              # 单元测試
examples/           # 示例實現
```

## 贡獻

我們歡迎贡獻！請查看我們的[贡獻指南](CONTRIBUTING.md)了解详情。

1. Fork 倉庫
2. 創建功能分支
3. 進行更改
4. 為新功能添加测試
5. 确保所有测試通過
6. 提交 Pull Request

### 開發環境設置

```bash
git clone https://github.com/huangdijia/crewai-php.git
cd crewai-php
composer install
composer check
```

## 示例

查看 `examples/` 目錄获取更多综合示例：

- `simple_crew.php` - 基本團隊設置和執行
- `parallel_crew.php` - 展示并行任務執行
- `flow.php` - 演示如何将多个 Crew 組合成流程并使用知识庫
- 更多示例即将推出！

## 路線图

- [ ] 增強的层次流程支持
- [ ] 更多内置工具（網络爬虫、文件操作等）
- [ ] 与流行 LLM 提供商的集成
- [ ] 高級内存持久化选项
- [ ] 實時協作功能
- [ ] 團隊管理的 Web 界面
- [ ] 性能優化

## 許可證

此项目根據 MIT 許可證授權 - 详情請参阅 [LICENSE](LICENSE) 文件。

## 致謝

- 受原始 [CrewAI](https://github.com/joaomdmoura/crewAI) Python 框架启發
- 採用現代 PHP 實践和強类型構建
- 专為生產使用而設计，具有全面的错誤處理

## 支持

- 📧 郵箱: [huangdijia@gmail.com](mailto:huangdijia@gmail.com)
- 🐛 问题: [GitHub Issues](https://github.com/huangdijia/crewai-php/issues)
- 💬 討論: [GitHub Discussions](https://github.com/huangdijia/crewai-php/discussions)

---

用 ❤️ 由 [Huangdijia](https://github.com/huangdijia) 制作
