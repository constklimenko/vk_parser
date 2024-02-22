<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vk_banners', function (Blueprint $table) {
            $table->integer('banner_id')->primary();
            $table->integer('campaign_id');
            $table->integer('group_id');
            $table->string('name');
            $table->string('city');
            $table->string('section');
            $table->string('subsection');
        });

        Schema::create('vk_banner_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('banner_id');
            $table->integer('shows');
            $table->integer('clicks');
            $table->integer('leads');
            $table->string('date');
            $table->double('spent');
        });

        Schema::create('vk_ads_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('access_token');
            $table->string('refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('vk_banner_stats');
        Schema::drop('vk_banner');
        Schema::drop('vk_ads_tokens');
    }
};
