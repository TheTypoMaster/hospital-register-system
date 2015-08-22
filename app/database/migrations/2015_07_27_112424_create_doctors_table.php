<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'doctors', function( $table ){
			$table->increments( 'id' );
			$table->string( 'name' );
			$table->string( 'photo' );
			$table->string( 'title')->nullable();
			$table->string( 'specialty' )->nullable();
			$table->text( 'description' )->nullable();
			$table->float( 'register_fee' )->default( 0.0 );
			$table->boolean( 'is_chief' )->default( false );
			$table->boolean( 'is_consultable' )->default( true );
			$table->integer( 'department_id' )->unsigned();
			$table->integer( 'user_id' )->unsigned();
			$table->timestamps();

			$table->index( 'user_id' );
			$table->foreign( 'user_id' )
				  ->references( 'id' )
				  ->on( 'users' )
				  ->onDelete( 'cascade' )
				  ->onUpdate( 'cascade' );

			$table->index( 'department_id' );
			$table->foreign( 'department_id' )
				  ->references( 'id' )
				  ->on( 'departments' )
				  ->onUpdate( 'cascade' );
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists( 'doctors' );
	}

}
