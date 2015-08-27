@extends("layouts.master_web")

@section("title")
	消息提醒
@stop

@section("css")
	@parent
    <link rel="stylesheet" type="text/css" href="/dist/css/doctor/message.css" />

@stop


@section("main-content")
	<div class="patient-wrapper">
		<div class="patient-caption">消息提醒</div>
		<div class="patient-table">
			<!-- 表格内容 START -->
			<div class="table-tr table-tr-clickable">
				<div class="table-td table-td01">
					2015.4.30
				</div>
				<div class="table-td table-td02">
					好好吃药，别着凉了！
				</div>
			</div>
			<div class="table-tr table-tr-clickable">
				<div class="table-td table-td01">
					2015.4.30
				</div>
				<div class="table-td table-td02">
					好好吃药，别着凉了！
				</div>
			</div>
			<div class="table-tr table-tr-clickable">
				<div class="table-td table-td01">
					2015.4.30
				</div>
				<div class="table-td table-td02">
					好好吃药，别着凉了！
				</div>
			</div>
			<div class="table-tr table-tr-clickable">
				<div class="table-td table-td01">
					2015.4.30
				</div>
				<div class="table-td table-td02">
					好好吃药，别着凉了！
				</div>
			</div>
			<div class="table-tr table-tr-clickable">
				<div class="table-td table-td01">
					2015.4.30
				</div>
				<div class="table-td table-td02">
					好好吃药，别着凉了！
				</div>
			</div>
			<div class="table-tr table-tr-clickable">
				<div class="table-td table-td01">
					2015.4.30
				</div>
				<div class="table-td table-td02">
					好好吃药，别着凉了！
				</div>
			</div>
			<div class="table-tr table-tr-clickable">
				<div class="table-td table-td01">
					2015.4.30
				</div>
				<div class="table-td table-td02">
					好好吃药，别着凉了！
				</div>
			</div>
			<div class="table-tr table-tr-clickable">
				<div class="table-td table-td01">
					2015.4.30
				</div>
				<div class="table-td table-td02">
					好好吃药，别着凉了！
				</div>
			</div>
			<!-- 表格内容 END -->
		</div>

		<div class="patient-footer">
			<div class="patient-select">
				<select class="patient-year">
					<option value="2010">2010年</option>
					<option value="2011">2011年</option>
					<option value="2012">2012年</option>
					<option value="2013">2013年</option>
					<option value="2014">2014年</option>
					<option value="2015">2015年</option>
				</select>
				<select class="patient-month">
					<option value="1">1月</option>
					<option value="2">2月</option>
					<option value="3">3月</option>
					<option value="4">4月</option>
					<option value="5">5月</option>
					<option value="6">6月</option>
					<option value="7">7月</option>
					<option value="8">8月</option>
					<option value="9">9月</option>
					<option value="10">10月</option>
					<option value="11">11月</option>
					<option value="12">12月</option>
				</select>
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
		<div class="page-mask"></div>
		<div class="page-details-mask">
			<div class="patient-details">
				<div class="patient-details-table">
					<div class="table-details-head">
						消息内容
					</div>
					<div class="table-details-content">
						<span>s什么也没有</span>
					</div>
				</div>

			</div>
		</div>
	</div>
@stop


@section("js-specify")
	@parent
	<script type="text/javascript" src="/dist/js/pages/doctor/message.js"></script>
@stop