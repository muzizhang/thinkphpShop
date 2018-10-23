<?php
namespace app\index\controller;

class Article
{
    //  文章列表
    public function getArticleList()
    {
        return view('article/article/list');
    }

    //  添加文章
    public function getArticleInsert()
    {
        return view('article/article/insert');
    }

    //  分类列表
    public function getArticleCategory()
    {
        return view('article/category');
    }   
}