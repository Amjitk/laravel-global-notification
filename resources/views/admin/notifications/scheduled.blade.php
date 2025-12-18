@extends('global-notification::layouts.app')

@section('title', 'Scheduled Logs')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Scheduled Logs</h1>
            <p class="text-slate-500 mt-1">Review notifications triggered by automated schedules.</p>
        </div>
        <div class="text-sm text-slate-500 bg-slate-100 px-4 py-2 rounded-lg border border-slate-200">
            Filtered by: <span class="font-mono font-bold text-slate-700">source: scheduled</span>
        </div>
    </div>

    @if ($logs->isEmpty())
        <div class="text-center py-24 bg-white rounded-3xl border border-slate-100 shadow-sm">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-6 ring-8 ring-slate-50/50">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-slate-900">No scheduled logs found</h3>
            <p class="text-slate-500 mt-2">Any notification sent with <code
                    class="text-indigo-600 bg-indigo-50 px-1 py-0.5 rounded">->withSource('scheduled')</code> will appear
                here.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/4">
                                Recipient</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/2">
                                Subject / Content</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/6">
                                Channel</th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/6 text-right">
                                Sent At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4 align-top">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs ring-4 ring-white shadow-sm">
                                            {{ substr($log->notifiable_type, strrpos($log->notifiable_type, '\\') + 1)[0] ?? '?' }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ substr($log->notifiable_type, strrpos($log->notifiable_type, '\\') + 1) }}
                                                #{{ $log->notifiable_id }}
                                            </p>
                                            @if ($log->meta['guest_email'] ?? false)
                                                <p class="text-xs text-slate-500">{{ $log->meta['guest_email'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="max-w-xl">
                                        <p class="text-sm font-medium text-slate-900 mb-1">
                                            {{ $log->data['subject'] ?? 'No Subject' }}
                                        </p>
                                        <div class="text-xs text-slate-500 leading-relaxed line-clamp-2">
                                            {{ \Illuminate\Support\Str::limit($log->data['content'] ?? '', 150) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 capitalize">
                                        {{ $log->channel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-top text-right whitespace-nowrap">
                                    <span class="text-xs font-medium text-slate-600 block">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $log->created_at->format('H:i A') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection
