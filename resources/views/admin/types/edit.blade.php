@extends('global-notification::layouts.app')

@section('title', 'Edit Notification Type')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Edit Notification Type</h1>
        <a href="{{ route('global-notification.notification-types.index') }}"
            class="text-indigo-600 hover:text-indigo-800">Back to List</a>
    </div>

    <form action="{{ route('global-notification.notification-types.update', $type->id) }}" method="POST" class="max-w-lg">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-bold mb-1">Name (Key)</label>
            <input type="text" name="name" value="{{ $type->name }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold mb-1">Description</label>
            <textarea name="description" class="w-full border p-2 rounded">{{ $type->description }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold mb-1">Variables</label>
            <input type="text" name="variables"
                value="{{ is_array($type->variables) ? implode(', ', $type->variables) : '' }}"
                class="w-full border p-2 rounded" placeholder="order_id, amount, customer_name">
            <p class="text-xs text-gray-500 mt-1">Comma separated list of variables available in templates.</p>
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Update
            Notification Type</button>
    </form>
@endsection
