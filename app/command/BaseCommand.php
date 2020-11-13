<?php

declare(strict_types=1);

namespace app\command;

use think\console\Command;
use app\model\Conf as ConfModel;

class BaseCommand extends Command
{
    public function loadConfig(){
        $confModel = new ConfModel();
        $configs = $confModel->select()->toArray();
        $c = [];
        foreach ($configs as $config) {
            $c[$config['conf_key']] = $config['conf_value'];
        }
        config($c, 'startadmin');
    }
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
