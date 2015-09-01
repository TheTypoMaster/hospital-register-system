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
		<div id="message_content" class="patient-table">
			<!-- 表格内容 START -->
			<!-- <div class="table-tr table-tr-clickable">
				<div class="table-td table-td01">
					2015.4.30
				</div>
				<div class="table-td table-td02">
					好好吃药，别着凉了！每日三次，每次10颗，饭后服用，忌吃辛辣。
				</div>
			</div> -->
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
				<input type="hidden" value="30" id="message_count" />
				<!-- 时间列表总条目数 END-->
				<ul id="message_pagination">
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
			<div class="patient-details">
				<div class="patient-details-table">
					<div class="table-details-head">
						消息内容
					</div>
					<div id="message_details" class="table-details-content">
						<span>s什么也没有</span>
					</div>
				</div>

			</div>
		</div>
	</div>
	<script type="text/template" id="message_template">
		<% for(var i = 0; i < array.length; i++){ if(array[i]["status"] == 3) var style='style="color:#14aef4;"' %>
			<div class="table-tr table-tr-clickable" data-status="<%- array[i]['status'] %>" data-id="<%- array[i]['id'] %>" <%- style %> >
				<div class="table-td table-td01">
					<%- array[i]["time"] %>
				</div>
				<div class="table-td table-td02" >
					<%- array[i]["content"] %>
				</div>
			</div>
		<% } %>
	</script>
@stop


@section("js-specify")
	@parent
	
	<script type="text/javascript" src="/dist/js/pages/doctor/message.js"></script>
@stop