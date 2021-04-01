<?php

namespace app\api\controller;

use app\api\BaseController;
use app\model\Keywords as KeywordsModel;
use app\model\Message as MessageModel;
use app\model\Song as SongModel;
use app\model\Room as RoomModel;
use app\model\User as UserModel;
use think\App;

class Message extends BaseController
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
            "message_id" => "=",
            "message_user" => "like", "message_type" => "like", "message_content" => "like", "message_to" => "like", "message_where" => "like",
        ];
        $this->insertFields = [
            //允许添加的字段列表
            "message_user", "message_type", "message_content", "message_to", "message_where",
        ];
        $this->updateFields = [
            //允许更新的字段列表
            "message_user", "message_type", "message_content", "message_to", "message_where",
        ];
        $this->insertRequire = [
            //添加时必须填写的字段
            // "字段名称"=>"该字段不能为空"

        ];
        $this->updateRequire = [
            //修改时必须填写的字段
            // "字段名称"=>"该字段不能为空"

        ];
        $this->model = new MessageModel();
    }
    public function translator()
    {
        if (!input('content')) {
            return jerr('Missing param {content}');
        }
        $content = input('content');
        return jok('translate success!', [
            'source' => $content,
            'target' => "Fuck you!"
        ]);
    }
    public function back()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!input('room_id')) {
            return jerr('缺少room_id');
        }
        if (!input('message_id')) {
            return jerr('你无权撤回这条消息');
        }
        $message_id = input('message_id');
        $room_id = input('room_id');

        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            return jerr("房间信息查询失败");
        }
        $message = $this->model->where('message_id', $message_id)->find();
        if (!$message) {
            return jerr('消息查询失败,无法撤回');
        }
        if ($message['message_to'] != $room_id && $message['message_where'] == 'channel') {
            return jerr('消息与房间信息不匹配');
        }
        if (!getIsAdmin($this->user)) {
            if ($message['message_user'] != $this->user['user_id'] && $room['room_user'] != $this->user['user_id']) {
                return jerr('你没有权限撤回该消息');
            }
        }
        if (time() > $message['message_createtime'] + 300 && !getIsAdmin($this->user)) {
            return jerr('你只能撤回5分钟内的消息');
        }
        $msg = [
            'user' => getUserData($this->user),
            "message_id" => $message_id,
            "type" => "back",
            "time" => date('H:i:s'),
        ];
        sendWebsocketMessage('channel', $room_id, $msg);
        $this->model->where('message_id', $message_id)->delete();
        return jok('撤回消息成功!');
    }
    public function clear()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!input('room_id')) {
            return jerr('缺少room_id', 400);
        }
        $room_id = input('room_id');

        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            return jerr("房间信息查询失败");
        }
        if (!getIsAdmin($this->user)) {
            if ($room['room_user'] != $this->user['user_id']) {
                return jerr('你没有权限删除房间聊天记录');
            }
        }
        $msg = [
            'user' => getUserData($this->user),
            "type" => "clear",
            "time" => date('H:i:s'),
        ];

        sendWebsocketMessage('channel', $room_id, $msg);
        $this->model->where('message_to', $room_id)->where("message_where", "channel")->delete();
        return jok('删除成功!');
    }
    public function getMessageList()
    {
        $roomModel = new RoomModel();
        $room_id = intval(input('room_id'));
        if (!$room_id) {
            return jerr("请传入room_id", 400);
        }

        $room = $roomModel->getRoomById($room_id);

        if (!$room) {
            return jerr("房间信息查询失败");
        }

        $message_where = input('message_where');
        $page = 1;
        if (input('page')) {
            $page = intval(input('page'));
        }
        if (input('access_token') == getTempToken()) {

            if ($room['room_public'] == 1) {
                return jok("加密房间", []);
            }

            $cache = cache("room_message_list_" . $room_id) ?? false;
            if ($cache) {
                return jok('from cache', $cache);
            }
            $per_page = 100;
            if (input('per_page')) {
                $per_page = intval(input('per_page'));
            }
            if ($per_page > 100) {
                $per_page = 100;
            }
            if ($per_page < 1) {
                $per_page = 1;
            }
            $map = [
                'message_status' => 0,
            ];
            if ($room_id) {
                $map['message_to'] = $room_id;
            }
            if ($message_where) {
                $map['message_where'] = $message_where;
            }
            $list = $this->model->where($map)->order('message_id desc')->limit($per_page)->page($page)->select();
            cache("room_message_list_" . $room_id, $list, 10);
            return jok('success', $list);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }


        $savedPassword = cache('password_room_' . $room['room_id'] . "_password_" . $this->user['user_id']) ?? '';

        if ($room['room_public'] == 1 && $this->user['user_id'] != $room['room_user'] && !getIsAdmin($this->user) && $savedPassword != $room['room_password']) {
            return jok("加密房间", []);
        }

        $cache = cache("room_message_user_" . $this->user['user_id'] . "_list_" . $room_id . "_page" . $page) ?? false;
        if ($cache) {
            return jok("from cache", $cache);
        }
        $per_page = 100;
        if (input('per_page')) {
            $per_page = intval(input('per_page'));
        }
        if ($per_page > 100) {
            $per_page = 100;
        }
        if ($per_page < 1) {
            $per_page = 1;
        }
        $map = [
            'message_status' => 0,
        ];
        if ($room_id) {
            $map['message_to'] = $room_id;
        }
        if ($message_where) {
            $map['message_where'] = $message_where;
        }
        $list = $this->model->where($map)->order('message_id desc')->limit($per_page)->page($page)->select();
        cache("room_message_user_" . $this->user['user_id'] . "_list_" . $room_id . "_page" . $page, $list, 10);
        return jok('success', $list);
    }
    public function send()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }
        if (!input("type")) {
            return jerr("消息类型参数缺失");
        }
        $at = input('at');
        if (!input("to")) {
            return jerr("发送对象参数缺失");
        }
        $room_id = input('to');
        $roomModel = new RoomModel();

        if (!input("where")) {
            return jerr("消息范围参数缺失");
        }
        $where = '';
        $msg_resource = (input('msg'));
        $msg_decode = rawurldecode($msg_resource);
        $room = $roomModel->getRoomById($room_id);

        if (!$room) {
            return jerr("房间信息查询失败");
        }

        $savedPassword = cache('password_room_' . $room['room_id'] . "_password_" . $this->user['user_id']) ?? '';

        if ($room['room_public'] == 1 && $this->user['user_id'] != $room['room_user'] && !getIsAdmin($this->user) && $savedPassword != $room['room_password']) {
            return jerr("密码错误，发送失败 ");
        }


        if (!getIsAdmin($this->user) && $this->user['user_id'] != $room['room_user'] && $room['room_sendmsg'] == 1) {
            return jerr('全员禁言中,你暂时无法发言');
        }

        if (!getIsAdmin($this->user) && $this->user['user_id'] != $room['room_user'] && $room['room_sendmsg'] == 2) {
            $isGuest = cache('guest_room_' . $room['room_id'] . '_user_' . $this->user['user_id']) ?? false;
            if (!$isGuest) {
                return jerr('抱歉,当前房间仅允许嘉宾发言!');
            }
        }

        $type = input('type');
        if ($type == 'text') {
            if (!str_replace(' ', '', $msg_decode)) {
                return jerr("咱好歹说点啥吧？");
            }
        }
        switch (input('where')) {
            case 'channel':
                $where = 'channel';
                if (!$room) {
                    return jerr("房间信息查询失败");
                }

                $isBan = cache('shutdown_room_' . $room_id . '_user_' . $this->user['user_id']);
                if ($isBan && $this->user['user_id'] > 1) {
                    return jerr("你被房主禁止了发言权限!");
                }
                break;
            default:
                return jerr("未知的消息范围");
        }

        if (!input('at') && !input("?msg")) {
            return jerr("消息内容参数缺失");
        }


        if (getIsAdmin($this->user)) {
            //管理员
            if (strpos($msg_decode, '@all') !== false) {
                $type = 'all';
                $content = str_replace('@all', '', $msg_decode);
                $msg = [
                    'type' => $type,
                    'content' => rawurldecode($content),
                ];
                sendWebsocketMessage('system', 'all', $msg);
                return jok('');
            }
        } else {
            $blackIpList = ['112.64.12.121', '223.104.212.2', '223.104.213.93'];
            $ip = getClientIp();
            if (in_array($ip, $blackIpList)) {
                return jerr("你所在的IP地址" . $ip . "被Ban,你无法发送消息。");
            }
            if ($type == 'text') {
                if ($this->user['user_id'] != $room['room_user']) {
                    //非房主
                    if ($type == 'text' && mb_strlen($msg_decode) > 200) {
                        return jerr('发送文字超过最大限制！');
                    }
                    if (cache('last_' . $this->user['user_id'])) {
                        return jerr('发送消息太频繁啦~');
                    }
                    if (cache('message_' . $this->user['user_id']) == $msg_decode) {
                        return jerr('灌水可耻,请不要重复发送相同信息');
                    }
                }
            }
            if ($type == 'img') {
                if ((time() > strtotime(date('Y-m-d 18:00:00')) || time() < strtotime(date('Y-m-d 09:00:00'))) && strpos(rawurldecode(input('msg')), 'images/emoji') === false && strpos(rawurldecode(input('msg')), 'img.doutula.com') === false) {
                    return jerr("18:00-09:00禁止发送自定义上传图片");
                }
                if (strpos(rawurldecode(input('msg')), rawurldecode(input('resource'))) !== false) {
                } else {
                    return jerr('图片发送失败,我怀疑你在搞事情');
                }
                if (strpos(rawurldecode(input('msg')), 'https://') !== false || strpos(rawurldecode(input('msg')), 'http://') !== false) {
                    //绝对路径
                    if (
                        strpos(rawurldecode(input('msg')), config('startadmin.api_url')) === false && strpos(rawurldecode(input('msg')), config('startadmin.static_url')) === false &&
                        strpos(rawurldecode(input('msg')), 'img.doutula.com') === false
                    ) {
                        return jerr('暂不支持站外图');
                    }
                }
            }
        }
        //全局预处理消息
        $jump_room = false;
        switch ($type) {
            case 'text':
                if (strpos(rawurldecode(input('msg')), config('startadmin.frontend_url')) !== false) {
                    if (preg_match('/com\/(\d+)/', rawurldecode(input('msg')), $match)) {
                        $jump_id = $match[1];
                        $jump_room = $roomModel->getRoomById($jump_id);
                        if ($jump_room) {
                            $lastJump = cache('chat_message_jump_' . $this->user['user_id']) ?? false;
                            if ($lastJump && !getIsAdmin($this->user) && false) {
                                return jerr('发送机票过于频繁');
                            } else {
                                cache('chat_message_jump_' . $this->user['user_id'], 1, 60);
                                //jump消息
                                if ($jump_room['room_password']) {
                                    $jump_room['room_password'] = true;
                                } else {
                                    $jump_room['room_password'] = false;
                                }
                                $type = 'jump';
                                $message_id = $this->model->insertGetId([
                                    'message_user' => $this->user['user_id'],
                                    'message_type' => 'text',
                                    'message_content' => '',
                                    'message_to' => $room_id,
                                    'message_where' => $where,
                                    'message_status' => 1,
                                    'message_createtime' => time(),
                                    'message_updatetime' => time(),
                                ]);
                                $msg = [
                                    'type' => $type,
                                    'jump' => $jump_room,
                                    'message_id' => $message_id,
                                    'message_time' => time(),
                                    'user' => getUserData($this->user),
                                ];
                                sendWebsocketMessage($where, $room_id, $msg);
                                $this->model->where('message_id', $message_id)->update([
                                    'message_type' => 'text',
                                    'message_content' => json_encode($msg),
                                    'message_status' => 0,
                                ]);
                                return jok('');
                            }
                        }
                    }
                }
                try {
                    $filterUrl = filter_var(str_replace(' ', '', $msg_decode), FILTER_VALIDATE_URL);
                    if ($filterUrl) {
                        $title = "分享一个链接给你呀";
                        $desc = '没有读取到网页信息,你就将就着自己点进去再慢慢看吧~';
                        $img = '';
                        $result = file_get_contents($filterUrl);
                        if (preg_match('/<title>(.*?)<\/title>/', $result, $match)) {
                            $title = $match[1];
                            $metas = get_meta_tags($filterUrl);
                            $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
                            if (preg_match($pattern, $result, $matchContent)) {
                                $img = $matchContent[1];
                            }
                            if (array_key_exists('description', $metas)) {
                                $desc = $metas['description'];
                            }
                        }
                        $message_id = $this->model->insertGetId([
                            'message_user' => $this->user['user_id'],
                            'message_type' => 'text',
                            'message_content' => '',
                            'message_to' => $room_id,
                            'message_where' => $where,
                            'message_status' => 1,
                            'message_createtime' => time(),
                            'message_updatetime' => time(),
                        ]);
                        $msg = [
                            'type' => 'link',
                            'desc' => rawurlencode($desc),
                            'title' => rawurlencode($title),
                            'img' => rawurlencode($img),
                            'link' => rawurlencode($filterUrl),
                            'message_id' => $message_id,
                            'message_time' => time(),
                            'user' => getUserData($this->user),
                        ];
                        sendWebsocketMessage($where, $room_id, $msg);
                        $this->model->where('message_id', $message_id)->update([
                            'message_type' => 'link',
                            'message_content' => json_encode($msg),
                            'message_status' => 0,
                        ]);
                        return jok('');
                    }
                } catch (\Exception $e) {
                }

                $keywordModel = new KeywordsModel();
                $keywordList = $keywordModel->where('keywords_status', 0)->order('keywords_all desc')->select();

                foreach ($keywordList as $keywords) {
                    if ($keywords['keywords_all'] == 1) {
                        //全局替换
                        if (strpos($msg_decode, $keywords['keywords_source']) !== false) {
                            $msg_decode = $keywords['keywords_target'];
                            break;
                        } else {
                            //取出中文再试试
                            $temp = preg_match_all('/[\x{4e00}-\x{9fa5}]+/u', $msg_decode, $matches);
                            if (count($matches[0]) > 0) {
                                $temp = join('', $matches[0]);
                                if (strpos($temp, $keywords['keywords_source']) !== false) {
                                    $msg_decode = $keywords['keywords_target'];
                                    break;
                                }
                            }
                        }
                    } else {
                        //局部替换
                        if (strpos(rawurldecode($msg_decode), $keywords['keywords_source']) !== false) {
                            $msg_decode = rawurldecode(str_replace(rawurlencode($keywords['keywords_source']), rawurlencode($keywords['keywords_target']), $msg_decode));
                            break;
                        } else {
                            $temp = preg_match_all('/[\x{4e00}-\x{9fa5}]+/u', rawurldecode($msg_decode), $matches);
                            if (count($matches[0]) > 0) {
                                $temp = join('', $matches[0]);
                                if (strpos($temp, $keywords['keywords_source']) !== false) {
                                    $msg_decode = rawurldecode(str_replace(rawurlencode($keywords['keywords_source']), rawurlencode($keywords['keywords_target']), $msg_decode));
                                    break;
                                }
                            }
                        }
                    }
                }
                $message_id = $this->model->insertGetId([
                    'message_user' => $this->user['user_id'],
                    'message_type' => 'text',
                    'message_content' => '',
                    'message_to' => $room_id,
                    'message_where' => $where,
                    'message_status' => 1,
                    'message_createtime' => time(),
                    'message_updatetime' => time(),
                ]);

                $msg = [
                    'type' => $type,
                    'content' => rawurlencode(rawurlencode($msg_decode)),
                    'where' => $where,
                    'at' => $at,
                    'message_id' => $message_id,
                    'message_time' => time(),
                    'resource' => rawurlencode(rawurlencode($msg_decode)) ?? '',
                    'user' => getUserData($this->user),
                ];
                sendWebsocketMessage($where, $room_id, $msg);
                $this->model->where('message_id', $message_id)->update([
                    'message_type' => 'text',
                    'message_content' => json_encode($msg),
                    'message_status' => 0,
                ]);
                cache('last_' . $this->user['user_id'], 1, 1);
                cache('message_' . $this->user['user_id'], $msg_decode, 10);

                //彩蛋区域
                $ifRobotEnable = false;
                if ($at && $at['user_id'] == 1) {
                    //机器人被@
                    $robotShutdown = cache('shutdown_room_' . $room_id . '_user_1') ?? false;
                    $rand = rand(100000, 999999);
                    if (!$robotShutdown && $rand < 800000) {
                        $ifRobotEnable = true;
                    }
                } else {
                    $robotShutdown = cache('shutdown_room_' . $room_id . '_user_1') ?? false;
                    $rand = rand(100000, 999999);
                    if (!$robotShutdown && $rand % 10 == 0) {
                        $ifRobotEnable = true;
                    }
                }
                if ($ifRobotEnable) {
                    $url = "https://api.ai.qq.com/fcgi-bin/nlp/nlp_textchat";
                    $tencentAiArray = [
                        "app_id" => config('startadmin.tencent_ai_appid'),
                        "time_stamp" => time(),
                        "nonce_str" => md5(time() . rand(100000, 999999)),
                        "session" => $this->user['user_id'],
                        "question" => $msg_decode
                    ];
                    $postData = http_build_query([
                        "app_id" => $tencentAiArray['app_id'],
                        "time_stamp" => $tencentAiArray['time_stamp'],
                        "nonce_str" => $tencentAiArray['nonce_str'],
                        "sign" => getTencentAiSign($tencentAiArray, config('startadmin.tencent_ai_appkey')),
                        "session" => $tencentAiArray['session'],
                        "question" => $tencentAiArray['question']
                    ]);
                    $ret = curlHelper($url, 'POST', $postData);
                    $json = json_decode($ret['body'], true);
                    if ($json['ret'] == 0) {
                        $robotInfo = $this->userModel->where("user_id", 1)->find();
                        $msg = [
                            'type' => 'text',
                            'content' => rawurlencode(rawurlencode($json['data']['answer'])),
                            'where' => $where,
                            'at' => [
                                'user_id' => $this->user['user_id'],
                                'user_name' => $this->user['user_name']
                            ],
                            'message_id' => 0,
                            'message_time' => time(),
                            'resource' => rawurlencode(rawurlencode($json['data']['answer'])),
                            'user' => getUserData($robotInfo),
                        ];
                        sendWebsocketMessage('channel', $room_id, $msg);
                        return jok('');
                    }
                }
                return jok('');
                break;
            case 'img':
                if (cache('last_' . $this->user['user_id']) && !getIsAdmin($this->user) && $this->user['user_id'] != $room['room_user']) {
                    return jerr('发送图片太频繁啦~');
                }
                if (cache('message_' . $this->user['user_id']) == $msg_decode && !getIsAdmin($this->user) && $this->user['user_id'] != $room['room_user']) {
                    return jerr('请不要连续发送相同的图片');
                }
                $message_id = $this->model->insertGetId([
                    'message_user' => $this->user['user_id'],
                    'message_type' => 'text',
                    'message_content' => '',
                    'message_to' => $room_id,
                    'message_where' => $where,
                    'message_status' => 1,
                    'message_createtime' => time(),
                    'message_updatetime' => time(),
                ]);
                $msg = [
                    'type' => $type,
                    'content' => $msg_decode,
                    'where' => $where,
                    'at' => $at,
                    'message_id' => $message_id,
                    'message_time' => time(),
                    'resource' => rawurlencode(input('resource')) ?? '',
                    'user' => getUserData($this->user),
                ];

                sendWebsocketMessage($where, $room_id, $msg);
                $this->model->where('message_id', $message_id)->update([
                    'message_type' => 'img',
                    'message_content' => json_encode($msg),
                    'message_status' => 0,
                ]);
                cache('last_' . $this->user['user_id'], 1, 1);
                cache('message_' . $this->user['user_id'], $msg_decode, 10);
                return jok('');
                break;
            default:
                return jerr('未知的消息类型');
        }
    }
    public function touch()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }

        if (!input('at') || !input('room_id')) {
            return jerr("参数错误,缺少at/room_id");
        }
        $room_id = input('room_id');

        $roomModel = new RoomModel();
        $room = $roomModel->getRoomById($room_id);
        if (!$room) {
            return jerr("房间信息查询失败");
        }

        $at = input('at');
        if ($at) {
            $user = $this->userModel->where('user_id', $at)->find();
            if ($user) {
                $at = getUserData($user);
                if ($at['user_id'] == $this->user['user_id']) {
                    return jerr("“自己摸自己是一种什么样的感觉？”——佚名");
                }
            } else {
                return jerr("被摸的人信息查询失败");
            }
        } else {
            return jerr("你这是想摸谁？");
        }
        $touchCDTime = 60;
        if (!getIsAdmin($this->user)) {
            //不是管理员 判断是否是房主
            if ($room['room_user'] != $this->user['user_id']) {
                $touchLastTime = cache('touch_' . $this->user['user_id']) ?? 0;
                $touchNeedTime = $touchCDTime - (time() - $touchLastTime);
                if ($touchNeedTime > 0) {
                    return jerr('你摸得太频繁啦，请' . $touchNeedTime . 's后再试');
                }
            }
        }
        cache('touch_' . $this->user['user_id'], time(), $touchCDTime);

        $msg = [
            'user' => getUserData($this->user),
            "type" => "touch",
            'at' => $at,
            "time" => date('H:i:s'),
        ];
        sendWebsocketMessage('channel', $room_id, $msg);

        //彩蛋区域
        $robotInfo = $this->userModel->where("user_id", 1)->find();
        $rand = rand(100000, 999999);
        $robotShutdown = cache('shutdown_room_' . $room_id . '_user_1') ?? false;
        if ($rand < 800000 && intval($at['user_id']) == 1 && !$robotShutdown) {
            $randStr = [
                '再摸小心我给你多戴几顶绿帽子！',
                '摸摸摸整天就知道摸摸摸，再摸我要生气了',
                '摸锤子摸，没见过机器人啊？',
                '别摸了~啊~受不了了~~~',
                '再摸试试，给你两哈',
                '别摸了，赶紧点歌去吧。。。',
                '莫挨劳资',
            ];
            $msg = [
                'type' => 'text',
                'content' => rawurlencode(rawurlencode($randStr[rand(0, count($randStr) - 1)])),
                'where' => $room_id,
                'at' => [
                    'user_id' => $this->user['user_id'],
                    'user_name' => $this->user['user_name']
                ],
                'message_id' => 0,
                'message_time' => time(),
                'resource' => rawurlencode(rawurlencode($randStr[rand(0, count($randStr) - 1)])),
                'user' => getUserData($robotInfo),
            ];
            sendWebsocketMessage('channel', $room_id, $msg);
        }
        return jok('操作成功');
    }
}
