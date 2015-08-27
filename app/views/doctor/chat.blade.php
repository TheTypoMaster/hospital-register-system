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
    <div class="top">
        复诊提醒
    </div>
    <iframe class="chat-page" src="{{{ $chat_url }}}" frameborder="0">
    </iframe>
</div>

@stop