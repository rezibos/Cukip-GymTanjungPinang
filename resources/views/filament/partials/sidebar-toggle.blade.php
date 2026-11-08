<button
    type="button"
    class="admin-nav-toggle"
    data-admin-sidebar-toggle
    x-data="{}"
    x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
    x-bind:aria-expanded="$store.sidebar.isOpen"
    x-bind:aria-label="$store.sidebar.isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi'"
    aria-controls="fi-main-sidebar"
>
    <svg class="admin-nav-toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>
