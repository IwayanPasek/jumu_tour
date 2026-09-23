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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            // Foreign key ke regions dengan aturan restrictOnDelete (tidak boleh terhapus jika masih memiliki destinasi)
            $table->foreignId('region_id')
                ->constrained('regions')
                ->restrictOnDelete();

            // Foreign key ke categories dengan aturan nullOnDelete (jika kategori dihapus, destinasi tetap utuh)
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            
            // Presisi koordinat desimal untuk Google Maps (akurasi skala centimeter/meter)
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();

            // Index yang relevan untuk optimasi query katalog dan filter
            $table->index('region_id');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('display_order');
            $table->index(['is_active', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
