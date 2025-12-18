@extends('global-notification::layouts.app')

@section('title', 'Notification Types')

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('global-notification.notification-types.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Create New Type</a>
    </div>

    <table class="w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-50">
                <th class="border p-2 text-left">ID</th>
                <th class="border p-2 text-left">Name</th>
                <th class="border p-2 text-left">Description</th>
                <th class="border p-2 text-left">Templates</th>
                <th class="border p-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($types as $type)
                <tr>
                    <td class="border p-2">{{ $type->id }}</td>
                    <td class="border p-2 font-semibold">{{ $type->name }}</td>
                    <td class="border p-2 text-gray-600">{{ $type->description }}</td>
                    <td class="border p-2">
                        @foreach ($type->templates as $template)
                            <span class="bg-gray-200 px-2 py-1 rounded text-xs">{{ $template->channel }}</span>
                        @endforeach
                    </td>
                    <td class="border p-2">
                        <a href="{{ route('global-notification.notification-types.show', $type->id) }}"
                            class="text-blue-600 hover:underline">Configure</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
