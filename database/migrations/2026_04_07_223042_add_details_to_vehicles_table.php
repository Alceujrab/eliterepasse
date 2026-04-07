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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->boolean('has_report')->default(false)->after('status');
            $table->boolean('has_factory_warranty')->default(false)->after('has_report');
            $table->boolean('is_on_sale')->default(false)->after('has_factory_warranty');
            $table->boolean('is_just_arrived')->default(false)->after('is_on_sale');
            $table->string('engine')->nullable()->after('transmission');
            $table->integer('doors')->nullable()->after('color');
            $table->string('fipe_code')->nullable()->after('sale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'has_report',
                'has_factory_warranty',
                'is_on_sale',
                'is_just_arrived',
                'engine',
                'doors',
                'fipe_code',
            ]);
        });
    }
};
