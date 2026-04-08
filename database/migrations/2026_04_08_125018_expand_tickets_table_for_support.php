<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Identificação
            if (! Schema::hasColumn('tickets', 'numero')) {
                $table->string('numero')->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('tickets', 'titulo')) {
                $table->string('titulo')->nullable()->after('numero');
            }

            // Prioridade e categoria
            if (! Schema::hasColumn('tickets', 'prioridade')) {
                $table->enum('prioridade', ['baixa', 'media', 'alta', 'urgente'])->default('media');
            }
            if (! Schema::hasColumn('tickets', 'categoria')) {
                $table->enum('categoria', [
                    'duvida', 'problema_tecnico', 'financeiro', 'contrato', 'veiculo', 'outro'
                ])->default('duvida');
            }

            // Atribuição
            if (! Schema::hasColumn('tickets', 'atribuido_a')) {
                $table->unsignedBigInteger('atribuido_a')->nullable();
            }
            if (! Schema::hasColumn('tickets', 'atribuido_em')) {
                $table->timestamp('atribuido_em')->nullable();
            }

            // Resolução
            if (! Schema::hasColumn('tickets', 'resolucao')) {
                $table->text('resolucao')->nullable();
            }
            if (! Schema::hasColumn('tickets', 'resolvido_em')) {
                $table->timestamp('resolvido_em')->nullable();
            }
            if (! Schema::hasColumn('tickets', 'fechado_em')) {
                $table->timestamp('fechado_em')->nullable();
            }

            // SLA
            if (! Schema::hasColumn('tickets', 'prazo_resposta')) {
                $table->timestamp('prazo_resposta')->nullable();
            }

            // Rating
            if (! Schema::hasColumn('tickets', 'avaliacao')) {
                $table->unsignedTinyInteger('avaliacao')->nullable();
            }
            if (! Schema::hasColumn('tickets', 'avaliacao_comentario')) {
                $table->text('avaliacao_comentario')->nullable();
            }
        });

        // Normalizar status existentes para valores válidos antes de alterar o enum
        DB::statement("UPDATE tickets SET status = 'aberto' WHERE status NOT IN ('aberto','em_atendimento','aguardando_cliente','resolvido','fechado')");

        // Alterar status via SQL direto (mais seguro para MySQL)
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('aberto','em_atendimento','aguardando_cliente','resolvido','fechado') NOT NULL DEFAULT 'aberto'");

        // Gerar número nos tickets existentes
        DB::statement("UPDATE tickets SET numero = CONCAT('TKT-', YEAR(created_at), '-', LPAD(id, 6, '0')) WHERE numero IS NULL");
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'numero', 'titulo', 'prioridade', 'categoria',
                'atribuido_a', 'atribuido_em',
                'resolucao', 'resolvido_em', 'fechado_em',
                'prazo_resposta', 'avaliacao', 'avaliacao_comentario',
            ]);
        });
    }
};
