@extends('global-notification::layouts.app')

@section('title', 'Notification Templates')

@section('content')
    <div class="bg-white rounded-lg shadow-sm border border-slate-200">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center bg-slate-50 rounded-t-lg">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Notification Templates</h2>
                <p class="text-slate-500 text-sm mt-1">Manage your notification message templates</p>
            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Channel</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Content</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates as $template)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-800 font-medium">
                                {{ $template->type->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                    {{ ucfirst($template->channel) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-md truncate">
                                {{ $template->content }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form
                                    action="{{ route('global-notification.notification-templates.destroy', $template->id) }}"
                                    method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span class="text-slate-500">No templates found</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
