<?php

namespace app\api\controller;

use app\api\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return jok("Hello World!");
    }
}
