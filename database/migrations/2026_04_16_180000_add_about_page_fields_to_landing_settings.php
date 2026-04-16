<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('about_page_hero_title')->nullable()->after('about_image');
            $table->string('about_page_hero_subtitle')->nullable()->after('about_page_hero_title');
            $table->text('about_page_mission')->nullable()->after('about_page_hero_subtitle');
            $table->text('about_page_vision')->nullable()->after('about_page_mission');
            $table->text('about_page_values')->nullable()->after('about_page_vision');
            $table->text('about_page_history')->nullable()->after('about_page_values');
            $table->string('about_page_history_image')->nullable()->after('about_page_history');
            $table->string('about_page_video_url')->nullable()->after('about_page_history_image');
            $table->json('about_page_stats')->nullable()->after('about_page_video_url');
            $table->json('about_page_team')->nullable()->after('about_page_stats');
            $table->json('about_page_testimonials')->nullable()->after('about_page_team');
            $table->json('about_page_gallery')->nullable()->after('about_page_testimonials');
        });
    }

    public function down(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn([
                'about_page_hero_title',
                'about_page_hero_subtitle',
                'about_page_mission',
                'about_page_vision',
                'about_page_values',
                'about_page_history',
                'about_page_history_image',
                'about_page_video_url',
                'about_page_stats',
                'about_page_team',
                'about_page_testimonials',
                'about_page_gallery',
            ]);
        });
    }
};
