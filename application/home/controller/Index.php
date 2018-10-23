<?php
namespace app\home\controller;

class Index
{
    //   首页
    public function getIndex()
    {
        return view('index');
    }

    //   搜索
    public function getSearch()
    {
        return view('search');
    }
}