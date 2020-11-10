<?php

namespace app\model;

use think\facade\Db;

use app\model\BaseModel;

class Log extends BaseModel
{
    public function getListByPage($maps, $order = null, $field = "*")
    {
        //联查user/node表的相关字段
        $resource = $this->view('log', $field)->view('user', 'user_id,user_name', 'user.user_id = log.log_user', 'left')->view('node', '*', 'node.node_id=log.log_node', 'left');
        foreach ($maps as $map) {
            switch (count($map)) {
                case 1:
                    $resource = $resource->where($map[0]);
                    break;
                case 2:
                    $resource = $resource->where($map[0], $map[1]);
                    break;
                case 3:
                    $resource = $resource->where($map[0], $map[1], $map[2]);
                    break;
                default:
            }
        }
        if ($order) {
            $resource = $resource->order($order);
        }

        return $resource->paginate($this->per_page);
    }
    public function getLogStatus()
    {
        //MySQL在5.7及以上版本中的ONLY_FULL_GROUP_BY问题处理方案
        //https://blog.hamm.cn/2018747.html
        $datalist = $this->field('count(log_id) as visitcount,log_node')->view('log', '*')->view('user', '*', 'user.user_id = log.log_user', 'left')->view('node', '*', 'node.node_id=log.log_node', 'left')->group("log_node")->order("visitcount desc")->select();
        return $datalist;
    }
    /**
     * 删除访问日志
     *
     * @return void
     */
    public function cleanLog()
    {
        //清空log表
        Db::execute("truncate table " . config('database.connections.mysql.prefix') . "log");
        return true;
    }
}
