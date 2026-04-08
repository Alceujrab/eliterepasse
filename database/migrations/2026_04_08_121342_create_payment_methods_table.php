<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('nome');                         // PIX, Cartão, Boleto, Financiamento
            $table->string('tipo')->unique();               // pix, cartao, boleto, financiamento
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0);
            $table->json('config')->nullable();             // Configurações específicas do tipo
            $table->timestamps();
        });

        // Seeds padrão
        DB::table('payment_methods')->insert([
            ['nome' => 'PIX', 'tipo' => 'pix', 'ativo' => true, 'ordem' => 1,
             'config' => json_encode(['campos' => ['chave_pix', 'qr_code']]),
             'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Cartão de Crédito', 'tipo' => 'cartao', 'ativo' => true, 'ordem' => 2,
             'config' => json_encode(['campos' => ['bandeira', 'max_parcelas']]),
             'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Boleto Bancário', 'tipo' => 'boleto', 'ativo' => true, 'ordem' => 3,
             'config' => json_encode(['campos' => ['vencimento']]),
             'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Financiamento', 'tipo' => 'financiamento', 'ativo' => true, 'ordem' => 4,
             'config' => json_encode(['campos' => ['banco', 'prazo', 'taxa']]),
             'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Transferência (TED/DOC)', 'tipo' => 'ted', 'ativo' => true, 'ordem' => 5,
             'config' => json_encode(['campos' => ['banco', 'agencia', 'conta']]),
             'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
