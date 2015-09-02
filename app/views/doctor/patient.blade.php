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
			<div class="table-container">
				<!-- <div class="patient-tr patient-table-content">
					<div class="patient-td">04月27日</div>
					<div class="patient-td">
						<button type="button" disabled="disabled" class="patient-td-btn">
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
				</div> -->
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
					<option value="2015" selected>2015年</option>
				</select>
				<select class="patient-month">
					<option value="01">1月</option>
					<option value="02">2月</option>
					<option value="03">3月</option>
					<option value="04">4月</option>
					<option value="05">5月</option>
					<option value="06">6月</option>
					<option value="07">7月</option>
					<option value="08" selected>8月</option>
					<option value="09">9月</option>
					<option value="10">10月</option>
					<option value="11">11月</option>
					<option value="12">12月</option>
				</select>
				<div class="jump-link">跳转</div>
			</div>
			<div class="pagination-wrapper">
				<!-- 时间列表总条目数 START-->
				<input type="hidden" value="{{{ $total_current }}}" id="schedule_count" />
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
		<div class="patient-mask"></div>
		<div class="patient-details-mask">
			<div class="patient-details">
				<div class="patient-details-table">
					<div class="patient-details-tr patient-details-caption">
						<div class="patient-details-td patient-details-td01">04月27日</div>
						<div class="patient-details-td patient-details-td02">病人</div>
					</div>
					<!-- 病人列表 START -->
					<div class="patient-details-container">
						<!-- <div class="patient-details-tr patient-details-content">
							<div class="patient-details-td patient-details-td01">08：20</div>
							<div class="patient-details-td patient-details-td02">
								阿拉登
								<button class="patient-status-btn">
									<img src="/images/doc_web/u12_d.png" alt="" class="bg">
									<span class="bg">已就诊</span>
								</button>
								<button class="patient-status-btn">
									<img src="/images/doc_web/u12.png" alt="" class="bg">
									<span class="bg">未就诊</span>
								</button>
							</div>

						</div> -->
					</div>
					<!-- 病人列表 END -->
				</div>

				<div class="patient-pagination-wrapper pagination-wrapper">
					<!-- 病人列表总条目数 START-->
					<input type="hidden" value="50" id="patient_count" />
					<!-- 病人列表总条目数 END-->
					<ul class="details-pagination-container">
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
		</div>
	</div>
	<!-- 日期列表 START -->
	<script type="text/template" id="patient_date_list">
		<% for(var d in array){ %>
		<div class="patient-tr patient-table-content">
			<div class="patient-td"><%- d %></div>
			<% if(array[d][0]){ %>
			<div class="patient-td">
				<button data-id="<%- array[d][0]['id'] %>" data-period="<%- array[d][0]['period']%>" type="button" class="patient-td-btn">
					<img src="/images/doc_web/u12.png" alt="" class="bg">
					<span class="bg">查看病人</span>
				</button>
			</div>
			<% } %>
			<% if(array[d][1]){ %>
			<div class="patient-td">
				<button data-id="<%- array[d][1]['id'] %>" data-period="<%- array[d][1]['period']%>" type="button" class="patient-td-btn">
					<img src="/images/doc_web/u12.png" alt="" class="bg">
					<span class="bg">查看病人</span>
				</button>
			</div>
			<% } %>
		</div>
		<% } %>
	</script>
	<!-- 日期列表 END -->

	<!-- 病人列表 START -->
	<script type="text/template" id="patient_list">
		<% for(var i = 0; i < array.length; i ++){ 
				array[i]["time"] = array[i]["time"].replace(/(:00)$/,""); 
				if(array[i]['status'] == 0){
					var status = "block";
				}
				else{
					var status = "none";
				}
		%>
		<div class="patient-details-tr patient-details-content">
			<div class="patient-details-td patient-details-td01"><%- array[i]["time"] %></div>
			<div class="patient-details-td patient-details-td02" data-id="<%- array[i]['id'] %>">
				<%- array[i]["name"] %>
				<button class="patient-status-btn">
					<img src="/images/doc_web/u12_d.png" alt="" class="bg">
					<span class="bg">已就诊</span>
				</button>
				<button class="patient-status-btn patient-status-not" style="display:<%- status %>;">
					<img src="/images/doc_web/u12.png" alt="" class="bg">
					<span class="bg">未就诊</span>
				</button>
			</div>
		</div>
		<% } %>
	</script>
	<!-- 病人列表 END -->

@stop

@section("js-specify")
	@parent
	<script type="text/javascript" src="/dist/js/pages/doctor/patient.js"></script>
@stop