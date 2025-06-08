<?php
return [
    'driver' => 'smtp',
    'host' => 'smtp.mailtrap.io',
    'port' => 2525,
    'username' => null,
    'password' => null,
    'encryption' => null,
    'from' => [
        'address' => 'noreply@votingsystem.com',
        'name' => 'Voting System'
    ],
    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
];