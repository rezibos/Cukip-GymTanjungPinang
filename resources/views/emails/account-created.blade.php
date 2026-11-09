<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun CukipGym</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px; background-color:#ffffff; border-radius:20px; overflow:hidden;">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#171717; padding:32px 36px;">
                            <p style="margin:0; color:#ffffff; font-size:18px; font-weight:700; letter-spacing:-.03em;">CukipGym</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:36px;">
                            <h1 style="margin:0 0 8px; font-size:24px; font-weight:700; color:#171717; letter-spacing:-.04em;">Selamat datang, {{ $user->name }}!</h1>
                            <p style="margin:0 0 28px; font-size:14px; line-height:1.7; color:#737373;">Pembayaran membership kamu berhasil. Akun CukipGym sudah otomatis dibuatkan untukmu. Gunakan kredensial di bawah ini untuk login:</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f2; border-radius:14px; padding:24px;">
                                <tr>
                                    <td style="padding:24px;">
                                        <p style="margin:0 0 14px; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.15em; color:#858585;">Kredensial Login</p>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#858585; width:80px;">Email</td>
                                                <td style="padding:6px 0; font-size:14px; font-weight:600; color:#171717;">{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#858585; width:80px;">Password</td>
                                                <td style="padding:6px 0; font-size:14px; font-weight:600; color:#171717;">{{ $plainPassword }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0; font-size:12px; line-height:1.7; color:#9a9a9a;">Demi keamanan, segera ganti password setelah login pertama kali.</p>

                            <a href="{{ url('/login') }}" style="display:inline-block; margin-top:28px; padding:14px 28px; background-color:#171717; color:#ffffff; font-size:13px; font-weight:600; text-decoration:none; border-radius:100px;">Login Sekarang →</a>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:0 36px 32px;">
                            <p style="margin:0; padding-top:24px; border-top:1px solid #e5e5e5; font-size:11px; line-height:1.6; color:#9a9a9a;">Email ini dikirim otomatis oleh sistem CukipGym. Jika kamu tidak merasa mendaftar, abaikan email ini.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
