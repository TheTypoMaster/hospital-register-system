@extends('layouts.master')

@section('title')
    预约挂号
@stop

@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="/dist/css/register/select_period.css" />
@stop

@section('js-lib')
    @parent
@stop

@section('js-common')
    @parent
@stop

@section('js-specify')

@stop

@section('body-title')
    挂号
@stop

@section('body-main')
    <div class="doc-info-wrap">
        <div class="doc-info-top clearfix">
            <img class="doc-pic float-left" src="{{{ $doctor['photo'] }}}"/>
            <div class="doc-info-detail float-left">
                <div class="doc-name">{{{ $doctor['name'] }}}</div>
                <div class="doc-title">职称: {{{ $doctor['title'] }}}</div>
                <div class="doc-section">科室：{{{ $doctor['department'] }}}</div>
                <div class="doc-hospital">医院：{{{ $doctor['hospital'] }}}</div>
            </div>
        </div>
        <p class="doc-info-desc">
            {{{ $doctor['specialty'] }}}
        </p>
        <div class="slide-btn">
            <img src="/images/icons/arrow_down.png" />
        </div>
    </div>
@stop

@section('body-bottom')
    <div class="list-wrap">
        <div class="list-head l-grey">
            {{{ $schedule['date'] }}} {{{ $schedule['period'] == 0 ? '上午' : '下午' }}} 号源列表
        </div>

        <table class="register-list">
            @foreach ( $periods as $period )
                <tr class="register-item">
                    <td class="register-time">
                        {{{ $period['start'] }}}-{{{ $period['end'] }}}
                    </td>
                    <td class="register-total">
                        总数：{{{ $period['total'] }}}
                    </td>
                    <td class="register-remain">
                        <span>剩余：</span>
                        <span class="l-orange">{{{ $period['total'] - $period['current'] }}}</span>
                    </td>
                    <td class="register-btn">
                        <button class="btn">
                            <a href="/pay?period_id={{{ $period['id'] }}}">挂号</a>
                        </button>
                    </td>
                </td>
            @endforeach
        </table>
    </div>
@stop
