<?php

declare(strict_types=1);

namespace app\command;

use think\console\Command;

class BaseCommand extends Command
{
    protected function console($text, $break = true)
    {
        print_r($text . ($break ? PHP_EOL : ''));
    }
    protected function success($text, $break = true)
    {
        print_r(chr(27) . "[42m" . "$text" . chr(27) . "[0m" . ($break ? PHP_EOL : ''));
    }
    protected function error($text, $break = true)
    {
        print_r(chr(27) . "[41m" . "$text" . chr(27) . "[0m" . ($break ? PHP_EOL : ''));
    }
    protected function warning($text, $break = true)
    {
        print_r(chr(27) . "[43m" . "$text" . chr(27) . "[0m" . ($break ? PHP_EOL : ''));
    }
}
