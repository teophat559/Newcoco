<?php

namespace BackendApi\Utils;

class Locale {
    private static $instance = null;
    private $locale;
    private $translations = [];

    private function __construct() {
        $this->locale = $this->getDefaultLocale();
        $this->loadTranslations();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setLocale($locale) {
        if (!in_array($locale, AVAILABLE_LOCALES)) {
            throw new \Exception('Unsupported locale: ' . $locale);
        }

        $this->locale = $locale;
        $this->loadTranslations();
    }

    public function getLocale() {
        return $this->locale;
    }

    public function translate($key, array $params = []) {
        $translation = $this->getTranslation($key);

        if ($translation === null) {
            return $key;
        }

        if (!empty($params)) {
            $translation = $this->replaceParams($translation, $params);
        }

        return $translation;
    }

    public function formatDate($date, $format = null) {
        $format = $format ?? DATE_FORMAT;
        return date($format, strtotime($date));
    }

    public function formatTime($time, $format = null) {
        $format = $format ?? TIME_FORMAT;
        return date($format, strtotime($time));
    }

    public function formatDateTime($datetime, $format = null) {
        $format = $format ?? DATETIME_FORMAT;
        return date($format, strtotime($datetime));
    }

    public function formatNumber($number, $decimals = null) {
        $decimals = $decimals ?? DECIMAL_PLACES;
        return number_format(
            $number,
            $decimals,
            DECIMAL_SEPARATOR,
            THOUSANDS_SEPARATOR
        );
    }

    public function formatCurrency($amount, $currency = null) {
        $currency = $currency ?? DEFAULT_CURRENCY;
        $symbol = CURRENCY_SYMBOL;
        $formatted = $this->formatNumber($amount);

        if (CURRENCY_POSITION === 'left') {
            return $symbol . ' ' . $formatted;
        }

        return $formatted . ' ' . $symbol;
    }

    private function getDefaultLocale() {
        // Try to get locale from session
        if (isset($_SESSION['locale']) && in_array($_SESSION['locale'], AVAILABLE_LOCALES)) {
            return $_SESSION['locale'];
        }

        // Try to get locale from browser
        $browserLocale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
        if (in_array($browserLocale, AVAILABLE_LOCALES)) {
            return $browserLocale;
        }

        // Use default locale
        return DEFAULT_LOCALE;
    }

    private function loadTranslations() {
        $file = LOCALE_DIR . $this->locale . '.php';

        if (file_exists($file)) {
            $this->translations = require $file;
        } else {
            $this->translations = [];
        }
    }

    private function getTranslation($key) {
        $keys = explode('.', $key);
        $translation = $this->translations;

        foreach ($keys as $k) {
            if (!isset($translation[$k])) {
                return null;
            }
            $translation = $translation[$k];
        }

        return $translation;
    }

    private function replaceParams($string, array $params) {
        $replace = [];
        foreach ($params as $key => $value) {
            $replace[':' . $key] = $value;
        }
        return strtr($string, $replace);
    }
}