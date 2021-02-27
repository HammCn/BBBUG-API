<?php

declare (strict_types = 1);

namespace app\command;

use think\console\Input;
use think\console\Output;
use PHPMailer\PHPMailer\PHPMailer;

use app\model\User as UserModel;

class Test extends BaseCommand
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Download')
            ->setDescription('StartAdmin Test Command');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->loadConfig();
        $userModel = new UserModel();
        $userList = $userModel->field('user_account')->where('user_app',1)->select();
        foreach($userList as $user){
            $email = $user['user_account'];
            echo $email.PHP_EOL;
            $email_account = config('startadmin.email_account');
            $email_password = config('startadmin.email_password');
            $email_host = config('startadmin.email_host');
            $email_remark = config('startadmin.email_remark');
            $email_port = config('startadmin.email_port');
            $mail = new PHPMailer();
            $mail->isSMTP(); // 使用SMTP服务
            $mail->CharSet = "utf8"; // 编码格式为utf8，不设置编码的话，中文会出现乱码
            $mail->Host = $email_host; // 发送方的SMTP服务器地址
            $mail->SMTPAuth = true; // 是否使用身份验证
            $mail->Username = $email_account; // 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱
            $mail->Password = $email_password; // 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！</span><span style="color:#333333;">
            $mail->SMTPSecure = "ssl"; // 使用ssl协议方式
            $mail->Port = $email_port; // 邮箱的ssl协议方式端口号是465/994

            $mail->setFrom($email_account, $email_remark); // 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
            $mail->addAddress($email, '用户'); // 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
            $mail->addReplyTo($email_account, $email_remark); // 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
            //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)
            //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)
            //$mail->addAttachment("bug0.jpg");// 添加附件

            $mail->Subject = "[".$email_remark."]回归邀请"; // 邮件标题
            $mail->Body = "2021新年新气象，BBBUG开发团队祝你工作顺利，身体健康！\n\nhttps://BBBUG.COM"; // 邮件正文

            //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用
            if (!$mail->send()) { // 发送邮件
                echo ("Mailer Error: " . $mail->ErrorInfo).PHP_EOL;
            } else {
                echo ('发送成功').PHP_EOL;
            }
            usleep(100*1000);
        }
    }
}
