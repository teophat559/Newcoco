<?php

// Locale configuration
define('DEFAULT_LOCALE', 'en');
define('AVAILABLE_LOCALES', ['en', 'vi']);
define('LOCALE_DIR', __DIR__ . '/../locale/');

// Date and time format
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Currency
define('DEFAULT_CURRENCY', 'VND');
define('CURRENCY_SYMBOL', '₫');
define('CURRENCY_POSITION', 'right'); // left or right

// Number format
define('DECIMAL_SEPARATOR', '.');
define('THOUSANDS_SEPARATOR', ',');
define('DECIMAL_PLACES', 2);