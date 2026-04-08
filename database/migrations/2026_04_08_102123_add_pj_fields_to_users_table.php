<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dados PJ
            $table->string('razao_social')->nullable()->after('name');
            $table->string('nome_fantasia')->nullable()->after('razao_social');
            $table->string('cnpj', 18)->nullable()->unique()->after('nome_fantasia');
            $table->string('inscricao_estadual')->nullable()->after('cnpj');

            // Endereço
            $table->string('cep', 9)->nullable()->after('inscricao_estadual');
            $table->string('logradouro')->nullable()->after('cep');
            $table->string('numero', 10)->nullable()->after('logradouro');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('bairro')->nullable()->after('complemento');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');

            // Login Social
            $table->string('social_id')->nullable()->after('estado');
            $table->string('social_provider')->nullable()->after('social_id');
            $table->string('avatar_url')->nullable()->after('social_provider');

            // Observações
            $table->text('observacoes')->nullable()->after('avatar_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'razao_social', 'nome_fantasia', 'cnpj', 'inscricao_estadual',
                'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
                'social_id', 'social_provider', 'avatar_url', 'observacoes',
            ]);
        });
    }
};
