<?php

declare(strict_types=1);

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
        $this->loadConfig();
        while (true) {
            //暂停一下 避免对redis频繁读取
            usleep(500 * 1000);
            $rooms = $this->getRoomList();
            if (!$rooms) {
                print_r('暂无房间开启点歌' . PHP_EOL);
                continue;
            }
            foreach ($rooms as $room) {
                try {
                    $song = $this->getPlayingSong($room['room_id']);
                    if ($song && $song['song']) {
                        //歌曲正在播放
                        if (time() < $song['song']['length'] + $song['since']) {
                            //预先缓存下一首歌
                            $this->preLoadMusicUrl($room);
                            continue;
                        }
                        // 歌曲已超时
                        if ($room['room_type'] == 4 && $room['room_playone']) {
                            //是单曲循环的电台房间 重置播放时间
                            $song['since'] = time();
                            $this->playSong($room['room_id'], $song, true); //给true 保留当前房间歌曲
                            continue;
                        }
                    }
                    //其他房间
                    $song = $this->getSongFromList($room['room_id']);
                    if ($song) {
                        $this->playSong($room['room_id'], $song);
                    } else {
                        if ($room['room_type'] == 4) {
                            //电台模式
                            $song = $this->getSongByUser($room['room_user']);
                            if ($song) {
                                $this->playSong($room['room_id'], $song);
                            }
                        } else {
                            if ($room['room_robot'] == 0) {
                                $song = $this->getSongByRobot();
                                $this->playSong($room['room_id'], $song);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    print_r($e->getLine());
                    print_r($e->getMessage());
                    // print_r($song);
                    // print_r($room['room_id']);
                    cache('SongNow_' . $room['room_id'], null);
                    continue;
                }
            }
        }
    }
    protected function addSongToList($room_id, $song)
    {
        $songList = cache('SongList_' . $room_id) ?? [];
        $isExist = false;
        for ($i = 0; $i < count($songList); $i++) {
            if ($songList[$i]['song']['mid'] == $song['song']['mid']) {
                $isExist = true;
            }
        }
        if (!$isExist) {
            array_push($songList, $song);
            cache('SongList_' . $room_id, $songList, 86400);
        }
    }
    protected function preLoadMusicUrl($room)
    {
        $preRoomId = $room['room_id'];
        $songList = $this->getSongList($preRoomId);
        $song = false;
        if (count($songList) > 0) {
            $song = $songList[0];
        } else {
            if ($room['room_type'] == 4) {
                $song = $this->getSongByUser($room['room_user']);
            } else {
                if ($room['room_robot'] == 0) {
                    $song = $this->getSongByRobot();
                }
            }
            if ($song) {
                $this->addSongToList($preRoomId, $song);
            }
        }
        if (!$song) {
            return;
        }
        $preMid = $song['song']['mid'];
        if ($preMid > 0) {
            $preSong = cache('song_play_temp_url_' . $preMid) ?? false;
            $preCount = cache('song_pre_load_count') ?? 0;
            if (!$preSong && $preCount < 5) {
                print_r("请缓存 " . $room['room_id'] . " " . $preMid);
                cache('song_pre_load_count', $preCount + 1, 60);
                $url = 'http://kuwo.cn/url?rid=' . $preMid . '&type=convert_url3&br=128kmp3';
                $result = curlHelper($url)['body'];
                $arr = json_decode($result, true);
                if ($arr['code'] == 200) {
                    if ($arr['url']) {
                        $tempList = cache('song_waiting_download_list') ?? [];
                        array_push($tempList, [
                            'mid' => $preMid,
                            'url' => $arr['url']
                        ]);
                        cache('song_waiting_download_list', $tempList);
                        cache('song_play_temp_url_' . $preMid, $arr['url'], 3600);
                    }
                }
            }
        } else {
            //用户自己上传的歌曲 刷新一遍CDN
            $isCdnLoaded = cache('cdn_load_mid_' . $preMid) ?? false;
            if (!$isCdnLoaded) {
                $loadUrl = config('startadmin.api_url') . "/api/song/playurl?mid=" . $preMid;
                echo $loadUrl . PHP_EOL;
                $ch = curl_init();
                $curl_opt = array(
                    CURLOPT_URL, $loadUrl,
                    CURLOPT_RETURNTRANSFER, 1,
                    CURLOPT_TIMEOUT, 1,
                    CURLOPT_SSL_VERIFYPEER, false,
                    CURLOPT_SSL_VERIFYHOST, false
                );
                curl_setopt_array($ch, $curl_opt);
                curl_exec($ch);
                curl_close($ch);
                cache('cdn_load_mid_' . $preMid, 1, 60);
            }
        }
        $isPreloadSend = cache('pre_load_mid_' . $preMid) ?? false;
        if (!$isPreloadSend) {
            $msg = [
                "url" => config('startadmin.api_url') . "/api/song/playurl?mid=" . $preMid,
                "type" => "preload",
                "time" => date('H:i:s'),
            ];
            sendWebsocketMessage('channel', $preRoomId, $msg);
            cache('pre_load_mid_' . $preMid, 1, 60);
        }
    }
    protected function getSongByUser($user_id)
    {
        $userModel = new UserModel();
        $songModel = new SongModel();
        $playerWaitSong = $songModel->where('song_user', $user_id)->orderRand()->find();
        if (!$playerWaitSong) {
            return false;
        }
        $playerWaitSong = [
            'mid' => $playerWaitSong['song_mid'],
            'name' => $playerWaitSong['song_name'],
            'pic' => $playerWaitSong['song_pic'] ?? '',
            'length' => $playerWaitSong['song_length'],
            'singer' => $playerWaitSong['song_singer'],
        ];
        $user = $userModel->where('user_id', $user_id)->find();
        if (!$user) {
            return false;
        }
        $song = [
            'user' => getUserData($user),
            'song' => $playerWaitSong,
            'since' => time(),
        ];
        return $song;
    }
    protected function playSong($room_id, $song, $last = false)
    {
        if ($last) {
            cache('SongNow_' . $room_id, $song);
        } else {
            cache('SongNow_' . $room_id, $song, 3600);
        }
        $songList = $this -> getSongList($room_id);

        cache("song_detail_" . $song['song']['mid'], $song['song'], 3600);
        $msg = [
            'at' => $song['at'] ?? false,
            'user' => $song['user'],
            'song' => $song['song'],
            'since' => $song['since'],
            "type" => "playSong",
            "time" => date('H:i:s'),
            'count' => count($songList) ?? 0
        ];
        sendWebsocketMessage('channel', $room_id, $msg);
    }
    protected function getPlayingSong($room_id)
    {
        return  cache('SongNow_' . $room_id) ?? false;
    }
    protected function getSongFromList($room_id)
    {
        $songList = cache('SongList_' . $room_id) ?? [];
        if (count($songList) > 0) {
            $songNow = $songList[0];
            $songNow['since'] = time() + 5;
            array_shift($songList);
            cache('SongList_' . $room_id, $songList, 86400);
            return $songNow;
        } else {
            return false;
        }
    }
    protected function getSongList($room_id)
    {
        $songList = cache('SongList_' . $room_id) ?? [];
        return $songList;
    }
    protected function getRoomList()
    {
        $roomModel = new RoomModel();
        $rooms = cache('RoomList') ?? false;
        if (!$rooms) {
            $rooms = $roomModel->field('room_id,room_robot,room_type,room_playone,room_user')->where('room_type in (1,4) and room_realonline > 0 or room_id < 1000')->select();
            $rooms = $rooms ? $rooms->toArray() : [];
            if ($rooms) {
                cache('RoomList', $rooms, 5);
            }
        }
        return $rooms;
    }
    protected function getSongByRobot()
    {
        // 从热门榜单中随机点一首歌
        $bangIdArray = [278, 284, 26, 64, 187, 281, 153, 17, 16, 158, 145, 93, 185, 290, 279, 264, 283, 282, 255];
        $bangId = $bangIdArray[rand(0, count($bangIdArray) - 1)];
        $randNumber = rand(10000000, 99999999);
        //function curlHelper($url, $method = 'GET', $data = null, $header = [], $cookies = "")
        $result = curlHelper('http://kuwo.cn/api/www/bang/bang/musicList?bangId=' . $bangId . '&pn=1&rn=100', 'GET', null, [
            'csrf: ' . $randNumber,
        ], "kw_token=" . $randNumber);
        if (!$result['body']) {
            return false;
        }
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
        ], 3600);

        $userModel = new UserModel();
        $robotInfo = $userModel->where("user_id", 1)->find();
        return [
            'song' => [
                'mid' => $song['rid'],
                'name' => $song['name'],
                'pic' => $song['pic'],
                'length' => $song['duration'],
                'singer' => $song['artist'],
            ],
            'since' => time(),
            'count' => 1,
            'user' => [
                "app_id" => 1,
                "app_name" => "BBBUG",
                "app_url" => "https://bbbug.com",
                "user_admin" => $robotInfo['user_admin'],
                "user_head" => $robotInfo['user_head'],
                "user_id" => $robotInfo['user_id'],
                "user_name" => rawurldecode($robotInfo['user_name']),
                "user_remark" => rawurldecode($robotInfo['user_remark']),
            ],
        ];
    }
}
