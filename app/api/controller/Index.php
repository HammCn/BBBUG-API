<?php

namespace app\api\controller;

use app\api\BaseController;

class Index extends BaseController
{
    public function index()
    {
        $wechat_app_enabled = cache('wechat_app_enabled') ?? 'close';
        if ($wechat_app_enabled == 'open') {
            return jok($wechat_app_enabled, [
                'success' => 1,
                'systemVersion' => time()
            ]);
        }
        return jok($wechat_app_enabled, [
            'success' => 0,
            'data' => json_decode(file_get_contents('https://h5.oschina.net/apiv3/projectRecommend?size=50&page=1'), true)['data']['items']
        ]);
    }
    public function detail()
    {
        if (!input('id')) {
            return jerr('参数错误');
        }
        $id = intval(input('id'));
        return jok("Hello World!", json_decode(file_get_contents('https://h5.oschina.net/apiv3/projectDetail?id=' . $id), true)['data']['project']);
    }
}
