<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evolution_instances', function (Blueprint $table) {
            $table->id();
            $table->string('nome');                       // Nome identificador ex: "Principal"
            $table->string('instancia');                  // Nome da instância na Evolution API
            $table->string('url_base');                   // ex: https://api.auto.inf.br
            $table->text('api_key');                      // Token de autenticação
            $table->boolean('ativo')->default(true);
            $table->boolean('padrao')->default(false);    // Instância padrão do sistema
            $table->integer('status_conexao')->default(0); // 0=desconhecido, 1=conectado, 2=desconectado
            $table->timestamp('verificado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evolution_instances');
    }
};
