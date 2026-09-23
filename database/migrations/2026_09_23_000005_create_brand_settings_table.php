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
        Schema::create('brand_settings', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('primary_color', 30)->nullable();
            $table->string('secondary_color', 30)->nullable();
            $table->string('whatsapp_number', 50)->nullable();
            $table->string('contact_email')->nullable();
            $table->text('about_text')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_settings');
    }
};
