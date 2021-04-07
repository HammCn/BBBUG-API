<?php

declare (strict_types = 1);

namespace app\api;

use app\model\Access as AccessModel;
use app\model\App as AppModel;
use app\model\Auth as AuthModel;
use app\model\Conf as ConfModel;
use app\model\Group as GroupModel;
use app\model\Log as LogModel;
use app\model\Node as NodeModel;
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
    //筛选字段
    protected $searchFilter = [];
    //更新字段
    protected $updateFields = [];
    //更新时的必须字段
    protected $updateRequire = [];
    //添加字段
    protected $insertFields = [];
    //添加时的必须字段
    protected $insertRequire = [];
    //excel查询字段 用来查询
    protected $excelField = [
        "id" => "编号",
        "createtime" => "创建时间",
        "updatetime" => "修改时间",
    ];
    //excel 表头
    protected $excelTitle = "数据导出表";
    //EXCEL 单元格字母
    protected $excelCells = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD'];

    //主键key
    protected $pk = '';
    //表名称
    protected $table = '';
    //主键value
    protected $pk_value = 0;

    //模型
    protected $userModel;
    protected $accessModel;
    protected $authModel;
    protected $nodeModel;
    protected $groupModel;
    protected $confModel;
    protected $logModel;

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
        $this->authModel = new AuthModel();
        $this->nodeModel = new NodeModel();
        $this->groupModel = new GroupModel();
        $this->confModel = new ConfModel();
        $this->logModel = new LogModel();

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
        if(input('plat')=='weapp' && $this->controller!='Index'){
            $wechat_app_enabled = cache('wechat_app_enabled') ?? 'close';
            if ($wechat_app_enabled != 'open') {
                echo jerr('error',503);
                die;
            }
        }
    }
    /**
     * 检测授权
     *
     * @return void
     */
    protected function access()
    {
        //查询当前访问的节点
        $this->node = $this->nodeModel->where(['node_module' => $this->module, 'node_controller' => strtolower($this->controller), 'node_action' => $this->action])->find();
        if (!$this->node) {
            return jerr("请勿访问没有声明的API节点！", 503);
        }
        if ($this->node['node_status'] == 1) {
            return jerr("你访问的节点[" . $this->node['node_title'] . "]被禁用", 503);
        }
        if (!input("plat")) {
            return jerr("plat参数为必须", 400);
        }
        $this->plat = input('plat');
        if (!input("version")) {
            return jerr("version参数为必须", 400);
        }
        $this->version = input('version');
        if ($this->node['node_login']) {
            //节点需要强制登录
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
            if ($this->node['node_access']) {
                //节点是否需要授权
                if (!$this->user['user_group']) {
                    return jerr("用户没有所属的用户组", 403);
                }
                $where = [
                    "group_id" => $this->user['user_group'],
                ];
                $this->group = $this->groupModel->where($where)->find();
                if (!$this->group) {
                    return jerr("用户组信息查询失败", 403);
                }
                if ($this->group['group_status'] == 1) {
                    return jerr("你所在的用户组[" . $this->group['group_name'] . "]被禁用", 403);
                }
                $this->logModel->insert([
                    "log_user" => $this->user['user_id'],
                    "log_node" => $this->node['node_id'],
                    "log_createtime" => time(),
                    "log_ip" => getClientIp(),
                    "log_browser" => getBrowser(),
                    "log_os" => getOs(),
                    "log_updatetime" => time(),
                    "log_gets" => rawurlencode(json_encode(input("get."))),
                    "log_posts" => rawurlencode(json_encode(input("post."))),
                    "log_cookies" => rawurlencode(json_encode($_COOKIE)),
                ]);
                if ($this->group['group_id'] > 1) {
                    //其他用户
                    $where = [
                        "auth_group" => $this->user["user_group"],
                        "auth_node" => $this->node['node_id'],
                    ];
                    $auth = $this->authModel->auth($this->group['group_id'], $this->node['node_id']);
                    if (!$auth) {
                        return jerr("你没有权限访问[" . $this->node['node_title'] . "]这个接口", 403);
                    }
                }
            }
        } else {
            //节点不需要强制登录
            //但有可能也会传access过来，这里当有access_token时，查一下用户并设定全局用户信息
            if (input("?access_token")) {
                $access_token = input("access_token");
                $this->user = $this->userModel->getUserByAccessToken($access_token) ?? null;
            }
        }
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
     * 按主键集合批量禁用 1,2,3,4
     *
     * @return void
     */
    protected function disableByMultiple()
    {
        $list = explode(',', $this->pk_value);
        $this->model->where($this->pk, 'in', $list)->update([
            $this->table . "_status" => 1,
            $this->table . "_updatetime" => time(),
        ]);
    }
    /**
     * 单个禁用 可传入自定义$map
     * 默认按主键ID禁用
     *
     * @param  array $map
     * @return void
     */
    protected function disableBySingle($map = null)
    {
        if ($map == null) {
            $map = [$this->pk => $this->pk_value];
        }
        $this->model->where($map)->update([
            $this->table . "_status" => 1,
            $this->table . "_updatetime" => time(),
        ]);
    }
    /**
     * 按主键集合批量启用 1,2,3,4
     *
     * @return void
     */
    protected function enableByMultiple()
    {
        $list = explode(',', $this->pk_value);
        $this->model->where($this->pk, 'in', $list)->update([
            $this->table . "_status" => 0,
            $this->table . "_updatetime" => time(),
        ]);
    }
    /**
     * 单个启用 可传入自定义$map
     * 默认按主键ID启用
     *
     * @param  array $map
     * @return void
     */
    protected function enableBySingle($map = null)
    {
        if ($map == null) {
            $map = [$this->pk => $this->pk_value];
        }
        $this->model->where($map)->update([
            $this->table . "_status" => 0,
            $this->table . "_updatetime" => time(),
        ]);
    }
    /**
     * 按主键集合批量删除1,2,3,4
     *
     * @return void
     */
    protected function deleteByMultiple()
    {
        $list = explode(',', $this->pk_value);
        $this->model->where($this->pk, 'in', $list)->delete();
    }
    /**
     * 单个删除 默认主键ID
     *
     * @param  array $map
     * @return void
     */
    protected function deleteBySingle($map = null)
    {
        if ($map == null) {
            $map = [$this->pk => $this->pk_value];
        }
        $this->model->where($map)->delete();
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
    /**
     * 从请求中获取查询排序(默认主键DESC)
     *
     * @return void
     */
    protected function getOrderFromRequest()
    {
        if (input('order')) {
            $order = rawurldecode(input('order'));
        } else {
            $order = strtolower($this->controller) . "_id desc";
        }
        return $order;
    }
    /**
     * 设置分页查询每页数量
     *
     * @param  int|null 每页数量
     * @return void
     */
    protected function setGetListPerPage($per_page = null)
    {
        if ($per_page) {
            $this->model->per_page = intval($per_page);
        } else if (!intval(input('per_page'))) {
            $this->model->per_page = intval(input('per_page'));
        }
    }
    /**
     * 获取查询列表的Where参数
     *
     * @return array
     */
    protected function getDataFilterFromRequest()
    {
        $map = [];
        $filter = input('post.');
        foreach ($filter as $k => $v) {
            if ($k == 'filter') {
                $k = input('filter');
                $v = input('keyword');
            }
            if ($v === '' || $v === null) {
                continue;
            }
            if (array_key_exists($k, $this->searchFilter)) {
                switch ($this->searchFilter[$k]) {
                    case "like":
                        array_push($map, [$k, 'like', "%" . $v . "%"]);
                        break;
                    case "=":
                        array_push($map, [$k, '=', $v]);
                        break;
                    default:
                }
            }
        }
        return $map;
    }
    protected function getExcelFields()
    {
        $excelField = [];
        foreach ($this->excelField as $k => $v) {
            if ($k == "*") {
                continue;
            } else {
                array_push($excelField, [
                    $k, $v,
                ]);
            }
        }
        return $excelField;
    }
    /**
     * 导出Excel
     *
     * @param  array 筛选条件 默认 getDataFilterFromRequest()
     * @param  mixed 排序方式 默认 getOrderFromRequest()
     * @return string 下载文件名
     */
    protected function exportExcelData($map = null, $order = null)
    {
        $datalist = $this->model->getList($map, $order);
        $datalist = $datalist ? $datalist->toArray() : [];
        $excelField = $this->getExcelFields();
        $PHPExcel = new \PHPExcel(); //实例化
        $PHPExcel
            ->getProperties() //获得文件属性对象，给下文提供设置资源
            ->setCreator("StartAdmin") //设置文件的创建者
            ->setLastModifiedBy("StartAdmin") //设置最后修改者
            ->setDescription("Export by StartAdmin"); //设置备注

        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle($this->excelTitle); //给当前活动sheet设置名称

        $PHPSheet->mergeCells('A1:' . $this->excelCells[count($excelField) - 1] . "1");
        $PHPSheet->setCellValue('A1', $this->excelTitle);
        $PHPSheet->getRowDimension(1)->setRowHeight(40);
        $PHPSheet->getStyle('A1')->getFont()->setSize(18)->setBold(true); //字体大小

        $PHPSheet->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平方向上对齐
        $PHPSheet->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向上中间居中
        $PHPSheet->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向上中间居中

        if (count($excelField) > count($this->excelCells)) {
            echo 'Error and you need check Excel Cells Keys...';
            die;
        }
        $PHPSheet->getRowDimension(2)->setRowHeight(30);
        for ($column = 0; $column < count($excelField); $column++) {
            $PHPSheet->setCellValue($this->excelCells[$column] . "2", $excelField[$column][1]);
            $PHPSheet->getStyle($this->excelCells[$column])->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            for ($line = 0; $line < count($datalist); $line++) {
                $string = $datalist[$line][$this->table . "_" . $excelField[$column][0]];
                switch ($excelField[$column][0]) {
                    case 'createtime':
                    case 'updatetime':
                        $PHPSheet->getColumnDimension($this->excelCells[$column])->setWidth(25);
                        $PHPSheet->setCellValue($this->excelCells[$column] . ($line + 3), date('Y-m-d H:i:s', $string));
                        break;
                    default:
                        if ($column != 0) {
                            $PHPSheet->getColumnDimension($this->excelCells[$column])->setWidth(20);
                        }
                        $PHPSheet->setCellValueExplicit($this->excelCells[$column] . ($line + 3), $string, \PHPExcel_Cell_DataType::TYPE_STRING);
                }
            }
        }

        //***********************画出单元格边框*****************************
        $styleArray = array(
            'borders' => array(
                'inside' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN, //细边框
                    //'color' => array('argb' => 'FFFF0000'),
                ),
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK, //边框是粗的
                    //'color' => array('argb' => 'FFFF0000'),
                ),
            ),
        );
        $PHPSheet->getStyle('A2:' . $this->excelCells[count($excelField) - 1] . (count(
            $datalist
        ) + 2))->applyFromArray($styleArray);
        //***********************画出单元格边框结束*****************************

        //设置全部居中对齐
        $PHPSheet->getStyle('A1:' . $this->excelCells[count($excelField) - 1] . (count(
            $datalist
        ) + 2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //设置全部字体
        $PHPSheet->getStyle('A1:' . $this->excelCells[count($excelField) - 1] . (count(
            $datalist
        ) + 2))->getFont()->setName('微软雅黑');
        //设置格式为文本
        // $PHPSheet->getStyle('A1:' . $this->excelCells[count($excelField) - 1] . (count(
        //     $datalist
        // ) + 2))->getNumberFormat()
        //     ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header('Content-Disposition: attachment;filename="' . $this->excelTitle . "_" . date('Y-m-d_H:i:s') . '.xlsx"'); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
        return $this->excelTitle . "_" . date('Y-m-d_H:i:s') . '.xlsx"';
    }
    public function __call($method, $args)
    {
        return jerr("API接口方法不存在", 404);
    }
}
