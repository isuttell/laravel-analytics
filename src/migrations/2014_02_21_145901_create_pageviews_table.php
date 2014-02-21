<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageviewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pageviews', function($table)
		{
		    $table->increments('id');
		    $table->integer('visitoranalytics_id')->default(0);
		    $table->integer('status')->default(200);
		    $table->string('method')->nullable();
		    $table->boolean('ajax')->default(false);
		    $table->string('url');
		    $table->string('referrer')->nullable();
		    $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('pageviews');
	}

}