<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect to index.php
header('Location: ' . SITE_URL);
exit;