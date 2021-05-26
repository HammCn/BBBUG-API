<?php

namespace app\model;

use think\Model;

/**
 * BaseModel 数据模型基类
 */
class BaseModel extends Model
{
    /**
     * 分页获取条数
     *
     * @var int 分页获取条数
     */
    public $per_page = 10;
    /**
     * 分页获取数据
     *
     * @param  array 筛选数组
     * @param  string 排序方式
     * @param  string 搜索字段
     * @return void
     */
}
