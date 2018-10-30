<?php
namespace app\index\controller;

use think\captcha\Captcha;
use app\index\model\User;
use think\Controller;

class Login extends Controller
{
    protected $middleware = ['InLoginCheck'];
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
            //  写入用户登录信息表
            if(request()->ip == '0.0.0.0')
            {
                \Db::name('admin_login')
                    ->data([
                        'admin_id'=>$user->id,
                        'admin_name'=>$_POST['name'],
                        'content'=>'登陆成功',
                        'login_ip'=>request()->ip,
                        'login_address'=>'无法获取',
                    ])
                    ->insert();
            }
            else
            {
                \Db::name('admin_login')
                    ->data([
                        'admin_id'=>$user->id,
                        'admin_name'=>$_POST['name'],
                        'content'=>'登陆成功',
                        'login_ip'=>request()->ip,
                        'login_address'=>request()->InLoginCheck,
                    ])
                    ->insert();
            }
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
}