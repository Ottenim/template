<?php

namespace Template\LandingLeadCapture\Models;

use Illuminate\Database\Eloquent\Model;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'interest',
        'privacy_consent',
        'source',
        'campaign',
        'tag',
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
        return LeadCaptureConfig::fromConfig()->databaseTable();
    }
}
