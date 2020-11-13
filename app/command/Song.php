<?php

declare (strict_types = 1);

namespace app\command;

use app\model\Room as RoomModel;
use app\model\Song as SongModel;
use app\model\User as UserModel;
use think\console\Input;
use think\console\Output;

class Song extends BaseCommand
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Test')
            ->setDescription('StartAdmin Test Command');
    }

    protected function execute(Input $input, Output $output)
    {
        $roomModel = new RoomModel();
        $songModel = new SongModel();
        $userModel = new UserModel();
        while (true) {
            usleep(10 * 1000);
            $rooms = cache('RoomList') ?? [];
            if (!$rooms) {
                $rooms = $roomModel->field('room_id,room_robot,room_type,room_playone,room_user')->where('room_type in (1,4) and room_realonline > 0 or room_id < 1000')->select();
                $rooms = $rooms ? $rooms->toArray() : [];
                if ($rooms) {
                    cache('RoomList', $rooms, 5);
                }
            }
            $rooms = cache('RoomList') ?? [];
            if (!$rooms) {
                print_r('暂无房间开启点歌' . PHP_EOL);
                continue;
            }else{
                // print_r(count($rooms)."个房间需要歌曲".PHP_EOL);
            }
            foreach ($rooms as $room) { 
                // cache('SongList_' . $room['room_id'],null);
                // if($room['room_id']==10246){
                //     cache('SongNow_' . $room['room_id'],null);
                // }
                $songList = cache('SongList_' . $room['room_id']) ?? [];
                $songNow = cache('SongNow_' . $room['room_id']) ?? false;
                if ($songNow) {
                    // print_r($room['room_id']);
                    // print_r($songNow['song']);
                    if (time() > $songNow['song']['length'] + $songNow['since']) {
                        //已超时 切歌
                        if ($room['room_type'] == 4 && $room['room_playone']) {
                            //是单曲循环的房间
                            $songNow['since'] = time();
                            cache('SongNow_' . $room['room_id'], $songNow);
                        } else {
                            cache('SongNow_' . $room['room_id'], null);
                            $songNow=false;
                        }
                        // print_r('房间' . $room['room_id'] . '已超时,切歌' . PHP_EOL);
                    } else {
                        //歌曲正在播放中
                        if(count($songList)>0){
                            //歌曲列表中还有歌 取出第一个读取缓存
                            $preMid = $songList[0]['song']['mid'];
                            $preSong = cache('song_play_temp_url_'.$preMid) ?? false;
                            $preSongName = $songList[0]['song']['name'];
                            $preRoomId = $room['room_id'];
                            if(!$preSong){
                                $url = 'http://kuwo.cn/url?rid=' . $preMid . '&type=convert_url3&br=128kmp3';
                                $result = curlHelper($url)['body'];
                                $arr = json_decode($result, true);
                                if ($arr['code'] == 200) {
                                    if($arr['url']){
                                        $tempList = cache('song_waiting_download_list') ?? [];
                                        array_push($tempList,[
                                            'mid'=>$preMid,
                                            'url'=>$arr['url']
                                        ]);
                                        cache('song_waiting_download_list',$tempList);
                                        cache('song_play_temp_url_'.$preMid,$arr['url'],600);
                                        print_r($preRoomId." 歌曲预缓存成功 ".$preSongName.PHP_EOL);
                                    }
                                }
                            }
                        }
                        continue;
                    }
                }

                if (!$songNow) {
                    //没有歌
                    if ($room['room_type'] == 4) {
                        //随机查询一首房主的歌
                        $playerWaitSong = cache('song_wait_'.$room['room_id']) ?? false;
                        cache('song_wait_'.$room['room_id'],null);
                        if(!$playerWaitSong){
                            $temp = $songModel->where('song_user', $room['room_user'])->orderRand()->find();
                            if (!$temp) {
                                continue;
                            }
                            
                            $playerWaitSong = [
                                'mid' => $temp['song_mid'],
                                'name' => $temp['song_name'],
                                'pic' => $temp['song_pic'] ?? '',
                                'length' => $temp['song_length'],
                                'singer' => $temp['song_singer'],
                            ];
                        }
                        $user = $userModel->where('user_id', $room['room_user'])->find();
                        if (!$user) {
                            continue;
                        }
                        $songNow = [
                            'user' => getUserData($user),
                            'song' => $playerWaitSong,
                            'since' => time(),
                        ];
                        cache('SongNow_' . $room['room_id'], $songNow, 600);
                    } else {
                        if (count($songList) > 0) {
                            $songNow = $songList[0];
                            $songNow['since'] = time() + 5;
                            array_shift($songList);
                            cache('SongList_' . $room['room_id'], $songList, 86400);
                            cache('SongNow_' . $room['room_id'], $songNow, 600);
                        } else {
                            if ($room['room_robot'] == 0) {
                                $songNow = $this->getOneMusic();
                                cache('SongNow_' . $room['room_id'], $songNow, 600);
                            } else {
                                continue;
                            }
                        }
                    }
                }
                if (!$songNow) {
                    continue;
                }
                print_r('房间' . $room['room_id'] . '已获取到歌曲' . PHP_EOL);
                
                cache("song_detail_".$songNow['song']['mid'],$songNow['song'],600);
                $msg = [
                    'at' => $songNow['at'] ?? false,
                    'user' => $songNow['user'],
                    'song' => $songNow['song'],
                    'since' => $songNow['since'],
                    "type" => "playSong",
                    "time" => date('H:i:s'),
                ];
                $ret = curlHelper(getWebsocketApiUrl(), "POST", http_build_query([
                    'type' => 'channel',
                    'to' => $room['room_id'],
                    'token' => getWebsocketToken(),
                    'msg' => json_encode($msg),
                ]), [
                    'content-type:application/x-www-form-rawurlencode',
                ]);
            }
        }
    }

    protected function getOneMusic()
    {
        $bangIdArray = [278, 284, 26, 64, 187, 281, 153, 17, 16, 158, 145];
        $bangId = $bangIdArray[rand(0, count($bangIdArray) - 1)];
        $randNumber = rand(10000000, 99999999);
        //function curlHelper($url, $method = 'GET', $data = null, $header = [], $cookies = "")
        $result = curlHelper('http://kuwo.cn/api/www/bang/bang/musicList?bangId=' . $bangId . '&pn=1&rn=100', 'GET', null, [
            'csrf: ' . $randNumber,
        ], "kw_token=" . $randNumber);
        $arr = json_decode($result['body'], true);
        if ($arr['code'] != 200) {
            return false;
        }
        $list = $arr['data']['musicList'];
        $song = $list[rand(0, count($list) - 1)];
        cache('song_detail_' . $song['rid'], [
            'mid' => $song['rid'],
            'name' => $song['name'],
            'pic' => $song['pic'],
            'length' => $song['duration'],
            'singer' => $song['artist'],
        ],3600);
        return [
            'song' => [
                'mid' => $song['rid'],
                'name' => $song['name'],
                'pic' => $song['pic'],
                'length' => $song['duration'],
                'singer' => $song['artist'],
            ],
            'since' => time(),
            'user' => [
                "app_id" => 1,
                "app_name" => "BBBUG",
                "app_url" => "https://bbbug.com",
                "user_admin" => true,
                "user_head" => "https://cdn.bbbug.com/uploads/thumb/image/20201105/1bafe18a648eb26bbdf7f24b6fe53dfc.jpg",
                "user_id" => 1,
                "user_name" => "BBBUG点歌机器人",
                "user_remark" => "别@我,我只是个测试帐号",
            ],
        ];
    }
}
