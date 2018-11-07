<?php
namespace app\home\controller;

use app\home\model\User;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class Register
{
    public function getIndex()
    {
        return view('register');
    }

    //  判断用户输入的用户名是否已经存在
    public function getUsername()
    { 
        $user = User::where('nickname',$_GET['name'])->find();
        if($user == null)
        {
            echo json_encode([
                'status'=>'200'
            ]);
        }
        else
        {
            echo json_encode([
                'status'=>'404'
            ]);
        }
    }

    //   发送验证码
    public function postSms()
    {
        //  判断传入的数据是手机号还是邮箱
        if(preg_match('/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/',$_POST['phone']))
        {
            $config = [
                'accessKeyId'    => 'LTAIU4H7g4bipz3o',
                'accessKeySecret' => 'LuVhkGvcGenM9b34MsCCXJujfI9otD',
            ];
            $code = rand(100000, 999999);
            $client  = new Client($config);
            $sendSms = new SendSms;
            $sendSms->setPhoneNumbers($_POST['phone']);
            $sendSms->setSignName('itmuzi');
            $sendSms->setTemplateCode('SMS_147419883');
            $sendSms->setTemplateParam(['code' => $code]);
            //  执行发送
            $data = $client->execute($sendSms);
            session('code',$code);
            echo json_encode([
                'message'=>$data->Message,
                'code'=>$data->Code
            ]);
        }
        else if(preg_match(('/^[a-z0-9]+([._-][a-z0-9]+)*@([0-9a-z]+\.[a-z]{2,14}(\.[a-z]{2})?)$/i'),$_POST['phone']))
        {
            // Create the Transport
            $transport = (new \Swift_SmtpTransport('smtp.126.com',25))     //  邮箱服务器
            ->setUsername('itmuzi@126.com')      //  邮箱用户名
            ->setPassword('xx1314')       //   邮箱密码，有的邮件服务器是授权码
            ;
            $rand = rand(100000, 999999);
            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($transport);
            // Create a message
            $message = (new \Swift_Message('尊敬的用户您好'))      //  邮件标题
            ->setFrom(['itmuzi@126.com' => '全栈1班'])      //  发送者
            ->setTo([$_POST['phone'],$_POST['phone'] => $_POST['phone']])     //  发送对象，数组形式支持多个
            ->setBody('您的验证码为：'.$rand.',该验证码10分钟有效，请勿泄露于他人！')      //  邮件内容
            ;
            session('code',$rand);
            // Send the message
            $result = $mailer->send($message);     //  发送    成功，返回1   true
            if($result)
            {
                echo json_encode([
                    'message'=>'ok',
                    'code'=>'ok'
                ]);
            }
            else
            {
                echo json_encode([
                    'message'=>'false',
                    'code'=>'false'
                ]);
            }
        }
    }

    //   插入数据
    public function postInfo()
    {
        if(isset($_POST['code']) && $_POST['code'] == @session('code'))
        {
            //   判断注册账号是邮箱/手机号
            if(preg_match('/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/',$_POST['phone']))
            {
                $user = new User([
                    'login_name'=>$_POST['username'],
                    'login_pwd'=>md5($_POST['password']),
                    'nickname'=>$_POST['username'],
                    'phone'=>$_POST['phone'],
                ]);
            }
            else if(preg_match(('/^[a-z0-9]+([._-][a-z0-9]+)*@([0-9a-z]+\.[a-z]{2,14}(\.[a-z]{2})?)$/i'),$_POST['phone']))
            {
                $user = new User([
                    'login_name'=>$_POST['username'],
                    'login_pwd'=>md5($_POST['password']),
                    'nickname'=>$_POST['username'],
                    'email'=>$_POST['phone'],
                ]);
            }
            $user->save();
            echo json_encode([
                'status'=>'200'
            ]);
        }
        else
        {
            echo json_encode([
                'status'=>'404'
            ]);
        }
    }
    
}