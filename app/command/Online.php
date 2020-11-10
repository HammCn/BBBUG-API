<?php

declare (strict_types = 1);

namespace app\command;

use app\model\Online as OnlineModel;
use app\model\Room as RoomModel;
use app\model\User as UserModel;
use think\console\Input;
use think\console\Output;

class Online extends BaseCommand
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Online')
            ->setDescription('StartAdmin Test Command');
    }

    protected function execute(Input $input, Output $output)
    {
        $userModel = new UserModel();
        $roomModel = new RoomModel();
        $onlineModel = new OnlineModel();

        $rooms = $roomModel->select();
        foreach ($rooms as $room) {
            $ret = curlHelper(getWebsocketApiUrl() . "?channel=" . $room['room_id']);
            $arr = json_decode($ret['body'], true);

            $order = 'user_id asc';
            $ret = $userModel->view('user', 'user_id')->where([
                ['user_id', 'in', $arr ?? []],
            ])->count();
            if($ret>0){
                $onlineModel->insert([
                    'online_room' => $room['room_id'],
                    'online_count' => $ret,
                    'online_date' => date('Y-m-d'),
                    'online_hour' => date('H'),
                    'online_createtime' => time(),
                    'online_updatetime' => time(),
                ]);
                $score = $onlineModel->field('online_count')->where('online_room',$room['room_id'])->sum('online_count') ?? 0;
                $roomModel->where('room_id',$room['room_id'])->update([
                    'room_score'=>$score
                ]);
            }
        }
    }
}
