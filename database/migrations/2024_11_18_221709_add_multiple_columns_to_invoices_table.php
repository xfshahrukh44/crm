<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnsToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('recurring', 10, 2)->nullable();
            $table->string('sale_or_upsell')->nullable();
            $table->string('transfer_by')->nullable();
            $table->string('card_or_wire')->nullable();
            $table->decimal('refunded_cb', 10, 2)->nullable();
            $table->date('refund_cb_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
