<?php 
namespace app\home\controller;

use app\home\model\User;

class Login
{
    public function getIndex()
    {
        return view('login');
    }

    //  忘记密码
    public function getforget()
    {
        return view('forget');
    }

    //  极验验证
    public function getStartCaptchaServlet()
    {
        $GtSdk = new \GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
        $data = array(
                "user_id" => "test", # 网站用户id
                "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                "ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
            );

        $status = $GtSdk->pre_process($data, 1);
        $_SESSION['gtserver'] = $status;
        $_SESSION['user_id'] = $data['user_id'];
        echo $GtSdk->get_response_str();
    }

    //  二次验证
    public function postVerifyLoginServlet()
    {
        $GtSdk = new \GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
        $data = array(
                "user_id" => $_SESSION['user_id'], # 网站用户id
                "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                "ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
            );


        if ($_SESSION['gtserver'] == 1) {   //服务器正常
            $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
            if ($result) {
                header('Location: /homeLogin/Validate');
            } else{
                header('Location: /homeLogin/forget');
            }
        }else{  //服务器宕机,走failback模式
            if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
                header('Location: /homeLogin/Validate');
            }else{
                header('Location: /homeLogin/forget');
            }
        }
    }

    //  忘记密码验证
    public function postPhone()
    {
        $name = $this->getIsWho($_POST['phone']);
        $user = 'false';
        if($name)
        {
            //  查询手机号
            $user = User::where('phone',$_POST['phone'])->find();
        }
        else
        {
            //  查询邮箱
            $user = User::where('email',$_POST['phone'])->find();
        }
        $_SESSION['phone'] = $_POST['phone'];
        $this->getParams($user);
    }

    //  手机/邮箱验证
    public function getValidate()
    {
        return view('modifyPhone');
    }

    //  发送验证码
    public function getCode()
    {
        $config = [
            'accessKeyId'    => 'LTAIU4H7g4bipz3o',
            'accessKeySecret' => 'LuVhkGvcGenM9b34MsCCXJujfI9otD',
        ];
        $code = rand(100000, 999999);
        $client  = new Client($config);
        $sendSms = new SendSms;
        $sendSms->setPhoneNumbers($_SESSION['phone']);
        $sendSms->setSignName('itmuzi');
        $sendSms->setTemplateCode('SMS_147419883');
        $sendSms->setTemplateParam(['code' => $code]);
        //  执行发送
        $data = $client->execute($sendSms);
        $_SESSION['code'] = $code;
        echo json_encode([
            'message'=>$data->Message,
            'code'=>$data->Code
        ]);
    }

    //   验证验证码是否正确
    public function getPhoneCode()
    {
        $user = 'false';
        if($_GET['code'] == $_SESSION['code'])
        {
            $user = User::where('phone',$_SESSION['phone'])->find();
        }
        $this->getParams($user);
    }

    //  修改密码
    public function getSend()
    {
        return view('sendPhone');
    }

    //  修改密码
    public function postSetPassword()
    {
        $user = User::where('phone',$_SESSION['phone'])->update(['login_pwd'=>md5($_POST['login_pwd'])]);
        $this->getParams($user);
    }

    //  登录验证
    public function postUsername()
    {
        $name = $this->getIsWho($_POST['name']);
        $user = 'false';
        if($name)
        {
            //  查询手机号
            $user = User::where('phone',$_POST['name'])->where('login_pwd',md5($_POST['password']))->find();
        }
        else
        {
            //  查询邮箱
            $user = User::where('email',$_POST['name'])->where('login_pwd',md5($_POST['password']))->find();
        }
        $_SESSION['login_name'] = $user['login_name'];
        $_SESSION['id'] = $user['id'];
        $this->getParams($user);
    }

    //   判断传入的值是手机号、邮箱、用户名
    public function getIsWho($name)
    {
        if(preg_match('/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/',$name))
            return true;
        else if(preg_match(('/^[a-z0-9]+([._-][a-z0-9]+)*@([0-9a-z]+\.[a-z]{2,14}(\.[a-z]{2})?)$/i'),$name))
            return false;
    }

    //  验证判断是否存在
    public function getParams($user)
    {
        if($user)
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
}