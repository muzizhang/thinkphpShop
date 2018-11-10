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
        //   商品分类
        $category = \Db::table('category')
                        ->where('parent_id',$_GET['goods'])
                        ->select();
        //   根据分类，取出分类下对应的商品
        $sql = "select * from goods where locate('-".$_GET['goods']."-',category_id)>0";
        $goods = \Db::query($sql);
        //  取出商品图片
        foreach($goods as $v)
        {
            $image[] = \Db::table('goods_spu')
                        ->alias('gspu')
                        ->join('goods_image gimage','gspu.spu_id = gimage.spu_id')
                        ->where('gspu.goods_id',$v['id'])
                        ->field('gimage.path')
                        ->find();
            $price[] = \Db::table('goods_spu')
                            ->alias('gspu')
                            ->join('goods_sku gsku','gspu.spu_id = gsku.spu_id')
                            ->where('gspu.goods_id',$v['id'])
                            ->field('gsku.price')
                            ->find();
        }
        return view('search',[
            'category'=>$category,
            'goods'=>$goods,
            'image'=>$image,
            'price'=>$price,
        ]);
    }
}