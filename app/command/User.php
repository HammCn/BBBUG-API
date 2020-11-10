<?php

declare (strict_types = 1);

namespace app\command;

use app\model\User as UserModel;
use think\console\Input;
use think\console\Output;

class User extends BaseCommand
{
    protected function configure()
    {
        // 指令配置
        $this->setName('User')
            ->setDescription('StartAdmin Test Command');
    }

    protected function execute(Input $input, Output $output)
    {
        $userModel = new UserModel();
        $userModel->where('1=1')->update([
            'user_song' => 0,
            'user_img' => 0,
            'user_chat' => 0,
            'user_pass' => 0,
            'user_push' => 0,
            'user_songsend'=>0,
            'user_songrecv'=>0,
            'user_gamesongscore' => 0,
        ]);
    }
}
