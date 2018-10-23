<?php
namespace app\home\controller;

class Seckill
{
    //   秒杀
    public function getIndex()
    {
        return view('seckill_index');
    }

    //   秒杀列表
    public function getItem()
    {
        return view('seckill_item');
    }
}