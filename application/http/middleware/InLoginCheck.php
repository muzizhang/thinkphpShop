<?php

namespace app\http\middleware;
use think\Request;

class InLoginCheck
{
    public function handle($request, \Closure $next)
    {
        $requestIp = new Request();
        $ip = $requestIp->ip();
        if(preg_match('/(2(5[0-5]{1}|[0-4]\d{1})|[0-1]?\d{1,2})(\.(2(5[0-5]{1}|[0-4]\d{1})|[0-1]?\d{1,2})){3}/',$ip))
        {
            if($ip != '0.0.0.0')
            {
                $ipContent = file_get_contents("http://apis.juhe.cn/ip/ip2addr?ip=".$ip."&key=63b18b9d95e59ee7dccc9e6476aaeb7b");
                $city = json_decode($ipContent);
                $request->InLoginCheck = $city->result->area;
                $request->ip = $ip;
            }else
            {
                $request->ip = $ip;
            }
        }

        return $next($request);
    }
}
