<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->integer('sales_agent_id')->nullable();
            $table->integer('transfer_by_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('merchant_id')->nullable();
            $table->integer('currency')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('service')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->decimal('recurring', 8, 2)->nullable();
            $table->string('sale_upsell')->nullable();
            $table->string('department')->nullable();
            $table->string('type')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('refund_cb', 8, 2)->nullable()->default(0.00);
            $table->date('refund_cb_date')->nullable();
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
        Schema::dropIfExists('admin_invoices');
    }
}
