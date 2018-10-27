<?php
namespace app\index\controller;

use think\captcha\Captcha;
use app\index\model\User;

class Login
{
    public function getindex()
    {
        return view('index');
    }

    //   验证用户登录
    public function postLogin()
    {
        if(!captcha_check($_POST['code']))
        {
            echo json_encode([
                'status'=>'500'
            ]);
            die;
        }
        $user = User::where('name',$_POST['name'])
                        ->where('password',md5($_POST['password']))
                        ->find();
        if($user)
        {
            session('name',$_POST['name']);
            session('id',$user->id);
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

    //  验证码
    public function getVerify()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }
}