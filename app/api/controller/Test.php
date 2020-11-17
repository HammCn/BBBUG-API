<?php

namespace app\api\controller;

use app\api\BaseController;

class Test extends BaseController
{
    public function index()
    {
        print_r(cache("SongNow_10291"));
        // print_r(cache("SongNow_10291",false));
    }
}
