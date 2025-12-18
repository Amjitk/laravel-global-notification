@extends('global-notification::layouts.app')

@section('title', 'My Notifications')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Notifications</h1>
            <p class="text-slate-500 mt-1">Manage and view your system alerts.</p>
        </div>

        <div class="flex p-1 bg-slate-100/50 rounded-xl border border-slate-200">
            <a href="{{ request()->fullUrlWithQuery(['read' => 'false']) }}"
                class="px-5 py-2 text-sm font-semibold rounded-lg transition-all {{ request('read') == 'false' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700' }}">
                Unread
            </a>
            <a href="{{ request()->fullUrlWithQuery(['read' => 'true']) }}"
                class="px-5 py-2 text-sm font-semibold rounded-lg transition-all {{ request('read') == 'true' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700' }}">
                Read
            </a>
            <a href="{{ route('global-notification.user.index') }}"
                class="px-5 py-2 text-sm font-semibold rounded-lg transition-all {{ !request()->has('read') ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 hover:text-slate-700' }}">
                All
            </a>
        </div>
    </div>

    @if ($notifications->isEmpty())
        <div class="text-center py-24 bg-white rounded-3xl border border-slate-100 shadow-sm">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-6 ring-8 ring-slate-50/50">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-slate-900">No notifications</h3>
            <p class="text-slate-500 mt-2">You're all caught up! check back later.</p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach ($notifications as $notification)
                <div
                    class="group relative bg-white p-6 rounded-2xl border transition-all duration-300 {{ is_null($notification->read_at) ? 'border-indigo-100 shadow-[0_4px_20px_-4px_rgba(99,102,241,0.1)]' : 'border-slate-100 hover:border-slate-300' }}">
                    <div class="flex items-start gap-5">
                        <div class="flex-shrink-0 mt-1">
                            @if (is_null($notification->read_at))
                                <div class="relative flex h-3 w-3">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                                </div>
                            @else
                                <div class="h-3 w-3 rounded-full bg-slate-200"></div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-lg font-semibold text-slate-900 truncate">
                                        {{ $notification->data['subject'] ?? 'System Notification' }}
                                    </h4>
                                    @if (!empty($notification->meta['is_manual']))
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200 uppercase tracking-wide">Manual</span>
                                    @endif
                                    @if ($notification->notifiable_type === 'guest')
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200 uppercase tracking-wide">External</span>
                                    @endif
                                </div>
                                <span class="flex-shrink-0 text-xs font-medium text-slate-400 whitespace-nowrap">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>

                            @if (!empty($notification->meta['guest_email']))
                                <div class="mb-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Sent to: {{ $notification->meta['guest_email'] }}
                                    </span>
                                </div>
                            @endif

                            <div class="text-slate-600 leading-relaxed text-sm">
                                {!! nl2br(e($notification->data['content'] ?? '')) !!}
                            </div>

                            <div class="mt-4 flex items-center gap-4">
                                @if (is_null($notification->read_at))
                                    <form action="{{ route('global-notification.user.read', $notification->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Mark as read
                                        </button>
                                    </form>
                                @else
                                    <div
                                        class="text-xs font-medium text-slate-400 flex items-center gap-1 bg-slate-50 py-1 px-2 rounded">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Read
                                    </div>
                                @endif

                                <span class="text-xs text-slate-300 font-mono">{{ $notification->channel }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $notifications->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
