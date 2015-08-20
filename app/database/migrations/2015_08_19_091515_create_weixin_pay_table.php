<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeixinPayTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'weixin_pay', function( $table ){

			$table->timestamps();
			
			// 订单号
			$table->string('trade_no');

			// 交易类型
			$table->string('trade_type');

			// 交易状态
			$table->string('status');

			// 创建订单时间
			$table->string('time_start');
			
			// 支付完成时间
			$table->string('time_end')->nullable();

			// 支付失效时间
			$table->string('time_expire')->nullable();

			/* 

			 附加数据：生成订单时附带数据，查询订单和支付通知时附带
			 	{
			 		period_id': , 
			 		user_id: 
			 	}
			 */
			$table->string('attach')->nullable();

			// 订单费用
			$table->integer('total_fee')->unsigned();
			
			// 业务结果
			$table->string('result_code')->nullable();
			
			// 错误代码
			$table->string('error_code')->nullable();
			
			// 错误代码描述
			$table->string('error_message')->nullable();
			
			// 用户id
			$table->integer( 'user_id' )->unsigned();

			// 对应挂号记录id，支付成功时
			$table->integer('record_id')->unsigned()->nullable();

			// 微信用户open_id
			$table->string('open_id')->nullable();

			$table->index( 'user_id' );
			$table->index( 'open_id' );
			$table->primary( 'trade_no' );
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists( 'weixin_pay' );
	}

}
