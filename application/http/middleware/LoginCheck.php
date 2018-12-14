<?php

namespace app\http\middleware;

class LoginCheck
{
    public function handle($request, \Closure $next)
    {
        //  前置中间件
        if(session('id') == null)
        {
            return redirect('/');
        }
        else
        {
            //  获取将要访问的路径
            $path = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'],'/') : '/index';
            // 设置一个白名单
            $whiteList = ['index','home'];
            $url_path = json_decode(session('url_path'));
            if($url_path !== null)
            {
                // 判断是否有权访问
                if(!in_array($path, array_merge($whiteList, $url_path)))
                {
                    die('无权访问！');
                }
            }
        }
        return $next($request);
    }
}
