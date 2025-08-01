<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_description', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('tag')->nullable();
            $table->text('overview');
            $table->string('image_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->decimal('price', 15, 2);
            $table->decimal('price_discount', 15, 2)->nullable();
            $table->string('instructor_name')->nullable();
            $table->string('instructor_position')->nullable();
            $table->string('instructor_image_url')->nullable();
            $table->integer('video_count')->default(0);
            $table->integer('duration')->default(0);
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_description');
    }
};


