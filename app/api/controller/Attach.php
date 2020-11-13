<?php

namespace app\api\controller;

use app\api\BaseController;
use app\model\Attach as AttachModel;
use think\App;
use think\exception\ValidateException;
use think\facade\Filesystem;

class Attach extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        //筛选字段
        $this->searchFilter = [
            "attach_id" => "=", //相同筛选
            "attach_key" => "like", //相似筛选
            "attach_value" => "like", //相似筛选
            "attach_desc" => "like", //相似筛选
            "attach_readonly" => "=", //相似筛选
        ];
        $this->model = new AttachModel();
    }
    public function search()
    {
        $arr = [];
        if (input("keyword")) {
            $keyword = input('keyword');
            $cache = cache("face_image_list_keyword_" . sha1($keyword)) ?? false;
            if ($cache) {
                return jok('', $cache);
            }

            $html = curlHelper("https://www.doutula.com/tag/" . rawurlencode($keyword))['body'];
            $html = str_replace(PHP_EOL, '', $html);
            $html = str_replace(' ', '', $html);
            $html = str_replace('　', '', $html);
            $arr = [];
            if (preg_match_all('/<imgreferrerpolicy="no-referrer"src=".*?"data-backup="(.*?)"/', $html, $match)) {
                // print_r($match[0]);
                foreach ($match[1] as $item) {
                    if (count($arr) >= 20) {
                        break;
                    }
                    array_push($arr, str_replace('http://img.doutula.com/', 'https://img_proxy.bbbug.com/', $item));
                }
            }
            cache("face_image_list_keyword_" . sha1($keyword), $arr, 86400);
            return jok('', $arr);
        }
        return jerr('我认为你有必要输入个关键词');
    }
    /**
     * 上传图片
     *
     * @return void
     */
    public function uploadImage()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }
        try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:' . config("startadmin.upload_max_image") . '|fileExt:php,' . config("startadmin.upload_image_type")])
                    ->check(['file' => $file]);

                $sha = $file->sha1();
                $this->model->where('attach_sha', $sha)->where('attach_user', $this->user['user_id'])->inc('attach_used')->update();
                $attach_data = $this->model->where('attach_sha', $sha)->where('attach_user', $this->user['user_id'])->find();
                $image = \think\Image::open($file);
                if (!$attach_data) {
                    $saveName = Filesystem::putFile('image', $file);
                    
                    $path = "thumb/" . $saveName;
                    if(strtolower($file->extension()) == 'gif'){
                        $path = $saveName;
                    }else{
                         $dir = "./uploads/thumb/image/" . date('Ymd');
                        if (!is_dir($dir)) {
                            mkdir(iconv("UTF-8", "GBK", $dir), 0777, true);
                        }
                        if (input('isHead')) {
                            $image->thumb(400, 400, \think\Image::THUMB_CENTER)->save('./uploads/' . $path);
                        } else {
                            $image->thumb(400, 400, \think\Image::THUMB_SCALING)->save('./uploads/' . $path);
                        }
                    }
                    $attach_data = array(
                        'attach_path' => $saveName,
                        'attach_thumb' => $path,
                        'attach_sha' => $sha,
                        'attach_type' => $file->extension(),
                        'attach_size' => $file->getSize(),
                        'attach_createtime' => time(),
                        'attach_updatetime' => time(),
                        'attach_user' => $this->user['user_id'],
                    );
                    $attach_id = $this->model->insertGetId($attach_data);
                    $attach_data = $this->model->where(["attach_id" => $attach_id])->find();
                }
                if (input("?extend")) {
                    $attach_data['extend'] = input("extend");
                }
                return jok('上传成功！', $attach_data);
            } catch (ValidateException $e) {
                return jerr($e->getMessage());
            }
        } catch (\Exception $error) {
            return jerr('上传文件失败，请检查你的文件！' . $error->getMessage());
        }
    }
    /**
     * 上传图片
     *
     * @return void
     */
    public function uploadHead()
    {
        if (input('access_token') == getTempToken()) {
            return jerr('请登录后体验完整功能!', 401);
        }
        $error = $this->access();
        if ($error) {
            return $error;
        }
        try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:' . config("startadmin.upload_max_image") . '|fileExt:php,' . config("startadmin.upload_image_type")])
                    ->check(['file' => $file]);

                $sha = $file->sha1();
                $this->model->where('attach_sha', $sha)->where('attach_user', $this->user['user_id'])->inc('attach_used')->update();
                $attach_data = $this->model->where('attach_sha', $sha)->where('attach_user', $this->user['user_id'])->find();
                $image = \think\Image::open($file);
                // if (!$attach_data) {
                $saveName = Filesystem::putFile('image', $file);
                // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
                $path = "thumb/" . $saveName;
                $dir = "./uploads/thumb/image/" . date('Ymd');
                if (!is_dir($dir)) {
                    mkdir(iconv("UTF-8", "GBK", $dir), 0777, true);
                }
                $image->thumb(400, 400, \think\Image::THUMB_CENTER)->save('./uploads/' . $path);
                $attach_data = array(
                    'attach_path' => $saveName,
                    'attach_thumb' => $path,
                    'attach_sha' => $sha,
                    'attach_type' => $file->extension(),
                    'attach_size' => $file->getSize(),
                    'attach_createtime' => time(),
                    'attach_updatetime' => time(),
                    'attach_user' => $this->user['user_id'],
                );
                $attach_id = $this->model->insertGetId($attach_data);
                $attach_data = $this->model->where(["attach_id" => $attach_id])->find();
                // }
                if (input("?extend")) {
                    $attach_data['extend'] = input("extend");
                }
                return jok('上传成功！', $attach_data);
            } catch (ValidateException $e) {
                return jerr($e->getMessage());
            }
        } catch (\Exception $error) {
            return jerr('上传文件失败，请检查你的文件！' . $error->getMessage());
        }
    }
    /**
     * 上传文件
     *
     * @return void
     */
    public function uploadFile()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:' . config("startadmin.upload_max_file") . '|fileExt:php,' . config("startadmin.upload_file_type")])
                    ->check(['file' => $file]);
                $saveName = Filesystem::putFile('normal', $file);
                $attach_data = array(
                    'attach_path' => $saveName,
                    'attach_type' => $file->extension(),
                    'attach_size' => $file->getSize(),
                    'attach_user' => $this->user['user_id'],
                );
                $attach_id = $this->insertRow($attach_data);
                $attach_data = $this->getRowByPk($attach_id);
                if (input("?extend")) {
                    $attach_data['extend'] = input("extend");
                }
                return jok('上传成功！', $attach_data);
            } catch (ValidateException $e) {
                return jerr($e);
            }
        } catch (\Exception $error) {
            return jerr('上传文件失败，请检查你的文件！');
        }
    }
}
