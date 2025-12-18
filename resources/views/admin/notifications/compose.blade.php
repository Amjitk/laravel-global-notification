@extends('global-notification::layouts.app')

@section('title', 'Compose Notification')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Compose Notification</h1>
            <p class="text-slate-500 mt-1">Send invitations, announcements, or custom alerts.</p>
        </div>

        <form action="{{ route('global-notification.notifications.send') }}" method="POST"
            class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            @if ($errors->any())
                <div class="lg:col-span-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative"
                    role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Recipient Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Recipients</h2>

                    <!-- Toggle -->
                    <div class="flex gap-6 mb-6">
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="recipient_type" value="users" checked
                                onchange="toggleRecipients('users')"
                                class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-medium text-slate-700 group-hover:text-slate-900">Registered
                                Users</span>
                        </label>
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="recipient_type" value="emails" onchange="toggleRecipients('emails')"
                                class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-medium text-slate-700 group-hover:text-slate-900">External Email
                                Addresses</span>
                        </label>
                    </div>

                    <!-- User Selection -->
                    <div id="users-selector" class="block">
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">Select
                            Users</label>
                        <div class="border border-slate-200 rounded-xl overflow-hidden max-h-64 overflow-y-auto">
                            <div class="p-2 bg-slate-50 space-y-1">
                                @foreach ($users as $user)
                                    <label
                                        class="flex items-center p-3 hover:bg-white rounded-lg cursor-pointer group transition-colors">
                                        <input type="checkbox" name="recipients[]" value="{{ $user->id }}"
                                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-slate-900">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Custom Emails -->
                    <div id="emails-input" class="hidden">
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">Enter Email
                            Addresses</label>
                        <textarea name="custom_emails" rows="3"
                            class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g. john@example.com, jane@company.com"></textarea>
                        <p class="text-xs text-slate-500 mt-2">Separate multiple emails with commas.</p>
                    </div>
                </div>

                <!-- Message Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Message Content</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Subject</label>
                            <input type="text" name="subject" required
                                class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5"
                                placeholder="Notification Title">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Content / Body</label>
                            <textarea name="content" rows="6" required
                                class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5"
                                placeholder="Write your message here..."></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar / Settings -->
            <div class="space-y-6">

                <!-- Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sticky top-24">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-4">Sending Options</h3>

                    <!-- Channels -->
                    <div class="mb-6">
                        <label class="block text-xs font-medium text-slate-500 mb-3">CHANNELS</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="channels[]" value="database" checked
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-slate-700">Database (In-App)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="channels[]" value="mail"
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-slate-700">Email</span>
                            </label>
                        </div>
                    </div>

                    <!-- Custom Sender -->
                    <div class="mb-6 pt-6 border-t border-slate-100">
                        <label class="block text-xs font-medium text-slate-500 mb-3">CUSTOM SENDER (OPTIONAL)</label>
                        <div class="space-y-4">
                            <div>
                                <input type="email" name="from_email"
                                    class="w-full text-sm rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="From Email (e.g. support@acme.com)">
                            </div>
                            <div>
                                <input type="text" name="from_name"
                                    class="w-full text-sm rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="From Name (e.g. Acme Support)">
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                        Send Notification
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleRecipients(type) {
            const usersDiv = document.getElementById('users-selector');
            const emailsDiv = document.getElementById('emails-input');

            if (type === 'users') {
                usersDiv.classList.remove('hidden');
                emailsDiv.classList.add('hidden');
            } else {
                usersDiv.classList.add('hidden');
                emailsDiv.classList.remove('hidden');
            }
        }
    </script>
@endsection
