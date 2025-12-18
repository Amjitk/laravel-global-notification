@extends('global-notification::layouts.app')

@section('title', 'Manage Type: ' . $type->name)

@section('content')
    <div class="mb-6">
        <h2 class="text-lg font-semibold mb-2">Details</h2>
        <div class="bg-gray-50 p-4 rounded border">
            <p><strong>Name/Key:</strong> {{ $type->name }}</p>
            <p><strong>Description:</strong> {{ $type->description }}</p>
            <p><strong>Model Type:</strong> {{ $type->model_type ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="text-xl font-bold mb-4">Templates</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($type->templates as $template)
                <div class="border p-4 rounded shadow-sm relative">
                    <span
                        class="absolute top-2 right-2 px-2 py-1 rounded text-xs {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <h4 class="font-bold uppercase text-gray-600 mb-2">{{ $template->channel }}</h4>
                    <p class="text-sm"><strong>Subject:</strong> {{ $template->subject }}</p>
                    <div class="mt-2 text-sm text-gray-600 bg-gray-100 p-2 rounded h-20 overflow-hidden">
                        {{ Str::limit($template->content, 100) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="border-t pt-6">
        <h3 class="text-xl font-bold mb-4">Add/Edit Template</h3>
        <form action="{{ route('notification-templates.store') }}" method="POST" class="bg-gray-50 p-6 rounded border">
            @csrf
            <input type="hidden" name="notification_type_id" value="{{ $type->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Channel</label>
                    <select name="channel" class="w-full border p-2 rounded">
                        <option value="mail">Email</option>
                        <option value="database">Database/UI</option>
                        <option value="sms">SMS</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Subject (Optional)</label>
                    <input type="text" name="subject" class="w-full border p-2 rounded" placeholder="Email Subject">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Content</label>
                <textarea name="content" rows="4" class="w-full border p-2 rounded"
                    placeholder="Hello @{{ user_name }}, your order #@{{ order_id }} is ready!"></textarea>
                <p class="text-xs text-gray-500 mt-1">Use @{{ variable }} for dynamic content.</p>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Save
                Template</button>
        </form>
    </div>
@endsection
