<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expandir landing_settings com novos campos
        Schema::table('landing_settings', function (Blueprint $table) {
            // Logo
            $table->string('logo_path')->nullable()->after('whatsapp_number');

            // Menu items (JSON array: [{label, url}])
            $table->json('menu_items')->nullable()->after('logo_path');

            // Sobre Nós
            $table->string('about_title')->nullable()->after('menu_items');
            $table->text('about_text')->nullable()->after('about_title');
            $table->string('about_image')->nullable()->after('about_text');

            // Contato
            $table->string('contact_phone')->nullable()->after('about_image');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->string('contact_address')->nullable()->after('contact_email');
            $table->string('contact_city')->nullable()->after('contact_address');
            $table->string('contact_state')->nullable()->after('contact_city');
            $table->string('contact_lat')->nullable()->after('contact_state');
            $table->string('contact_lng')->nullable()->after('contact_lat');

            // Footer
            $table->string('footer_text')->nullable()->after('contact_lng');
            $table->json('footer_links')->nullable()->after('footer_text');

            // Redes sociais
            $table->string('social_instagram')->nullable()->after('footer_links');
            $table->string('social_facebook')->nullable()->after('social_instagram');
            $table->string('social_youtube')->nullable()->after('social_facebook');
        });

        // Tabela de banners
        Schema::create('landing_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('image_path');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_banners');

        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path', 'menu_items',
                'about_title', 'about_text', 'about_image',
                'contact_phone', 'contact_email', 'contact_address',
                'contact_city', 'contact_state', 'contact_lat', 'contact_lng',
                'footer_text', 'footer_links',
                'social_instagram', 'social_facebook', 'social_youtube',
            ]);
        });
    }
};
