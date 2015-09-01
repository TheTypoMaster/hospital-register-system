@extends("layouts.master_web")

@section("title")
	病人医嘱
@stop

@section("css")
	@parent
    <link rel="stylesheet" type="text/css" href="/dist/css/doctor/advice.css" />

@stop


@section("main-content")
	<div class="patient-wrapper">
		<div class="patient-caption">
			病人医嘱
			<button class="add-btn">
				<img src="/images/doc_web/advice_add_btn.png" alt="" class="bg">
				<span class="bg">新增医嘱</span>
			</button>
		</div>
		<div class="patient-table">
			<!-- 表格头 START -->
			<div class="table-tr table-tr-caption">
				<div class="table-td table-td01">
					姓名
				</div>
				<div class="table-td table-td02">
					医嘱
				</div>
			</div>
			<!-- 表格头 END -->

			<!-- 表格内容 START -->
			<div class="advice-content-container">
				<div class="table-tr table-tr-clickable">
					<div class="table-td table-td01">
						任性
					</div>
					<div class="table-td table-td02">
						好好吃药，别着凉了！
					</div>
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
					<option value="01">1月</option>
					<option value="02">2月</option>
					<option value="03">3月</option>
					<option value="04">4月</option>
					<option value="05">5月</option>
					<option value="06">6月</option>
					<option value="07">7月</option>
					<option value="08">8月</option>
					<option value="09">9月</option>
					<option value="10">10月</option>
					<option value="11">11月</option>
					<option value="12">12月</option>
				</select>
				<div class="jump-link">跳转</div>
			</div>
			<div class="patient-pagination-wrapper">
				<!-- 时间列表总条目数 START-->
				<input type="hidden" value="50" id="advice_count" />
				<!-- 时间列表总条目数 END-->
				<ul class="pagination-container">
					<li class="page-num active">上一页</li>
					<li class="page-num">#n</li>
					<li class="page-num">#n</li>
					<li class="page-num">#n</li>
					<li class="page-num">#n</li>
					<li class="page-num">#n</li>
					<li class="page-num">下一页</li>
				</ul>
			</div>
		</div>
		<div class="page-mask"></div>
		<div class="page-details-mask">
			<!-- 医嘱内容显示 SATRT -->
			<div id="advice_show" class="patient-details">
				<div class="patient-details-table">
					<div class="table-details-head">
						医嘱
					</div>
					<div class="table-details-content">
						<span>这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。</span>
					</div>
				</div>
			</div>
			<!-- 医嘱内容显示 END -->

			<!-- 病人列表——增加医嘱 START -->
			<div id="advice_list" class="patient-details">
				<div class="patient-details-table">
					<div class="table-details-head">
						病人
					</div>
					<div class="table-details-content">
						<!-- 表格内容行 START -->
						<div class="table-details-tr">
							<span class="table-details-name">凌晓辉</span>
							<button class="table-details-add">
								<img src="/images/doc_web/u12.png" alt="" class="bg">
								<span class="bg">增加医嘱</span>
							</button>
						</div>
						<div class="table-details-tr">
							<span class="table-details-name">凌晓辉</span>
							<button class="table-details-add">
								<img src="/images/doc_web/u12.png" alt="" class="bg">
								<span class="bg">增加医嘱</span>
							</button>
						</div>
						<div class="table-details-tr">
							<span class="table-details-name">凌晓辉</span>
							<button class="table-details-add">
								<img src="/images/doc_web/u12.png" alt="" class="bg">
								<span class="bg">增加医嘱</span>
							</button>
						</div>
						<div class="table-details-tr">
							<span class="table-details-name">凌晓辉</span>
							<button class="table-details-add">
								<img src="/images/doc_web/u12.png" alt="" class="bg">
								<span class="bg">增加医嘱</span>
							</button>
						</div>
						<div class="table-details-tr">
							<span class="table-details-name">凌晓辉</span>
							<button class="table-details-add">
								<img src="/images/doc_web/u12.png" alt="" class="bg">
								<span class="bg">增加医嘱</span>
							</button>
						</div>
						<div class="table-details-tr">
							<span class="table-details-name">凌晓辉</span>
							<button class="table-details-add">
								<img src="/images/doc_web/u12.png" alt="" class="bg">
								<span class="bg">增加医嘱</span>
							</button>
						</div>
						<div class="table-details-tr">
							<span class="table-details-name">凌晓辉</span>
							<button class="table-details-add">
								<img src="/images/doc_web/u12.png" alt="" class="bg">
								<span class="bg">增加医嘱</span>
							</button>
						</div>						
						<!-- 表格内容行 END -->
					</div>
				</div>

				<!-- 分页 START -->
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
				<!-- 分页 END -->
			</div>
			<!-- 病人列表——增加医嘱 END -->

			<!-- 增加医嘱输入框 START -->
			<div id="advice_input" class="patient-details">
				<div class="patient-details-table table-details-input">
					<div class="table-details-head">
						凌晓辉医嘱
					</div>
					<div class="table-details-content">
						<textarea id="advice_content" class="textarea"></textarea>
					</div>
				</div>

				<div class="table-details-submit">
					<button class="submit-btn">
						<img src="/images/doc_web/u12.png" alt="" class="bg">
						<span class="bg">确定</span>
					</button>
				</div>
			</div>
			<!-- 增加医嘱输入框 END -->

		</div>
	</div>
	<script type="text/template" id="advice_template">
		<div class="table-tr table-tr-clickable">
			<div class="table-td table-td01">
				任性
			</div>
			<div class="table-td table-td02">
				好好吃药，别着凉了！
			</div>
		</div>
	</script>
@stop


@section("js-specify")
	@parent
	<script type="text/javascript" src="/dist/js/pages/doctor/advice.js"></script>
@stop