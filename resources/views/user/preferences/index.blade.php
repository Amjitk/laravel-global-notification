@extends('global-notification::layouts.app')

@section('title', 'Notification Preferences')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Notification Preferences</h1>
        <p class="text-slate-500 mt-1">Manage exactly what updates you want to receive.</p>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @if ($types->isEmpty())
            <div class="p-12 text-center text-slate-500">
                No notification configs found.
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($types as $type)
                    @php
                        // Get unique channels for this type's templates
$channels = $type->templates()->where('is_active', true)->pluck('channel')->unique();
                    @endphp

                    @if ($channels->isNotEmpty())
                        <div class="p-6 sm:p-8 hover:bg-slate-50/50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $type->name }}</h3>
                                    <p class="text-slate-500 text-sm mt-1">
                                        {{ $type->description ?? 'Receive updates about ' . str_replace('_', ' ', $type->name) }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3">
                                    @foreach ($channels as $channel)
                                        @php
                                            $isEnabled = true;
                                            $pref = $preferences
                                                ->where('notification_type_id', $type->id)
                                                ->where('channel', $channel)
                                                ->first();
                                            if ($pref) {
                                                $isEnabled = $pref->is_enabled;
                                            }
                                        @endphp
                                        <label class="flex items-center gap-3 cursor-pointer group">
                                            <div class="relative">
                                                <input type="checkbox" class="sr-only peer pref-toggle"
                                                    data-type-id="{{ $type->id }}" data-channel="{{ $channel }}"
                                                    {{ $isEnabled ? 'checked' : '' }}>
                                                <div
                                                    class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                                </div>
                                            </div>
                                            <span
                                                class="text-sm font-medium text-slate-600 group-hover:text-slate-900 capitalize">{{ $channel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toggles = document.querySelectorAll('.pref-toggle');

                toggles.forEach(toggle => {
                    toggle.addEventListener('change', async (e) => {
                        const typeId = e.target.dataset.typeId;
                        const channel = e.target.dataset.channel;
                        const isEnabled = e.target.checked;

                        try {
                            const response = await fetch(
                                '{{ route('global-notification.preferences.update') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        notification_type_id: typeId,
                                        channel: channel,
                                        is_enabled: isEnabled
                                    })
                                });

                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                        } catch (error) {
                            console.error('Error updating preference:', error);
                            alert('Failed to update preference. Please try again.');
                            e.target.checked = !isEnabled; // Revert
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
