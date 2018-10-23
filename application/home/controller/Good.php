<?php
namespace app\home\controller;

class Good
{
    //   商品列表
    public function getShop()
    {
        return view('shop');
    }

    //   商品详情
    public function getList()
    {
        return view('item');
    }
}