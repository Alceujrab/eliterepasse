<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evolution_instances', function (Blueprint $table) {
            $table->string('status_conexao')->default('close')->change();
        });
    }

    public function down(): void
    {
        Schema::table('evolution_instances', function (Blueprint $table) {
            $table->integer('status_conexao')->default(0)->change();
        });
    }
};
