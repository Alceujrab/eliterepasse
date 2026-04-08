<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Tipo do documento
            if (! Schema::hasColumn('documents', 'tipo')) {
                $table->enum('tipo', [
                    'crv',              // Certificado de Registro de Veículo
                    'crlv',             // Certificado de Registro e Licenciamento
                    'laudo_vistoria',   // Laudo de vistoria
                    'laudo_cautelar',   // Laudo cautelar
                    'nota_fiscal',      // Nota fiscal de compra
                    'historico_ipva',   // IPVA pago
                    'historico_multas', // Consulta de multas
                    'contrato_compra',  // Contrato de compra
                    'cnh',              // CNH do vendedor/comprador
                    'outro',
                ])->default('outro')->after('vehicle_id');
            }

            // Status de verificação
            if (! Schema::hasColumn('documents', 'status')) {
                $table->enum('status', ['pendente', 'verificado', 'rejeitado'])->default('pendente')->after('tipo');
            }

            // Metadados do arquivo
            if (! Schema::hasColumn('documents', 'nome_original')) {
                $table->string('nome_original')->nullable()->after('title');
            }
            if (! Schema::hasColumn('documents', 'mime_type')) {
                $table->string('mime_type')->nullable();
            }
            if (! Schema::hasColumn('documents', 'tamanho')) {
                $table->unsignedInteger('tamanho')->nullable();
            }

            // Quem verificou
            if (! Schema::hasColumn('documents', 'verificado_por')) {
                $table->unsignedBigInteger('verificado_por')->nullable();
            }
            if (! Schema::hasColumn('documents', 'verificado_em')) {
                $table->timestamp('verificado_em')->nullable();
            }
            if (! Schema::hasColumn('documents', 'motivo_rejeicao')) {
                $table->text('motivo_rejeicao')->nullable();
            }
            if (! Schema::hasColumn('documents', 'validade')) {
                $table->date('validade')->nullable(); // ex: CRLV vence em X/ano
            }
            if (! Schema::hasColumn('documents', 'observacoes')) {
                $table->text('observacoes')->nullable();
            }

            // Visibilidade
            if (! Schema::hasColumn('documents', 'visivel_cliente')) {
                $table->boolean('visivel_cliente')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'tipo', 'status', 'nome_original', 'mime_type', 'tamanho',
                'verificado_por', 'verificado_em', 'motivo_rejeicao',
                'validade', 'observacoes', 'visivel_cliente',
            ]);
        });
    }
};
