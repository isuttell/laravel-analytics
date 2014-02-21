<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorAnalyticsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('visitoranalytics', function($table)
		{
		    $table->increments('id');
		    $table->integer('user_id')->default(0);
		    $table->integer('ip'); //Store as a Long
		    $table->string('platform');
		    $table->string('browser');
		    $table->string('browser_version');
		    $table->boolean('crawler')->default(false);
		    $table->boolean('ismobiledevice')->default(false);
		    $table->smallInteger('cssversion')->default(0);
		    $table->string('lang');
		    $table->string('location');
		    $table->string('geo')->default('{}');
		    $table->string('user_agent');
		    $table->dateTime('last_activity');
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
		Schema::dropIfExists('visitoranalytics');
	}

}