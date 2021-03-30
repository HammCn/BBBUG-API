<?php
function getTencentAiSign($params, $appkey)
{
    ksort($params);
    $str = '';
    foreach ($params as $key => $value)
    {
        if ($value !== '')
        {
            $str .= $key . '=' . urlencode($value) . '&';
        }
    }
    $str .= 'app_key=' . $appkey;
    $sign = strtoupper(md5($str));
    return $sign;
}
function getTempToken()
{
    return config('startadmin.api_guest_token');
}
function getUserData($user)
{
    return [
        "user_id" => $user['user_id'],
        "user_icon" => $user['user_icon'],
        "user_touchtip" => rawurldecode($user['user_touchtip']),
        "user_sex" => $user['user_sex'],
        "user_vip" => $user['user_vip'],
        "user_extra" => $user['user_extra'],
        "user_device" => $user['user_device'],
        "user_name" => ($user['user_name']),
        "user_head" => ($user['user_head']),
        "user_remark" => ($user['user_remark']),
        "app_id" => $user['app_id'],
        "app_name" => $user['app_name'],
        "app_url" => $user['app_url'],
        "user_admin" => getIsAdmin($user),
    ];
}
function getTopHost($url){
    $url = strtolower($url);  //首先转成小写
    $hosts = parse_url($url);
    return $hosts['host'] ?? '';
}
function getIsAdmin($user)
{
    return in_array($user['user_group'], [1]);
}
function getWebsocketApiUrl()
{
    return config('startadmin.websocket_http');
}
function sendWebsocketMessage($type,$to,$msg){
    $ret = curlHelper(getWebsocketApiUrl(), "POST", http_build_query([
        'type' => $type,
        'to' => $to,
        'token' => sha1(config('startadmin.websocket_token')),
        'msg' => json_encode($msg),
    ]), [
        'content-type:application/x-www-form-rawurlencode',
    ]);
}
/**
 * 输出正常JSON
 *
 * @param string 提示信息
 * @param array  输出数据
 * @return json
 */
function jok($msg = 'success', $data = null)
{
    header("content-type:application/json;chartset=uft-8");
    if ($data !== null) {
        echo json_encode(["code" => 200, "msg" => $msg, 'data' => $data]);
    } else {
        echo json_encode(["code" => 200, "msg" => $msg]);
    }
    die;
}
/**
 * 输出错误JSON
 *
 * @param string 错误信息
 * @param int 错误代码
 * @return json
 */
function jerr($msg = 'error', $code = 500)
{ 
    header("content-type:application/json;chartset=uft-8");
    echo json_encode(["code" => $code, "msg" => $msg]);
    die;
}
/**
 * 密码+盐 加密
 *
 * @param string 明文密码
 * @param string 盐
 * @return string
 */
function encodePassword($password, $salt)
{
    return sha1($password . $salt . $password . $salt);
}
/**
 * 密码校验 6-16
 *
 * @param string 明文密码
 * @return boolean 是否校验通过
 */
function isValidPassword($password)
{
    return preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?!.*\s).{6,}/', $password);
}
/**
 * 获取随机字符
 *
 * @param int $len
 * @return void
 */
function getRandString($len)
{
    $string = '';
    $randString = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ($i = 0; $i < $len; $i++) {
        $string .= $randString[rand(0, strlen($randString) - 1)];
    }
    return $string;
}
/**
 * 获取随机字母
 *
 * @param int 长度
 * @return string
 */
function getRandChar($len)
{
    $string = '';
    $randString = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for ($i = 0; $i < $len; $i++) {
        $string .= $randString[rand(0, strlen($randString) - 1)];
    }
    return $string;
}
/**
 * 遍历类的方法
 *
 * @param string 指定的类名称
 * @return array
 */
function getClassMethods($class)
{
    $array_result = [];
    $array_all = get_class_methods($class);
    if ($parent_class = get_parent_class($class)) {
        $array_parent = get_class_methods($parent_class);
        $array_result = array_diff($array_all, $array_parent);
    } else {
        $array_result = $array_all;
    }
    return $array_result;
}
/**
 * 获取包含协议和端口的域名
 *
 * @return string
 */
