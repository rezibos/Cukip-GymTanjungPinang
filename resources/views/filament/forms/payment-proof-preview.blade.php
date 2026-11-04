<div>
    @php $record = $getRecord(); @endphp

    @if ($record?->payment_proof_url)
        <a href="{{ $record->payment_proof_url }}" target="_blank" class="text-sm text-primary-600 underline">
            Lihat bukti transfer (buka di tab baru)
        </a>
    @else
        <p class="text-sm text-gray-500">Bukti transfer belum diunggah oleh user.</p>
    @endif
</div>