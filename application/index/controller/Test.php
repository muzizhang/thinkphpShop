<?php
namespace app\index\controller;

use think\controller;
use think\cache\driver\Redis;
use think\Request;

class Test
{
    public function read()
    {
        Log::write('测试测试','debug');
    }
    //  redis
    public function redis()
    {
        $redis = new Redis();
        $handler = $redis->hand();
        // $handler->lpush('list','zhangsan');
        var_dump($handler->lrange('list',0,-1));
    }

}