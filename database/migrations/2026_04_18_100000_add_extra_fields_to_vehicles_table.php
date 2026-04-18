<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->text('description')->nullable()->after('fipe_code');
            $table->string('renavam', 11)->nullable()->after('plate');
            $table->string('chassi', 17)->nullable()->after('renavam');
            $table->unsignedTinyInteger('num_owners')->nullable()->after('mileage');
            $table->string('steering', 30)->nullable()->after('engine');
            $table->string('video_url')->nullable()->after('media');
            $table->boolean('accepts_trade')->default(false)->after('is_just_arrived');
            $table->boolean('ipva_paid')->default(false)->after('accepts_trade');
            $table->boolean('licensing_ok')->default(false)->after('ipva_paid');
            $table->boolean('is_armored')->default(false)->after('licensing_ok');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'renavam',
                'chassi',
                'num_owners',
                'steering',
                'video_url',
                'accepts_trade',
                'ipva_paid',
                'licensing_ok',
                'is_armored',
            ]);
        });
    }
};
