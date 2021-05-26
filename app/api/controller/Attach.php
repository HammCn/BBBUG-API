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
                    array_push($arr, $item);
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
        $error = $this->access();
        if ($error) {
            return $error;
        }
        try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:' . config("startadmin.upload_max_image") . '|fileExt:' . config("startadmin.upload_image_type")])
                    ->check(['file' => $file]);

                $sha = $file->sha1();
                $this->model->where('attach_sha', $sha)->where('attach_user', $this->user['user_id'])->inc('attach_used')->update();
                $attach_data = $this->model->where('attach_sha', $sha)->where('attach_user', $this->user['user_id'])->find();
                $image = \think\Image::open($file);
                if (!$attach_data) {
                    $saveName = Filesystem::putFile('image', $file);

                    $path = "thumb/" . $saveName;
                    if (strtolower($file->extension()) == 'gif') {
                        $path = $saveName;
                    } else {
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
                    $weapp_appid = config('startadmin.weapp_appid'); //小程序APPID
                    $weapp_appkey = config("startadmin.weapp_appkey"); //小程序的APPKEY
                    if ($weapp_appid && $weapp_appkey) {
                        $weapp = new Weapp($this->app);
                        $error = $weapp->checkImg("./uploads/".$saveName);
                        if($error){
                            return $error;
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
        $error = $this->access();
        if ($error) {
            return $error;
        }
        try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:' . config("startadmin.upload_max_image") . '|fileExt:' . config("startadmin.upload_image_type")])
                    ->check(['file' => $file]);

                $sha = $file->sha1();
                $this->model->where('attach_sha', $sha)->where('attach_user', $this->user['user_id'])->inc('attach_used')->update();
                $attach_data = $this->model->where('attach_sha', $sha)->where('attach_user', $this->user['user_id'])->find();
                $image = \think\Image::open($file);

                if (strtolower($file->extension()) == 'gif') {
                    return jerr("头像不支持Gif头像,请换其他的格式");
                }

                if (!$attach_data) {
                    $saveName = Filesystem::putFile('image', $file);

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
                }
                if (input("?extend")) {
                    $attach_data['extend'] = input("extend");
                }


                $obj = getimagesize('./uploads/' . $attach_data['attach_path']);

                if (end($obj) == "image/gif") {
                    return jerr("不要尝试钻空子上传Gif图片当头像,那真的不高端 - Hamm");
                }
                
                $weapp_appid = config('startadmin.weapp_appid'); //小程序APPID
                $weapp_appkey = config("startadmin.weapp_appkey"); //小程序的APPKEY
                if ($weapp_appid && $weapp_appkey) {
                    $weapp = new Weapp($this->app);
                    $error = $weapp->checkImg("./uploads/".$saveName);
                    if($error){
                        return $error;
                    }
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
    public function uploadMusic()
    {
        $error = $this->access();
        if ($error) {
            return $error;
        }
        try {
            $file = request()->file('file');
            try {
                validate(['file' => 'filesize:8388608' . '|fileExt:mp3'])
                    ->check(['file' => $file]);
                $saveName = strtolower(Filesystem::putFile('normal', $file));
                copy('./uploads/' . $saveName, str_replace('.mp3', '.jpg', './uploads/' . $saveName)); //拷贝到新目录
                unlink('./uploads/' . $saveName); //删除旧目录下的文件
                $saveName = str_replace('.mp3', '.jpg', $saveName);
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
                validate(['file' => 'filesize:' . config("startadmin.upload_max_file") . '|fileExt:' . config("startadmin.upload_file_type")])
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
