<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentUsersWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Users';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->with('role')->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('role.name')->label('Role')->default('No Role'),
            ])
            ->paginated(false);
    }
}
