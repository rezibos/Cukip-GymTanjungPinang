<x-filament-panels::page>
    <div class="member-page-intro"><p>Semua pembayaran membership milik akunmu, dari yang masih menunggu sampai yang sudah lunas.</p></div>

    @if ($transactions->isEmpty())
        <div class="member-empty-panel"><p>Belum ada transaksi.</p><span>Pembayaran membership pertamamu akan muncul di sini.</span></div>
    @else
        <div class="member-transactions-table">
            @foreach ($transactions as $transaction)
                <article>
                    <div><p class="member-eyebrow">{{ $transaction->invoice_number }}</p><h2>{{ $transaction->membership->plan->name }}</h2><span>{{ $transaction->created_at->translatedFormat('d M Y, H:i') }}</span></div>
                    <div class="member-transaction-total"><strong>{{ $transaction->formatted_amount }}</strong><span>{{ $transaction->status->label() }}</span></div>
                    <a href="{{ route('membership.invoice', $transaction) }}" class="member-button member-button-secondary">Lihat invoice ↗</a>
                </article>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
