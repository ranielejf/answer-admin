<?php

namespace App\Filament\Widgets;

use App\Models\Issue;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentIssuesWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Issues';

    public function table(Table $table): Table
    {
        return $table
            ->query(Issue::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('issue_key')->label('Key')->default('-'),
                Tables\Columns\TextColumn::make('summary')->limit(60)->default('-'),
                Tables\Columns\TextColumn::make('assignee')->default('Unassigned'),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->paginated(false);
    }
}
