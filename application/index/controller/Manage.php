<?php
namespace app\index\controller;

use app\index\model\Privilege;
use think\Db;
use app\index\model\Admin;
use app\index\model\RoleAdmin;
use think\Controller;

class Manage extends Controller
{
    //  权限列表
    public function getPri()
    {
        //  取出所有角色对应的管理员名称，以及数量
        $role = Db::table('role')
                    ->alias('r')
                    ->field('r.id,r.role_name,GROUP_CONCAT(distinct p.pri_name) pri_name,GROUP_CONCAT(distinct p.path) path')
                    ->leftJoin('privilege_role pr','r.id = pr.role_id')
                    ->leftJoin('privilege p','pr.pri_id = p.id')
                    ->order('r.id','asc')
                    ->group('r.role_name')
                    ->select();
        return view('manage/privilege/index',[
            'role'=>$role,
        ]);
    }
    
    //  查看权限
    public function getPriWatch()
    {
        //  递归取出所有权限信息
        $pri = new Privilege;
        $data = $pri->findAll();
        return view('manage/privilege/priList',[
            'data'=>$data
        ]);
    }

    //  编辑权限
    public function getPriListUpdate()
    {
        //   取出所有权限
        $pri = new Privilege;
        $data = $pri->findAll();
        //   根据id，取出对应的权限
        $pri = Db::table('privilege')->where('id',$_GET['id'])->find();
        return view('manage/privilege/priEdit',[
            'data'=>$data,
            'pri'=>$pri,
        ]);
    }

    //  处理权限
    public function postPriListEdit()
    {
        Db::name('privilege')
            ->where('id',$_GET['id'])
            ->data(['path'=>$_POST['path'],'parent_id'=>$_POST['privilege'],'pri_name'=>$_POST['pri_name']])
            ->update();
        $this->redirect('/manage/PriWatch');
    }

    //  删除权限
    public function getPriListDelete()
    {
        Db::table('privilege')->where('id',$_GET['id'])->delete();
        $this->redirect('/manage/PriWatch');
    }

    //  查看角色
    public function getRoleWatch()
    {
        //  取出所有角色
        $role = Db::table('role')->select();
        return view('manage/privilege/roleList',[
            'role'=>$role
        ]);
    }

    //  编辑角色
    public function getRoleListUpdata()
    {
        $role = Db::table('role')->where('id',$_GET['id'])->find();
        return view('manage/privilege/roleEdit',[
            'role'=>$role,
        ]);
    }

    //  处理角色
    public function postRoleListEdit()
    {
        $role = Db::name('role')
                ->where('id',$_GET['id'])
                ->data(['role_name'=>$_POST['role_name']])
                ->update();
        $this->redirect('/manage/RoleWatch');
    }

    //  删除角色
    public function getRoleListDelete()
    {
        Db::table('role')->where('id',$_GET['id'])->delete();
        $this->redirect('/manage/RoleWatch');
    }


    //  添加权限
    public function getPriInsert()
    {
        //  取出一级权限，二级权限
        $privilege = new Privilege;
        $pri = $privilege->findAll();
        //  取出所有角色
        $role = DB::table('role')->select();
        return view('manage/privilege/insert',[
            'pri'=>$pri,
            'role'=>$role,
        ]);
    }

    //  处理添加权限
    public function postPriAdd()
    {
        //   保存数据
        $pri = Privilege::create([
            'pri_name'=>$_POST['name'],
            'parent_id'=>$_POST['privilege'],
            'path'=>$_POST['path']
        ]);
        static $data = [];
        foreach($_POST['role'] as $v)
        {
            $data[] = ['pri_id'=>$_POST['privilege'],'role_id'=>$v];
        }

        Db::name('privilege_role')->insertAll($data);
        $this->redirect('/manage/pri');
    }

    //  编辑权限
    public function getPriEdit()
    {
        //  取出一级权限，二级权限
        $privilege = new Privilege;
        $pri = $privilege->findAll();
        //  取出所有角色
        $role = Db::table('role')->select();
        //  根据id  取出对应的数据
        $data = Db::table('role')
                ->alias('r')
                ->where('r.id',$_GET['id'])
                ->field('r.id,r.role_name,GROUP_CONCAT(distinct p.pri_name) pri_name,GROUP_CONCAT(distinct p.path) path')
                ->leftJoin('privilege_role pr','r.id = pr.role_id')
                ->leftJoin('privilege p','pr.pri_id = p.id')
                ->group('r.id')
                ->find();
        //   将权限分隔
        $pri_name = explode(',',$data['pri_name']);
        $priId = Db::table('privilege')->where(1,1);
        //   查找出多条权限数据
        foreach($pri_name as $v)
        {
            $priId = $priId->whereor('pri_name',$v);
        }
        $priId = $priId->select();
        return view('manage/privilege/edit',[
            'pri'=>$pri,
            'role'=>$role,
            'data'=>$data,
            'priId'=>$priId,
            'pri_name'=>$pri_name
        ]);
    }

    //   处理编辑
    public function postPriUpdate()
    {
        
        echo '<pre>';
        var_dump($_POST);
    }

