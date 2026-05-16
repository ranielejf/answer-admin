<?php

namespace App\Filament\Resources\IssueResource\Pages;

use App\Filament\Resources\IssueResource;
use App\Models\Issue;
use App\Models\IssueComment;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewIssue extends ViewRecord
{
    protected static string $resource = IssueResource::class;

    protected static string $view = 'filament.resources.issue-resource.pages.view-issue';

    public string $curatedSummary = '';

    public string $curatedDescription = '';

    public array $curatedResponses = [];

    public bool $replaceClone = false;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->curatedSummary = (string) ($this->record->summary ?? '');
        $this->curatedDescription = (string) ($this->record->description ?? '');

        $this->curatedResponses = $this->record->comments()
            ->orderBy('created_at_jira')
            ->get()
            ->mapWithKeys(fn (IssueComment $comment) => [$comment->comment_id => (string) ($comment->body ?? '')])
            ->all();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn (): bool => ! $this->isSourceIssue()),
        ];
    }

    public function isSourceIssue(): bool
    {
        return empty($this->record->source_issue_id);
    }

    public function cloneForCuration(): void
    {
        if (! $this->isSourceIssue()) {
            Notification::make()->title('Only source issues can be cloned for curation.')->warning()->send();

            return;
        }

        $this->validate([
            'curatedSummary' => ['required', 'string', 'max:500'],
            'curatedDescription' => ['nullable', 'string'],
            'curatedResponses' => ['array'],
            'replaceClone' => ['boolean'],
        ]);

        $sourceIssue = $this->record;
        $curatedIssueKey = $this->buildCuratedIssueKeyFromSource((string) $sourceIssue->issue_key);

        $existingCuratedIssue = Issue::query()->where('issue_key', $curatedIssueKey)->first();
        if ($existingCuratedIssue && ! $this->replaceClone) {
            Notification::make()->title('A curated clone already exists. Enable replace clone to continue.')->warning()->send();

            return;
        }

        $curatedIssue = $existingCuratedIssue ?? new Issue;
        $curatedIssue->issue_key = $curatedIssueKey;
        $curatedIssue->fill([
            'summary' => $this->curatedSummary,
            'description' => $this->curatedDescription,
            'status' => $sourceIssue->status,
            'assignee' => $sourceIssue->assignee,
            'created_by' => auth()->user()?->email,
            'created_at_jira' => $existingCuratedIssue?->created_at_jira ?? now(),
            'updated_at_jira' => now(),
            'is_active' => true,
            'is_approved' => true,
            'curation_status' => Issue::CURATION_ACTIVE,
            'visibility' => 'public_internal',
            'source_issue_id' => $sourceIssue->id,
        ]);
        $curatedIssue->save();

        $curatedIssue->comments()->delete();

        foreach ($sourceIssue->comments()->orderBy('created_at_jira')->get() as $sourceComment) {
            $body = trim((string) ($this->curatedResponses[$sourceComment->comment_id] ?? ''));
            if ($body === '') {
                continue;
            }

            IssueComment::query()->create([
                'comment_id' => Str::ulid()->toBase32(),
                'issue_key' => $curatedIssue->issue_key,
                'author' => $sourceComment->author,
                'user_id' => auth()->id(),
                'body' => $body,
                'comment_type' => $sourceComment->comment_type,
                'created_at_jira' => now(),
                'contains_attachment' => (bool) $sourceComment->contains_attachment,
                'is_approved' => true,
                'is_active' => true,
            ]);
        }

        $sourceIssue->update([
            'curated_issue_id' => $curatedIssue->id,
            'is_curated' => true,
            'curated_at' => now(),
            'curated_by' => auth()->user()?->email,
        ]);

        Notification::make()->title('Curated issue cloned successfully.')->success()->send();

        $this->redirect(IssueResource::getUrl('view', ['record' => $curatedIssue]));
    }


    public function approveComment(string $commentId): void
    {
        $comment = IssueComment::query()->findOrFail($commentId);
        $comment->load('issue');

        $comment->update(['is_approved' => true]);

        if ($comment->issue && ! $comment->issue->is_approved && $comment->issue->curation_status !== Issue::CURATION_REJECTED) {
            $comment->issue->update([
                'curation_status' => Issue::CURATION_ACTIVE,
                'is_active' => true,
                'is_approved' => true,
            ]);
        }

        Notification::make()->title('Comment approved successfully.')->success()->send();
    }

    public function disapproveComment(string $commentId): void
    {
        $comment = IssueComment::query()->findOrFail($commentId);
        $comment->update(['is_approved' => false]);

        Notification::make()->title('Comment disapproved.')->warning()->send();
    }

    public function toggleCommentStatus(string $commentId): void
    {
        $comment = IssueComment::query()->findOrFail($commentId);
        $comment->update(['is_active' => ! (bool) $comment->is_active]);

        Notification::make()->title('Comment status updated.')->success()->send();
    }

    private function buildCuratedIssueKeyFromSource(string $issueKey): string
    {
        if (preg_match('/^FFS-(\d+)$/', $issueKey, $matches) === 1) {
            return 'FFC-'.$matches[1];
        }

        return 'FFC-'.strtoupper(Str::random(8));
    }
}
