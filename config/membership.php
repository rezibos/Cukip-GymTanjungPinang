<?php

return [
    'catalog' => [
        'Day Pass' => [
            'duration_days' => 1,
            'price' => 50000,
            'description' => 'Experience our gym facilities and start your fitness journey with no commitment.',
            'features' => ['Main Gym Access', 'Cardio & Weight Equipment'],
        ],
        'Regular' => [
            'duration_days' => 30,
            'price' => 250000,
            'yearly_price' => 2400000,
            'description' => 'Build your routine with essential gym access and facilities for everyday fitness.',
            'features' => ['Main Gym Access', 'Cardio & Weight Equipment', 'Locker & Shower', 'Fitness Classes'],
        ],
        'VIP' => [
            'duration_days' => 30,
            'price' => 500000,
            'yearly_price' => 4800000,
            'description' => 'Elevate your training with premium facilities and exclusive fitness benefits.',
            'features' => [
                'Main Gym Access',
                'Cardio & Weight Equipment',
                'Locker & Shower',
                'Fitness Classes',
                'Exclusive VIP Floor',
                'Premium Equipment',
                'Private Training',
                'VIP Lounge',
            ],
        ],
    ],
];
