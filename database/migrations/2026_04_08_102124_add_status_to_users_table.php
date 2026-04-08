<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pendente', 'ativo', 'bloqueado'])
                ->default('pendente')
                ->after('is_admin');

            $table->timestamp('aprovado_em')->nullable()->after('status');
            $table->unsignedBigInteger('aprovado_por')->nullable()->after('aprovado_em');
        });

        // Admin existente fica ativo automaticamente
        DB::table('users')->where('is_admin', true)->update(['status' => 'ativo']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'aprovado_em', 'aprovado_por']);
        });
    }
};
