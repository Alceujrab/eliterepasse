<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate');
            $table->string('brand');
            $table->string('model');
            $table->string('version');
            $table->integer('manufacture_year');
            $table->integer('model_year');
            $table->integer('mileage')->default(0);
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->string('color')->nullable();
            $table->string('category')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('fipe_price', 10, 2)->nullable();
            $table->decimal('profit_margin', 10, 2)->nullable();
            $table->json('accessories')->nullable();
            $table->json('media')->nullable();
            $table->json('location')->nullable();
            $table->enum('status', ['available', 'reserved', 'sold'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
