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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('format'); // txt, pdf, md, etc
            $table->longText('original_content');
            $table->integer('file_size');
            $table->timestamp('file_created_at')->nullable();
            $table->timestamp('file_modified_at')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();
            
            $table->index('file_path');
            $table->index('is_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
