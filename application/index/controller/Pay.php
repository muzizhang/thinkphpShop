<?php
namespace app\index\controller;

class Pay
{
    //  支付管理
    public function getindex()
    {
        return view('pay/management');
    }

    //  支付方式
    public function getMethod()
    {
        return view('pay/Payment_method');
    }

    //  支付详情
    public function getDetail()
    {
        return view('pay/Payment_details');
    }

    //  支付配置
    public function getConfigure()
    {
        return view('pay/Payment_Configure');
    }
}