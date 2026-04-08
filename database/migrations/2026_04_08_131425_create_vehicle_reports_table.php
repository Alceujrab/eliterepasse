<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('criado_por')->nullable(); // usuário que criou

            // Identificação
            $table->string('numero')->unique();                   // REL-2026-000001
            $table->enum('tipo', [
                'vistoria_entrada',   // ao entrar no estoque
                'cautelar',           // laudo cautelar pré-venda
                'revisao',            // revisão periódica
                'avaria',             // registro de avaria
            ])->default('vistoria_entrada');

            // Status do laudo
            $table->enum('status', ['rascunho', 'em_revisao', 'aprovado', 'reprovado'])
                ->default('rascunho');

            // Avaliação geral
            $table->unsignedTinyInteger('nota_geral')->nullable(); // 0-10
            $table->text('conclusao')->nullable();
            $table->text('recomendacoes')->nullable();

            // Aprovação
            $table->unsignedBigInteger('aprovado_por')->nullable();
            $table->timestamp('aprovado_em')->nullable();

            // Arquivo PDF gerado
            $table->string('pdf_path')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_reports');
    }
};
