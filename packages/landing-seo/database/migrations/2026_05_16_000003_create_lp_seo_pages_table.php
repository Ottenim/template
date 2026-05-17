<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('landing-seo.database.table', 'lp_seo_pages'), function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->string('path', 2048)->nullable()->index();
            $table->string('route_name')->nullable()->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('canonical_url', 2048)->nullable();
            $table->string('image_url', 2048)->nullable();
            $table->string('favicon_url', 2048)->nullable();
            $table->string('robots', 80)->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 2048)->nullable();
            $table->string('og_type', 80)->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image', 2048)->nullable();
            $table->string('twitter_card', 80)->nullable();
            $table->json('schema')->nullable();
            $table->boolean('sitemap_enabled')->default(true)->index();
            $table->string('sitemap_changefreq', 40)->nullable();
            $table->decimal('sitemap_priority', 2, 1)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('landing-seo.database.table', 'lp_seo_pages'));
    }
};
