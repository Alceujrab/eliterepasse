<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Substituir company_id por user_id (comprador direto)
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('vehicle_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('vehicle_id');

            // Valores financeiros
            $table->decimal('valor_compra', 12, 2)->nullable()->after('payment_method_id');
            $table->decimal('valor_fipe', 12, 2)->nullable()->after('valor_compra');

            // Status detalhado
            $table->string('status')->default('pendente')->change(); // pendente, confirmado, cancelado, faturado

            // Observações
            $table->text('observacoes')->nullable();

            // Data de confirmação
            $table->timestamp('confirmado_em')->nullable();
            $table->unsignedBigInteger('confirmado_por')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id', 'vehicle_id']);
            $table->dropColumn(['user_id', 'vehicle_id', 'payment_method_id', 'valor_compra', 'valor_fipe', 'observacoes', 'confirmado_em', 'confirmado_por']);
        });
    }
};
