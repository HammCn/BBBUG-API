<?php

namespace app\api\controller;

use app\api\BaseController;
use app\model\Room as RoomModel;
use app\model\User as UserModel;
use app\model\Song as SongModel;
use think\App;

class Room extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //查询列表时允许的字段
        $this->selectList = "*";
        //查询详情时允许的字段
        $this->selectDetail = "*";
        //筛选字段
        $this->searchFilter = [
            "room_id" => "=",
            "room_user" => "like", "room_name" => "like", "room_type" => "like", "room_password" => "like", "room_notice" => "like",
        ];
        $this->insertFields = [
            //允许添加的字段列表
            "room_user", "room_name", "room_type", "room_password", "room_notice",
        ];
        $this->updateFields = [
            //允许更新的字段列表
            "room_user", "room_name", "room_type", "room_password", "room_notice", "room_robot", "room_addsong",
            "room_sendmsg", "room_public", "room_playone", "room_votepass", "room_votepercent",
            "room_addsongcd", "room_pushdaycount", "room_pushsongcd", "room_addcount", "room_hide", "room_background"
        ];
        $this->insertRequire = [
            //添加时必须填写的字段
            // "字段名称"=>"该字段不能为空"
            "room_name" => "房间名称必须填写呀",

        ];
        $this->updateRequire = [
            //修改时必须填写的字段
            // "字段名称"=>"该字段不能为空"
            "room_name" => "房间名称必须填写呀",

        ];
        $this->model = new RoomModel();
    }
    public function saveMyRoom()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "参数必须填写", 400);
        }
        $item = $this->model->getRoomById($this->pk_value);
        if (empty($item)) {
            return jerr("数据查询失败", 404);
        }
        if ($item['room_user'] != $this->user['user_id'] && !getIsAdmin($this->user)) {
            return jerr("你没有权限修改此房间信息");
        }
        //校验Update字段是否填写
        $error = $this->validateUpdateFields();
        if ($error) {
            return $error;
        }
        //从请求中获取Update数据
        $data = $this->getUpdateDataFromRequest();

        $reConnect = false;


        //根据主键更新这条数据
        if (empty($data['room_public'])) {
            $data['room_public'] = 0;
        }
        if ($data['room_public'] == 0) {
            //设置公开 取消密码
            $data['room_password'] = '';
        } else {
            //设置加密
            if (empty($item['room_password'])) {
                //原来没设置密码
                if (empty($data['room_password'])) {
                    return jerr('请输入一个房间密码');
                } else {
                    //输入了密码 需要用户重新连接
                    $reConnect = true;
                }
            } else {
                //原来设置了密码
                if (empty($data['room_password'])) {
                    //没有输入 不修改密码
                    unset($data['room_password']);
                } else {
                    // 输入了密码 需要修改
                    $reConnect = true;
                }
            }
            if (!empty($data['room_password']) && (strlen($data['room_password']) > 16 || strlen($data['room_password']) < 4)) {
                return jerr('密码长度应为4-16位');
            }
        }

        if (!isset($data['room_type']) || !in_array($data['room_type'], [0, 1, 4])) {
            $data['room_type'] = 1;
        }

        if (!empty(input('room_addsongcd')) && intval(input('room_addsongcd')) < 60 && intval(input('room_addsongcd')) > 0) {
            $data['room_addsongcd'] = intval($data['room_addsongcd']);
        } else {
            $data['room_addsongcd'] = 60;
        }

        if (!empty(input('room_pushsongcd'))  && intval(input('room_pushsongcd')) > 0) {
            $data['room_pushsongcd'] = intval($data['room_pushsongcd']);
        } else {
            $data['room_pushsongcd'] = 60;
        }

        if (!empty(input('room_pushdaycount')) && intval(input('room_pushdaycount')) > 0) {
            $data['room_pushdaycount'] = intval($data['room_pushdaycount']);
        } else {
            $data['room_pushdaycount'] = 5;
        }

        if (!empty(input('room_addcount')) && intval(input('room_addcount')) > 0) {
            $data['room_addcount'] = intval($data['room_addcount']);
        } else {
            $data['room_addcount'] = 5;
        }

        if (input('room_background')) {
            $data['room_background'] = input('room_background');
            if (strpos(strtolower($data['room_background']), '.jpg') === FALSE && strpos(strtolower($data['room_background']), '.png') === FALSE) {
                return jerr('房间背景支持JPG/PNG图片');
            }
            if (strpos(strtolower($data['room_background']), config('startadmin.api_url')) === FALSE && strpos(strtolower($data['room_background']), config('startadmin.static_url')) === FALSE) {
                return jerr('房间背景不支持站外图');
            }
        }

        if ($data['room_name']) {
            $data['room_name'] = mb_substr($data['room_name'], 0, 20, 'utf-8');
            $data['room_name'] = rawurlencode($data['room_name']);
        }

        if ($data['room_notice']) {
            $data['room_notice'] = mb_substr($data['room_notice'], 0, 600, 'utf-8');
            $data['room_notice'] = rawurlencode($data['room_notice']);
        }

        $this->updateByPk($data);
        $msg = [
            'type' => 'roomUpdate',
            'reConnect' => $reConnect ? 1 : 0,
            'user' => getUserData($this->user),
        ];

        sendWebsocketMessage('channel', $this->pk_value, $msg);

        return jok('房间信息修改成功');
    }
    public function create()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        // return jerr('创建房间功能暂时关闭');
        //校验Insert字段是否填写
        $error = $this->validateInsertFields();
        if ($error) {
            return $error;
        }
        $canCreateRoom = cache('create_room_user_' . $this->user['user_id']) ?? false;
        if (!$canCreateRoom) {
            $songModel = new SongModel();
            $song = $songModel->field('song_id')->where('song_user', $this->user['user_id'])->select();
            if (count($song) < 30) {
                return jerr('点歌超过30首才可能建房间!');
            }
            if (time() - $this->user['user_createtime'] < 86400 * 3) {
                return jerr('注册时间超过3天才能创建房间!');
            }
        }
        $myRoom = $this->model->where('room_user', $this->user['user_id'])->find();
        if ($myRoom) {
            return jerr('创建失败,你已经有了一个房间');
        }
        //从请求中获取Insert数据
        $data = $this->getInsertDataFromRequest();
        //添加这行数据
        $data['room_user'] = $this->user['user_id'];
        // if ($data['room_password']) {
        //     $data['room_public'] = 1;
        // } else {
        //     $data['room_public'] = 0;
        // }
        $room_id = $this->insertRow($data);
        return jok('你的私人房间创建成功!', [
            'room_id' => $room_id
        ]);
    }
    public function getWebsocketUrl()
    {
        if (!input('channel')) {
            return jerr("请选择一个房间呀");
        }
        $channel = input('channel');

        $item = $this->model->where('room_id', $channel)->find();
        if (!$item) {
            return jerr('没有查询到房间信息');
        }
        $ip = getClientIp();
        $where = cache('ip_addr_' . $ip) ?? '';
        if (!$where) {
            $data = curlHelper('https://ipchaxun.com/' . $ip . '/', 'GET', [], [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.72 Safari/537.36',

            ]);
            if ($data['body']) {
                if (preg_match('/<span class="name">归属地：<\/span><span class="value">(.*?)<\/span>/', $data['body'], $matches)) {
                    $where = $matches[1];
                    $where = str_replace('中国', '', $where);
                    $where = str_replace('区', '', $where);
                    $where = str_replace('县', '', $where);
                    $where = str_replace('市', '', $where);
                    cache('ip_addr_' . $ip, $where, 3600);
                }
            }
        }
        $plat = '';
        if (input('referer')) {
            $referer = (input('referer'));
            if (strpos($referer, 'v2ex.com') !== false) {
                $plat = 'v2ex';
            } else if (strpos($referer, 'juejin.cn') !== false) {
                $plat = '掘金';
            } else if (strpos($referer, 'oschina.net') !== false) {
                $plat = 'OSChina';
            } else if (strpos($referer, 'gitee.com') !== false) {
                $plat = 'Gitee';
            } else if (strpos($referer, 'jianshu.com') !== false) {
                $plat = '简书';
            } else if (strpos($referer, 'csdn.net') !== false) {
                $plat = 'CSDN';
            } else if (strpos($referer, 'segmentfault.com') !== false) {
                $plat = '思否';
            } else if (strpos($referer, 'github.com') !== false) {
                $plat = 'Github';
            } else if (strpos($referer, 'gitee.io') !== false) {
                $plat = 'OSC动弹';
            }
        }
        if (input('access_token') == getTempToken()) {
            if ($item['room_public'] == 1) {
                return jerr('禁止游客进入密码房间');
            }
            // $user_id = preg_replace("/[^\.]{1,3}$/", "*", $ip) . $_SERVER['REMOTE_PORT'];
            $user_id = $ip . ":" . $_SERVER['REMOTE_PORT'];
            $lastSend = cache('channel_' . $channel . '_user_' . $ip) ?? false;
            if (!$lastSend) {
                $string = '欢迎';
                if ($where) {
                    $string .= '来自' . $where . '的';
                }
                if ($plat) {
                    $string .= $plat . '用户';
                } else {
                    $string .= '临时用户';
                }
                $msg = [
                    'type' => 'join',
                    'name' => '临时用户',
                    'where' => $where,
                    'plat' => $plat,
                    'user' => null,
                    'content' => $string,
                ];

                sendWebsocketMessage('channel', $channel, $msg);
                cache('channel_' . $channel . '_user_' . $ip, time(), 30);
            }

            return jok('success', [
                'account' => $user_id,
                'channel' => $channel,
                'ticket' => sha1("account" . $user_id . "channel" . $channel . 'salt' . $channel),
            ]);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }

        if ($item['room_public'] == 1 && $this->user['user_id'] != $item['room_user'] &&  !in_array($this->user['user_id'], [1])) {
            $savedPassword = cache('password_room_' . $item['room_id'] . "_password_" . $this->user['user_id']) ?? '';
            $inputPassword = input('room_password');
            if ($item['room_password'] != $savedPassword && $item['room_password'] != $inputPassword) {
                return jerr("房间密码错误，获取服务器地址失败");
            }
        }

        $lastSend = cache('channel_' . $channel . '_user_' . $this->user['user_id']) ?? false;
        if (!$lastSend) {
            $string = '欢迎';
            if ($where) {
                $string .=  $where . '的';
            }
            $string .= rawurldecode($this->user['user_name']) . '回来!';
            $msg = [
                'type' => 'join',
                'name' => rawurldecode($this->user['user_name']),
                'where' => $where,
                'plat' => $plat,
                'user' => getUserData($this->user),
                'content' => $string,
            ];

            if ($this->user['user_id'] > 0 &&  !in_array($this->user['user_id'], [1])) {
                sendWebsocketMessage('channel', $channel, $msg);
            }
            cache('channel_' . $channel . '_user_' . $this->user['user_id'], time(), 30);
        }

        return jok('success', [
            'account' => $this->user['user_id'],
            'channel' => $channel,
            'ticket' => sha1("account" . $this->user['user_id'] . "channel" . $channel . 'salt' . $channel),
        ]);
    }
    public function hotRooms()
    {
        if (input('access_token') == getTempToken()) {
            $order = 'room_order desc,room_online desc,room_id asc';
            //设置Model中的 per_page
            $this->setGetListPerPage();
            $dataList = cache('room_list_guest') ?? false;
            if ($dataList) {
                return jok('from cache', $dataList);
            }
            $dataList = $this->model->getHotRooms($order, $this->selectList);
            for ($i = 0; $i < count($dataList); $i++) {
                if (in_array($dataList[$i]['user_group'], [1])) {
                    $dataList[$i]['user_admin'] = true;
                } else {
                    $dataList[$i]['user_admin'] = false;
                }
                unset($dataList[$i]['user_group']);
                unset($dataList[$i]['room_password']);
            }
            cache('room_list_guest', $dataList, 60);
            return jok('数据获取成功', $dataList);
        }
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $order = 'room_order desc,room_online desc,room_id asc';
        //设置Model中的 per_page
        $this->setGetListPerPage();
        $dataList = cache('room_list') ?? false;
        if ($dataList) {
            return jok('from cache', $dataList);
        }
        $dataList = $this->model->getHotRooms($order, $this->selectList);
        for ($i = 0; $i < count($dataList); $i++) {
            if (in_array($dataList[$i]['user_group'], [1])) {
                $dataList[$i]['user_admin'] = true;
            } else {
                $dataList[$i]['user_admin'] = false;
            }
            unset($dataList[$i]['room_password']);
        }
        cache('room_list', $dataList, 10);
        return jok('数据获取成功', $dataList);
    }
    public function myRoom()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $myRoom = $this->model->where('room_user', $this->user['user_id'])->find();
        unset($myRoom['room_password']);
        return jok('获取成功', $myRoom);
    }
    public function getRoomInfo()
    {
        $userModel = new UserModel();
        if ($this->pk_value != 888) {
            // return jerr("子房间维护中");
        }
        if (input('access_token') == getTempToken()) {
            if (!$this->pk_value) {
                return jerr($this->pk . "必须填写", 400);
            }
            $item = $this->model->getRoomById($this->pk_value);
            if (empty($item)) {
                return jerr("没有查询到数据", 404);
            }
            if ($item['room_public'] == 1) {
                return jerr("暂不支持游客进入密码房间");
            }
            unset($item['room_password']);
            if ($item['room_status'] == 1) {
                return jerr($item['room_reason'], 301);
            }
            $admin = $userModel->where("user_id", $item['room_user'])->find();
            $item['admin'] = getUserData($admin);

            return jok('数据加载成功', $item);
        }
        //校验Access与RBAC
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        $item = $this->model->getRoomById($this->pk_value);
        if (empty($item)) {
            return jerr("没有查询到数据", 404);
        }
        if ($item['room_public'] == 1 && $this->user['user_id'] != $item['room_user'] && !in_array($this->user['user_id'], [1])) {
            $savedPassword = cache('password_room_' . $item['room_id'] . "_password_" . $this->user['user_id']) ?? '';
            $inputPassword = input('room_password');
            if ($item['room_password'] != $savedPassword && $item['room_password'] != $inputPassword) {
                cache('password_room_' . $item['room_id'] . "_password_" . $this->user['user_id'], null);
                return jerr("房间密码错误，进入房间失败", 302);
            }
            cache('password_room_' . $item['room_id'] . "_password_" . $this->user['user_id'], $item['room_password'], 86400);
        }
        if ($item['room_status'] == 1) {
            return jerr($item['room_reason'], 301);
        }
        unset($item['room_password']);

        $admin = $userModel->where("user_id", $item['room_user'])->find();
        $item['admin'] = getUserData($admin);

        return jok('数据加载成功', $item);
    }
}
