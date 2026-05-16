<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoginLogResource\Pages;
use App\Models\LoginLog;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LoginLogResource extends Resource
{
    protected static ?string $model = LoginLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user.role'])->latest('login_at'))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('ip_address')->label('IP Address')->searchable(),
                Tables\Columns\TextColumn::make('user_agent')->label('Browser')->limit(50)->toggleable(),
                Tables\Columns\TextColumn::make('login_at')->label('Login')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('logout_at')->label('Logout')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('logout_at')->label('Active')->boolean(fn (LoginLog $record): bool => $record->logout_at === null),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('User')
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                Tables\Filters\Filter::make('ip_address')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('ip_address')->label('IP Address'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['ip_address'] ?? null),
                            fn (Builder $q): Builder => $q->where('ip_address', 'like', '%'.$data['ip_address'].'%')
                        );
                    }),
                TernaryFilter::make('active_only')
                    ->label('Active sessions')
                    ->placeholder('All')
                    ->trueLabel('Only active')
                    ->falseLabel('Only ended')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNull('logout_at'),
                        false: fn (Builder $query): Builder => $query->whereNotNull('logout_at'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultPaginationPageOption(20)
            ->paginated([20, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoginLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
