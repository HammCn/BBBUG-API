<?php

declare(strict_types=1);

namespace app\index;

use think\App;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

	/**
	 * 构造方法
	 * @access public
	 * @param  App  $app  应用对象
	 */
	public function __construct(App $app)
	{
        $this->app     = $app;
        $this->request = $this->app->request;
    }
}
