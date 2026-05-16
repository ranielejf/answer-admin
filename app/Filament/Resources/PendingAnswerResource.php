<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendingAnswerResource\Pages;
use App\Models\Issue;
use App\Models\IssueComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PendingAnswerResource extends Resource
{
    protected static ?string $model = IssueComment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Q&A';

    protected static ?string $navigationLabel = 'Pending Answers';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('body')->required()->rows(8)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->pendingApproval()->with('issue')->orderByDesc('created_at_jira'))
            ->columns([
                Tables\Columns\TextColumn::make('issue.issue_key')->label('Issue')->searchable(),
                Tables\Columns\TextColumn::make('issue.summary')->label('Summary')->limit(50),
                Tables\Columns\TextColumn::make('author')->searchable(),
                Tables\Columns\TextColumn::make('body')->limit(100),
                Tables\Columns\TextColumn::make('created_at_jira')->label('Date')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('contains_attachment')->label('Attachments')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (IssueComment $record): void {
                        $record->load('issue');
                        $record->update(['is_approved' => true]);

                        if ($record->issue && ! $record->issue->is_approved && $record->issue->curation_status !== Issue::CURATION_REJECTED) {
                            $record->issue->update([
                                'curation_status' => Issue::CURATION_ACTIVE,
                                'is_active' => true,
                                'is_approved' => true,
                            ]);
                        }

                        Notification::make()->title('Comment approved successfully.')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (IssueComment $record): void {
                        $record->update(['is_approved' => false]);
                        Notification::make()->title('Comment rejected.')->warning()->send();
                    }),
            ])
            ->bulkActions([])
            ->paginated([15, 30, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendingAnswers::route('/'),
            'edit' => Pages\EditPendingAnswer::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        return false;
    }
}
