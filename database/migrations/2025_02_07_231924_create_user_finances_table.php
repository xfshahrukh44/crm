<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFinancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_finances', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->decimal('daily_target', 8, 2)->default(0.00);
            $table->decimal('monthly_target', 8, 2)->default(0.00);
            $table->decimal('daily_printing_costs', 8, 2)->default(0.00);
            $table->decimal('monthly_printing_costs', 8, 2)->default(0.00);
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
        Schema::dropIfExists('user_finances');
    }
}
