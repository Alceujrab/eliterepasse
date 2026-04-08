<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_report_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_report_id');

            // Grupo / seção do laudo
            $table->string('grupo');   // Motor, Carroceria, Elétrica, Interior, Documentos, etc.
            $table->string('item');    // Descrição do item verificado
            $table->enum('resultado', ['ok', 'atencao', 'reprovado', 'nao_avaliado'])
                ->default('nao_avaliado');
            $table->text('observacao')->nullable();
            $table->integer('ordem')->default(0);

            // Fotos do item (JSON com paths)
            $table->json('fotos')->nullable();

            $table->timestamps();

            $table->foreign('vehicle_report_id')
                ->references('id')->on('vehicle_reports')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_report_items');
    }
};
