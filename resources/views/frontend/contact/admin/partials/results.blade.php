@if($messages->count() > 0)
    <div class="text-xs text-slate-500 px-6 pt-4 pb-2">
        Showing {{ $messages->firstItem() }} to {{ $messages->lastItem() }} of {{ $messages->total() }} results
    </div>
@endif

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-100 border-b border-slate-200">
            <tr>
                <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Customer</th>
                <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Subject</th>
                <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Phone</th>
                <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Date</th>
                <th class="text-left px-6 py-4 text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-slate-200">
            @forelse($messages as $message)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-900">{{ $message->name }}</div>
                        <div class="text-xs text-slate-500">{{ $message->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-900">{{ $message->subject }}</div>
                        <div class="text-xs text-slate-500">
                            {{ \Illuminate\Support\Str::limit($message->message, 60) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        {{ $message->phone ?: '-' }}
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $message->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('inventory.contacts.show', $message) }}" class="text-emerald-600 hover:text-emerald-900 font-medium">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="text-slate-500">
                            <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m5 6H6a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v9a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">No contact messages found</p>
                            <p class="text-sm mt-1">Customer messages will appear here after they submit the contact form.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($messages->hasPages())
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
        {{ $messages->appends(request()->query())->links() }}
    </div>
@endif
