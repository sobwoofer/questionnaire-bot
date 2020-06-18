<?php

return [
    'name' => 'Saturn',
    'custom_url_segment' => 'admin',
    'entities' => [
        'tag' => [
            'list' => \App\Sharp\Tag\ListTag::class,
            'form' => \App\Sharp\Tag\FormTag::class,
            'show' => \App\Sharp\Tag\ShowTag::class,
//            'validator' => \App\Sharp\SpaceshipSharpValidator::class,
//            'policy' => \App\Sharp\Policies\SpaceshipPolicy::class
        ]
    ],
    'auth' => [
        'login_attribute' => 'email',
        'password_attribute' => 'password',
        'display_attribute' => 'name',
    ],
    'menu' => [
        [
            'label' => 'Post',
            'icon' => 'fa-superpowers',
            'entity' => 'post'
        ],
        [
            'label' => 'Tag',
            'icon' => 'fa-superpowers',
            'entity' => 'tag'
        ]
    ]
];
