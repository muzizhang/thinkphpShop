<?php
namespace app\index\controller;

use think\Controller;

class Base extends Controller
{
    public function __construct()
    {
        $arr = ['index/index,login/index,login/Login,login/Verify'];
        //  判断全局变量中$_SERVER['REQUEST_URI']  是否在某一个固定的数组中
        if(!in_array($_SERVER['REQUEST_URI'],$arr))
        {
            //  根据session('id')  取出对应的角色名称
            $role_name = \Db::table('admin')
                            ->alias('a')
                            ->where('a.id',session('id'))
                            ->field('r.role_name')
                            ->leftJoin('role_admin ra','a.id = ra.admin_id')
                            ->leftJoin('role r','r.id = ra.role_id')
                            ->find();

            $url = $_SERVER['REQUEST_URI'];
            //  如果存在，判断其中是否有？存在
            if(strpos($url,'?'))
            {
                //  将其进行截取
                $str = explode('?',$url);
                $url = $str[0]; 
                $params = $str[1];
            }
            //  删除url路径的第一个   /
            $url = ltrim($url,'/');
            //   根据其url，进行搜索   行为id，行为path
            $data = \Db::table('privilege')
                    ->where('url','like','%'.$url.'%')
                    ->find();
            
            //  编辑，删除
            if(isset($params))
            {
                $this->Edit_Delete($params,$data,$role_name['role_name']);
            }

            //  新建
            //   判断其路径中是否存在，insert /  add  等字符串
            if(stripos($url,'insert'))
            {
                $this->insert($data,$role_name['role_name']);
            }
        }
    }

    //  新建
    public function insert($data,$role_name)
    {
        //   判断其是否会有下一步操作
        $urlAfterPart = explode(',',$data['url']);
        $path_info = ltrim($_SERVER['PATH_INFO'],'/');
        if($path_info == $urlAfterPart)
        {
            \Db::table('admin_logs')
                ->data([
                    'admin_id'=>session('id'),
                    'admin_name'=>session('name'),
                    'admin_role'=>$role_name,
                    'behavior_type_id'=>$data['id'],
                    'behavior_type_name'=>$data['pri_name'],
                    'behavior_type_path'=>$data['path'],
                    'practive'=>$_POST
                ])
                ->insert();
        }
        else 
        {
            \Db::table('admin_logs')
                ->data([
                    'admin_id'=>session('id'),
                    'admin_name'=>session('name'),
                    'admin_role'=>$role_name,
                    'behavior_type_id'=>$data['id'],
                    'behavior_type_name'=>$data['pri_name'],
                    'behavior_type_path'=>$data['path'],
                    'practive'=>$_POST
                ])
                ->insert();
        }
    }

    //  编辑、删除
    public function Edit_Delete($params,$data,$role_name)
    {
        //   将其根据& 符号进行分割
        $params = explode('&',$params);
        $array = [];
        foreach($params as $v)
        {
            $v = explode('=',$v);
            $array[$v[0]] = $v[1];
        }
        if(isset($array['id']))
        {
            //  判断其是否有下一步操作，
            $urlAfterPart = explode(',',$data['url']);
            $path_info = ltrim($_SERVER['PATH_INFO'],'/');
            if($path_info == $urlAfterPart[1])
            {
                //  根据id，查询出对应的数据，将其放置到redis内存中，指定时间写入数据库中
                \Db::name('admin_logs')
                    ->data([
                        'admin_id'=>session('id'),
                        'admin_name'=>session('name'),
                        'admin_role'=>$role_name,
                        'behavior_type_id'=>$data['id'],
                        'behavior_type_name'=>$data['pri_name'],
                        'behavior_type_path'=>$data['path'],
                        'practive'=>$_POST
                    ])
                    ->insert();
            }
            else
            {
                //   将其放置到redis内存中，指定时间写入数据库中
                \Db::name('admin_logs')
                    ->data([
                        'admin_id'=>session('id'),
                        'admin_name'=>session('name'),
                        'admin_role'=>$role_name,
                        'behavior_type_id'=>$data['id'],
                        'behavior_type_name'=>$data['pri_name'],
                        'behavior_type_path'=>$data['path'],
                        'practive'=>$data['pri_name']
                    ])
                    ->insert();
            }
        }
    }
}