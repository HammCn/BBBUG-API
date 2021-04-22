<?php

namespace app\api\controller;

use app\api\BaseController;
use app\model\App as AppModel;
use app\model\Room as RoomModel;
use app\model\Sms as SmsModel;
use app\model\User as model;
use think\App;

class User extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //查询字段
        $this->selectList = "*";
        $this->selectDetail = "*";
        //筛选字段
        $this->searchFilter = [
            "user_id" => "=",
            "user_account" => "like",
            "user_name" => "like",
            "user_truename" => "like",
            "user_status" => "=",
        ];
        $this->insertFields = [
            "user_account", "user_password", "user_name", "user_idcard", "user_email", "user_group", "user_truename",
        ];
        $this->updateFields = [
            "user_account", "user_password", "user_name", "user_idcard", "user_email", "user_group", "user_truename",
        ];
        $this->insertRequire = [
            'user_name' => "用户昵称必须填写",
            'user_account' => "用户帐号必须填写",
            'user_password' => "密码必须填写",
            'user_group' => "用户组必须填写",
        ];
        $this->updateRequire = [
            'user_name' => "用户昵称必须填写",
            'user_account' => "用户帐号必须填写",
            'user_group' => "用户组必须填写",
        ];
        $this->excelField = [
            "id" => "编号",
            "account" => "帐号",
            "name" => "昵称",
            "idcard" => "身份证",
            "email" => "邮箱",
            "createtime" => "创建时间",
            "updatetime" => "修改时间",
        ];
        $this->model = new model();
    }
    public function add()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $error = $this->validateInsertFields();
        if ($error) {
            return $error;
        }
        $data = $this->getInsertDataFromRequest();
        $data['user_ipreg'] = "127.0.0.1";
        $user = $this->model->getUserByAccount($data["user_account"]);
        if ($user) {
            return jerr("帐号已存在，请重新输入");
        }
        $salt = getRandString(4);
        $password = $data["user_password"];
        $password = encodePassword($password, $salt);
        $data["user_salt"] = $salt;
        $data["user_password"] = $password;
        $this->insertRow($data);
        return jok('用户添加成功');
    }
    public function update()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        if (!isInteger($this->pk_value)) {
            return jerr("修改失败,参数错误", 400);
        }
        $item = $this->model->where($this->pk, $this->pk_value)->find();
        if (empty($item)) {
            return jerr("数据查询失败", 404);
        }
        if (intval($this->pk_value) == 1) {
            return jerr("无法修改超管用户信息");
        }
        foreach ($this->updateRequire as $k => $v) {
            if (!input($k)) {
                return jerr($v);
            }
        }
        $data = [];
        foreach (input('post.') as $k => $v) {
            if (in_array($k, $this->updateFields)) {
                $data[$k] = $v;
            }
        }
        $user = $this->model->getUserByAccount($data["user_account"]);
        if ($user && $user[$this->pk] != $item[$this->pk]) {
            return jerr("帐号已存在，请重新输入");
        }
        if (input('new_password')) {
            //设置密码
            $salt = getRandString(4);
            $password = input('new_password');
            $password = encodePassword($password, $salt);
            $data["user_salt"] = $salt;
            $data["user_password"] = $password;
        }
        if ($this->user['user_group'] != 1) {
            //除超级管理员组外 其他任何组不允许修改用户组
            unset($data['user_group']);
        }
        $data[$this->table . "_updatetime"] = time();
        $this->model->where($this->pk, $this->pk_value)->update($data);
        return jok('用户信息更新成功');
    }

    /**
     * 禁用用户
     *
     * @return void
     */
    public function disable()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "参数必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            $map = [$this->pk => $this->pk_value];
            $item = $this->model->where($map)->find();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            if ($item["user_group"] == 1) {
                return jerr("超级管理员不允许操作！");
            }
            $this->model->where($map)->update([
                $this->table . "_status" => 1,
                $this->table . "_updatetime" => time(),
            ]);
        } else {
            $list = explode(',', $this->pk_value);
            $this->model->where($this->pk, 'in', $list)->where("user_group > 1")->update([
                $this->table . "_status" => 1,
                $this->table . "_updatetime" => time(),
            ]);
        }
        return jok("禁用用户成功");
    }

    /**
     * 启用用户
     *
     * @return void
     */
    public function enable()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "参数必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            $map = [$this->pk => $this->pk_value];
            $item = $this->model->where($map)->find();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            if ($item["user_group"] == 1) {
                return jerr("超级管理员不允许操作！");
            }
            $this->model->where($map)->update([
                $this->table . "_status" => 0,
                $this->table . "_updatetime" => time(),
            ]);
        } else {
            $list = explode(',', $this->pk_value);
            $this->model->where($this->pk, 'in', $list)->where("user_group > 1")->update([
                $this->table . "_status" => 0,
                $this->table . "_updatetime" => time(),
            ]);
        }
        return jok("启用用户成功");
    }

    /**
     * 删除用户
     *
     * @return void
     */
    public function delete()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        if (isInteger($this->pk_value)) {
            $map = [$this->pk => $this->pk_value];
            $item = $this->model->where($map)->find();
            if (empty($item)) {
                return jerr("数据查询失败", 404);
            }
            //
            if ($item["user_group"] == 1) {
                return jerr("超级管理员不允许操作！");
            }
            $this->model->where($map)->delete();
        } else {
            $list = explode(',', $this->pk_value);
            //批量删除只允许删除用户组不为1的用户
            $this->model->where($this->pk, 'in', $list)->where("user_group > 1")->delete();
        }
        return jok('删除用户成功');
    }
    public function detail()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!$this->pk_value) {
            return jerr($this->pk . "必须填写", 400);
        }
        $item = $this->model->field($this->selectDetail)->where($this->pk, $this->pk_value)->find();
        if (empty($item)) {
            return jerr("没有查询到数据", 404);
        }
        return jok('数据加载成功', $item);
    }
    public function getList()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        $map = [];
        $filter = input('post.');
        foreach ($filter as $k => $v) {
            if ($k == 'filter') {
                $k = input('filter');
                $v = input('keyword');
            }
            if ($v === '' || $v === null) {
                continue;
            }
            if (array_key_exists($k, $this->searchFilter)) {
                switch ($this->searchFilter[$k]) {
                    case "like":
                        array_push($map, [$k, 'like', "%" . $v . "%"]);
                        break;
                    case "=":
                        array_push($map, [$k, '=', $v]);
                        break;
                    default:
                }
            }
        }
        $order = strtolower($this->controller) . "_id desc";
        if (input('order')) {
            $order = rawurldecode(input('order'));
        }
        if (input('per_page')) {
            $this->model->per_page = intval(input('per_page'));
        }
        $dataList = $this->model->getListByPage($map, $order, $this->selectList);
        return jok('用户列表获取成功', $dataList);
    }
    public function login()
    {
        if (!input("user_account")) {
            return jerr('请确认帐号是否正确填写', 400);
        }
        if (!input("user_password")) {
            return jerr('请确认密码是否正确填写', 400);
        }
        $plat = input("plat");
        $user_account = input("user_account");
        $user_password = input("user_password");

        // cache("MAIL_".$user_account,null);
        $code = cache("MAIL_" . $user_account) ?? '';
        $user = false;
        if ($code == $user_password) {
            //验证通过
            if (preg_match("/^[1-9][0-9]*$/", $user_account)) {
                $user = $this->model->where('user_id', $user_account)->find();
            } else {
                $user = $this->model->where('user_account', $user_account)->find();
            }
            if (!$user) {
                //没有查询到用户
                $this->model->regByLogin($user_account, explode('@', $user_account)[0]);
                $user = $this->model->where('user_account', $user_account)->find();
                cache("MAIL_" . $user_account, null);
            }
        } else {
            //登录获取用户信息
            $user = $this->model->login($user_account, $user_password);
        }

        if ($user) {
            //创建一个新的授权
            $access = $this->accessModel->createAccess($user['user_id'], $plat);
            if ($access) {

                $device = false;
                switch (input('user_device')) {
                    case 'Windows':
                        $device = 'Windows';
                        break;
                    case 'iPad':
                        $device = 'iPad';
                        break;
                    case 'iPhone':
                        $device = 'iPhone';
                        break;
                    case 'Ubuntu':
                        $device = 'Ubuntu';
                        break;
                    case 'Android':
                        $device = 'Android';
                        break;
                    case 'MacOS':
                        $device = 'MacOS';
                        break;
                    default:
                        $device = 'UnKnown';
                }
                if (!input('user_device')) {
                    $device = getOs();
                }
                $this->model->where('user_id', $user['user_id'])->update([
                    'user_device' => $device,
                ]);

                setCookie('access_token', $access['access_token'], time() + 3600, '/');
                return jok('登录成功', ['access_token' => $access['access_token']]);
            } else {
                return jerr('登录系统异常');
            }
        } else {
            return jerr('帐号或密码错误');
        }
    }
    public function getRankList()
    {
        $type = input('type');
        switch ($type) {
            case 'img':
                $ret = $this->model->field('user_id,user_name,user_remark,user_head,user_group,user_img')
                    ->order('user_img desc,user_id desc')->where('user_img', '>', 0)->limit(50)->select();
                return jok('success', $ret);
                break;
            case 'chat':
                $ret = $this->model->field('user_id,user_name,user_remark,user_head,user_group,user_chat')
                    ->order('user_chat desc,user_id desc')->where('user_chat', '>', 0)->limit(50)->select();
                return jok('success', $ret);
                break;
            case 'song':
                $ret = $this->model->field('user_id,user_name,user_remark,user_head,user_group,user_song')
                    ->order('user_song desc,user_id desc')->where('user_song', '>', 0)->limit(50)->select();
                return jok('success', $ret);
                break;
            case 'pass':
                $ret = $this->model->field('user_id,user_name,user_remark,user_head,user_group,user_pass')
                    ->order('user_pass desc,user_id desc')->where('user_pass', '>', 0)->limit(50)->select();
                return jok('success', $ret);
                break;
            case 'push':
                $ret = $this->model->field('user_id,user_name,user_remark,user_head,user_group,user_push')
                    ->order('user_push desc,user_id desc')->where('user_push', '>', 0)->limit(50)->select();
                return jok('success', $ret);
                break;
            case 'songsend':
                $ret = $this->model->field('user_id,user_name,user_remark,user_head,user_group,user_songsend')
                    ->order('user_songsend desc,user_id desc')->where('user_songsend', '>', 0)->limit(50)->select();
                return jok('success', $ret);
                break;
            case 'songrecv':
                $ret = $this->model->field('user_id,user_name,user_remark,user_head,user_group,user_songrecv')
                    ->order('user_songrecv desc,user_id desc')->where('user_songrecv', '>', 0)->limit(50)->select();
                return jok('success', $ret);
                break;
            default:
        }
        return jok('success', []);
    }
    public function online()
    {
        if (!input('room_id')) {
            return jerr('缺少room_id');
        }
        $simpleData = input('sync') == 'yes' ? true : false;
        $room_id = intval(input('room_id'));
        $ret = curlHelper(getWebsocketApiUrl() . "?channel=" . $room_id);
        $arr = json_decode($ret['body'], true);
        $count = $this->model->where('user_id', 'in', $arr)->count('user_id');

        if ($simpleData) {
            //同步一下该频道的在线人数
            $roomModel = new RoomModel();
            $myRoom = $roomModel->where('room_id', $room_id)->update([
                'room_online' => $count,
                'room_realonline' => count($arr)
            ]);
        }

        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            return jerr("房间信息查询失败");
        }

        $order = 'user_id asc';

        $list = [];
        $field = [
            'user' => 'user_id,user_name,user_head,user_group,user_remark,user_device,user_sex,user_extra,user_icon,user_vip',
            'app' => 'app_id,app_name,app_url'
        ];
        if ($simpleData) {
            $field = [
                'user' => 'user_id,user_group',
                'app' => 'app_id'
            ];
            $list = $this->model->view('user', $field['user'])->view('app', $field['app'], 'user.user_app = app.app_id')->where([
                ['user_id', 'in', $arr ?? []],
            ])->order($order)->select();
        } else {
            $cache = cache('online_list_' . $room_id) ?? false;
            if ($cache) {
                return jok('from cache', $cache);
            }
            if ($room['room_public']) {
                $ret = $this->model->view('user', $field['user'])->view('app', $field['app'], 'user.user_app = app.app_id')->where([
                    ['user_id', 'in', $arr ?? []],
                    ['user_id', 'not in', []]
                ])->where('user_group', 1)->whereOr("user_id", 1)->order($order)->select();
            } else {
                $ret = $this->model->view('user', $field['user'])->view('app', $field['app'], 'user.user_app = app.app_id')->where([
                    ['user_id', 'in', $arr ?? []],
                    ['user_id', 'not in', []]
                ])->where('user_group', 1)->whereOr("user_id", 1)->order($order)->select();
            }
            $ret = $ret ? $ret->toArray() : [];
            for ($i = 0; $i < count($ret); $i++) {
                $ret[$i]['user_admin'] = getIsAdmin($ret[$i]);
                $ret[$i]['user_shutdown'] = $this->getCacheStatus('shutdown', $room_id, $ret[$i]['user_id']);
                $ret[$i]['user_songdown'] = $this->getCacheStatus('songdown', $room_id, $ret[$i]['user_id']);
                $ret[$i]['user_guest'] = $this->getCacheStatus('guestctrl', $room_id, $ret[$i]['user_id']);
            }
            $list = array_merge($list, $ret);

            $ret = $this->model->view('user', $field['user'])->view('app', $field['app'], 'user.user_app = app.app_id')->where([
                ['user_id', 'in', $arr ?? []],
            ])->where('user_group', 5)->where('user_id', 'like', $room['room_user'])->order($order)->select();
            $ret = $ret ? $ret->toArray() : [];
            for ($i = 0; $i < count($ret); $i++) {
                $ret[$i]['user_admin'] = getIsAdmin($ret[$i]);
                $ret[$i]['user_shutdown'] = $this->getCacheStatus('shutdown', $room_id, $ret[$i]['user_id']);
                $ret[$i]['user_songdown'] = $this->getCacheStatus('songdown', $room_id, $ret[$i]['user_id']);
                $ret[$i]['user_guest'] = $this->getCacheStatus('guestctrl', $room_id, $ret[$i]['user_id']);
            }
            $list = array_merge($list, $ret);

            $ret = $this->model->view('user', $field['user'])->view('app', $field['app'], 'user.user_app = app.app_id')->where([
                ['user_id', 'in', $arr ?? []],
            ])->where('user_group', 5)->where('user_id', 'not like', $room['room_user'])->order($order)->select();

            $ret = $ret ? $ret->toArray() : [];
            for ($i = 0; $i < count($ret); $i++) {
                $ret[$i]['user_admin'] = getIsAdmin($ret[$i]);
                $ret[$i]['user_shutdown'] = $this->getCacheStatus('shutdown', $room_id, $ret[$i]['user_id']);
                $ret[$i]['user_songdown'] = $this->getCacheStatus('songdown', $room_id, $ret[$i]['user_id']);
                $ret[$i]['user_guest'] = $this->getCacheStatus('guestctrl', $room_id, $ret[$i]['user_id']);
            }
            $list = array_merge($list, $ret);

            if (input('sync') != 'yes') {
                cache('online_list_' . $room_id, $list, 5);
            }
        }

        return jok('success', $list);
    }
    protected function getCacheStatus($type, $room_id, $user_id)
    {
        switch ($type) {
            case 'shutdown':
                return cache('shutdown_room_' . $room_id . '_user_' . $user_id) ? true : false;
                break;
            case 'songdown':
                return cache('songdown_room_' . $room_id . '_user_' . $user_id) ? true : false;
                break;
            case 'guestctrl':
                return cache('guest_room_' . $room_id . '_user_' . $user_id) ? true : false;
                break;
            default:
        }
        return false;
    }
    /**
     * 用户注册接口
     *
     * @return void
     */
    public function reg()
    {
        if (!input("phone")) {
            return jerr("手机号不能为空！", 400);
        }
        $phone = input("phone");
        if (!input("code")) {
            return jerr("短信验证码不能为空！", 400);
        }
        $code = input("code");
        if (!input("password")) {
            return jerr("密码不能为空！", 400);
        }
        $password = input("password");
        $name = $phone;
        if (input("name")) {
            $name = input("name");
        }
        $smsModel = new SmsModel();
        if ($smsModel->validSmsCode($phone, $code)) {
            $user = $this->model->where([
                "user_account" => $phone,
            ])->find();
            if ($user) {
                return jerr("该手机号已经注册！");
            }
            $result = $this->model->reg($phone, $password, $name);
            if ($result) {
                return jok("用户注册成功");
            } else {
                return jerr("注册失败，请重试！");
            }
        } else {
            return jerr("短信验证码已过期，请重新获取");
        }
    }
    public function motifyPassword()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!input("oldPassword")) {
            return jerr("你必须要输入你的原密码！", 400);
        }
        if (!input("newPassword")) {
            return jerr("你必须输入一个新的密码！", 400);
        }
        $old_password = input("oldPassword");
        $new_password = input("newPassword");
        if (strlen($new_password) < 6 || strlen($new_password) > 16) {
            return jerr("新密码应为6-16位！");
        }
        if ($this->user['user_password'] != encodePassword($old_password, $this->user['user_salt'])) {
            return jerr("原密码输入不正确，请重试！");
        }
        $result = $this->model->motifyPassword($this->user['user_id'], $new_password);
        if ($result) {
            return jok("密码已重置，请使用新密码登录");
        } else {
            return jerr("注册失败，请重试！");
        }
    }

    /**
     * 重置密码
     *
     * @return void
     */
    public function resetPassword()
    {
        if (!input("phone")) {
            return jerr("手机号不能为空！", 400);
        }
        if (!input("code")) {
            return jerr("短信验证码不能为空！", 400);
        }
        if (!input("password")) {
            return jerr("密码不能为空！", 400);
        }
        $phone = input("phone");
        $code = input("code");
        $password = input("password");
        $smsModel = new SmsModel();
        if ($smsModel->validSmsCode($phone, $code)) {
            $user = $this->model->where([
                "user_account" => $phone,
            ])->find();
            if (!$user) {
                return jerr("该手机号尚未注册！", 404);
            }
            $result = $this->model->motifyPassword($user['user_id'], $password);
            if ($result) {
                return jok("密码已重置，请使用新密码登录");
            } else {
                return jerr("注册失败，请重试！");
            }
        } else {
            return jerr("短信验证码已过期，请重新获取");
        }
    }

    /**
     * 获取我的信息
     *
     * @return void
     */
    public function getMyInfo()
    {
        if (input('access_token') == getTempToken()) {
            return jok('', [
                'user_id' => -1,
                'user_name' => 'Ghost',
                'user_head' => 'new/images/nohead.jpg',
                'user_admin' => false,
                'myRoom' => false,
            ]);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }

        $myInfo = $this->user;
        $roomModel = new RoomModel();
        $myRoom = $roomModel->getRoomByUser($this->user['user_id']);
        $myRoom = $myRoom ? $myRoom->toArray() : false;
        $needMotify = false;
        if ($myInfo['user_password'] == '123456') {
            $needMotify = true;
        }
        $myInfo['user_needmotify'] = $needMotify;

        $myInfo = getUserData($myInfo);
        $myInfo['myRoom'] = $myRoom;

        $pushCount = cache('push_song_card_user_' . $myInfo['user_id']) ?? 0;
        $passCount = cache('pass_song_card_user_' . $myInfo['user_id']) ?? 0;

        $myInfo['push_count'] = intval($pushCount);
        $myInfo['pass_count'] = intval($passCount);

        return jok('', $myInfo);
    }
    public function updateMyInfo()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!input("user_name")) {
            return jerr("你确定飘到连名字都可以不要了吗？", 400);
        }
        $data = [];
        if (input('user_name')) {
            $data['user_name'] = rawurldecode(input('user_name'));
            $data['user_name'] = mb_substr($data['user_name'], 0, 20, 'utf-8');
            $data['user_name'] = rawurlencode($data['user_name']);
        }
        $data['user_touchtip'] = rawurldecode(input('user_touchtip'));
        $data['user_touchtip'] = mb_substr($data['user_touchtip'], 0, 20, 'utf-8');
        $data['user_touchtip'] = rawurlencode($data['user_touchtip']);

        if (input('user_head')) {
            $data['user_head'] = input('user_head');
            if (strpos(strtolower($data['user_head']), '.gif') !== FALSE) {
                return jerr('头像不支持Gif,不要尝试钻空子');
            }
        }
        if (input('user_remark')) {
            $data['user_remark'] = input('user_remark');
            $data['user_remark'] = rawurldecode(input('user_remark'));
            $data['user_remark'] = mb_substr($data['user_remark'], 0, 50, 'utf-8');
        } else {
            $data['user_remark'] = $this->model->getOneRemark();
        }
        if (input('?user_sex')) {
            $data['user_sex'] = input('user_sex');
            if (intval($data['user_sex']) == 1) {
                $data['user_sex'] = 1;
            } else {
                $data['user_sex'] = 0;
            }
        }

        if (!empty($data['user_head'])) {
            $domain = getTopHost(urldecode($data['user_head']));
            if ($domain) {
                if (strpos($domain, getTopHost(config('startadmin.api_url'))) === FALSE && strpos($domain, getTopHost(config('startadmin.static_url'))) === FALSE) {
                    $obj = getimagesize(urldecode($data['user_head']));
                    if (!$obj || end($obj) == "image/gif") {
                        unset($data['user_head']);
                    }
                } else {
                    $obj = getimagesize(urldecode($data['user_head']));
                    if (!$obj || end($obj) == "image/gif") {
                        unset($data['user_head']);
                    }
                }
            }
        }

        if (input('user_password')) {
            $password = input('user_password');
            if (strlen($password) < 6 || strlen($password) > 16) {
                return jerr("新密码应为6-16位！");
            }
            $salt = getRandString(4);
            $password = encodePassword($password, $salt);
            $data["user_salt"] = $salt;
            $data["user_password"] = $password;
            $this->model->where("user_id", $this->user['user_id'])->update($data);
            return jok("更新成功,请下次使用新密码登录!");
        } else {
            $this->model->where("user_id", $this->user['user_id'])->update($data);
            return jok("资料更新成功");
        }
    }
    public function index()
    {
        // $userlist = $this->model->select();
        // foreach ($userlist as $item) {
        //     $this->model->where('user_id', $item['user_id'])->update([
        //         'user_name' => rawurlencode($item['user_name']),
        //     ]);
        // }
    }
    public function guestctrl()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }

        if (!input('user_id') || !input('room_id')) {
            return jerr("参数错误,缺少user_id/room_id");
        }
        $user_id = input('user_id');
        $room_id = input('room_id');

        $user = $this->model->where('user_id', $user_id)->find();
        if (!$user) {
            return jerr("用户信息查询失败");
        }

        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            return jerr("房间信息查询失败");
        }
        if ($room['room_user'] != $this->user['user_id'] && !getIsAdmin($this->user)) {
            return jerr("你无权操作");
        }
        if (getIsAdmin($user)) {
            return jerr("你无权操作管理员");
        }
        if ($this->user['user_id'] == $user_id) {
            return jerr("自己给自己设置嘉宾是不是太没意思了?");
        }
        if ($user_id == $room['room_user']) {
            return jerr("他是房主,你没必要给他设置嘉宾.");
        }
        cache('online_list_' . $room_id, null);
        $isSet = cache('guest_room_' . $room_id . '_user_' . $user_id) ?? false;
        if ($isSet) {
            cache('guest_room_' . $room_id . '_user_' . $user_id, null);

            $msg = [
                'user' => getUserData($this->user),
                'guest' => getUserData($user),
                "type" => "guest_remove",
                "time" => date('H:i:s'),
            ];
            sendWebsocketMessage('channel', $room_id, $msg);
            return jok("取消嘉宾身份成功!");
        } else {
            cache('guest_room_' . $room_id . '_user_' . $user_id, time());
            $msg = [
                'user' => getUserData($this->user),
                'guest' => getUserData($user),
                "type" => "guest_add",
                "time" => date('H:i:s'),
            ];
            sendWebsocketMessage('channel', $room_id, $msg);
            return jok("设置嘉宾成功!");
        }
    }
    public function shutdown()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }

        if (!input('user_id') || !input('room_id')) {
            return jerr("参数错误,缺少user_id/room_id");
        }
        $user_id = input('user_id');
        $room_id = input('room_id');

        $user = $this->model->where('user_id', $user_id)->find();
        if (!$user) {
            return jerr("用户信息查询失败");
        }

        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            return jerr("房间信息查询失败");
        }
        if ($user_id > 1) {
            if ($room['room_user'] != $this->user['user_id'] && !getIsAdmin($this->user)) {
                return jerr("你无权操作");
            }
            if (getIsAdmin($user) && $this->user['user_id'] != $room['room_user'] && $this->user['user_id'] != 1) {
                return jerr("你无权操作管理员");
            }
        }

        cache('online_list_' . $room_id, null);
        cache('shutdown_room_' . $room_id . '_user_' . $user_id, time());
        $msg = [
            'user' => getUserData($this->user),
            'ban' => getUserData($user),
            "type" => "shutdown",
            "time" => date('H:i:s'),
        ];
        sendWebsocketMessage('channel', $room_id, $msg);
        return jok("禁止发言成功!");
    }
    public function songdown()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }

        if (!input('user_id') || !input('room_id')) {
            return jerr("参数错误,缺少user_id/room_id");
        }
        $user_id = input('user_id');
        $room_id = input('room_id');

        $user = $this->model->where('user_id', $user_id)->find();
        if (!$user) {
            return jerr("用户信息查询失败");
        }

        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            return jerr("房间信息查询失败");
        }
        if ($room['room_user'] != $this->user['user_id'] && !getIsAdmin($this->user)) {
            return jerr("你无权操作");
        }
        if (getIsAdmin($user) && $this->user['user_id'] != $room['room_user'] && $this->user['user_id'] != 1) {
            return jerr("你无权操作管理员");
        }

        cache('online_list_' . $room_id, null);
        cache('songdown_room_' . $room_id . '_user_' . $user_id, time());
        $msg = [
            'user' => getUserData($this->user),
            'ban' => getUserData($user),
            "type" => "songdown",
            "time" => date('H:i:s'),
        ];
        sendWebsocketMessage('channel', $room_id, $msg);
        return jok("禁止点歌成功!");
    }
    public function removeBan()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }

        if (!input('user_id') || !input('room_id')) {
            return jerr("参数错误,缺少user_id/room_id");
        }
        $user_id = input('user_id');
        $room_id = input('room_id');

        $user = $this->model->where('user_id', $user_id)->find();
        if (!$user) {
            return jerr("用户信息查询失败");
        }

        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            return jerr("房间信息查询失败");
        }

        if ($user_id > 1) {
            if ($room['room_user'] != $this->user['user_id'] && !getIsAdmin($this->user)) {
                return jerr("你无权操作");
            }
            if (getIsAdmin($user) && $this->user['user_id'] != $room['room_user'] && $this->user['user_id'] != 1) {
                return jerr("你无权操作管理员");
            }
        }

        cache('online_list_' . $room_id, null);
        cache('shutdown_room_' . $room_id . '_user_' . $user_id, null);
        cache('songdown_room_' . $room_id . '_user_' . $user_id, null);
        $msg = [
            'user' => getUserData($this->user),
            'ban' => getUserData($user),
            "type" => "removeban",
            "time" => date('H:i:s'),
        ];
        sendWebsocketMessage('channel', $room_id, $msg);
        return jok("用户解禁成功!");
    }
    public function getUserInfo()
    {
        if (!input('user_id')) {
            return jerr('参数错误,缺失user_id');
        }
        $user_id = intval(input('user_id'));
        $user = $this->model->where('user_id', $user_id)->find();
        if (!$user) {
            return jerr('用户信息查询失败');
        }
        $user['user_admin'] = getIsAdmin($user);
        $appModel = new AppModel();
        $app = $appModel->view('app', 'app_id,app_name,app_url,app_status')->where('app_id', $user['user_app'])->find();
        if (!$app) {
            return jerr("用户没有所属应用");
        }
        $app = $app->toArray();
        if ($app['app_status'] == 1) {
            return jerr("所在应用被封禁");
        }
        $user = getUserData($user);
        $user = array_merge($user, $app);

        $roomModel = new RoomModel();
        $myRoom = $roomModel->getRoomByUser($user_id);
        $myRoom = $myRoom ? $myRoom->toArray() : false;
        if ($myRoom) {
            $myRoom['room_password'] = null;
        }
        $user['myRoom'] = $myRoom;



        $pushCount = cache('push_song_card_user_' . $user['user_id']) ?? 0;
        $passCount = cache('pass_song_card_user_' . $user['user_id']) ?? 0;

        $user['push_count'] = intval($pushCount);
        $user['pass_count'] = intval($passCount);

        return jok('success', $user);
    }
    public function openlogin()
    {
        if (!input('openid') || !input('appid') || !input('appkey')) {
            return jerr('Missing appid/appkey/openid/headimg/nickname');
        }
        $openid = input('openid');
        $appid = input('appid');
        $appkey = input('appkey');
        $sex = input('sex') ?? 0;
        $head = input('head') ?? '';
        $nickname = input('nickname') ?? '新用户';
        $extra = input('extra') ?? '';

        $appModel = new AppModel();
        $app = $appModel->where('app_id', $appid)->find();
        if (!$app) {
            return jerr('app not found!');
        }
        if ($app['app_key'] != $appkey) {
            return jerr('app error!');
        }
        if ($app['app_status'] == 1) {
            return jerr('app disabled!');
        }

        $user = $this->model->where('user_openid', $openid)->where('user_app', $app['app_id'])->find();
        if (!$user) {
            $this->model->regByOpen($openid, $nickname, $head, $sex,  $appid, $extra);
            $user = $this->model->where('user_openid', $openid)->where('user_app', $app['app_id'])->find();
        }
        if ($user) {
            //创建一个新的授权
            $access = $this->accessModel->createAccess($user['user_id'], $appid);
            if ($access) {
                $this->userModel->where('user_id', $user['user_id'])->update([
                    'user_device' => getOs(),
                ]);
                setCookie('access_token', $access['access_token'], time() + 3600, '/');
                return jok('登录成功', ['access_token' => $access['access_token']]);
            } else {
                return jerr('登录系统异常');
            }
        } else {
            return jerr('帐号或密码错误');
        }
    }
    public function thirdLogin()
    {
        if (!input('from')) {
            return jerr('where are you from?');
        }
        $from  = input('from');
        if (!input('code')) {
            return jerr("What's your passcode?");
        }
        $code  = input('code');
        switch ($from) {
            case 'qq':
                $app_id = '1003';
                $cliend_id = '101904044';
                $client_key = 'b3e2cace11af99c7354409422ecbab51';
                $redirect_uri = config('startadmin.frontend_url') . 'qq';
                $url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&code={$code}&client_id={$cliend_id}&redirect_uri={$redirect_uri}&client_secret=" . $client_key;
                $result = curlHelper($url);
                if ($result['detail']['http_code'] == 200) {
                    if (preg_match('/access_token=(.*?)&/', $result['body'], $match)) {
                        $access_token = $match[1];
                        $url = "https://graph.qq.com/oauth2.0/me?access_token={$access_token}";
                        $result = curlHelper($url);
                        if ($result['detail']['http_code'] == 200) {
                            if (preg_match('/openid":"(.*?)"/', $result['body'], $match)) {
                                $openid = $match[1];
                                $url = "https://graph.qq.com/user/get_user_info?access_token={$access_token}&openid={$openid}&oauth_consumer_key=" . $cliend_id;
                                $result = curlHelper($url);
                                $user = json_decode($result['body'], true);
                                if ($user['ret'] == 0) {
                                    $openid = $openid;
                                    $nickname = $user['nickname'];
                                    $head = $user['figureurl_qq_2'] ?? $user['figureurl_qq_1'];
                                    $extra = $openid;
                                    $sex = $user['gender'] == '男' ? 1 : 0;
                                    $user = $this->model->where('user_openid', $openid)->where('user_app', $app_id)->find();
                                    if (!$user) {
                                        $this->model->regByOpen($openid, $nickname, $head, $sex,  $app_id, $extra);
                                        $user = $this->model->where('user_openid', $openid)->where('user_app', $app_id)->find();
                                    }
                                    if ($user) {
                                        //创建一个新的授权
                                        $access = $this->accessModel->createAccess($user['user_id'], $app_id);
                                        if ($access) {
                                            $this->userModel->where('user_id', $user['user_id'])->update([
                                                'user_device' => getOs(),
                                            ]);
                                            setCookie('access_token', $access['access_token'], time() + 3600, '/');
                                            return jok('登录成功', ['access_token' => $access['access_token']]);
                                        } else {
                                            return jerr('登录系统异常');
                                        }
                                    } else {
                                        return jerr('帐号或密码错误');
                                    }
                                }
                            }
                        }
                    }
                }
                return jerr('使用QQ账号登录失败,请重试');
                break;
            case 'gitee':
                $app_id = '1001';
                $cliend_id = 'd2c3e3c6f5890837a69c65585cc14488e4075709db1e89d4cb4c64ef1712bdbb';
                $client_key = 'eca633af5faf95fb1e5a6e605347683dddb5485b574cc3303ba0a27c2cefc9a6';
                $redirect_uri = config('startadmin.frontend_url') . 'gitee';
                $url = "https://gitee.com/oauth/token?grant_type=authorization_code&code={$code}&client_id=" . $cliend_id . "&redirect_uri={$redirect_uri}&client_secret=" . $client_key;
                $result = curlHelper($url, 'POST', [], [], "");
                if ($result['detail']['http_code'] == 200) {
                    $obj = json_decode($result['body'], true);
                    $access_token = $obj['access_token'];
                    //关注Hamm
                    curlHelper("https://gitee.com/api/v5/user/following/hamm", "PUT", http_build_query([
                        "access_token" => $access_token,
                    ]));

                    $url = "https://gitee.com/api/v5/user?access_token={$access_token}";
                    $result = curlHelper($url);
                    if ($result['detail']['http_code'] == 200) {
                        $user = json_decode($result['body'], true);
                        $openid = $user['id'];
                        $nickname = $user['name'];
                        $head = $user['avatar_url'];
                        $extra = $user['login'];
                        $sex = 0;
                        $user = $this->model->where('user_openid', $openid)->where('user_app', $app_id)->find();
                        if (!$user) {
                            $this->model->regByOpen($openid, $nickname, $head, $sex,  $app_id, $extra);
                            $user = $this->model->where('user_openid', $openid)->where('user_app', $app_id)->find();
                        }
                        if ($user) {
                            //创建一个新的授权
                            $access = $this->accessModel->createAccess($user['user_id'], $app_id);
                            if ($access) {
                                $this->userModel->where('user_id', $user['user_id'])->update([
                                    'user_device' => getOs(),
                                ]);
                                setCookie('access_token', $access['access_token'], time() + 3600, '/');
                                return jok('登录成功', ['access_token' => $access['access_token']]);
                            } else {
                                return jerr('登录系统异常');
                            }
                        } else {
                            return jerr('帐号或密码错误');
                        }
                    }
                }
                return jerr('使用码云账号登录失败,请重试');
                break;
            case 'oschina':
                $app_id = '1002';
                $cliend_id = 'utwQOfbgBgBcwBolfNft';
                $client_key = '0cAwcRfuuCcQhJUgt1ynKldwmxfymJ8n';
                $redirect_uri = config('startadmin.frontend_url') . 'oschina';
                $url = "https://www.oschina.net/action/openapi/token?grant_type=authorization_code&code={$code}&client_id=" . $cliend_id . "&redirect_uri={$redirect_uri}&client_secret=" . $client_key;
                $result = curlHelper($url, 'POST', [], [], "");
                if ($result['detail']['http_code'] == 200) {
                    $obj = json_decode($result['body'], true);
                    $access_token = $obj['access_token'];
                    $url = "https://www.oschina.net/action/openapi/user?access_token={$access_token}";
                    $result = curlHelper($url);
                    if ($result['detail']['http_code'] == 200) {
                        $user = json_decode($result['body'], true);
                        $openid = $user['id'];
                        $nickname = $user['name'];
                        $head = explode('!', $user['avatar'])[0];
                        $extra = str_replace('https://my.oschina.net/', '', $user['url']);
                        $sex = $user['gender'] == 'male' ? 1 : 0;
                        $user = $this->model->where('user_openid', $openid)->where('user_app', $app_id)->find();
                        if (!$user) {
                            $this->model->regByOpen($openid, $nickname, $head, $sex,  $app_id, $extra);
                            $user = $this->model->where('user_openid', $openid)->where('user_app', $app_id)->find();
                        }
                        if ($user) {
                            //创建一个新的授权
                            $access = $this->accessModel->createAccess($user['user_id'], $app_id);
                            if ($access) {
                                $this->userModel->where('user_id', $user['user_id'])->update([
                                    'user_device' => getOs(),
                                ]);
                                setCookie('access_token', $access['access_token'], time() + 3600, '/');
                                return jok('登录成功', ['access_token' => $access['access_token']]);
                            } else {
                                return jerr('登录系统异常');
                            }
                        } else {
                            return jerr('帐号或密码错误');
                        }
                    }
                }
                return jerr('使用开源中国账号登录失败,请重试');
                break;
            case 'ding':
                $app_id = '1004';
                $cliend_id = 'dingoag8afgz20g2otw0jf';
                $client_key = 'fkWK4AanFg_U96xC2Jh1oH_-CcDXNPVHzAnrg_vNNsZRS5nxDj-Zp61qiFXTGGXs';
                $time = time() . "000";
                $s = hash_hmac('sha256', $time, $client_key, true);
                $signature = base64_encode($s);
                $urlencode_signature = urlencode($signature);

                $url = "https://oapi.dingtalk.com/sns/getuserinfo_bycode?accessKey=" . $cliend_id . "&timestamp=" . $time . "&signature=" . $urlencode_signature;
                $result = curlHelper($url, 'POST', json_encode([
                    "tmp_auth_code" => $code
                ]), [
                    'content-type: application/json'
                ], "");
                if ($result['detail']['http_code'] == 200) {
                    $user = json_decode($result['body'], true);
                    if ($user['errcode'] == 0) {
                        $user = $user['user_info'];
                        $openid = $user['openid'];
                        $nickname = $user['nick'];
                        $head = "new/images/nohead.jpg";
                        $extra = "";
                        $sex = 0;
                        $user = $this->model->where('user_openid', $openid)->where('user_app', $app_id)->find();
                        if (!$user) {
                            $this->model->regByOpen($openid, $nickname, $head, $sex,  $app_id, $extra);
                            $user = $this->model->where('user_openid', $openid)->where('user_app', $app_id)->find();
                        }
                        if ($user) {
                            //创建一个新的授权
                            $access = $this->accessModel->createAccess($user['user_id'], $app_id);
                            if ($access) {
                                $this->userModel->where('user_id', $user['user_id'])->update([
                                    'user_device' => getOs(),
                                ]);
                                setCookie('access_token', $access['access_token'], time() + 3600, '/');
                                return jok('登录成功', ['access_token' => $access['access_token']]);
                            } else {
                                return jerr('登录系统异常');
                            }
                        } else {
                            return jerr('帐号或密码错误');
                        }
                    }
                }
                return jerr('使用钉钉账号登录失败,请重试');
                break;
            default:
                return jerr("暂不支持的第三方平台登录");
        }
    }
    public function openTemp()
    {
        return jok('临时凭证获取成功', [
            'access_token' => getTempToken(),
        ]);
    }
}
