@extends('layouts.master_web')

@section('title')
    复诊咨询
@stop

@section('main-content')
<iframe src="{{{ $chat_url }}}" frameborder="0">
</iframe>
@stop