@extends('layouts.master_web')

@section('title')
    复诊咨询
@stop

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="/dist/css/doctor/chat.css" />
@stop

@section('main-content')
    <div class="chat-wrap">
        <div class="top">
            复诊提醒
        </div>
        <div class="chat-body clearfix">
            <ul class="users-list">
                <li class="user">
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
                <li class="user">
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
                <li class="user">
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
                <li class="user">
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
                <li class="user">
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
                <li class="user">
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
                <li class="user">
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
                <li class="user">
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
                <li class="user">
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
            </ul>
            <div class="chat-main">
                <div class="msg-wrap">
                    <div class="cur-time">17:30</div>
                    <div class="msg-inner">
                        <div class="item from clearfix">
                            <img src="/images/doc_web/u70.png" class="photo" />
                            <div class="msg-scope">
                                <div class="nickname">林志玲</div>
                                <div class="content">今晚有空么</div>
                            </div>
                        </div>
                        <div class="item to clearfix">
                            <img src="/images/doc_web/u70.png" class="photo" />
                            <div class="msg-scope">
                                <div class="nickname">Me</div>
                                <div class="content">当然!</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="input-wrap">
                    <div class="tool"></div>
                    <div class="input-inner">
                        <textarea class="input-content"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop