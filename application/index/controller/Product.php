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
        return view('product/category/index');
    }

    //  添加分类
    public function getCategoryInsert()
    {
        return view('product/category/insert');
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