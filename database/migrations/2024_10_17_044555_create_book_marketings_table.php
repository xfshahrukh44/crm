<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookMarketingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_marketings', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('agent_id')->nullable();
            $table->text('client_name')->nullable();
            $table->text('project_name')->nullable();
            $table->text('package_chosen')->nullable();
            $table->text('project_duration')->nullable();
            $table->text('desired_results')->nullable();
            $table->text('business_name')->nullable();
            $table->text('business_email')->nullable();
            $table->text('business_contact')->nullable();
            $table->text('business_address')->nullable();
            $table->text('business_location')->nullable();
            $table->text('business_website_url')->nullable();
            $table->text('business_working_hours')->nullable();
            $table->text('where_is_your_book_published')->nullable();
            $table->text('business_category')->nullable();
            $table->text('facebook_link')->nullable();
            $table->text('instagram_link')->nullable();
            $table->text('instagram_password')->nullable();
            $table->text('twitter_link')->nullable();
            $table->text('twitter_password')->nullable();
            $table->text('linkedin_link')->nullable();
            $table->text('pinterest_link')->nullable();
            $table->text('pinterest_password')->nullable();
            $table->text('youtube_link')->nullable();
            $table->text('youtube_gmail')->nullable();
            $table->text('youtube_gmail_password')->nullable();
            $table->text('social_media_platforms')->nullable();
            $table->text('target_locations')->nullable();
            $table->text('target_audiences')->nullable();
            $table->text('age_bracket')->nullable();
            $table->text('keywords')->nullable();
            $table->text('unique_selling_points')->nullable();
            $table->text('exclude_information')->nullable();
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
        Schema::dropIfExists('book_marketings');
    }
}
