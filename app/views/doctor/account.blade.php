@extends("layouts.master_web")

@section("title")
	个人账户
@stop

@section("css")
	@parent
    <link rel="stylesheet" type="text/css" href="/dist/css/doctor/account.css" />

@stop


@section("main-content")
	<div class="account-wrapper">
		<div class="account-tr account-tr01">
			<span class="account-title">个人资料</span>
			<span class="account-edit">
				<img src="/images/doc_web/u76.png">
				修改资料
			</span>
		</div>
		<div class="account-tr account-tr02">
			<div class="account-trs-container">
				<div class="account-tr">
					<span class="account-key">姓名：</span>
					<!-- <span class="account-span">王磊</span> -->
					<input type="text" class="account-input account-no-edit" value="王磊" readonly="true" />
				</div>
				<div class="account-tr">
					<span class="account-key">职称：</span>
					<!-- <span class="account-span">主任医师</span> -->
					<input type="text" class="account-input account-no-edit" value="主任医师" readonly="true" />
				</div>
				<div class="account-tr">
					<span class="account-key">科室：</span>
					<!-- <span class="account-span">小儿科</span> -->
					<input type="text" class="account-input account-no-edit" value="小儿科" readonly="true" />
				</div>
				<div class="account-tr" style="">
					<span class="account-key">专长：</span>
					<!-- <span class="account-span">小儿呼吸道、大肠肠、小儿头大无脑痴呆症、先天性小儿麻痹症、装逼症</span> -->
					<textarea class="account-input account-skills account-no-edit" readonly="true">小儿呼吸道、大肠肠、小儿头大无脑痴呆症、先天性小儿麻痹症、装逼症</textarea>
				</div>
			</div>
			<div class="account-avatar-container">
				<div class="account-avatar">
					<img src="/images/doc_web/u54.jpg">
				</div>
				<div class="account-upload-btn">
					修改头像
					<input type="file" class="account-upload-btn account-file">
				</div>
			</div>
		</div>
		<div class="account-tr account-tr03">
			<span class="account-key">简介：</span>
			<!-- <span class="span">小儿呼吸道、大肠肠、小儿头大无脑痴呆症、先天性小儿麻痹症、装逼症、小儿呼吸道、大肠肠、小儿头大无脑痴呆症、先天性小儿麻痹症、装逼症</span> -->
			<textarea class="account-no-edit" readonly="true">小儿呼吸道、大肠肠、小儿头大无脑痴呆症、先天性小儿麻痹症、装逼症、小儿呼吸道、大肠肠、小儿头大无脑痴呆症、先天性小儿麻痹症、装逼症</textarea>
			<div class="account-submit">确定修改</div>
		</div>
	</div>
@stop