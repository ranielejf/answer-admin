<?php

namespace App\Filament\Widgets;

use App\Models\Issue;
use App\Models\IssueComment;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', (string) User::query()->count()),
            Stat::make('Total Issues', (string) Issue::query()->count()),
            Stat::make('Pending Comments', (string) IssueComment::query()->where('is_approved', false)->count()),
            Stat::make('Workspaces', (string) Workspace::query()->count()),
            Stat::make('Roles', (string) Role::query()->count()),
            Stat::make('Total Comments', (string) IssueComment::query()->count()),
        ];
    }
}
