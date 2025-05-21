<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->text('comments')->nullable();
            $table->integer('comments_id')->nullable();
            $table->timestamp('comments_timestamp')->nullable();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->text('comments')->nullable();
            $table->integer('comments_id')->nullable();
            $table->timestamp('comments_timestamp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
