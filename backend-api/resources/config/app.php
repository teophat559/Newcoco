<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    'name' => $_ENV['APP_NAME'] ?? 'Contest Platform',

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */
    'env' => $_ENV['APP_ENV'] ?? 'production',

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */
    'debug' => $_ENV['APP_DEBUG'] ?? false,

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */
    'timezone' => 'Asia/Ho_Chi_Minh',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */
    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */
    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */
    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */
    'key' => $_ENV['APP_KEY'] ?? null,

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Service Configuration
    |--------------------------------------------------------------------------
    */
    'services' => [
        'database' => [
            'driver' => $_ENV['DB_CONNECTION'] ?? 'mysql',
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? '3306',
            'database' => $_ENV['DB_DATABASE'] ?? 'forge',
            'username' => $_ENV['DB_USERNAME'] ?? 'forge',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ],
        'mail' => [
            'driver' => $_ENV['MAIL_MAILER'] ?? 'smtp',
            'host' => $_ENV['MAIL_HOST'] ?? 'smtp.mailgun.org',
            'port' => $_ENV['MAIL_PORT'] ?? 587,
            'username' => $_ENV['MAIL_USERNAME'] ?? null,
            'password' => $_ENV['MAIL_PASSWORD'] ?? null,
            'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            'from' => [
                'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@example.com',
                'name' => $_ENV['MAIL_FROM_NAME'] ?? 'Example',
            ],
        ],
        'session' => [
            'driver' => $_ENV['SESSION_DRIVER'] ?? 'file',
            'lifetime' => 120,
            'expire_on_close' => false,
            'encrypt' => false,
            'files' => storage_path('framework/sessions'),
            'connection' => null,
            'table' => 'sessions',
            'lottery' => [2, 100],
            'cookie' => 'laravel_session',
            'path' => '/',
            'domain' => null,
            'secure' => false,
            'http_only' => true,
            'same_site' => 'lax',
        ],
        'cache' => [
            'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
            'path' => storage_path('framework/cache'),
            'lifetime' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */
    'aliases' => [
        'App' => BackendApi\Core\Application::class,
        'Config' => BackendApi\Core\Config::class,
        'DB' => BackendApi\Core\Database::class,
        'Mail' => BackendApi\Core\Mail::class,
        'Session' => BackendApi\Core\Session::class,
        'Cache' => BackendApi\Core\Cache::class,
        'Log' => BackendApi\Core\Logger::class,
        'Auth' => BackendApi\Core\Auth::class,
        'Validator' => BackendApi\Core\Validator::class,
        'Storage' => BackendApi\Core\Storage::class,
    ],
];