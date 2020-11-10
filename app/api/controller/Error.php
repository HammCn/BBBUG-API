<?php

namespace app\api\controller;

use app\api\BaseController;

class Error extends BaseController
{
    public function index()
    {
        return jerr("Api not found", 404);
    }
}
