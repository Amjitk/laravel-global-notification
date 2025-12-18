@extends('global-notification::layouts.app')

@section('title', 'Create Notification Type')

@section('content')
    <form action="{{ route('global-notification.notification-types.store') }}" method="POST" class="max-w-lg">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-bold mb-1">Name (Key)</label>
            <input type="text" name="name" class="w-full border p-2 rounded" placeholder="e.g. order_placed" required>
            <p class="text-xs text-gray-500 mt-1">This key will be used in code to trigger notifications.</p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold mb-1">Description</label>
            <textarea name="description" class="w-full border p-2 rounded" placeholder="Description of when this fires..."></textarea>
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">Save Notification
            Type</button>
    </form>
@endsection
