<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id');            // comprador
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('created_by')->nullable(); // admin que gerou

            // Identificação
            $table->string('numero')->unique();               // CONT-2026-000001
            $table->string('template')->default('padrao');    // template blade usado

            // Dados do contrato (snapshot no momento da assinatura)
            $table->json('dados_comprador')->nullable();      // snapshot do user
            $table->json('dados_veiculo')->nullable();        // snapshot do vehicle
            $table->json('dados_pagamento')->nullable();      // snapshot do payment

            // Valores
            $table->decimal('valor_contrato', 12, 2)->nullable();
            $table->string('forma_pagamento')->nullable();

            // Status
            $table->enum('status', [
                'rascunho',       // gerado, não enviado
                'aguardando',     // enviado para assinatura
                'assinado',       // assinado por ambas as partes
                'cancelado',
            ])->default('rascunho');

            // Geolocalização da assinatura do cliente
            $table->decimal('lat_assinatura', 10, 7)->nullable();
            $table->decimal('lng_assinatura', 10, 7)->nullable();
            $table->string('endereco_assinatura')->nullable(); // endereço reverso via Google Maps API
            $table->string('ip_assinatura', 45)->nullable();
            $table->string('user_agent_assinatura')->nullable();

            // Timestamps de assinatura
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('assinado_em')->nullable();     // assinatura do cliente
            $table->timestamp('assinado_admin_em')->nullable(); // assinatura do admin

            // Hash de verificação de integridade
            $table->string('hash_verificacao', 64)->nullable()->unique();

            // Arquivo PDF gerado
            $table->string('pdf_path')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
