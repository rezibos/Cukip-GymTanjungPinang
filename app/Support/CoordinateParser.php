<?php

namespace App\Support;

class CoordinateParser
{
    /**
     * Mengambil latitude dan longitude dari teks yang ditempel.
     * Mengembalikan ['lat' => ..., 'lng' => ...] atau null jika tidak ditemukan.
     */
    public static function parse(string $input): ?array
    {
        $input = urldecode(trim($input));
        $num = '(-?\d{1,3}(?:\.\d+)?)';

        // [pola, urutan angka]. Diperiksa dari yang paling akurat.
        $patterns = [
            ['/!3d' . $num . '!4d' . $num . '/', 'latlng'],                    // pin tempat (link panjang)
            ['/!2d' . $num . '!3d' . $num . '/', 'lnglat'],                    // kode embed
            ['/@' . $num . ',' . $num . '/', 'latlng'],                        // .../@0.91,104.46,17z
            ['/[?&](?:q|ll|query)=' . $num . ',' . $num . '/', 'latlng'],      // ?q=0.91,104.46
            ['/^\s*' . $num . '\s*[,;]\s*' . $num . '\s*$/', 'latlng'],        // 0.9187, 104.4665
        ];

        foreach ($patterns as [$regex, $order]) {
            if (! preg_match($regex, $input, $m)) {
                continue;
            }

            [$lat, $lng] = $order === 'latlng'
                ? [(float) $m[1], (float) $m[2]]
                : [(float) $m[2], (float) $m[1]];

            // Tolak angka yang tidak masuk akal (misalnya lat dan lng tertukar)
            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                continue;
            }

            return ['lat' => round($lat, 7), 'lng' => round($lng, 7)];
        }

        return null;
    }
}