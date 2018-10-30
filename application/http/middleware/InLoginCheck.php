<?php

namespace app\http\middleware;
use think\Request;

class InLoginCheck
{
    public function handle($request, \Closure $next)
    {
        $response  = $next($request);
        $requestIp = new Request();
        $ip = $requestIp->ip();
        if(preg_match('/(2(5[0-5]{1}|[0-4]\d{1})|[0-1]?\d{1,2})(\.(2(5[0-5]{1}|[0-4]\d{1})|[0-1]?\d{1,2})){3}/',$ip))
        {
            if($ip != '0.0.0.0')
            {
                $ipContent = file_get_contents("http://apis.juhe.cn/ip/ip2addr?ip=".$ip."&key=63b18b9d95e59ee7dccc9e6476aaeb7b");
                $city = json_decode($ipContent);
                \Db::name('admin_login')
                        ->data([
                            'admin_id'=>session('id'),
                            'admin_name'=>session('name'),
                            'content'=>'登陆成功',
                            'login_ip'=>$ip,
                            'login_address'=>$city->result->area,
                        ])
                        ->insert();
            }
            else
            {
                 \Db::name('admin_login')
                    ->data([
                        'admin_id'=>session('id'),
                        'admin_name'=>session('name'),
                        'content'=>'登陆成功',
                        'login_ip'=>$ip,
                        'login_address'=>'无法获取',
                    ])
                    ->insert();
            }
        }
        else
        {
            throw new \think\exception\HttpException(404, '页面不存在');
        }

        return $response;
    }
}
