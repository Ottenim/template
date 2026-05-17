<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('landing-lead-capture.database.table', 'lp_leads'), function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('interest')->nullable();
            $table->boolean('privacy_consent')->default(false);
            $table->string('source')->nullable();
            $table->string('campaign')->nullable();
            $table->string('tag')->nullable();
            $table->string('source_page')->nullable();
            $table->text('source_url')->nullable();
            $table->json('metadata')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index(['source', 'campaign']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('landing-lead-capture.database.table', 'lp_leads'));
    }
};
