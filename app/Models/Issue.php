<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    public function resolveRouteBindingQuery($query, $value, $field = null): \Illuminate\Database\Eloquent\Builder
    {
        $resolvedField = $field ?? $this->getRouteKeyName();

        return $query->where(function ($innerQuery) use ($resolvedField, $value): void {
            $innerQuery->where($resolvedField, $value);

            if ($resolvedField !== "issue_key") {
                $innerQuery->orWhere("issue_key", $value);
            }
        });
    }

    use HasUlids;

    public const CURATION_PENDING = 'pending';
    public const CURATION_ACTIVE = 'active';
    public const CURATION_INACTIVE = 'inactive';
    public const CURATION_REJECTED = 'rejected';

    protected function casts(): array
    {
        return [
            'created_at_jira' => 'datetime',
            'updated_at_jira' => 'datetime',
            'is_active' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public static function getCurationStatuses(): array
    {
        return [
            self::CURATION_PENDING,
            self::CURATION_ACTIVE,
            self::CURATION_INACTIVE,
            self::CURATION_REJECTED,
        ];
    }

    public function comments(): HasMany
    {
        return $this->hasMany(IssueComment::class, 'issue_key', 'issue_key');
    }

    public function curationStatusLabel(): string
    {
        return ucfirst((string) $this->curation_status);
    }
}
