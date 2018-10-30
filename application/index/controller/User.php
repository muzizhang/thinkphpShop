<?php
namespace app\index\controller;

class User extends Base
{
    //  用户列表
    public function getList()
    {
        return view('user/list');
    }

    //   等级管理
    public function getGrading()
    {
        return view('user/grading');
    }

    //   会员记录管理
    public function getRecord()
    {
        return view('user/record');
    }
}