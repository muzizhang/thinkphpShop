<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// 获取地址栏的参数
function getUrlParams($params=[])
{
    //  删除循环变量
    foreach($params as $v)
    {
        unset($_GET[$v]);
    }
    $str = '';
    foreach($_GET as $k=>$v)
    {
        $str .= "$k={$v}&";
    }
    return $str;
}



/**
 *+----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 *+----------------------------------------------------------
 * @static
 * @access public
 *+----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 *+----------------------------------------------------------
 * @return string
 *+----------------------------------------------------------
 */
 
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
    if(function_exists("mb_substr")){
        if($suffix){
            if(strlen($str)>$length)
                return mb_substr($str, $start, $length, $charset)."...";
            else
                return mb_substr($str, $start, $length, $charset);
        }else{
            return mb_substr($str, $start, $length, $charset);
        }
    }elseif(function_exists('iconv_substr')) {
        if($suffix){
            return iconv_substr($str,$start,$length,$charset);
        }else{
            return iconv_substr($str,$start,$length,$charset);
        }
    }
}