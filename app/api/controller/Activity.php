<?php

namespace app\api\controller;

use think\facade\View;
use think\facade\Db;
use app\api\BaseController;
use app\model\Song as SongModel;
use app\model\Room as RoomModel;
use app\model\User as UserModel;

class Activity extends BaseController
{
    public function index(){
        $user_id =  input('user_id');
        if(!$user_id){
            header('Location: https://bbbug.com');die;
        }
        $userModel = new UserModel();
        $user = $userModel->where('user_id',$user_id)->find();
        if(!$user){
            header('Location: https://bbbug.com');die;
        }
        
        $_count = Db::query("select count(user_id) as _count from sa_user")[0]['_count'];
        View::assign('user_total_count', $_count);

        $_count = Db::query("select count(room_id) as _count from sa_room")[0]['_count'];
        View::assign('room_total_count', $_count);

        $_count = Db::query("select sum(song_play) as _count from sa_song")[0]['_count'];
        View::assign('song_total_count', $_count);

        $_count = Db::query("select count(DISTINCT(song_mid)) as _count from sa_song")[0]['_count'];
        View::assign('song_total', $_count);

        $_count = Db::query("SELECT song_singer,sum(song_play) as song_play from sa_song group by song_singer order by song_play desc limit 10;");
        $arr = [];
        foreach($_count as $_item){
            $arr[] = '<span class="value" style="font-size:24px">'.$_item['song_singer'].'('.$_item['song_play'].'次)</span><br>';
        }
        View::assign('singer_list', $arr);

        $_count = Db::query("SELECT song_singer,sum(song_play) as song_play from sa_song where song_user = ".$user_id."  group by song_singer order by song_play desc limit 10;");
        $arr = [];
        foreach($_count as $_item){
            $arr[] = '<span class="value" style="font-size:24px">'.$_item['song_singer'].'('.$_item['song_play'].'次)</span><br>';
        }
        View::assign('my_singer_name', $arr);

        $_count = Db::query("select count(DISTINCT(song_mid)) as _count from sa_song where song_user = ".$user_id)[0]['_count'];
        View::assign('my_song_count', $_count);

        $_count = Db::query("SELECT song_name,sum(song_play) as song_play from sa_song where song_user = ".$user_id."  group by song_name order by song_play desc limit 10;");
        $arr = [];
        foreach($_count as $_item){
            $arr[] = '<span class="value" style="font-size:24px">'.$_item['song_name'].'('.$_item['song_play'].'次)</span><br>';
        }
        View::assign('my_song_name', $arr);

        $_count = Db::query("select count(song_id) as song_count,user_name from sa_song join sa_user on sa_user.user_id = sa_song.song_user group by song_user order by song_count desc limit 10");
        $arr = [];
        foreach($_count as $_item){
            $arr[] = '<span class="value" style="font-size:24px">'.urldecode($_item['user_name']).' ('.$_item['song_count'].'首)</span><br>';
        }
        View::assign('hot_user', $arr);

        $_count = Db::query("select count(message_id) as message_count,user_name from sa_message join sa_user on sa_user.user_id = sa_message.message_user where message_type like 'img' group by message_user order by message_count desc limit 10");
        $arr = [];
        foreach($_count as $_item){
            $arr[] = '<span class="value" style="font-size:24px">'.urldecode($_item['user_name']).' ('.$_item['message_count'].'张)</span><br>';
        }
        View::assign('img_user', $arr);

        $_count = Db::query("select count(message_id) as message_count,user_name from sa_message join sa_user on sa_user.user_id = sa_message.message_user where message_type like 'text' group by message_user order by message_count desc limit 10");
        $arr = [];
        foreach($_count as $_item){
            $arr[] = '<span class="value" style="font-size:24px">'.urldecode($_item['user_name']).' ('.$_item['message_count'].'句)</span><br>';
        }
        View::assign('text_user', $arr);

        return View::fetch();
    }
}
