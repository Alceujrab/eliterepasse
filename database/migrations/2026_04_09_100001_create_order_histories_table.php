<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status_de')->nullable();       // status anterior
            $table->string('status_para');                  // novo status
            $table->string('acao');                         // ex: "pedido_criado", "contrato_gerado", "fatura_gerada", "pagamento_confirmado"
            $table->text('descricao')->nullable();          // descrição legível
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // quem fez
            $table->json('dados')->nullable();              // dados extras (contrato_id, financial_id, etc.)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_histories');
    }
};
