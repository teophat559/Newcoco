<?php

if (!function_exists('storage_path')) {
    /**
     * Get the path to the storage directory.
     *
     * @param string $path
     * @return string
     */
    function storage_path($path = '') {
        return dirname(__DIR__) . '/storage' . ($path ? '/' . $path : $path);
    }
}

// ... existing code ...