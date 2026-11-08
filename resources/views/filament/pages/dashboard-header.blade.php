<div class="admin-dashboard-header">
    <div class="admin-dashboard-heading">
        <div>
            <p class="admin-eyebrow">CukipGym / Panel admin</p>
            <h1>Ringkasan operasional.</h1>
            <p class="admin-page-description">Aktivitas gym dan hal yang perlu kamu tindak lanjuti, dalam satu tampilan.</p>
        </div>
        <span class="admin-date"><x-filament::icon icon="heroicon-o-calendar-days" class="size-4" />{{ now()->translatedFormat('d M Y') }}</span>
    </div>
    <div class="admin-welcome">
        <div class="admin-welcome-copy">
            <p class="admin-eyebrow">Selamat datang kembali</p>
            <h2>Halo, {{ auth()->user()->name }}.</h2>
            <p>Pantau pembayaran, kelola member, dan siapkan jadwal latihan berikutnya.</p>
        </div>
        <div class="admin-welcome-actions">
            <a href="{{ \App\Filament\Resources\Transactions\TransactionResource::getUrl('index') }}" class="admin-action-primary">Periksa transaksi <span aria-hidden="true">↗</span></a>
            <a href="{{ \App\Filament\Resources\ClassSessions\ClassSessionResource::getUrl('create') }}" class="admin-action-secondary">Tambah sesi <span aria-hidden="true">＋</span></a>
        </div>
    </div>
    <div class="admin-dashboard-links">
        <p>Data mengikuti aktivitas yang tersimpan di sistem.</p>
        <a href="{{ route('home') }}" target="_blank" rel="noopener">Lihat website <span aria-hidden="true">↗</span></a>
    </div>
</div>
