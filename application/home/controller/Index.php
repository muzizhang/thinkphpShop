<?php
namespace app\home\controller;

class Index
{
    //   首页
    public function getIndex()
    {
        // echo "<pre>";
        //   取出一级分类
        $category = $this->getCate();
        //   取出10个品牌
        $brand = \Db::table('brand')
                    ->limit(10)
                    ->select();
        $brand1 = \Db::table('brand')
                    ->limit(16)
                    ->select();
        // var_dump($brand);
        return view('index',[
            'category'=>$category,
            'brand'=>$brand,
            'brand1'=>$brand1,
        ]);
    }

    //  分类
    public function getCate()
    {
        $data = \Db::table('category')
                    ->where('parent_id','0')
                    ->select();
        foreach($data as $k=>$v)
        {
            $data[$k][$v['cate_name']] = \Db::table('category')
                        ->where('parent_id',$v['id'])
                        ->select();
            foreach($data[$k][$v['cate_name']] as $k1=>$v1)
            {
                $data[$k][$v['cate_name']][$k1][$v1['cate_name']] = \Db::table('category')
                            ->where('parent_id',$v1['id'])
                            ->select();
            }
        }
        return $data;
    }

    //   搜索
    public function getSearch()
    {
        return view('search');
    }
}