<?php
namespace app\home\controller;

class Cart
{
    //  成功加入购物车
    public function getSuccessCart()
    {
        return view('success_cart');
    }

    //  购物车
    public function getIndex()
    {
        return view('cart');
    }
    
    //  结算
    public function getsettlement()
    {
        return view('getOrderInfo');
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