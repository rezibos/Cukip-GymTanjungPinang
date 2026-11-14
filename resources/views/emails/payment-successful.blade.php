<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pembayaran CukipGym</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f3f3; font-family:Arial, Helvetica, sans-serif; color:#202020;">
    @php
        $checkoutItems = collect($payment->checkout_items ?? []);
        $transactionItems = $payment->transactions->map(fn ($transaction) => [
            'name' => $transaction->membership->plan->name,
            'quantity' => $transaction->quantity,
            'subtotal' => $transaction->amount,
        ]);
        $items = $checkoutItems->isNotEmpty() ? $checkoutItems : $transactionItems;
        $customerName = $payment->customer_name ?? $payment->user?->name ?? 'Member CukipGym';
        $customerEmail = $payment->customer_email ?? $payment->user?->email;
        $customerPhone = $payment->customer_phone ?? $payment->user?->phone;
        $paymentMethod = ucwords(str_replace('_', ' ', $payment->payment_type ?? 'Midtrans'));
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f3f3; padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px; background-color:#ffffff; border:1px solid #dddddd;">
                    <tr>
                        <td align="center" style="padding:32px 32px 20px;">
                            <p style="margin:0; color:#171717; font-size:17px; font-weight:700; letter-spacing:-.02em;">CukipGym</p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:0 32px 28px;">
                            <p style="margin:0 0 8px; color:#23825b; font-size:11px; font-weight:700; letter-spacing:.14em; text-transform:uppercase;">Pembayaran berhasil</p>
                            <h1 style="margin:0; color:#202020; font-size:25px; font-weight:700; line-height:1.3;">Terima kasih atas pembayaran Anda</h1>
                            <p style="max-width:460px; margin:12px auto 0; color:#666666; font-size:13px; line-height:1.65;">Hai {{ $customerName }}, pembayaran membership Anda telah diverifikasi oleh Midtrans. Invoice dan akun CukipGym Anda sudah siap.</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 26px;">
                            <p style="margin:0 0 9px; color:#333333; font-size:13px; font-weight:700;">Informasi pelanggan</p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fafafa; border:1px solid #e7e7e7;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:4px 0; width:120px; color:#777777; font-size:12px;">Nama</td>
                                                <td style="padding:4px 0; color:#202020; font-size:12px; font-weight:600;">{{ $customerName }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:4px 0; color:#777777; font-size:12px;">Email</td>
                                                <td style="padding:4px 0; color:#202020; font-size:12px; font-weight:600;">{{ $customerEmail }}</td>
                                            </tr>
                                            @if ($customerPhone)
                                                <tr>
                                                    <td style="padding:4px 0; color:#777777; font-size:12px;">WhatsApp</td>
                                                    <td style="padding:4px 0; color:#202020; font-size:12px; font-weight:600;">{{ $customerPhone }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="padding:4px 0; color:#777777; font-size:12px;">Metode pembayaran</td>
                                                <td style="padding:4px 0; color:#202020; font-size:12px; font-weight:600;">{{ $paymentMethod }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 26px;">
                            <p style="margin:0 0 9px; color:#333333; font-size:13px; font-weight:700;">Ringkasan pesanan</p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e5e5e5;">
                                <tr>
                                    <td style="padding:12px 0; width:120px; border-bottom:1px solid #e5e5e5; color:#777777; font-size:12px;">Nomor invoice</td>
                                    <td align="right" style="padding:12px 0; border-bottom:1px solid #e5e5e5; color:#202020; font-size:12px; font-weight:600; word-break:break-word;">{{ $payment->order_id }}</td>
                                </tr>
                                @foreach ($items as $item)
                                    <tr>
                                        <td colspan="2" style="padding:12px 0; border-bottom:1px solid #e5e5e5;">
                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="color:#202020; font-size:12px; font-weight:600;">{{ $item['name'] }} <span style="color:#888888; font-weight:400;">× {{ $item['quantity'] }}</span></td>
                                                    <td align="right" style="color:#202020; font-size:12px; font-weight:600;">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="padding:16px 0; color:#202020; font-size:13px; font-weight:700;">Total dibayar</td>
                                    <td align="right" style="padding:16px 0; color:#202020; font-size:17px; font-weight:700;">Rp{{ number_format($payment->gross_amount, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 30px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5; border:1px solid #e1e1e1; border-radius:10px;">
                                <tr>
                                    <td style="padding:22px 24px;">
                                        <p style="margin:0 0 5px; color:#202020; font-size:14px; font-weight:700;">Akun CukipGym Anda</p>
                                        @if ($plainPassword)
                                            <p style="margin:0 0 16px; color:#666666; font-size:12px; line-height:1.55;">Gunakan data berikut untuk login. Password ini dibuat khusus untuk akun baru Anda.</p>
                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="padding:5px 0; width:120px; color:#777777; font-size:12px;">Email login</td>
                                                    <td style="padding:5px 0; color:#202020; font-size:13px; font-weight:700;">{{ $customerEmail }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:5px 0; color:#777777; font-size:12px;">Password sementara</td>
                                                    <td style="padding:5px 0; color:#202020; font-size:13px; font-weight:700; letter-spacing:.04em;">{{ $plainPassword }}</td>
                                                </tr>
                                            </table>
                                            <p style="margin:15px 0 0; color:#777777; font-size:11px; line-height:1.55;">Demi keamanan, simpan data ini dan segera ganti password setelah login.</p>
                                        @else
                                            <p style="margin:0; color:#666666; font-size:12px; line-height:1.55;">Pembayaran telah ditambahkan ke akun {{ $customerEmail }}. Gunakan password akun Anda yang sudah ada untuk login.</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <div style="text-align:center;">
                                <a href="{{ route('login') }}" style="display:inline-block; margin-top:22px; padding:13px 28px; background-color:#2563eb; border-radius:999px; color:#ffffff; font-size:13px; font-weight:700; text-decoration:none;">Login ke CukipGym</a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:22px 32px; border-top:1px solid #e5e5e5; color:#888888; font-size:11px; line-height:1.6;">
                            Email ini dikirim otomatis setelah pembayaran diverifikasi oleh Midtrans.<br>
                            Jika Anda tidak melakukan pembelian ini, segera hubungi admin CukipGym.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
