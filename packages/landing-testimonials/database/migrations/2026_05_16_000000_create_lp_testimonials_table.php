<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('landing-testimonials.database.table', 'lp_testimonials'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->text('text');
            $table->string('role', 120)->nullable();
            $table->string('company', 120)->nullable();
            $table->string('avatar', 2048)->nullable();
            $table->string('logo', 2048)->nullable();
            $table->unsignedTinyInteger('rating')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('landing-testimonials.database.table', 'lp_testimonials'));
    }
};
