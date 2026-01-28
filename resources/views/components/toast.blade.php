<div id="gn-toast-container" class="fixed bottom-5 right-5 z-50 pointer-events-none">
    <!-- Toasts will be injected here -->
</div>

<template id="gn-toast-template">
    <div
        class="gn-toast transform transition-all duration-300 translate-x-full opacity-0 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden mb-3">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900 gn-toast-subject"></p>
                    <p class="mt-1 text-sm text-gray-500 gn-toast-content"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button
                        class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 gn-toast-close">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('gn-toast-container');
        const template = document.getElementById('gn-toast-template');
        let audio = new Audio(
        'https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3'); // Optional sound
        let lastSeenId = localStorage.getItem('gn_last_seen_id') || 0;

        const showToast = (notification) => {
            const clone = template.content.cloneNode(true);
            const toastEl = clone.querySelector('.gn-toast');

            clone.querySelector('.gn-toast-subject').textContent = notification.subject;
            clone.querySelector('.gn-toast-content').textContent = notification.content;

            clone.querySelector('.gn-toast-close').addEventListener('click', () => {
                toastEl.classList.remove('translate-x-0', 'opacity-100');
                toastEl.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toastEl.remove(), 300);
            });

            container.appendChild(clone);

            // Trigger reflow to enable transition
            requestAnimationFrame(() => {
                toastEl.classList.remove('translate-x-full', 'opacity-0');
                toastEl.classList.add('translate-x-0', 'opacity-100');
            });

            // Auto hide after 5s
            setTimeout(() => {
                if (toastEl.parentNode) {
                    toastEl.classList.remove('translate-x-0', 'opacity-100');
                    toastEl.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => toastEl.remove(), 300);
                }
            }, 5000);

            // Play sound
            // audio.play().catch(e => console.log('Audio autoplay blocked'));
        };

        const pollNotifications = async () => {
            try {
                const response = await fetch('{{ route('global-notification.in-app.latest') }}');
                const data = await response.json();

                if (data.success && data.notification) {
                    const notif = data.notification;

                    // Only show if ID is greater than last seen and it's reasonably fresh (e.g. within 60s)
                    // We can check freshness on server, here we just check ID
                    if (notif.id > lastSeenId) {
                        lastSeenId = notif.id;
                        localStorage.setItem('gn_last_seen_id', lastSeenId);
                        showToast(notif);
                    }
                }
            } catch (error) {
                console.error('Notification poll error:', error);
            }
        };

        // Poll every 30 seconds
        setInterval(pollNotifications, 30000);

        // Initial check after 2s
        setTimeout(pollNotifications, 2000);
    });
</script>
