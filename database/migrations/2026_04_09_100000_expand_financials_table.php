<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financials', function (Blueprint $table) {
            $table->string('numero')->unique()->nullable()->after('order_id');
            $table->string('descricao')->nullable()->after('numero');
            $table->decimal('valor', 12, 2)->default(0)->after('descricao');
            $table->string('forma_pagamento')->nullable()->after('valor');
            $table->date('data_vencimento')->nullable()->after('forma_pagamento');
            $table->date('data_pagamento')->nullable()->after('data_vencimento');
            $table->foreignId('criado_por')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->text('observacoes')->nullable()->after('criado_por');

            // Atualizar status default de 'pending' para 'em_aberto'
            $table->string('status')->default('em_aberto')->change();
        });
    }

    public function down(): void
    {
        Schema::table('financials', function (Blueprint $table) {
            $table->dropForeign(['criado_por']);
            $table->dropColumn([
                'numero', 'descricao', 'valor', 'forma_pagamento',
                'data_vencimento', 'data_pagamento', 'criado_por', 'observacoes',
            ]);
            $table->string('status')->default('pending')->change();
        });
    }
};