function getFullDomain()
{
    return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME']) . "://" . $_SERVER['HTTP_HOST'];
}
/**
 * 获取客户端IP
 *
 * @return string
 */
function getClientIp()
{
    foreach (array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    ) as $key) {
        if (array_key_exists($key, $_SERVER)) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if ((bool) filter_var(
                    $ip,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6
                    // FILTER_FLAG_NO_PRIV_RANGE |
                    // FILTER_FLAG_NO_RES_RANGE
                )) {
                    return $ip;
                }
            }
        }
    }
    return null;
}

/**
 * 取文本中间
 *
 * @param string 原始字符串
 * @param string 左边字符串
 * @param string 右边字符串
 * @return string
 */
function getSubstr($str, $leftStr, $rightStr)
{
    $left = strpos($str, $leftStr);
    $right = strpos($str, $rightStr, $left);
    if ($left < 0 or $right < $left) {
        return '';
    }

    return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}
/**
 * 获取操作系统
 *
 * @return string
 */
function getOs()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return 'Other';
    }
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($agent, 'windows nt')) {
        $platform = 'Windows';
    } elseif (strpos($agent, 'macintosh')) {
        $platform = 'MacOS';
    } elseif (strpos($agent, 'ipod')) {
        $platform = 'iPod';
    } elseif (strpos($agent, 'ipad')) {
        $platform = 'iPad';
    } elseif (strpos($agent, 'iphone')) {
        $platform = 'iPhone';
    } elseif (strpos($agent, 'android')) {
        $platform = 'Android';
    } elseif (strpos($agent, 'unix')) {
        $platform = 'Unix';
    } elseif (strpos($agent, 'linux')) {
        $platform = 'Linux';
    } else {
        $platform = 'Other';
    }
    return $platform;
}
/**
 * 获取浏览器
 *
 * @return void
 */
function getBrowser()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return 'Unknown';
    }
    $agent = $_SERVER["HTTP_USER_AGENT"];
    if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) //ie11判断
    {
        return "IE";
    } else if (strpos($agent, 'Firefox') !== false) {
        return "Firefox";
    } else if (strpos($agent, 'Chrome') !== false) {
        return "Chrome";
    } else if (strpos($agent, 'Opera') !== false) {
        return 'Opera';
    } else if ((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false) {
        return 'Safari';
    } else {
        return 'Unknown';
    }
}
/**
 * 是否手机请求
 *
 * @return boolean
 */
function isMobileRequest()
{
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = '0';
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|uc|qq|wechat|micro|messenger|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        $mobile_browser++;
    }

    if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) {
        $mobile_browser++;
    }

    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        $mobile_browser++;
    }

    if (isset($_SERVER['HTTP_PROFILE'])) {
        $mobile_browser++;
    }

    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-',
    );
    if (in_array($mobile_ua, $mobile_agents)) {
        $mobile_browser++;
    }

    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
        $mobile_browser++;
    }

    // Pre-final check to reset everything if the user is on Windows
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) {
        $mobile_browser = 0;
    }

    // But WP7 is also Windows, with a slightly different characteristic
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) {
        $mobile_browser++;
    }

    if ($mobile_browser > 0) {
        return true;
    } else {
        return false;
    }

}
/**
 * 身份证号验证
 * @param $id
 * @return bool
 */
function isIDCard($id)
{
    $id = strtoupper($id);
    $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
    $arr_split = array();
    if (!preg_match($regx, $id)) {
        return false;
    }
    if (15 == strlen($id)) //检查15位
    {
        $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
        @preg_match($regx, $id, $arr_split);
        //检查生日日期是否正确
        $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) {
            return false;
        } else {
            return true;
        }
    } else { //检查18位
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) //检查生日日期是否正确
        {
            return false;
        } else {
            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ($i = 0; $i < 17; $i++) {
                $b = (int) $id[$i];
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $val_num = $arr_ch[$n];
            if ($val_num != substr($id, 17, 1)) {
                return false;
            } else {
                return true;
            }
        }
    }
}
/**
 * 是否是整数
 *
 * @param string 输入内容
 * @return boolean
 */
