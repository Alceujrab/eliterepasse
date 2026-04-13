<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // admin que registrou

            // Documentos do pedido
            $table->string('tipo_documento'); // atpv, atpve, transferencia, outro
            $table->string('titulo')->nullable();
            $table->string('file_path')->nullable(); // arquivo do documento
            $table->string('nome_original')->nullable();

            // Envio / Rastreio
            $table->string('metodo_envio')->nullable(); // correios, transportadora, motoboy, retirada, outro
            $table->string('metodo_envio_detalhe')->nullable(); // nome da transportadora, etc
            $table->string('codigo_rastreio')->nullable();
            $table->string('comprovante_despacho_path')->nullable(); // comprovante de envio
            $table->string('comprovante_despacho_nome')->nullable();
            $table->timestamp('despachado_em')->nullable();

            $table->string('status')->default('disponivel'); // disponivel, despachado, entregue
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
    }
};
