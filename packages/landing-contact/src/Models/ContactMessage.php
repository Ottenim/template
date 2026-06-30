<?php

namespace Template\LandingContact\Models;

use Illuminate\Database\Eloquent\Model;
use Template\LandingContact\Config\ContactConfig;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'interest',
        'message',
        'privacy_consent',
        'source_page',
        'source_url',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'privacy_consent' => 'boolean',
        ];
    }

    public function getTable(): string
    {
        return ContactConfig::fromConfig()->databaseTable();
    }
}
