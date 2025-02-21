<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePressReleaseFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('press_release_forms', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('agent_id')->nullable();
            $table->string('book_title')->nullable();
            $table->string('author_name')->nullable();
            $table->string('genre')->nullable();
            $table->string('publisher')->nullable();
            $table->string('publication_date')->nullable();
            $table->string('isbn')->nullable();

            $table->text('synopsis')->nullable();
            $table->string('unique_selling_points')->nullable();
            $table->string('target_audience')->nullable();

            $table->text('short_biography')->nullable();
            $table->string('previous_works')->nullable();
            $table->string('award_recognition')->nullable();

            $table->text('quote_excerpts')->nullable();
            $table->text('reviews')->nullable();
            $table->text('tie_ins')->nullable();

            $table->string('formats_and_availability')->nullable();
            $table->string('price')->nullable();
            $table->string('events')->nullable();
            $table->string('media_kit')->nullable();

            $table->string('press_contact')->nullable();
            $table->string('twitter')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('publishers_contact')->nullable();

            $table->text('cta')->nullable();
            $table->string('book_cover_image')->nullable();
            $table->string('author_photo')->nullable();

            $table->text('key_highlights')->nullable();

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
        Schema::dropIfExists('press_release_forms');
    }
}
