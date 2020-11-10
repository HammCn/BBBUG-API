<?php

namespace app\model;

use think\facade\Db;
use app\model\Node as NodeModel;

use app\model\BaseModel;

class Auth extends BaseModel
{
    /**
     * 判断用户组是否获得某节点的授权
     *
     * @param int 用户组ID
     * @param int 节点ID
     * @return void
     */
    public function auth($auth_group, $auth_node)
    {
        $auth = $this->where([
            "auth_group" => $auth_group,
            "auth_node" => $auth_node
        ])->find();
        return $auth ? true : false;
    }

    /**
     * 根据用户组 获取管理后台菜单
     *
     * @param int 用户组ID
     * @return void
     */
    public function getAdminMenuListByUserId($group_id)
    {
        $NodeModel = new NodeModel();
        if ($group_id == 1) {
            //超级管理员组
            $list =  $NodeModel
                ->where([
                    "node_pid"   =>  0,
                    "node_show"   =>  1,
                ])
                ->order("node_order desc,node_id asc")
                ->select();
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['subList'] = $this->getSubAdminListByPid($list[$i]['node_id'], $group_id);
            }
            return $list;
        } else {
            //其他组
            $list = $NodeModel
                ->alias("node")
                ->view('node', '*')
                ->view('auth', '*', 'node.node_id=auth.auth_node', 'left')
                ->where([
                    "node_pid"   =>  0,
                    "node_show"   =>  1,
                    "auth_group"    => $group_id
                ])
                ->order("node_order desc,node_id asc")
                ->select();
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]['subList'] = $this->getSubAdminListByPid($list[$i]['node_id'], $group_id);
            }
            return $list;
        }
    }

    /**
     * 根据节点ID 获取用户组的子菜单
     *
     * @param int 节点ID
     * @param int 用户组ID
     * @return void
     */
    public function getSubAdminListByPid($node_id, $group_id = 1)
    {
        $NodeModel = new NodeModel();
        if ($group_id == 1) {
            //超级管理员组
            return $NodeModel
                ->where([
                    "node_pid"   =>  $node_id,
                    "node_show"   =>  1
                ])
                ->order("node_order desc,node_id asc")
                ->select();
        } else {
            //其他组
            return $NodeModel
                ->alias("node")
                ->view('node', '*')
                ->view('auth', '*', 'node.node_id=auth.auth_node', 'left')
                ->where([
                    "node_pid"   =>  $node_id,
                    "node_show"   =>  1,
                    "auth_group"    => $group_id
                ])
                ->order("node_order desc,node_id asc")
                ->select();
        }
    }
    /**
     * 删除授权记录
     *
     * @return void
     */
    public function cleanAuth()
    {
        //清空auth表
        Db::execute("truncate table " . config('database.connections.mysql.prefix') . "auth");
        return true;
    }
}
