<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IssueResource\Pages;
use App\Models\Issue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IssueResource extends Resource
{
    protected static ?string $model = Issue::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Q&A';

    protected static ?string $navigationLabel = 'Issue Curation';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('issue_key')->label('Issue Key')->disabled()->dehydrated(false),
            Forms\Components\Select::make('status')
                ->options([
                    'open' => 'Open',
                    'in_progress' => 'In Progress',
                    'resolved' => 'Resolved',
                    'closed' => 'Closed',
                ])
                ->required(),
            Forms\Components\Textarea::make('summary')->required()->rows(3)->columnSpanFull(),
            Forms\Components\Textarea::make('description')->rows(8)->columnSpanFull(),
            Forms\Components\TextInput::make('assignee')->placeholder('Ex: user@example.com'),
            Forms\Components\Select::make('curation_status')
                ->options(array_combine(Issue::getCurationStatuses(), array_map('ucfirst', Issue::getCurationStatuses())))
                ->required(),
            Forms\Components\Toggle::make('is_active')->required(),
            Forms\Components\Toggle::make('is_approved')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'comments as total_comments',
                'comments as approved_comments_count' => fn (Builder $q) => $q->where('is_approved', true),
            ]))
            ->modifyQueryUsing(fn (Builder $query) => $query->where('curation_status', Issue::CURATION_PENDING)->withCount([
                'comments as total_comments',
                'comments as approved_comments_count' => fn (Builder $q) => $q->where('is_approved', true),
            ]))
            ->defaultSort('created_at_jira', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('issue_key')->label('Key')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('summary')->label('Summary')->searchable()->limit(70),
                Tables\Columns\TextColumn::make('curation_status')->label('Curation')->badge()->sortable(),
                Tables\Columns\TextColumn::make('assignee')->label('Assignee')->default('Unassigned')->sortable(),
                Tables\Columns\TextColumn::make('total_comments')->label('Comments')->sortable(),
                Tables\Columns\TextColumn::make('created_at_jira')->label('Created')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('curation_status')
                    ->label('Curation status')
                    ->options(array_combine(Issue::getCurationStatuses(), array_map('ucfirst', Issue::getCurationStatuses()))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Issue $record): bool => $record->curation_status === Issue::CURATION_PENDING)
                    ->action(function (Issue $record): void {
                        $record->update([
                            'curation_status' => Issue::CURATION_ACTIVE,
                            'is_active' => true,
                            'is_approved' => true,
                        ]);

                        $record->comments()->where('is_approved', false)->update(['is_approved' => true]);

                        Notification::make()->title('Issue approved successfully.')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Issue $record): bool => $record->curation_status === Issue::CURATION_PENDING)
                    ->action(function (Issue $record): void {
                        $record->update([
                            'curation_status' => Issue::CURATION_REJECTED,
                            'is_active' => false,
                            'is_approved' => false,
                        ]);

                        Notification::make()->title('Issue rejected successfully.')->success()->send();
                    }),
            ])
            ->bulkActions([])
            ->paginated([15, 30, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssues::route('/'),
            'view' => Pages\ViewIssue::route('/{record}'),
            'edit' => Pages\EditIssue::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
