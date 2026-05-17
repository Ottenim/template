<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('landing-pricing.database.table', 'lp_pricing_plans'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('description', 500)->nullable();
            $table->string('price', 80)->nullable();
            $table->string('currency', 20)->nullable();
            $table->string('billing_period_label', 40)->nullable();
            $table->json('features')->nullable();
            $table->string('cta_label', 80)->nullable();
            $table->string('cta_url', 2048)->nullable();
            $table->string('note', 500)->nullable();
            $table->string('badge', 80)->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('landing-pricing.database.table', 'lp_pricing_plans'));
    }
};
