<?php

namespace app\model;

use app\model\BaseModel;

class Room extends BaseModel
{
    public function getHotRooms($order = null, $field = "*")
    {
        $resource = $this->view('room', $field)->view('user', 'user_id,user_name,user_group,user_head', 'room.room_user = user.user_id');

        $resource = $resource->whereRaw("(room_online > 0 or room_order > 1000000) and room_hide = 0");
        if ($order) {
            $resource = $resource->order($order);
        }
        $list = $resource->select();
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['room_name'] = urldecode($list[$i]['room_name']);
            $list[$i]['room_notice'] = urldecode($list[$i]['room_notice']);
        }
        return $list;
    }
    public function getRoomById($room_id)
    {
        $room = $this->where('room_id', $room_id)->find();
        if ($room) {
            $room['room_name'] = urldecode($room['room_name']);
            $room['room_notice'] = urldecode($room['room_notice']);
        }
        return $room ?? false;
    }
    public function getRoomByUser($user_id)
    {
        $room = $this->where('room_user', $user_id)->find();
        if ($room) {
            $room['room_name'] = urldecode($room['room_name']);
            $room['room_notice'] = urldecode($room['room_notice']);
        }
        return $room ?? false;
    }
}
