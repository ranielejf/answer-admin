<x-filament-panels::page>

    <div class="space-y-6">
    <div class="mb-2 flex justify-end gap-2">
        @if($this->isSourceIssue())
            <x-filament::button wire:click="cloneForCuration" icon="heroicon-m-document-duplicate">Clone for Curation</x-filament::button>
        @else
            <x-filament::button tag="a" href="{{ \App\Filament\Resources\IssueResource::getUrl('edit', ['record' => $record]) }}" icon="heroicon-m-pencil-square">Edit</x-filament::button>
        @endif
        <x-filament::button color="gray" tag="a" href="{{ \App\Filament\Resources\IssueResource::getUrl('index') }}">Back</x-filament::button>
    </div>

        <section class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-xl font-semibold text-gray-900">Issue Information</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $record->issue_key }}</p>
            </div>
            <div class="grid grid-cols-1 gap-6 px-6 py-6 md:grid-cols-2">
                <div><p class="text-sm text-gray-500">Issue Key</p><p class="font-medium">{{ $record->issue_key }}</p></div>
                <div><p class="text-sm text-gray-500">Jira Status</p><p>{{ $record->status }}</p></div>
                <div class="md:col-span-2"><p class="text-sm text-gray-500">Summary</p><p>{{ $record->summary }}</p></div>
                <div class="md:col-span-2"><p class="text-sm text-gray-500">Description (Question)</p><div class="whitespace-pre-wrap">{{ $record->description }}</div></div>
                <div><p class="text-sm text-gray-500">Assignee</p><p>{{ $record->assignee ?? 'N/A' }}</p></div>
                <div><p class="text-sm text-gray-500">Created By</p><p>{{ $record->created_by ?? 'N/A' }}</p></div>
                <div><p class="text-sm text-gray-500">Curation Status</p><p>{{ ucfirst((string) $record->curation_status) }}</p></div>
                <div><p class="text-sm text-gray-500">Curated</p><p>{{ $record->is_curated ? 'Yes' : 'No' }}</p></div>
                <div><p class="text-sm text-gray-500">Created in Jira</p><p>{{ optional($record->created_at_jira)->format('d/m/Y H:i') }}</p></div>
                <div><p class="text-sm text-gray-500">Updated in Jira</p><p>{{ optional($record->updated_at_jira)->format('d/m/Y H:i') }}</p></div>
            </div>
        </section>
        @endif

        <section class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-xl font-semibold text-gray-900">Comments</h3>
                <p class="mt-1 text-sm text-gray-500">Total: {{ $record->comments()->count() }} comments</p>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($record->comments()->orderBy('created_at_jira')->get() as $comment)
                    <div class="px-6 py-5">
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <div class="text-sm font-semibold">{{ $comment->author }} <span class="ml-2 text-xs text-gray-500">{{ optional($comment->created_at_jira)->format('d/m/Y H:i') }}</span></div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $comment->is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">{{ $comment->is_active ? 'Active' : 'Inactive' }}</span>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs {{ $comment->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $comment->is_approved ? 'Approved' : 'Pending' }}</span>
                            </div>
                        </div>
                        <div class="whitespace-pre-wrap text-gray-700">{{ $comment->body }}</div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <x-filament::button size="xs" color="gray" tag="a" href="{{ \App\Filament\Resources\PendingAnswerResource::getUrl('edit', ['record' => $comment]) }}">Edit</x-filament::button>
                            <x-filament::button size="xs" color="warning" wire:click="toggleCommentStatus('{{ $comment->id }}')">{{ $comment->is_active ? 'Deactivate' : 'Activate' }}</x-filament::button>
                            @if($comment->is_approved)
                                <x-filament::button size="xs" color="danger" wire:click="disapproveComment('{{ $comment->id }}')">Disapprove</x-filament::button>
                            @else
                                <x-filament::button size="xs" color="success" wire:click="approveComment('{{ $comment->id }}')">Approve</x-filament::button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        @if($this->isSourceIssue())
        <section class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-5">
                <h3 class="text-xl font-semibold text-gray-900">Create Curated Issue</h3>
                <p class="mt-1 text-sm text-gray-500">After reviewing source content, prepare and clone curated content into FFC.</p>
            </div>
            <div class="space-y-4 px-6 py-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Curated Summary</label>
                    <input type="text" wire:model="curatedSummary" class="w-full rounded-lg border border-gray-300 px-3 py-2" />
                    @error('curatedSummary')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Curated Description</label>
                    <textarea wire:model="curatedDescription" rows="6" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
                </div>
                @if((bool) ($record->is_curated ?? false))
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" wire:model="replaceClone" class="rounded border-gray-300">
                        Replace existing curated clone
                    </label>
                @endif
                <div>
                    <h4 class="mb-2 text-base font-semibold text-gray-900">Curated Responses</h4>
                    <div class="space-y-3">
                        @foreach($record->comments()->orderBy('created_at_jira')->get() as $comment)
                            <div class="rounded-lg border border-gray-200 p-3">
                                <div class="mb-2 text-sm font-semibold">{{ $comment->author }}</div>
                                <textarea wire:model="curatedResponses.{{ $comment->comment_id }}" rows="4" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea>
                            </div>
                        @endforeach
                    </div>
                </div>
                
            </div>
        </section>
    </div>
</x-filament-panels::page>
