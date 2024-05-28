<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskStatusChangedLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_status_changed_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('task_id');
            $table->integer('user_id')->nullable();
            $table->string('column')->nullable();
            $table->integer('old')->nullable();
            $table->integer('new')->nullable();
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
        Schema::dropIfExists('task_status_changed_logs');
    }
}