    //  删除权限
    public function getPriDelete()
    {
        //  根据传递的角色id，删除对应的角色，在根据id去角色权限中间表，取出对应权限的id，删除权限，删除中间表数据
        Db::table('role')->where('id',$_GET['id'])->delete();
        $data = Db::table('privilege_role')->where('role_id',$_GET['id'])->distinct(true)->field('pri_id')->select();
        $pri = Db::table('privilege')->where(1,1);
        foreach($data as $v)
        {
            $pri = $pri->whereor('id',$v['pri_id']);
        }
        $pri->delete();
        Db::table('privilege_role')->where('role_id',$_GET['id'])->delete();
        $this->redirect('/manage/Pri');
    }

   

    //  管理员列表
    public function getAdmin()
    {
        //  取出所有的角色
        $roleAll = Db::table('role')->select();
        // echo '<pre>';
        // var_dump($roleAll);
        //  取出所有的角色，并且计算该角色下的管理员人数
        $role = Db::table('role')
                    ->alias('r')
                    ->field('r.id,r.role_name,count(ra.role_id) count')
                    ->leftJoin('role_admin ra','r.id = ra.role_id')
                    ->group('r.id')
                    ->select();
        //  取出所有管理员的对应信息，并添加分页,搜索
        $admin = Db::table('admin')
                    ->alias('a')
                    ->where(1,1);
                    
        if(isset($_GET['id']))
        {
            $admin = $admin->whereor('r.id',$_GET['id']);
        }
        if(isset($_GET['name']))
        {
            $admin = $admin->whereor('a.name','like','%'.$_GET['name'].'%');
        }
        if(isset($_GET['start']))
        {
            $admin = $admin->whereor('a.created_at',$_GET['start']);
        }
        $admin = $admin->field('a.*,GROUP_CONCAT(r.role_name) role_name')
                    ->leftJoin('role_admin ra','a.id = ra.admin_id')
                    ->leftJoin('role r','r.id = ra.role_id')
                    ->group('a.id')
                    ->paginate(15);
        //  分页
        //  获取分页显示
        $page = $admin->render();
        //  计算管理员的总数
        $count = Db::table('admin')->field('count(*) count')->find();
        return view('manage/admin',[
            'role'=>$role,
            'admin'=>$admin,
            'count'=>$count,
            'page'=>$page,
            'roleAll'=>$roleAll,
        ]);
    }

    //  添加管理员
    public function postAdminAdd()
    {
        $admin = Admin::create([
                    'name'=>$_POST['username'],
                    'password'=>md5($_POST['password'])
                ]);
        //  根据返回的数据id，添加数据  $admin->id
        RoleAdmin::create([
            'role_id'=>$_POST['role'],
            'admin_id'=>$admin->id,
        ]);
        echo json_encode([
            'status'=>'200'
        ]);
    }

    //  编辑前获取数据
    public function getEditBefore()
    {
        $admin = Db::table('admin')
                    ->alias('a')
                    ->where('a.id',$_GET['id'])
                    ->field('a.*,r.role_name,r.id role_id')
                    ->leftJoin('role_admin ra','a.id = ra.admin_id')
                    ->leftJoin('role r','r.id = ra.role_id')
                    ->find();
        return $admin;
    }

    // 编辑管理员
    public function postAdminEdit()
    {
        Db::name('role_admin')
            ->where('admin_id',$_GET['id'])
            ->data([
                'role_id'=>$_POST['role_id']
            ])
            ->update();
        echo json_encode([
            'status'=>'200'
        ]);
    }

    // 删除管理员
    public function getAdminDelete()
    {
        Db::table('admin')->where('id',$_GET['id'])->delete();
        $this->redirect('/manage/Pri');
    }

    //  个人信息列表
    public function getInfo()
    {
        //  根据session['id']  取出当前用户的详细信息
        $user = Db::table('admin')
                    ->alias('a')
                    ->where('a.id',session('id'))
                    ->leftJoin('role_admin ra','a.id = ra.admin_id')
                    ->leftJoin('role r','r.id = ra.role_id')
                    ->find();
        return view('manage/info',[
            'user'=>$user,
        ]);
    }

    //  修改个人信息
    public function postInfo()
    {
        //  将json字符串转换为数组
        $data = get_object_vars(json_decode($_POST['data']));

        Db::name('admin')
            ->where('id',session('id'))
            ->data($data)
            ->update();
        echo json_encode([
            'status'=>'200',
        ]);
    }

    //  修改密码
    public function postEditPassword()
    {
        //  判断输入的原密码是否与数据库中的密码一致
        $data = Db::table('admin')
                    ->where('password',md5($_POST['OldPassword']))
                    ->where('id',session('id'))
                    ->find();
        if($data != null)
        {
            //  将新密码进行修改
            Db::name('admin')
                    ->where('id',session('id'))
                    ->data([
                        'password'=>md5($_POST['NewPassword'])
                    ])
                    ->update();
            echo json_encode([
                'status'=>'200'
            ]);
        }
        else
        {
            echo json_encode([
                'status'=>'404'
            ]);
        }
    }
}