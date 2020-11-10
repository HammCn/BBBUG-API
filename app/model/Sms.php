<?php

namespace app\model;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Sms
{
    /**
     * 发送短信
     *
     * @param string 手机号码
     * @param string 验证码
     * @return void
     */
    public static function sendSms($phone, $code)
    {
        //初始化阿里云短信相关配置
        $alisms_appid = config('startadmin.alisms_appid');
        $alisms_appkey = config('startadmin.alisms_appkey');
        $alisms_sign = config('startadmin.alisms_sign');
        $alisms_template = config('startadmin.alisms_template');
        $error = null;
        if (!($alisms_appid && $alisms_appkey && $alisms_sign && $alisms_template)) {
            $error =  jerr('请先在后台配置阿里云短信相关参数！');
        }
        //创建一个阿里云授权客户端
        AlibabaCloud::accessKeyClient($alisms_appid, $alisms_appkey)->regionId('cn-hangzhou')->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->scheme('https')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $phone,
                        'SignName' => $alisms_sign,
                        'TemplateCode' => $alisms_template,
                        'TemplateParam' => '{"code":"' . $code . '"}',
                    ],
                ])
                ->request();
        } catch (ClientException $e) {
            $error =  jerr('阿里云短信发送异常');
        } catch (ServerException $e) {
            $error =  jerr('阿里云短信发送异常');
        }
        return $error;
    }

    /**
     * 校验短信验证码
     *
     * @param string 手机号码
     * @param string 验证码
     * @return bool
     */
    public function validSmsCode($phone, $code)
    {
        $_code = cache("SMS_" . $phone);
        if ($_code == $code) {
            cache('SMS_' . $phone, null);
            return true;
        } else {
            return false;
        }
    }
}
