<?php
namespace app\index\controller;

class System extends Base
{
    //  系统设置
    public function getindex()
    {
        return view('system/system');
    }

    //  系统栏目管理
    public function getSection()
    {
        return view('system/section');
    }

    //  系统日志
    public function getLog()
    {
        return view('system/log');
    }
}