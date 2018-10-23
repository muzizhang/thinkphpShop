<?php
namespace app\home\controller;

class User
{
    //  个人中心
    public function getIndex()
    {
        return view('index');
    }

    //  订单详情
    public function getOrderDetail()
    {
        return view('orderDetail');
    }

    //  待付款
    public function getOrderPay()
    {
        return view('order_pay');
    }

    //  待发货
    public function getOrderSend()
    {
        return view('order_send');
    }

    //  待收货
    public function getOrderReceive()
    {
        return view('order_receive');
    }

    //  待评价
    public function getOrderEvaluate()
    {
        return view('order_evaluate');
    }

    //  我的收藏
    public function getCollect()
    {
        return view('person_collect');
    }

    // 我的足迹
    public function getFootmark()
    {
        return view('person_footmark');
    }

    //  个人信息
    public function getSetInfo()
    {
        return view('setting_info');
    }

    //  地址管理
    public function getSetAddress()
    {
        return view('setting_address');
    }

    //  设置地址完成
    public function getAddressComplete()
    {
        return view('setting_address_complete');
    }

    //  安全管理
    public function getSetSafe()
    {
        return view('setting_safe');
    }

    //  绑定手机号
    public function getSetPhone()
    {
        return view('setting_address_phone');
    }
}