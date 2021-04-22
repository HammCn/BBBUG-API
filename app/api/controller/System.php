<?php

namespace app\api\controller;

use app\api\BaseController;

class System extends BaseController
{
    public function time()
    {
        return jok("Hello World!", [
            'time' => intval(microtime(true) * 1000),
        ]);
    }
}
