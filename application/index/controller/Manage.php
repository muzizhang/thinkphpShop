<?php
namespace app\index\controller;

class Manage
{
    //  权限列表
    public function getPri()
    {
        return view('manage/privilage/index');
    }
    
    //  添加权限
    public function getPriInsert()
    {
        return view('manage/privilage/insert');
    }

    //  管理员列表
    public function getAdmin()
    {
        return view('manage/admin');
    }
    
    //  角色列表
    public function getrole()
    {
        return view('manage/role');
    }
}