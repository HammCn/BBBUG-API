<?php

namespace app\api\controller;

use app\api\BaseController;
use app\model\Room as RoomModel;
use think\facade\View;

class Badge extends BaseController
{
    public function __call($room_id, $args)
    {
        header('Content-Type:image/svg+xml');
        $now = cache('SongNow_' . $room_id) ?? false;
        $song = [
            'name' => '歌曲读取中',
            'singer' => 'Loading...',
            'pic' => 'https://bbbug.hamm.cn/new/images/loading.png'
        ];
        $userName = '';
        if ($now) {
            $song = [
                'name' => htmlentities($now['song']['name']),
                'singer' => htmlentities($now['song']['singer']),
                'pic' => str_replace("http://", "https://", $now['song']['pic']),
            ];
            $userName = '点歌人: ' . urldecode($now['user']['user_name']);
        }
        $userName = htmlentities($userName);
        $song["pic"] = "data:image/jpeg;base64," . base64_encode(file_get_contents($song["pic"]));

        $song["bg"] = "data:image/jpeg;base64," . base64_encode(file_get_contents("https://bbbug.hamm.cn//new/images/player_bg.png"));

        $song["bar"] = "data:image/jpeg;base64," . base64_encode(file_get_contents("https://bbbug.hamm.cn//new/images/player_bar.png"));
        $song['name'] = html_entity_decode($song['name']);
        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            $room = [
                'room_name' => '房间信息读取失败',
                'room_id' => 888
            ];
        }
        header('Content-Type:image/svg+xml');
        $xmlData = <<<XMLDATA

<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="400" height="160">
<defs>
<filter id="filter_image" x="0" y="0">
  <feGaussianBlur stdDeviation="10" />
</filter>
</defs>
    <rect x="0" y="0" width="400" height="140" rx="10" ry="10" style="fill:#333;stroke-width:5;" />
    <rect id="songImageRect" x="30" y="30" width="80" height="80" rx="100" ry="100" style="fill:#333333;stroke-width:5;" />
    <clipPath id="songBgPath">
        <use xlink:href="#songBgRech" />
    </clipPath>
    <clipPath id="songImagePath">
        <use xlink:href="#songImageRect" />
    </clipPath>
    
    <g font-family="Consolas, PingFangSC-Regular, Microsoft YaHei" font-size="20">
    <text x="137" y="41" fill="black">{$song['name']}</text>
    <text x="138" y="40" fill="white">{$song['name']}</text>
    </g>
    
    <g font-family="Consolas, PingFangSC-Regular, Microsoft YaHei" font-size="14">
    <text size="16" x="137" y="59" fill="#000">{$song["singer"]}</text>
    <text size="16" x="138" y="60" fill="#999">{$song["singer"]}</text>
    </g>
    
    <g font-family="Consolas, PingFangSC-Regular, Microsoft YaHei" font-size="12">
    <text size="16" x="137" y="99" fill="#000">{$userName}</text>
    <text size="16" x="138" y="100" fill="#999">{$userName}</text>
    </g>
    
    
    <a xlink:href="https://bbbug.com/{$room['room_id']}" target="_blank" style="cursor:pointer;">
        <g font-family="Consolas, PingFangSC-Regular, Microsoft YaHei" font-size="12" text-anchor="right">
            <text size="16" x="137" y="119" fill="#000">ID:{$room['room_id']} {$room['room_name']}</text>
            <text size="16" x="138" y="120" fill="#666">ID:{$room['room_id']} {$room['room_name']}</text>
        </g>
    </a>
    <a xlink:href="https://bbbug.com/" target="_blank" style="cursor:pointer;">
        <g font-family="Consolas, PingFangSC-Regular, Microsoft YaHei" font-size="12" text-anchor="right">
            <text size="16" x="320" y="158" fill="#aaa" >BBBUG.COM</text>
        </g>
    </a>
    <image xlink:href="{$song['bg']}" width="120" height="120" x="10" y="10"/>
    <image xlink:href="{$song['pic']}" x="30" y="30" height="80" width="80" clip-path="url(#songImagePath)">
    
    <animateTransform
                      attributeName="transform"
                      attributeType="XML"
                      type="rotate"
                      from="0 70 70"
                      to="360 70 70"
                      dur="30"
                      repeatCount="indefinite" />
    </image>
    
    <image xlink:href="{$song['bar']}" height="80" x="64" y="0"/>
</svg> 


XMLDATA;
        echo $xmlData;
        die;
    }
    public function player()
    {
        $room_id = str_replace('//api/badge/player/', '', $_REQUEST['s']);
        if (!$room_id) {
            $room_id = 888;
        }
        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            header('Location: https://bbbug.com');
            return;
        }
        View::assign('room', $room);
        View::assign('access_token', getTempToken());
        return View::fetch();
    }
    public function bg()
    {
        header('Content-Type:image/svg+xml');
        echo View::fetch();
        die;
    }
}
