<!DOCTYPE html>
<html>
    <head>
        <title>
            @section('title')
            @show
        </title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        
        @section('css')
        <link rel="stylesheet" type="text/css" href="/dist/css/common.css" />
        <link rel="stylesheet" type="text/css" href="/dist/css/base_web.css" />
        @show
        
    </head>
    
    <body>
        <div class="wrap">
             <div class="top clearfix">
                <img class="logo" src="/images/doc_web/u6.png" alt="海口市妇幼保健院">
                <button class="logout-btn">退出</button>
                <div class="top-right">
                    <img class="photo" src="/images/doc_web/u8.png" alt="王磊">
                    <span class="name">王磊</span>
                </div>
            </div>
            <div class="main clearfix">
                <div class="navi">
                    <div class="sub-btn odd">
                        <img class="sub-icon" src="/images/icons/account.png">
                        <a class="sub-tl" href="/doc/home/account">个人账户</a>
                    </div>
                    <div class="sub-btn even">
                        <img class="sub-icon" src="/images/icons/patient.png">
                        <a class="sub-tl" href="/doc/home/patient">挂号病人</a>
                    </div>
                    <div class="sub-btn odd">
                        <img class="sub-icon" src="/images/icons/chat.png">
                        <a class="sub-tl" href="/doc/home/chat">复诊资讯</a>
                    </div>
                    <div class="sub-btn even">
                        <img class="sub-icon" src="/images/icons/comment.png">
                        <a class="sub-tl" href="/doc/home/comment">病人评价</a>
                    </div>
                    <div class="sub-btn odd">
                        <img class="sub-icon" src="/images/icons/advice.png">
                        <a class="sub-tl" href="/doc/home/advice">病人医嘱</a>
                    </div>
                    <div class="sub-btn even">
                        <img class="sub-icon" src="/images/icons/message.png">
                        <a class="sub-tl" href="/doc/home/message">消息提醒</a>
                    </div>
                </div>
                <div class="main-content">
                    @section('main-content')
                    @show
                </div>
            </div>
        </div>

        @section('js-lib')
            <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
        @show

        @section('js-common')
        @show

        @section('js-specify')
        @show
    </body>
</html>