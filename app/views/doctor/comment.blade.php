@extends("layouts.master_web")

@section("title")
	病人评价
@stop

@section("css")
	@parent
    <link rel="stylesheet" type="text/css" href="/dist/css/doctor/comment.css" />

@stop


@section("main-content")
	<div class="patient-wrapper">
		<div class="patient-caption">病人评价</div>
		<div class="patient-table">
			<!-- 表格头 START -->
			<div class="table-tr table-tr-caption">
				<div class="table-td table-td01">
					姓名
				</div>
				<div class="table-td table-td02">
					评价
				</div>
			</div>
			<!-- 表格头 END -->

			<!-- 表格内容 START -->
			<div class="comments-content-wrapper">
				<!-- <div class="table-tr table-tr-clickable">
					<div class="table-td table-td01">
						任冰
					</div>
					<div class="table-td table-td02">
						医生不错，很后即可几个客人价格，认真细心。
					</div>
				</div>
				<div class="table-tr table-tr-clickable">
					<div class="table-td table-td01">
						任冰
					</div>
					<div class="table-td table-td02">
						医生不错，很后即可几个客人价格，认真细心。
					</div>
				</div> -->
			</div>			
			<!-- 表格内容 END -->
		</div>

		<div class="patient-footer">
			<div class="patient-select">
				<select class="patient-year">
					@for ( $ys = $year_start - 1; $ys <= $year + 1; ++$ys )
						@if ( $ys == $year )
						<option value="{{{ $ys }}}" selected>{{{ $ys }}}年</option>
						@else
						<option value="{{{ $ys }}}">{{{ $ys }}}年</option>
						@endif
					@endfor
				</select>
				<select class="patient-month">
					@for ( $m = 1; $m != 13; ++$m )
						@if ( $m == $month )
							<option value="{{{ sprintf('%02d', $m) }}}" selected>{{{ $m }}}月</option>
						@else
							<option value="{{{ sprintf('%02d', $m) }}}">{{{ $m }}}月</option>
						@endif
					@endfor
				</select>
				<div class="jump-link">跳转</div>
			</div>
			<div class="pagination-wrapper">
				<!-- 时间列表总条目数 START-->
				<input type="hidden" value="{{{ $total_page }}}" id="comment_count" />
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
			<div class="patient-details">
				<div class="patient-details-table">
					<div class="table-details-head">
						评价内容
					</div>
					<div class="table-details-content">
						<span>这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。这个是评价内容框。</span>
					</div>
				</div>

			</div>
		</div>
	</div>
	<script type="text/template" id="comment_template">
		<% for(var i = 0; i < array.length; i ++){ %>
		<div class="table-tr table-tr-clickable">
			<div class="table-td table-td01">
				<%- array[i]["user_name"] %>
			</div>
			<div class="table-td table-td02">
				<%- array[i]["content"] %>
			</div>
		</div>
		<% } %>
	</script>
@stop
@section("js-specify")
	@parent
	<script type="text/javascript" src="/dist/js/pages/doctor/comment.js"></script>
@stop
