<?php
namespace app\index\controller;

class System extends Base
{
    //  系统设置
    public function getindex()
    {
        return view('system/system');
    }

    //  系统栏目管理
    public function getSection()
    {
        return view('system/section');
    }

    //  系统日志
    public function getLog()
    {
        //  取出所有的日志信息
        $data = \Db::table('admin_logs')
                    ->where(1,1);
        if(isset($_GET['name']))
        {
            $data = $data->where('admin_name','like','%'.$_GET['name'].'%')
                        ->whereor('admin_role','like','%'.$_GET['name'].'%')
                        ->whereor('behavior_type_name','like','%'.$_GET['name'].'%')
                        ->whereor('practive','like','%'.$_GET['name'].'%');
        }

        if(isset($_GET['start']))
        {
            $data = $data->where('created_at','>',$_GET['start']);
        }
        $data = $data->order('id','desc')
                    ->paginate(1,false,['query'=>request()->param()]);

        //   获取分页显示
        $page = $data->render();
        // echo '<pre>';
        // var_dump($data);
        // var_dump($page);
        return view('system/log',[
            'data'=>$data,
            'page'=>$page
        ]);
    }
}