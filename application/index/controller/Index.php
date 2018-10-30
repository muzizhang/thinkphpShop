<?php
namespace app\index\controller;

class Index extends Base
{
    public function getIndex()
    {
        return view('index');
    }

    public function getHome()
    {
        return view('home');
    }
}