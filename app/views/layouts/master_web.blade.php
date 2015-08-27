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
                <!-- <button class="logout-btn"> -->
                    <a class="logout-btn" href="/doc/logout">退出</a>
                    <!-- <a href="/doc/logout" class="bg">
                        <img src="/images/doc_web/u12.png" alt="" class="bg">
                        <span class="bg">退出</span>
                    </a> -->
                <!-- </button> -->
                <div class="top-right">
                    <img class="photo" src="/images/doc_web/u8.png" alt="王磊">
                    <span class="name">{{{ $name }}}</span>
                </div>
            </div>
            <div class="main clearfix">
                <div class="navi">
                    <div class="sub-btn odd">
                        <a class="sub-item" href="/doc/home/account">
                            <img class="sub-icon" src="/images/icons/account.png">
                            <span class="sub-tl">个人账户</span>
                        </a>
                        
                    </div>
                    <div class="sub-btn even">
                        <a class="sub-item" href="/doc/home/patient">
                            <img class="sub-icon" src="/images/icons/patient.png">
                            <span class="sub-tl">挂号病人</span>
                        </a>
                        
                    </div>
                    <div class="sub-btn odd">
                        <a class="sub-item" href="/doc/home/chat">
                            <img class="sub-icon" src="/images/icons/chat.png">
                            <span class="sub-tl">复诊资讯</span>
                        </a>
                        
                    </div>
                    <div class="sub-btn even">
                        <a class="sub-item" href="/doc/home/comment">
                            <img class="sub-icon" src="/images/icons/comment.png">
                            <span class="sub-tl">病人评价</span>
                        </a>
                        
                    </div>
                    <div class="sub-btn odd">
                        <a class="sub-item" href="/doc/home/advice">
                            <img class="sub-icon" src="/images/icons/advice.png">
                            <span class="sub-tl">病人医嘱</span>
                        </a>
                        
                    </div>
                    <div class="sub-btn even">
                        <a class="sub-item" href="/doc/home/message">
                            <img class="sub-icon" src="/images/icons/message.png">
                            <span class="sub-tl">消息提醒</span>
                        </a>
                        
                    </div>
                </div>
                <div class="main-content">
                    @section('main-content')
                    @show
                </div>
            </div>
        </div>

        @section('js-lib')
            <script src="/dist/js/lib/jquery-1.11.2.min.js"></script>
            <script src="/dist/js/lib/lodash.min.js"></script>
        @show

        @section('js-common')
        @show

        @section('js-specify')
        @show
    </body>
</html>