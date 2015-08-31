@extends('layouts.master_web')

@section('title')
    复诊咨询
@stop

@section('css')
    @parent
    <link rel="stylesheet" href="/dist/css/doctor/chat.css">
@stop

@section('js-specify')
    <script type="text/javascript" src="/dist/js/pages/chat.js"></script>
@stop

@section('main-content')

<div class="chat-wrap">
    <div class="chat-top">
        复诊咨询
        <div class="return">
            复诊提醒
        </div>
    </div>
    <iframe class="chat-page" src="{{{ $chat_url }}}" frameborder="0">
    </iframe>

    <div class="patient-mask"></div>
    <div class="patient-details-mask">
        <div class="patient-details">
            <div class="patient-details-table">
                <div class="patient-details-tr patient-details-caption">
                    病人
                </div>
                <!-- 病人列表 START -->
                <div class="patient-details-tr patient-details-content">
                    李四
                    <button class="patient-set-btn">
                        设置时间
                    </button>
                </div>
                <!-- 病人列表 END -->

                <script tpye="text/template" id="patient-template">
                    <div class="patient-details-tr patient-details-content">
                        <%- name %>
                        <button user_id="<%- user_id %>" class="patient-set-btn">
                            设置时间
                        </button>
                    </div>
                </script>
            </div>
            <div class="patient-pagination-wrapper">
                <span class="patient-page-next">下一页</span>
                <ul>
                    <li class="patient-page-num active">1</li>
                    <li class="patient-page-num">2</li>
                    <li class="patient-page-num">3</li>
                    <li class="patient-page-num">4</li>
                    <li class="patient-page-num">5</li>
                    <li class="patient-page-num">6</li>
                    <script tpye="text/template" id="user-template">
                        <li page="<%- page %>" class="patient-page-num"><%- page %></li>
                    </script>
                </ul>
                <span class="patient-page-prev">上一页</span>
            </div>
        </div>
        <div class="patient-record-wrap">
            <div class="patient-record-container">
                <div class="patient-record-top">
                    复诊提醒
                </div>
                <div class="patient-record-inner clearfix">
                    
                    <script type="text/javascript">
                        <div class="patient-record patient-record-item">
                            <div class="patient-item-top">
                                挂号记录
                            </div>
                            <div class="patient-item-time">
                                <span class="date"><%- date %></span>
                                <span class="period"><%- period %></span>
                                <span class="time"><%- time %></span>
                            </div>
                            <div class="patient-item-doc-info">
                                <%- doctor.name %> / <%- doctor.title %> / <%- doctor.department %>
                            </div>
                        </div>
                    </script>

                    <script type="text/javascript">
                        <div class="patient-return patient-record-item">
                            <div class="patient-item-top">
                                复诊时间
                            </div>
                            <div class="patient-item-date">
                                <% date %>
                            </div>
                            <div class="patient-item-doc-info">
                                <%- doctor.name %> / <%- doctor.title %> / <%- doctor.department %>
                            </div>
                        </div>
                    </script>
                    
                    <script type="text/javascript">
                        <div class="patient-return-add patient-record-item">
                            <div class="patient-item-top">
                                复诊时间
                            </div>
                            <div class="patient-item-date">
                                <input class="input-add-year" name="year" type="text"><span>年</span>
                                <input class="input-add-month" name="month" type="text"><span>月</span>
                                <input class="input-add-day" name="day" type="text"><span>日</span>
                            </div>
                            <button record_id="<%- record_id %>" class="patient-item-btn">确定时间</button>
                        </div>
                    </script>

                </div>
            </div>
        </div>
    </div>
</div>

@stop