<?php
namespace app\index\controller;

class Index extends Base
{
    public function index()
    {
        return view('index');
    }

    public function home()
    {
        return view('home');
    }
}