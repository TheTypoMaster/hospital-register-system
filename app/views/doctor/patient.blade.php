@extends("layouts.master_web")

@section("title")
	挂号病人
@stop

@section("css")
	@parent
    <link rel="stylesheet" type="text/css" href="/dist/css/doctor/patient.css" />

@stop


@section("main-content")
	<div class="patient-wrapper">
		<div class="patient-caption">我的挂号病人</div>
		<div class="patient-table">
			<div class="patient-tr patient-table-caption">
				<div class="patient-td">
					日期
				</div>
				<div class="patient-td">
					上午
				</div>
				<div class="patient-td">
					下午
				</div>
			</div>
			<!-- 表格内容 START-->
			<div class="patient-tr patient-tabel-content">
				<div class="patient-td">04月27日</div>
				<div class="patient-td">
					<button type="button" class="patient-td-btn">
						<img src="/images/doc_web/u12.png" alt="" class="bg">
						<span class="bg">查看病人</span>
					</button>
				</div>
				<div class="patient-td">
					<button type="button" class="patient-td-btn">
						<img src="/images/doc_web/u12.png" alt="" class="bg">
						<span class="bg">查看病人</span>
					</button>
				</div>
			</div>
			<!-- 表格内容 END-->
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
		<div class="patient-mask"></div>
		<div class="patient-details-mask">
			<div class="patient-details">
				<div class="patient-details-table">
					<div class="patient-details-tr patient-details-caption">
						<div class="patient-details-td patient-details-td01">04月27日</div>
						<div class="patient-details-td patient-details-td02">病人</div>
					</div>
					<!-- 病人列表 START -->
					<div class="patient-details-tr patient-details-content">
						<div class="patient-details-td patient-details-td01">08：20</div>
						<div class="patient-details-td patient-details-td02">阿拉登</div>
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
		</div>
	</div>
@stop

@section("js-specify")
	@parent
	<script type="text/javascript" src="/dist/js/pages/doctor/patient.js"></script>
@stop