<?php

namespace app\index\controller;

use app\index\BaseController;
use think\facade\View;

class Error extends BaseController
{
    public function __call($method, $args)
    {
        return 404;
    }
}
