<?php

namespace app\model;

use app\model\BaseModel;

class Room extends BaseModel
{
    public function getHotRooms($order = null, $field = "*")
    {
        $resource = $this->view('room', $field)->view('user', 'user_id,user_name,user_group,user_head', 'room.room_user = user.user_id');
        
        $resource = $resource->whereRaw("(room_online > 0 or room_id = 888 or room_id = 777) and room_hide = 0");
        if ($order) {
            $resource = $resource->order($order);
        }
        return $resource->select();
    }
}
