<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateColumnToAdminInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_invoices', function (Blueprint $table) {
            $table->date('date')->nullable();
            $table->string('service_name')->nullable();
            $table->string('sales_person_name')->nullable();
            $table->string('transfer_by_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('sr_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_invoices', function (Blueprint $table) {
            //
        });
    }
}
