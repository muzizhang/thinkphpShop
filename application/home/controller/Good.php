<?php
namespace app\home\controller;

class Good
{
    //   商品列表
    public function getShop()
    {
        return view('shop');
    }

    //   商品详情
    public function getList()
    {
        //  根据id，取出商品信息
        $goods = \Db::table('goods')
                        ->where('id',$_GET['spu'])
                        ->find();
        //  根据category_id  取出分类
        $category = substr($goods['category_id'],1,strlen($goods['category_id'])-2);
        $category = explode('-',$category);
        foreach($category as $v)
        {
            $cateName[] = \Db::table('category')
                            ->where('id',$v)
                            ->find();
        }
        //  取出spu
        $spu_id = \Db::table('goods_spu')
                    ->where('goods_id',$_GET['spu'])
                    ->find();
        //  取出商品图片
        $image = \Db::table('goods_image')
                    ->where('spu_id',$spu_id['spu_id'])
                    ->select();
        //  取出价钱
        $price = \Db::table('goods_sku')
                    ->where('spu_id',$spu_id['spu_id'])
                    ->find();
        //  取出规格
        $attr = \Db::table('goods_skuattr')
                    ->where('spu_id',$spu_id['spu_id'])
                    ->select();
        foreach($attr as $k=>$v)
        {
            $value[$k] = \Db::table('goods_skuvalue')
                        ->where('attr_id',$v['id'])
                        ->select();            
        }
        return view('item',[
            'goods'=>$goods,
            'cateName'=>$cateName,
            'image'=>$image,
            'price'=>$price,
            'attr'=>$attr,
            'value'=>$value,
        ]);
    }

    //  添加到购物车
    public function postCart()
    {
        //  判断当前用户购物车中是否存在当前商品
        $data = \Db::table('cart')
                    ->where('user_id',session('home_id'))
                    ->where('attr_value',$_POST['sku'])
                    ->where('goods_id',$_POST['id'])
                    ->find();
        if($data)
        {
            //  更新数量
            $cart = \Db::name('cart')
                        ->where('id',$data['id'])
                        ->data([
                            'count'=>$data['count']+1
                        ])
                        ->update();
            echo json_encode([
                'cartId'=>$data['id']
            ]);
        }
        else
        {
            //  根据商品id，取出对应的商品信息
            $goods = \Db::table('goods')
                        ->where('id',$_POST['id'])
                        ->find();
            //  spu
            $spu = \Db::table('goods_spu')
                        ->where('goods_id',$_POST['id'])
                        ->find();
            $sku = \Db::table('goods_sku')
                        ->where('sku_path',$_POST['sku'])
                        ->find();
            //    取出一张图片
            $image = \Db::table('goods_image')
                            ->where('spu_id',$spu['spu_id'])
                            ->field('xs_path')
                            ->find();
            $data = [
                'goods_name'=>$goods['goods_name'],
                'price'=>$sku['price'],
                'count'=>$_POST['count'],
                'goods_id'=>$goods['id'],
                'purchase_price'=>$_POST['count']*$sku['price'],
                'sku_id'=>$sku['id'],
                'user_id'=>session('home_id'),
                'spu_id'=>$spu['spu_id'],
                'attr_value'=>$_POST['sku'],
                'goods_image'=>$image['xs_path'],
            ];
            $cart = \Db::name('cart')
                        ->insertGetId($data);
            echo json_encode([
                'cartId'=>$cart
            ]);
        }
    }
}