function isInteger($input)
{
    return (ctype_digit(strval($input)));
}
function hmac($data, $key)
{
    if (function_exists('hash_hmac')) {
        return hash_hmac('md5', $data, $key);
    }

    $key = (strlen($key) > 64) ? pack('H32', 'md5') : str_pad($key, 64, chr(0));
    $ipad = substr($key, 0, 64) ^ str_repeat(chr(0x36), 64);
    $opad = substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64);
    return md5($opad . pack('H32', md5($ipad . $data)));
}
/**
 * 获取一个key摘要
 *
 * @param string 原始key
 * @return string
 */
function getTicket($key)
{
    return sha1($key . (env('SYSTEM_SALT') ?? 'StartAdmin') . $key);
}
/**
 * CURL请求
 *
 * @param  string URL地址
 * @param  mixed 请求方法,支持GET/POST/PUT/DELETE/PATCH/TRACE/OPTION/HEAD 默认GET
 * @param  mixed 请求数据包体
 * @param  mixed 请求头 数组
 * @param  mixed 请求COOKIES字符串
 * @return void
 */
function curlHelper($url, $method = 'GET', $data = null, $header = [], $cookies = "")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    switch ($method) {
        case "GET":
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            break;
        case "POST":
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "DELETE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "PATCH":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "TRACE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "TRACE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "OPTIONS":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "OPTIONS");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        case "HEAD":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "HEAD");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            break;
        default:
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT,3);   //只需要设置一个秒的数量就可以 
    $response = curl_exec($ch);
    $output = [];
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    // 根据头大小去获取头信息内容
    $output['header'] = substr($response, 0, $headerSize);
    $output['body'] = substr($response, $headerSize, strlen($response) - $headerSize);
    $output['detail'] = curl_getinfo($ch);
    curl_close($ch);
    return $output;
}
/**
 * 模拟表单上传文件请求
 * @param $$url 提交地址
 * @param $data 提交数据
 * @param $cookies 如设置了Content-Type将被自动覆写为formdata
 * ex.
 * $data = ['file'=>new \CURLFile(realpath($file_dir)),appid"=>"1234"];
 * $result = curl_form($url,$data);
 * @return mixed
 */
function curlForm($url, $data = null, $header = [], $cookies = "")
{
    $header[] = 'Content-Type: multipart/form-data';
    return curlHelper($url, "POST", $data, $header, $cookies);
}
/**
 * 多维数组合并（支持多数组）
 * @param arraylist arrayMergeMulti(['1'=>'1','2'=>'2','3'=>'3'],['4'=>'4','5'=>'5','6'=>'6'])
 * @return array
 */
function arrayMergeMulti()
{
    //获取当前方法捕获到的所有参数数组
    $args = func_get_args();
    $array = [];
    foreach ($args as $arg) {
        if (is_array($arg)) {
            foreach ($arg as $k => $v) {
                if (is_array($v)) {
                    $array[$k] = isset($array[$k]) ? $array[$k] : [];
                    $array[$k] = arrayMergeMulti($array[$k], $v);
                } else {
                    $array[$k] = $v;
                }
            }
        }
    }

    return $array;
}
/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list   查询结果
 * @param string $field 排序的字段名
 * @param array $sortBy 排序类型
 *                      asc正向排序 desc逆向排序 nat自然排序
 * @return array|bool
 */
function listSortBy($list, $field, $sortBy = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = [];
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }
        switch ($sortBy) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }
        return $resultSet;
    }

    return false;
}

/**
 * 格式化字节大小
 * @param  number   $size       字节数
 * @param  int      $float      小数保留位数
 * @param  string   $delimiter  数字和单位分隔符
 * @return string   格式化后的带单位的大小
 */
function formatBytes($size, $float = 2, $delimiter = '')
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }

    return round($size, $float) . $delimiter . $units[$i];
}
/**
 * 生成标准UUID
 *
 * @return string
 */
function getUuid()
{
    mt_srand((float) microtime() * 10000);
    $uuid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    return $uuid;
}
