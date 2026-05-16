<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VersionResource\Pages;
use App\Mail\VersionNotificationMail;
use App\Models\Role;
use App\Models\User;
use App\Models\Version;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class VersionResource extends Resource
{
    protected static ?string $model = Version::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('version_number')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true)
                ->default(fn (): string => Version::getNextVersion())
                ->disabled()
                ->dehydrated(),
            Forms\Components\TextInput::make('alert_title')
                ->maxLength(160),
            Forms\Components\Textarea::make('description')
                ->required()
                ->rows(5)
                ->columnSpanFull(),
            Forms\Components\CheckboxList::make('target_roles')
                ->label('Target Roles')
                ->options(fn (): array => Role::query()->orderBy('name')->pluck('name', 'slug')->all())
                ->columns(2)
                ->helperText('Leave empty to target all users.'),
            Forms\Components\Toggle::make('is_active')
                ->default(true)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('creator')->orderByDesc('created_at'))
            ->columns([
                Tables\Columns\TextColumn::make('version_number')->label('Version')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('alert_title')->limit(60)->toggleable(),
                Tables\Columns\TextColumn::make('description')->limit(80),
                Tables\Columns\TextColumn::make('creator.name')->label('Created By')->toggleable(),
                Tables\Columns\TextColumn::make('sent_at')->dateTime()->label('Email Sent At')->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('sendEmail')
                    ->label(fn (Version $record): string => $record->sent_at ? 'Resend Email' : 'Send Email')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Version $record): void {
                        $sentEmails = self::sendVersionEmails($record);
                        $existingEmails = $record->sent_to_emails ?? [];
                        $allSentEmails = array_merge($existingEmails, $sentEmails);

                        $record->update([
                            'sent_at' => now(),
                            'sent_to_emails' => $allSentEmails,
                        ]);

                        Notification::make()
                            ->title('Emails sent successfully to '.count($sentEmails).' user(s).')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVersions::route('/'),
            'create' => Pages\CreateVersion::route('/create'),
            'edit' => Pages\EditVersion::route('/{record}/edit'),
        ];
    }

    private static function sendVersionEmails(Version $version): array
    {
        $users = self::getTargetUsers($version);
        $sentEmails = [];

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new VersionNotificationMail($version, $user));

                $sentEmails[] = [
                    'email' => $user->email,
                    'name' => $user->name,
                    'sent_at' => now()->toDateTimeString(),
                ];
            } catch (\Exception $exception) {
                report($exception);
            }
        }

        return $sentEmails;
    }

    private static function getTargetUsers(Version $version)
    {
        if ($version->target_roles === null || empty($version->target_roles)) {
            return User::query()->whereHas('role')->get();
        }

        return User::query()
            ->whereHas('role', function (Builder $query) use ($version): void {
                $query->whereIn('slug', $version->target_roles);
            })
            ->get();
    }
}
