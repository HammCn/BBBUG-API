<?php

namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Test extends BaseController
{
    public function index()
    {
        $old = intval(input('old'));
        $new = intval(input('new'));
        if($old < 0 || $new < 0 || input('pwd') != '88102120'){
            return '参数错误';
        }

        $user = 
        Db::name('user')
        ->where('user_id', $new)->find();
        if($user){
            return 'ID '.$new.' 已被占用';
        }

        Db::name('user')
        ->where('user_id', $old)
        ->update(['user_id' => $new]);

        Db::name('access')
        ->where('access_user', $old)
        ->update(['access_user' => $new]);

        Db::name('attach')
        ->where('attach_user', $old)
        ->update(['attach_user' => $new]);

        Db::name('log')
        ->where('log_user', $old)
        ->update(['log_user' => $new]);

        Db::name('message')
        ->where('message_user', $old)
        ->update(['message_user' => $new]);

        Db::name('room')
        ->where('room_user', $old)
        ->update(['room_user' => $new]);

        Db::name('song')
        ->where('song_user', $old)
        ->update(['song_user' => $new]);
    }
}
