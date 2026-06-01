<?php

return [
    // Map Stripe price IDs to plan limits. You can add multiple plans.
    // Example price IDs: 'price_basic_monthly', 'price_pro_monthly'
    'plans' => [
        env('STRIPE_BASIC_PRICE', 'price_basic_monthly') => [
            'name' => 'Basic',
            'max_students' => 500,
            'max_teachers' => 50,
            'max_exams' => 100,
            // add other feature flags and limits as needed
        ],
        env('STRIPE_PREMIUM_PRICE', 'price_premium_monthly') => [
            'name' => 'Premium',
            'max_students' => 5000,
            'max_teachers' => 500,
            'max_exams' => 1000,
        ],
        env('STRIPE_GOLD_PRICE', 'price_gold_monthly') => [
            'name' => 'Gold',
            'max_students' => 15000,
            'max_teachers' => 1500,
            'max_exams' => 3000,
        ],
        // 'price_pro_monthly' => [
        //     'name' => 'Pro',
        //     'max_students' => 5000,
        //     'max_teachers' => 500,
        // ],
    ],

    // Default limits when no subscription is found (e.g., during trial)
'defaults' => [
        'max_students' => 100,
        'max_teachers' => 10,
        'max_exams' => 20,
    ],
];
