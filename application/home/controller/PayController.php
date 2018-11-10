<?php

namespace App\home\Controller;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use \think\Controller;

class PayController extends Controller
{
    protected $config = [
        'app_id' => '2016091700531229',
        'notify_url' => 'http://localhost:8000/homePay/notify',
        'return_url' => 'http://localhost:8000/homePay/return',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAta9gZo39lx9eAfNryTlyZAjaqntFjOx3eVoSTHwmyKqiKNKa08WAQmjVvKJpye1Bok8Xer9py6MMKaVAPSb7roDbK7biH1CjnmUa3UtaQlyqnEzI6ASHPWrSsg+0uUO/Q5iYeQMn91yrv6TEtB3dfHvyVAM31MRnvjlfL/M31OMKqm4nb24tAJSyL9yf9nXSwq3uOmZBoEND9pi5WdM/QkAb8EqvgzbfVkLx9CjPBIJd2YJShTG0T6TvWB5SpM5XgiUYRDZW3fmb2mdBDWy7Sz+L1P0foqnQMEoAz+dCN3tTy8IfQEhsaMqgvRMj0yHnn6XvEuIXIVLPbDE4EnAxOQIDAQAB',
        // 加密方式： **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAzgpiizZSzRCyw3+jCR6e0fbYSiqJWyyPiry523g93ZHM0RLuZac5DOZEQVMk36PaIhggkw1l+zpo8d2Tuwz1DSseCYYEB3IEmsTtJx945jtw5XY9P+1IKSgyoIRHvRTggX5KGqUE4Mw0FoSpsxf6+PlXnBtI8eALpnwroNF06bdnlMda7+jmjf/eb7+kJyMIOsSWrfm+fxiT/RMoXePGxjxNBAPPS4bMrc6sYEyh8S0senB2U+ak2cXIHPZAA8zTT0Ai3M829AKcP70oaEb/EffsBS610VNApY7fFAe5sgf2Ca+EpCGfnsSW/tamtvFqk00+aa13eyuXSPoQ8KkRAQIDAQABAoIBAQCniZY7sL5/dLaHdT/y6G0oOdoB5Liv5HoLzA4swYc8pZOv5f7ntaQUMyEJJTx0hV2YH5pNOLXWJNa05QF9NboFLSxNfiXEkBfiaYKVL2g1fdv9GboQQzdMEB2qnCVhQJqVcUV8iPaAfEtWjCUZNcSjssxkaWNVGmMeyxUGvYqdh0gMOkortOIreFKWK5mHnWt0I0KtiHYNI3+9yKeNk61gzAgKWwpEQDilsT/IvYiMtoQPNCLd01R92vjEiE3V1lbdH3Jj6YCb9y7efnYEhRfRt15iw0uWTS2IeA55HmGpGJWbylw3cofRlz/C8KfhbcIPQbRTjaP4y+2MfqHvKiYBAoGBAPiYQKhQgWJmV8XkwE/uTgIesJD+FUKZys5KJcm1Cn3bSEVJFiHxhPSBBNtFEr42HnbDUMwvA/00UOUp4x27I9UaizJ2MUl7SDFZ77juLJHWwZcHE2Mc+Yoe8y0jF6C+40dt3/Afd5w0b9CcuGAKFyrp700GgC4Vb4vaUpjTLXmxAoGBANQtntSk2M/kPHz4ktHi4r+PyowiiXihiFFo1HW8hQeGV9d347rm6bcE0gPJnuZcrzlGv/X/dkb8cEp+xtYpFomvW8pixjhi3xYoiqFCiTP77fS2TyLjbM3ziyBvVYX42WHeiYeT0ldAo0Ojq5pb2dGb4PT7+sffWcPGcw56cpBRAoGBAMcpa3uta8sCxUVFPBGNkS+/mKPA/BVskv9shhOwmGQ2fxMLN9Ef5u3gQ5zMHPUI9KUfW6cJ778236yJP2y9VjrP1j8qU9hUDYWEUTsujcSVcmlmANFCEGXo39gEqlRdOkcqruN4wmIB3KccN9axntgBrXdfQugowkIgOlY3sdIxAoGACPfMqMw25cKN0/Jlsj1WvCYFt5qWGOUq79XwdPF85e6Fs/O7SmEMK9ImVkalUrNELLWS04DyrNlqnZtyKAcgjr08sfcuzZ9QMo2QHnTDe9EAI8G44o6eQK40iTBrevgjqAFR6ssSruFqhSdbz3BmaneeMHyeAuir0JyIrGn8gaECgYAn6Qz/ohU5h9ifwNhE3ZnGsYWFvNhkFY7USFovF7hUsYZDtmTMaeGV29qs506W05DfVfRSwszdkV5AUbmnyYjCitx3lmBkzURQwp9SafGwqC949Xg9V/FkfU/diwzKV9DqBxWb/AN5gD6+1uFtKcETX88OlY5X1L1Ij9SYLdjtsg==',
        'log' => [ // optional
            'file' => '/static/logs/alipay.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ];

    public function getindex()
    {
        $order = [
            'out_trade_no' => $_GET['id'],
            'total_amount' => $_GET['sum'],
            'subject' => '订单提交成功，请尽快付款！订单号：'.$_GET['id'],
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// laravel 框架中请直接 `return $alipay`
    }

    public function getreturn()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！

        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
        //  更改字段值
        $value = [
            'order_status'=>1,
            'trading_time'=>$data->timestamp
        ];
        \Db::table('orders')
            ->where('order_id',$data->out_trade_no)
            ->where('user_id',session('home_id'))
            ->update($value);
        //  跳转
        $this->redirect('/homeUser/OrderSend');
    }

    public function getnotify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！

            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况

            Log::debug('Alipay notify', $data->all());
        } catch (Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// laravel 框架中请直接 `return $alipay->success()`
    }
}