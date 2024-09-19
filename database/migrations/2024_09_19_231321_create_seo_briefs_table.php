<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeoBriefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_briefs', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('agent_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('website_url')->nullable();
            $table->text('business_address')->nullable();
            $table->string('industry_or_niche')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->text('primary_business_goals')->nullable();
            $table->text('main_seo_objectives')->nullable();
            $table->text('kpis')->nullable();
            $table->text('primary_target_audience')->nullable();
            $table->text('secondary_niche')->nullable();
            $table->text('geographical_areas')->nullable();
            $table->integer('previously_done_seo')->nullable();
            $table->text('top_three_competitors')->nullable();
            $table->text('target_keywords')->nullable();
            $table->text('target_keywords_2')->nullable();
            $table->text('local_specific_or_geographical')->nullable();
            $table->integer('have_store')->nullable();
            $table->text('ga_gsc_admin_access')->nullable();
            $table->text('additional_comments')->nullable();
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
        Schema::dropIfExists('seo_briefs');
    }
}
