<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('users'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(60)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('changeUserRole')
                    ->label('Change User Role')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->options(fn (Role $record): array => $record->users()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('target_role_id')
                            ->label('Target Role')
                            ->options(fn (): array => Role::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Role $record, array $data): void {
                        $user = User::query()->find($data['user_id']);

                        if (! $user || $user->role_id !== $record->id) {
                            Notification::make()->title('User is not assigned to this role.')->danger()->send();

                            return;
                        }

                        if ($user->email === 'ranielejf@gmail.com' && $data['target_role_id'] !== $record->id) {
                            Notification::make()->title('Cannot change role for the main admin user.')->danger()->send();

                            return;
                        }

                        $user->update(['role_id' => $data['target_role_id']]);

                        Notification::make()->title('User role changed successfully.')->success()->send();
                    }),
                Tables\Actions\Action::make('removeUserFromRole')
                    ->label('Remove User')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->options(fn (Role $record): array => $record->users()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Role $record, array $data): void {
                        $user = User::query()->find($data['user_id']);

                        if (! $user || $user->role_id !== $record->id) {
                            Notification::make()->title('User is not assigned to this role.')->danger()->send();

                            return;
                        }

                        if ($user->email === 'ranielejf@gmail.com') {
                            Notification::make()->title('Cannot remove role from the main admin user.')->danger()->send();

                            return;
                        }

                        $user->update(['role_id' => null]);

                        Notification::make()->title('User removed from role successfully.')->success()->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Role $record, Tables\Actions\DeleteAction $action): void {
                        $systemRoles = ['admin', 'manager', 'analyst', 'user', 'guest'];

                        if (in_array($record->slug, $systemRoles, true)) {
                            Notification::make()
                                ->title('Cannot delete system roles.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }

                        if ($record->users()->count() > 0) {
                            Notification::make()
                                ->title('Cannot delete role with assigned users.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
