<?php
namespace app\index\controller;

use think\Request;
use think\facade\Env;

class Product extends Base
{
    //  产品列表页
    public function getindex()
    {
        return view('product/product/index');
    }

    //  添加产品
    public function getPinsert()
    {
        return view('product/product/insert');
    }

    //  分类列表
    public function getCategory()
    {   
        //  取出所有分类
        $data = \Db::table('category')
            ->field("*,concat(path,id,'-') pathId")
            ->where(1,1);
        if(isset($_GET['name']))
        {
            $data = $data->where('cate_name','like','%'.$_GET['name'].'%');
        }

        $data = $data->order('id,pathId')->select();
            
        return view('product/category/index',[
            'data'=>$data,
        ]);
    }

    //  获取所有的分类
    public function getCategoryAll()
    {
        $data = \Db::table('category')
                    ->field("*,concat(path,id,'-') pathId")
                    ->order('id,pathId')
                    ->select();
        echo json_encode([
            'data'=>$data
        ]);
    }

    //  添加分类
    public function getCategoryInsert()
    {
        //  取出一级分类
        $cat1_id = \Db::table('category')->where('parent_id',0)->select();
        return view('product/category/insert',[
            'cat1_id'=>$cat1_id,
        ]);
    }

    //  获取二，三级分类
    public function getCat2_id()
    {
        $data = \Db::table('category')
                    ->where('parent_id',$_GET['id'])
                    ->select();
        echo json_encode([
            'data'=>$data
        ]);
    }

    //  处理添加
    public function postCategoryAdd()
    {
        if(!isset($_POST['cat1_id']))
        {
            \Db::table('category')
                ->data([
                    'cate_name'=>$_POST['category_name']
                ])
                ->insert();
        }
        else if(!isset($_POST['cat2_id']))
        {
            \Db::table('category')
            ->data([
                'cate_name'=>$_POST['category_name'],
                'parent_id'=>$_POST['cat1_id'],
                'path'=>'-'.$_POST['cat1_id'].'-'
            ])
            ->insert();
        }
        else
        {
            \Db::table('category')
                ->data([
                    'cate_name'=>$_POST['category_name'],
                    'parent_id'=>$_POST['cat2_id'],
                    'path'=>'-'.$_POST['cat1_id'].'-'.$_POST['cat2_id'].'-'
                ])
                ->insert();
        }
        
        $this->redirect('/product/Category');
    }

    //   编辑分类
    public function getCategoryEdit()
    {
        //  取出所有分类
        $cat1_id = \Db::table('category')
                        ->field("*,concat(path,id,'-') pathId")
                        ->select();
        //  获取出当id的数据
        $data = \Db::table('category')->where('id',$_GET['id'])->find();
        // echo '<pre>';
        // var_dump($data);
        // var_dump($cat1_id);
        return view('product/category/edit',[
            'cat1_id'=>$cat1_id,
            'data'=>$data
        ]);
    }

    //  处理编辑
    public function postCategoryUpdate()
    {
        \Db::table('category')
            ->where('id',$_GET['id'])
            ->data(['cate_name'=>$_POST['category_name']])
            ->update();
        $this->redirect('/product/Category');
    }

    //  删除分类
    public function getCategoryDelete()
    {
        \Db::table('category')
            ->where('id',$_GET['id'])
            ->delete();
        $this->redirect('/product/Category');
    }

    //  品牌列表
    public function getBrand()
    {
        //  取出所有的品牌信息
        $data = \Db::table('brand')
                    ->where(1,1);
        if(isset($_GET['name']))
        {
            $data = $data->where('brand_name','like','%'.$_GET['name'].'%')
                        ->whereor('description','like','%'.$_GET['name'].'%');
        }
        if(isset($_GET['start']))
        {
            $data = $data->where('created_at','>',$_GET['start']);
        }
        if(isset($_GET['type']))
        {
            $data = $data->where('brand_type',$_GET['type']);
        }
        $data = $data->paginate(1,false,['query'=>request()->param()]);
        $page = $data->render();
        return view('product/brand/index',[
            'data'=>$data,
            'page'=>$page
        ]);
    }

    //  添加品牌
    public function getBrandInsert()
    {
        $req = new Request();
        $token = $req->token('__token__','sha1');
        return view('product/brand/insert',[
            'token'=>$token
        ]);
    }

    //  处理品牌添加
    public function postBrandAdd()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size'=>5242880,'ext'=>'jpg,png,gif,bmp,jpeg,webp,tif,pcx'])
                    ->move(Env::get('root_path').'public/static/upload');
        if($info){
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            //  上传成功，将其保存到数据库中
            \Db::table('brand')
                ->data([
                    'brand_name'=>$_POST['brand_name'],
                    'brand_LOGO'=>'/upload/'.$info->getSaveName(),
                    'brand_type'=>$_POST['type'],
                    'link'=>$_POST['brand_link'],
                    'description'=>$_POST['brand_desc']
                ])
                ->insert();

            $this->redirect('/product/Brand');
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    //   编辑品牌
    public function getBrandEdit()
    {
        //    取出当前品牌
        $data = \Db::table('brand')
                    ->where('id',$_GET['id'])
                    ->find();
        return view('product/brand/edit',[
            'data'=>$data
        ]);
    }

    //  处理编辑
    public function postBrandUpdate()
    {
        //  删除原图片
        $img = \Db::table('brand')
                    ->where('id',$_GET['id'])
                    ->field('brand_LOGO')
                    ->find();
        unlink(Env::get('root_path').'/public/static'.$img['brand_LOGO']);
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size'=>5242880,'ext'=>'jpg,png,gif,bmp,jpeg,webp,tif,pcx'])
                    ->move(Env::get('root_path').'public/static/upload');
        if($info){
            //  上传成功，将其保存到数据库中
            \Db::name('brand')
                ->where('id',$_GET['id'])
                ->data([
                    'brand_name'=>$_POST['brand_name'],
                    'brand_LOGO'=>'/upload/'.$info->getSaveName(),
                    'brand_type'=>$_POST['type'],
                    'link'=>$_POST['brand_link'],
                    'description'=>$_POST['brand_desc']
                ])
                ->update();

            $this->redirect('/product/Brand');
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    //  删除品牌
    public function getBrandDelete()
    {
        //  删除图片
        $img = \Db::table('brand')
                    ->where('id',$_GET['id'])
                    ->field('brand_LOGO')
                    ->find();
        unlink(Env::get('root_path').'/public/static'.$img['brand_LOGO']);
        //  删除数据库中的数据
        \Db::table('brand')->where('id',$_GET['id'])->delete();
        $this->redirect('/product/Brand');
    }
}