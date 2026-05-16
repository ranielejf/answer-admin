<x-filament-panels::page>
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Key</th>
                    <th class="px-4 py-3 text-left">Summary</th>
                    <th class="px-4 py-3 text-left">Curation</th>
                    <th class="px-4 py-3 text-left">Assignee</th>
                    <th class="px-4 py-3 text-left">Comments</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($this->getIssues() as $issue)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $issue->issue_key }}</td>
                        <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($issue->summary, 90) }}</td>
                        <td class="px-4 py-3">{{ ucfirst((string) $issue->curation_status) }}</td>
                        <td class="px-4 py-3">{{ $issue->assignee ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3">{{ $issue->total_comments }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No issues found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->getIssues()->links() }}
    </div>
</x-filament-panels::page>
