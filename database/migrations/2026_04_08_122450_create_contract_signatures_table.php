<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dropar se existir sem FK (foi criada na tentativa anterior sem FK)
        Schema::dropIfExists('contract_signatures');

        Schema::create('contract_signatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->enum('tipo', ['comprador', 'vendedor', 'testemunha']);
            $table->string('nome');
            $table->string('documento')->nullable();           // CPF/CNPJ

            // Assinatura (base64 da assinatura desenhada ou token)
            $table->longText('assinatura_base64')->nullable();
            $table->string('token_assinatura', 64)->nullable()->unique(); // link seguro

            // Geolocalização
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('endereco_geo')->nullable();
            $table->string('ip', 45)->nullable();

            $table->timestamp('assinado_em')->nullable();
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_signatures');
    }
};
