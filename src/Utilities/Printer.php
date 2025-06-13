
<?php

declare(strict_types=1);

namespace CrewAI\PHP\Utilities;

class Printer
{
    public function print(string $content, string $color = 'white'): void
    {
        // In a real application, this would use a more sophisticated logging/output system
        // For now, we'll just echo with basic color support for CLI
        $colorCode = match ($color) {
            'red' => "\033[31m",
            'green' => "\033[32m",
            'yellow' => "\033[33m",
            'blue' => "\033[34m",
            'magenta' => "\033[35m",
            'cyan' => "\033[36m",
            default => "\033[0m", // Reset color
        };
        echo $colorCode.$content."\033[0m".PHP_EOL;
    }
}
