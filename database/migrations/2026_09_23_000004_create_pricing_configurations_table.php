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
        Schema::create('pricing_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('base_price', 12, 2);
            $table->decimal('price_per_kilometer', 12, 2);
            $table->decimal('extra_passenger_price', 12, 2)->default(0);
            $table->decimal('parking_fee', 12, 2)->default(0);
            $table->decimal('toll_fee', 12, 2)->default(0);
            $table->decimal('night_service_fee', 12, 2)->default(0);
            $table->decimal('minimum_price', 12, 2)->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_configurations');
    }
};
