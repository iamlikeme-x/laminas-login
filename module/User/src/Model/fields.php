<?php

return [
    'id' => [
        'name' => 'id',
        'type' => 'hidden',
        'required' => true,
        'filters' => [
            ['name' => ToInt::class],
        ],
    ],
    'username' => [
        'name' => 'username',
        'type' => 'text',
        'options' => [
            'label' => 'Username',
        ],
        'required' => true,
        'filters' => [
            ['name' => StripTags::class],
            ['name' => StringTrim::class],
        ],
    ],
    'passwd' => [
        'name' => 'passwd',
        'type' => 'password',
        'options' => [
            'label' => 'Password',
        ],
        'required' => true,
        'filters' => [
            ['name' => StripTags::class],
            ['name' => StringTrim::class],
        ],
        'validators' => [
            [
                'name' => StringLength::class,
                'options' => [
                    'encoding' => 'UTF-8',
                    'min'      => 5,
                    'max'      => 100,
                ]
            ]
        ]
    ],
];