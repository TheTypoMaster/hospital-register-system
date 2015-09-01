@extends('layouts.master_web')

@section('title')
    复诊咨询
@stop

@section('css')
    @parent
    <link rel="stylesheet" href="/dist/css/doctor/chat.css">
@stop

@section('js-specify')
    <script type="text/javascript" src="/dist/js/pages/doctor/chat.js"></script>
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
                <!-- 病人列表 END -->
            </div>
            <div class="patient-pagination-wrapper">
                <span class="patient-page-next">下一页</span>
                <ul class="patient-pagination-list">
                </ul>
                <span class="patient-page-prev">上一页</span>
            </div>
        </div>
        <div class="patient-record-wrap">
            <div class="patient-record-container">
                <div class="patient-record-top">
                    <span>复诊提醒</span>
                    <button class="close-btn">返回</button>
                </div>
                <div class="patient-record-inner clearfix">
                </div>
            </div>
        </div>
    </div>
</div>

<script tpye="text/template" id="patient-template">
    <div class="patient-details-tr patient-details-content">
        <%- user_name %>
        <button record_id="<%- record_id %>" class="patient-set-btn">
            设置时间
        </button>
    </div>
</script>

<script tpye="text/template" id="pagination-template">
    <li page="<%- page %>" class="patient-page-num"><%- page %></li>
</script>

<script type="text/template" id="patient-record-template">
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
<script type="text/template" id="patient-return-template">
    <div class="patient-return patient-record-item">
        <div class="patient-item-top">
            复诊时间
        </div>
        <div class="patient-item-date">
            <%- date %>
        </div>
        <div class="patient-item-doc-info">
            <%- doctor.name %> / <%- doctor.title %> / <%- doctor.department %>
        </div>
    </div>
</script>
<script type="text/template" id="return-add-template">
    <div class="patient-return-add patient-record-item">
        <div class="patient-item-top">
            复诊时间
        </div>
        <div class="patient-item-date">
            <select class="select-year" name="year">
                <option value="2015">2015</option>
                <option value="2016">2016</option>
            </select>
            <span>年</span>
            <select class="select-month" name="month">
                <option value="01">1</option>
                <option value="02">2</option>
                <option value="03">3</option>
                <option value="04">4</option>
                <option value="05">5</option>
                <option value="06">6</option>
                <option value="07">7</option>
                <option value="08">8</option>
                <option value="09">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>
            <span>月</span>
            <select class="select-day" name="day">
                <option value="01">1</option>
                <option value="02">2</option>
                <option value="03">3</option>
                <option value="04">4</option>
                <option value="05">5</option>
                <option value="06">6</option>
                <option value="07">7</option>
                <option value="08">8</option>
                <option value="09">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
                <option value="24">24</option>
                <option value="25">25</option>
                <option value="26">26</option>
                <option value="27">27</option>
                <option value="28">28</option>
                <option value="29">29</option>
                <option value="30">30</option>
                <option value="31">31</option>
            </select>
            <span>日</span>
        </div>
        <button record_id="<%- record_id %>" class="patient-item-btn">确定时间</button>
    </div>
</script>

<!--
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
-->

@stop