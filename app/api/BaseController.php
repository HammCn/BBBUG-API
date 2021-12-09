<?php

declare (strict_types = 1);

namespace app\api;

use app\model\Access as AccessModel;
use app\model\App as AppModel;
use app\model\Conf as ConfModel;
use app\model\User as UserModel;
use think\App;
use think\facade\View;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    protected $model = null;
    //搜索字段
    protected $selectList = '*';
    protected $selectDetail = '*';
    //更新字段
    protected $updateFields = [];
    //更新时的必须字段
    protected $updateRequire = [];
    //添加字段
    protected $insertFields = [];
    //添加时的必须字段
    protected $insertRequire = [];
    //主键key
    protected $pk = '';
    //表名称
    protected $table = '';
    //主键value
    protected $pk_value = 0;

    //模型
    protected $userModel;
    protected $accessModel;
    protected $confModel;

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
    protected $plat = 'all';
    protected $version = 0;

    protected $module;
    protected $controller;
    protected $action;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        $this->module = "api";
        $this->controller = $this->request->controller() ? $this->request->controller() : "Index";
        $this->action = strtolower($this->request->action()) ? strtolower($this->request->action()) : "index";
        View::assign('controller', strtolower($this->controller));
        View::assign('action', strtolower($this->action));

        $this->table = strtolower($this->controller);
        $this->pk = $this->table . "_id";
        $this->pk_value = input($this->pk);

        $this->userModel = new UserModel();
        $this->accessModel = new AccessModel();
        $this->confModel = new ConfModel();

        $configs = $this->confModel->select()->toArray();
        $c = [];
        foreach ($configs as $config) {
            $c[$config['conf_key']] = $config['conf_value'];
        }
        config($c, 'startadmin');
        
        $apiUrl = getTopHost(config('startadmin.api_url'));
        $staticUrl = getTopHost(config('startadmin.static_url'));
        if(!$apiUrl){
            die('请先配置sa_conf表的api_url字段数据');
        }
        if(!$staticUrl){
            die('请先配置sa_conf表的static_url字段数据');
        }
    }
    /**
     * 检测授权
     *
     * @return void
     */
    protected function access()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        if (!input("plat")) {
            return jerr("plat参数为必须", 400);
        }
        $this->plat = input('plat');
        if (!input("version")) {
            return jerr("version参数为必须", 400);
        }
        $this->version = input('version');
        if (!input("?access_token")) {
            return jerr("AccessToken为必要参数", 400);
        }
        $access_token = input("access_token");
        $this->user = $this->userModel->getUserByAccessToken($access_token);
        if (!$this->user) {
            return jerr("登录过期，请重新登录", 401);
        }
        if ($this->user['user_status'] == 1) {
            return jerr("你的账户被禁用，登录失败", 401);
        }
        $user_device = getOs();
        if($user_device!='Other'){
            $this->userModel->where('user_id', $this->user['user_id'])->update([
                'user_device' => $user_device,
            ]);
        }else{
            switch(strtolower(input('plat'))){
                case 'vscode':
                    $this->userModel->where('user_id', $this->user['user_id'])->update([
                        'user_device' => 'VSCODE',
                    ]);
                    break;
            }
        }
        $this->user = $this->userModel->where('user_id', $this->user['user_id'])->find();

        $this->user = $this->user->toArray();
        $appModel = new AppModel();
        $app = $appModel->where('app_id', $this->user['user_app'])->find();
        if (!$app) {
            return jerr("用户没有所属应用");
        }
        $app = $app->toArray();
        if ($app['app_status'] == 1) {
            return jerr("所在应用被封禁");
        }
        $this->user = array_merge($this->user, $app);
    }
    /**
     * 从请求中获取Request数据
     *
     * @return void
     */
    protected function getInsertDataFromRequest()
    {
        $data = [];
        foreach (input('post.') as $k => $v) {
            if (in_array($k, $this->insertFields)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }
    /**
     * 校验Insert的字段
     *
     * @return void
     */
    protected function validateInsertFields()
    {
        foreach ($this->insertRequire as $k => $v) {
            if (!input($k)) {
                return jerr($v, 400);
            }
        }
        return null;
    }
    /**
     * 从请求中获取Update数据
     *
     * @return void
     */
    protected function getUpdateDataFromRequest()
    {
        $data = [];
        foreach (input('post.') as $k => $v) {
            if (in_array($k, $this->updateFields)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }
    /**
     * 校验Update的字段
     *
     * @return void
     */
    protected function validateUpdateFields()
    {
        foreach ($this->updateRequire as $k => $v) {
            if (!input($k)) {
                return jerr($v, 400);
            }
        }
        return null;
    }
    /**
     * 根据主键ID获取一行数据
     *
     * @param  int|null 主键ID
     * @return array|null
     */
    protected function getRowByPk($pk_value = null)
    {
        if (!$pk_value) {
            $pk_value = $this->pk_value;
        }
        $item = $this->model->where($this->pk, $pk_value)->find();
        return $item ? $item->toArray() : null;
    }
    /**
     * 根据主键ID更新数据
     *
     * @param  array 需要更新的KV数组
     * @param  int|null 主键ID 默认$this->pk_value
     * @param  bool 是否更新_updatetime字段 默认TRUE
     * @return void
     */
    protected function updateByPk($data, $pk_value = null, $auto_updatetime = true)
    {
        if (!$pk_value) {
            $pk_value = $this->pk_value;
        }
        if ($auto_updatetime) {
            $data[$this->table . "_updatetime"] = time();
        }
        $this->model->where($this->pk, $this->pk_value)->update($data);
    }
    /**
     * 添加一行数据
     *
     * @param  array 需要添加的KV数组
     * @param  bool 是否自动记录_createtime和_updatetime字段 默认true
     * @return int 添加返回的主键ID
     */
    protected function insertRow($data, $auto_inserttime = true)
    {
        if ($auto_inserttime) {
            $data[$this->table . "_updatetime"] = time();
            $data[$this->table . "_createtime"] = time();
        } else {
            $data[$this->table . "_updatetime"] = 0;
            $data[$this->table . "_createtime"] = 0;
        }
        $id = $this->model->insertGetId($data);
        return $id;
    }
    public function __call($method, $args)
    {
        return jerr("API接口方法不存在", 404);
    }
}
