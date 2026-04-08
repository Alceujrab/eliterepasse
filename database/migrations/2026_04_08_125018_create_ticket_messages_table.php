<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ticket_messages');

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');  // quem enviou

            $table->longText('mensagem');
            $table->boolean('is_internal')->default(false); // nota interna do admin
            $table->boolean('is_admin')->default(false);    // enviado pelo admin

            // Via WhatsApp
            $table->boolean('enviado_whatsapp')->default(false);
            $table->string('whatsapp_message_id')->nullable();

            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};
