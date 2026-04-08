<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('ticket_message_id')->nullable();
            $table->unsignedBigInteger('user_id');

            $table->string('nome_original');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('tamanho')->nullable(); // bytes

            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
