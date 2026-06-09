<?php

namespace Template\LandingCookieConsent\Models;

use Illuminate\Database\Eloquent\Model;

class CookieConsent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'consent_id',
        'version',
        'action',
        'categories',
        'policy_url',
        'page_url',
        'ip_address',
        'user_agent',
        'accepted_at',
        'expires_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function getTable(): string
    {
        return config('landing-cookie-consent.logging.database.table', 'lp_cookie_consents');
    }
}
