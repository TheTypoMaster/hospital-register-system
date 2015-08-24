@extends('layouts.master_web')

@section('title')
    复诊咨询
@stop

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="/dist/css/doctor/chat.css" />
@stop

@section(js-specify)

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
                <li class="user"\>
                    <img src="/images/doc_web/u70.png" class="photo" />
                    <span class="name">美女</span>
                </li>
            </ul>
            <div class="chat-main">
                <div uid="" class="msg-wrap">
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

            <!-- 当前用户信息 start -->
            <script type="text/template" id="current_user_info">
                {
                    'id': 1,
                    'name': ''
                    'photo': '/images/doc_web/u8.png',
                }
            </script>
            <!-- 当前用户信息 end -->

            <!-- 聊天用户模板 start -->
            <script type="text/template" id="user-info-template">
                <li uid="<%- user_id -%>" class="user">
                    <img src="<%- photo -%>" class="photo" />
                    <span class="name"><%- user_name -%></span>
                </li>
            </script>
            <!-- 聊天用户模板 end-->
            
            <!-- 聊天记录模板 start -->
            <script type="text/template" id="message-record-template">
                <div from_uid="<%- from_uid -%>" class="msg-wrap">
                    <div class="cur-time"><%- current_time -%></div>
                    <div class="msg-inner">
                    </div>
                </div>
            </script>
            <!-- 聊天记录模板 end -->

            <!-- 聊天消息模板 start -->
            <script type="text/template" id="message-template">
                <div class="item <%- class -%> clearfix">
                    <img src="<%- photo -%>" class="photo" />
                    <div class="msg-scope">
                        <div class="content"><%- content -%></div>
                    </div>
                </div>
            </script>
            <!-- 聊天消息模板 end -->
        </div>
    </div>
@stop