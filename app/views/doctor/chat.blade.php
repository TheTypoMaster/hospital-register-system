@extends('layouts.master_web')

@section('title')
    复诊咨询
@stop

@section('css')
    @parent
    <link rel="stylesheet" href="/dist/css/doctor/chat.css">
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
                    <div class="patient-record patient-record-item">
                        <div class="patient-item-top">
                            挂号记录
                        </div>
                        <div class="patient-item-time">
                            <span class="date">2015.3.25</span>
                            <span class="period">上午</span>
                            <span class="time">09:30</span>
                        </div>
                        <div class="patient-item-doc-info">
                            王磊 / 副主任医师 / 妇科
                        </div>
                    </div>

                    <div class="patient-return patient-record-item">
                        <div class="patient-item-top">
                            复诊时间
                        </div>
                        <div class="patient-item-date">
                            <span>2015</span>年
                            <span>5</span>月
                            <span>10</span>日
                        </div>
                        <div class="patient-item-doc-info">
                            王磊 / 副主任医师 / 妇科
                        </div>
                    </div>
                    
                    <div class="patient-return-add patient-record-item">
                        <div class="patient-item-top">
                            复诊时间
                        </div>
                        <div class="patient-item-date">
                            <input class="input-add-year" name="year" type="text"><span>年</span>
                            <input class="input-add-month" name="month" type="text"><span>月</span>
                            <input class="input-add-day" name="day" type="text"><span>日</span>
                        </div>
                        <div class="patient-item-doc-info">
                            王磊 / 副主任医师 / 妇科
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop