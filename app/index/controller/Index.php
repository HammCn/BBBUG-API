<?php

namespace app\index\controller;

use app\index\BaseController;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        header("Location: https://bbbug.com");
    }
}
