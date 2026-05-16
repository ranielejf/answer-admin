<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Version extends Model
{
    use HasUlids;

    protected $fillable = [
        'version_number',
        'alert_title',
        'description',
        'target_roles',
        'created_by',
        'sent_at',
        'sent_to_emails',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'target_roles' => 'array',
            'sent_at' => 'datetime',
            'sent_to_emails' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getCurrentVersion(): string
    {
        $version = self::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->first();

        return $version?->version_number ?? '1.0.0';
    }

    public static function getNextVersion(): string
    {
        $currentVersion = self::getCurrentVersion();

        if (! preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $currentVersion, $matches)) {
            return $currentVersion;
        }

        $major = (int) $matches[1];
        $minor = (int) $matches[2];
        $patch = (int) $matches[3] + 1;

        return "{$major}.{$minor}.{$patch}";
    }
}
