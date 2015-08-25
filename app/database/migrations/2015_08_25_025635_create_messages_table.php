<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'messages', function( $table ){
			$table->increments( 'id' );
			$table->integer( 'from_uid' )->unsigned()->index();
			$table->integer( 'to_uid' )->unsigned()->index();
			$table->integer( 'timestamp' )->unsigned()->index();
			$table->text( 'content' );
			$table->integer( 'status' ); // 0 - read, 1 - unread, 2 - expire
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists( 'messages' );
	}

}
