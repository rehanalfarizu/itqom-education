<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            // Tambahkan kolom foreign key
            $table->foreignId('course_description_id')->constrained('course_description')->onDelete('cascade');

            $table->string('title')->nullable();
            $table->string('instructor')->nullable();
            $table->integer('video_count')->nullable();
            $table->string('duration')->nullable();
            $table->decimal('original_price', 10, 2)->nullable(); // Ubah ke decimal
            $table->decimal('price', 10, 2)->nullable(); // Ubah ke decimal
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
