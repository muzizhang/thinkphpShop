<?php
namespace app\index\controller;

class Info
{
    //  评论，留言
    public function getInfoComment()
    {
        return view('info/comment');
    }

    //  意见反馈
    public function getInfoFeedback()
    {
        return view('info/feedback');
    }
}