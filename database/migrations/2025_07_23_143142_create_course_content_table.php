<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
             Schema::create('course_contents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_description_id')->constrained('course_description')->onDelete('cascade');
                $table->string('course_title');
                $table->string('slug')->unique();
                $table->json('materials'); // Store materials as JSON array
                $table->timestamps();

                // Index untuk performa
                $table->index('course_description_id');
                $table->index('slug');
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_content');
    }
};
