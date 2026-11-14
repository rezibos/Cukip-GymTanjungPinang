<?php

return [
    'catalog' => [
        '1 Day Pass' => [
            'duration_days' => 1,
            'price' => 50000,
            'original_price' => null,
            'discount_label' => null,
            'badge' => 'Trial',
            'description' => 'Coba pengalaman latihan di CukipGym untuk satu kali kunjungan tanpa komitmen panjang.',
            'benefits' => [],
        ],
        '1 Month' => [
            'duration_days' => 30,
            'price' => 500000,
            'original_price' => null,
            'discount_label' => null,
            'badge' => null,
            'description' => 'Nikmati akses membership selama satu bulan untuk mulai membangun rutinitas latihanmu.',
            'benefits' => [],
        ],
        '3 Month' => [
            'duration_days' => 90,
            'price' => 1350000,
            'original_price' => 1500000,
            'discount_label' => '10% OFF',
            'badge' => null,
            'description' => 'Mulai membangun kebiasaan latihan dengan masa aktif tiga bulan dan bonus pembelian.',
            'benefits' => [
                '1 sesi personal trainer',
                'Gratis gantungan kunci CukipGym',
            ],
        ],
        '6 Month' => [
            'duration_days' => 180,
            'price' => 2400000,
            'original_price' => 3000000,
            'discount_label' => '20% OFF',
            'badge' => null,
            'description' => 'Bangun progres lebih konsisten dengan masa aktif enam bulan dan bonus pendamping.',
            'benefits' => [
                '2 sesi personal trainer',
                'Gratis kaos CukipGym',
                'Gratis gantungan kunci CukipGym',
            ],
        ],
        '12 Month' => [
            'duration_days' => 365,
            'price' => 4200000,
            'original_price' => 6000000,
            'discount_label' => '30% OFF',
            'badge' => null,
            'description' => 'Jadikan latihan sebagai gaya hidup dengan membership satu tahun dan bonus eksklusif.',
            'benefits' => [
                '3 sesi personal trainer',
                'Gratis shaker CukipGym',
                'Gratis kaos CukipGym',
                'Gratis gantungan kunci CukipGym',
            ],
        ],
        '18 Month' => [
            'duration_days' => 540,
            'price' => 5400000,
            'original_price' => 9000000,
            'discount_label' => '40% OFF',
            'badge' => 'Best Value',
            'description' => 'Pilihan terlengkap untuk perjalanan latihan jangka panjang dengan nilai terbaik.',
            'benefits' => [
                '5 sesi personal trainer',
                'Gratis tas gym CukipGym',
                'Gratis handuk CukipGym',
                'Gratis shaker CukipGym',
                'Gratis kaos CukipGym',
                'Gratis gantungan kunci CukipGym',
            ],
        ],
    ],
];
