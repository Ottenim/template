<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('landing-cookie-consent.logging.database.table', 'lp_cookie_consents'), function (Blueprint $table) {
            $table->id();
            $table->string('consent_id', 100)->nullable();
            $table->string('version', 80)->nullable();
            $table->string('action', 40);
            $table->json('categories');
            $table->text('policy_url')->nullable();
            $table->text('page_url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('consent_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('landing-cookie-consent.logging.database.table', 'lp_cookie_consents'));
    }
};
