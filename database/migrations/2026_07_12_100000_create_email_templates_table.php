<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nome');
            $table->string('assunto');
            $table->string('saudacao')->nullable();
            $table->text('corpo');
            $table->string('texto_acao')->nullable();
            $table->string('url_acao')->nullable();
            $table->text('texto_rodape')->nullable();
            $table->json('variaveis_disponiveis')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
