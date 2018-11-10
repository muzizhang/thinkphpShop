<?php
namespace app\home\controller;

use \think\cache\driver\Redis;
use \app\home\Snowflake;

class Cart
{
    //  成功加入购物车
    public function getSuccessCart()
    {
        //   取出购物车信息
        $data = \Db::table('cart')
                    ->where('id',$_GET['cartId'])
                    ->find();
        $sku_attrvalue = $this->getattr($data);
        return view('success_cart',[
            'data'=>$data,
            'sku_attrvalue'=>$sku_attrvalue,
            'pc'=>$_GET['pc'],
        ]);
    }

    //  转换sku
    public function getattr($data)
    {
        $attr_value = explode('|',$data['attr_value']);
        foreach($attr_value as $k=>$v)
        {
            $sku = explode(':',$v);
            $attr[] = $sku[0];
            $value[] = $sku[1];
        }
        //  取出属性
        $sku = \Db::table('goods_spu')
                    ->alias('gspu')
                    ->join('goods_skuattr gattr','gspu.spu_id = gattr.spu_id')
                    ->where('gspu.goods_id',$data['goods_id'])
                    ->field('gattr.*')
                    ->select();
        //  取出属性值
        foreach($sku as $k=>$v)
        {
            $sku_value[$k] = \Db::table('goods_skuvalue')
                        ->where('attr_id',$v['id'])
                        ->select();
        }
        foreach($attr as $k=>$v)
        {
            foreach($sku_value[$k] as $k1=>$v1)
            {
                if($v == $sku[$k]['id'] && $value[$k] == $v1['id'])
                {
                    $sku_attrvalue[] = $sku[$k]['sku_name']." : ".$v1['sku_value'];
                }
            }
        }
        $sku_attrvalue = implode($sku_attrvalue,"   ");
        return $sku_attrvalue;
    }

    //  购物车
    public function getIndex()
    {
        //  取出该用户购物车的所有数据
        $data = \Db::table('cart')
                    ->where('user_id',session('home_id'))
                    ->order('id','desc')
                    ->select();
        foreach($data as $k=>$v)
        {
            $sku[] = $this->getattr($v);
            $stock[] = \Db::table('goods_sku')
                            ->where('sku_path',$v['attr_value'])
                            ->where('spu_id',$v['spu_id'])
                            ->field('stock')
                            ->select();
        }
        if($data == null)
        {
            return view('cart',[
                'data'=>null
            ]);
        }
        else
        {
            return view('cart',[
                'data'=>$data,
                'sku'=>$sku,
                'stock'=>$stock,
            ]);
        }   
    }

    //  更新购物车商品数量
    public function getCartCount()
    {
        $data = \Db::table('cart')
                    ->where('id',$_GET['goods_id'])
                    ->where('user_id',session('home_id'))
                    ->data([
                        'count'=>$_GET['count']
                    ])
                    ->update();
    }

    //  删除购物车数据
    public function getDeleteCart()
    {
        \Db::table('cart')
            ->where('id',$_GET['id'])
            ->where('user_id',session('home_id'))
            ->delete();
    }

    //  处理结算数据
    public function postCartSettlement()
    {
        $redis = $this->getRedis();
        //   根据商品id，取出对应的数组
        foreach($_POST['id'] as $k=>$v)
        {
            $data[] = \Db::table('cart')
                        ->where('id',$v)
                        ->where('user_id',session('home_id'))
                        ->find();
                //  同时删除选中商品
                \Db::table('cart')
                    ->where('id',$v)
                    ->where('user_id',session('home_id'))
                    ->delete();
        }
        var_dump($data);
        die;
        $redis->hSet('cart',session('home_id'),json_encode($data)); 
    }
    
    public function getRedis()
    {
        $red = new Redis;
        $redis = $red->hand();
        return $redis;
    }

    //  结算
    public function getsettlement()
    {
        $redis = $this->getRedis();
        $data = json_decode($redis->hget('cart',session('home_id')));
        $num = 0;
        $sum = 0;
        foreach($data as $k=>$v)
        {
            $num += $v->count;
            $sum += $v->price*$v->count;
        }
        return view('getOrderInfo',[
            'data'=>$data,
            'num'=>$num,
            'sum'=>$sum,
        ]);
    }

    //   提交订单数据
    public function postOrder()
    {
        //  判断支付方式
        if($_POST['payType'] == '支付宝付款')
        {
            $paytype = 0;
        }
        else if($_POST['payType'] == '微信付款')
        {
            $paytype = 1;
        }
        else if($_POST['payType'] == '货到付款')
        {
            $paytype = 2;
        }
        //  获取订单号
        $work = rand(1,10);
        $snow = new Snowflake($work);
        $order = $snow->nextId();
        $redis = $this->getRedis();
        $data = json_decode($redis->hget('cart',session('home_id')));
        $sum = 0;
        foreach($data as $k=>$v)
        {
            //  取出sku_id
            $sku_id = \Db::table('goods_sku')
                            ->where('sku_path',$v->attr_value)
                            ->find();
            \Db::table('orders')
                ->data([
                    'spu_id'=>$v->spu_id,
                    'sku_id'=>$sku_id['id'],
                    'attr_value'=>$v->attr_value,
                    'goods_name'=>$v->goods_name,
                    'goods_count'=>$v->count,
                    'order_price'=>$v->price,
                    'order_id'=>$order,
                    'consignee'=>$_POST['consignee'],
                    'address'=>$_POST['address'],
                    'phone'=>$_POST['phone'],
                    'payment_type'=>$paytype,
                    'user_id'=>session('home_id'),
                ])
                ->insert();
            $sum += $v->price;
        }
        echo json_encode([
            'order'=>$order,
            'count'=>$sum,
        ]);
    }

    //  提交订单
    public function getPay()
    {
        return view('pay');
    }

    //  支付成功
    public function getPaySuccess()
    {
        return view('paysuccess');
    }

    //  支付失败
    public function getPayFail()
    {
        return view('payfail');
    }

}