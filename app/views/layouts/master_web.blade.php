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
            <div class="main">
                <div class="navi">
                    <div class="sub-btn odd"></div>
                    <div class="sub-btn even"></div>
                    <div class="sub-btn odd"></div>
                    <div class="sub-btn even"></div>
                    <div class="sub-btn odd"></div>
                    <div class="sub-btn even"></div>
                </div>
                <div class="main-content">
                    @section('main-content')
                    @stop
                </div>
            </div>
        </div>

        @section('js-lib')
            <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
        @show

        @section('js-common')
            <script src="/dist/js/base.js" type="text/javascript"></script>
        @show

        @section('js-specify')
        @show
    </body>
</html>