<?php
namespace app\index\controller;

class Order extends Base
{
    //   订单信息
    public function getInfo()
    {
        return view('order/info');
    }

    //  订单
    public function getindex()
    {
        return view('order/order');
    }

    //  订单管理
    public function getManage()
    {
        return view('order/manage');
    }

    //  交易金额
    public function getAmount()
    {
        return view('order/amount');
    }

    //  订单处理
    public function getHandling()
    {
        return view('order/handling');
    }

    //  订单退款
    public function getRefund()
    {
        return view('order/refund');
    }
}