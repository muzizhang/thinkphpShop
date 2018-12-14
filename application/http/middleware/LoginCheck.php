<?php

namespace app\http\middleware;

class LoginCheck
{
    public function handle($request, \Closure $next)
    {
        // if(in_array($_SERVER['REQUEST_URI'],$arr))
        //  前置中间件
        if(session('id') == null)
        {
            return redirect('/');
        }
        return $next($request);
    }
}
