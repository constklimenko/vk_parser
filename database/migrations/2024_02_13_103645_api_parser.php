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
        Schema::create('vk_questions', function (Blueprint $table) {
            $table->id('question_id');
            $table->string('text');
        });

        Schema::create('vk_answers', function (Blueprint $table) {
            $table->integer('lead_id');
            $table->integer('question_id');
            $table->string('answer_option_id');
            $table->string('answer_text');
        });

        Schema::create('vk_forms', function (Blueprint $table) {
            $table->id('form_id');
            $table->string('name');
            $table->integer('ad_plan_id');
            $table->integer('ad_group_id');
            $table->integer('banner_id');
        });

        Schema::create('vk_leads', function (Blueprint $table) {
            $table->id('lead_id');
            $table->integer('form_id');
            $table->timestamp('created_at');
            $table->string('name');
            $table->string('phone');
            $table->integer('age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('vk_questions');
        Schema::drop('vk_answers');
        Schema::drop('vk_forms');
        Schema::drop('vk_leads');
    }
};
