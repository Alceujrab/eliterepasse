<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_data', function (Blueprint $table) {
            $table->id();
            // Relação polimórfica (order pode ter dados de PIX ou Cartão)
            $table->morphs('payable');
            $table->string('tipo'); // pix, cartao, boleto, financiamento, ted
            $table->json('dados');  // Dados específicos do método
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_data');
    }
};
