<?php

return [
    'name' => 'Bad bot',
    'custom_url_segment' => 'admin',
    'entities' => [
        'customer' => [
            'list' => \App\Sharp\Customer\ListCustomer::class,
            'show' => \App\Sharp\Customer\ShowCustomer::class,
//            'form' => \App\Sharp\Tag\FormTag::class,
//            'validator' => \App\Sharp\SpaceshipSharpValidator::class,
//            'policy' => \App\Sharp\Policies\SpaceshipPolicy::class
        ],
        'answer' => [
            'list' => \App\Sharp\Answer\ListAnswer::class,
            'show' => \App\Sharp\Answer\ShowAnswer::class,
        ],

    ],
    'auth' => [
        'login_attribute' => 'email',
        'password_attribute' => 'password',
        'display_attribute' => 'name',
    ],
    'menu' => [
        [
            'label' => 'Customers',
            'icon' => 'fa-superpowers',
            'entity' => 'customer'
        ],
//        [
//            'label' => 'Answers',
//            'icon' => 'fa-superpowers',
//            'entity' => 'answer'
//        ]
    ]
];
