@foreach (['success' => 'Berhasil', 'error' => 'Ada kendala'] as $type => $title)
    @if (session($type))
        <div
            hidden
            data-flash-notification
            data-notification-icon="{{ $type }}"
            data-notification-title="{{ session('notification_title', $title) }}"
        >{{ session($type) }}</div>
        <noscript>
            <div class="fixed inset-x-4 top-24 z-50 mx-auto max-w-md rounded-2xl border border-neutral-200 bg-white p-5 text-sm text-neutral-900 shadow-lg" role="{{ $type === 'error' ? 'alert' : 'status' }}">
                <strong>{{ session('notification_title', $title) }}</strong>
                <p class="mt-1">{{ session($type) }}</p>
            </div>
        </noscript>
    @endif
@endforeach
