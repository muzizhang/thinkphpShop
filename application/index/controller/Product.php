<?php
namespace app\index\controller;


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
        return view('product/brand/index');
    }

    //  添加品牌
    public function getBrandInsert()
    {
        return view('product/brand/insert');
    }
}