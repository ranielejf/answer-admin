<?php

namespace App\Filament\Pages;

use App\Models\Issue;
use Filament\Pages\Page;

class AllIssues extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Q&A';

    protected static ?string $navigationLabel = 'All Issues';

    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.pages.all-issues';

    public function getIssues()
    {
        return Issue::query()
            ->withCount([
                'comments as total_comments',
                'comments as approved_comments_count' => fn ($q) => $q->where('is_approved', true),
            ])
            ->orderByDesc('created_at_jira')
            ->paginate(15);
    }
}
