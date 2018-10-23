<?php
namespace app\index\controller;

class Index
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