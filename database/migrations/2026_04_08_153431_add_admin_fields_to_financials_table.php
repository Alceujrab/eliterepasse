<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financials', function (Blueprint $table) {
            $table->string('numero_fatura')->nullable()->after('order_id');       // Ex: FAT-2026-001234
            $table->decimal('valor', 15, 2)->nullable()->after('numero_fatura');  // Valor cobrado
            $table->date('data_vencimento')->nullable()->after('valor');
            $table->date('data_pagamento')->nullable()->after('data_vencimento');
            $table->string('forma_pagamento')->nullable()->after('data_pagamento'); // boleto, pix, transferencia
            $table->string('nota_fiscal_numero')->nullable()->after('forma_pagamento');
            $table->text('observacoes')->nullable()->after('nota_fiscal_numero');
            $table->unsignedBigInteger('criado_por')->nullable()->after('observacoes');
            $table->foreign('criado_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('financials', function (Blueprint $table) {
            $table->dropForeign(['criado_por']);
            $table->dropColumn(['numero_fatura', 'valor', 'data_vencimento', 'data_pagamento',
                'forma_pagamento', 'nota_fiscal_numero', 'observacoes', 'criado_por']);
        });
    }
};
