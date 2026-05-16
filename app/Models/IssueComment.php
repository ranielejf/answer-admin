<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueComment extends Model
{
    use HasUlids;

    protected $fillable = [
        'comment_id',
        'issue_key',
        'author',
        'user_id',
        'body',
        'comment_type',
        'created_at_jira',
        'contains_attachment',
        'is_approved',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'created_at_jira' => 'datetime',
            'contains_attachment' => 'boolean',
            'is_approved' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_key', 'issue_key');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }
}
