<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'title') && ! Schema::hasColumn('documents', 'titulo')) {
                $table->renameColumn('title', 'titulo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'titulo') && ! Schema::hasColumn('documents', 'title')) {
                $table->renameColumn('titulo', 'title');
            }
        });
    }
};
