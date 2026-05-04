<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rrt_page_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('language', 10);
            $table->string('name', 256)->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
            
            $table->foreign('page_id')->references('id')->on('rrt_pages')->onDelete('cascade');
            $table->unique(['page_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rrt_page_translations');
    }
}; 