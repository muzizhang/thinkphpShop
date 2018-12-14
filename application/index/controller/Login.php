<?php
namespace app\index\controller;

use think\captcha\Captcha;
use app\index\model\User;

class Login extends Base
{
    //  登录页
    public function index()
    {
        return view('index');
    }

    //   验证用户登录
    public function Login()
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
            $manage = new \app\index\controller\Manage();
            session('url_path',$manage->getUrlPath(session('id')));
            //  写入用户登录信息表
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
    public function Verify()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }

    //  退出登录
    public function loginOut()
    {
        // 清空session 
        Session::clear();
        $this->redirect('/');
    }
}