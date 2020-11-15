<?php

namespace app\api\controller;

use app\api\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return jok("Hello World!",[
            'hide'=>1
        ]);
        return jok("Hello World!",[
            'hide'=>0,
            'data'=>json_decode(file_get_contents('https://h5.oschina.net/apiv3/projectRecommend?size=50&page=1'),true)['data']['items']
        ]);
    }
}
