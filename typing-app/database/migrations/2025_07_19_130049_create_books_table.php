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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('drive_file_id')->unique(); // Google Drive file ID
            $table->string('drive_path')->nullable(); // Path in user's Drive
            $table->string('file_type')->default('epub'); // epub, system_book
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // EPUB metadata
            $table->boolean('is_system_book')->default(false); // Open source books
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
