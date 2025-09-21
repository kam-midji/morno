<?php

// Core Helper Functions

/**
 * Translates a given key into Persian.
 *
 * This function loads the translation file once per request and returns the
 * corresponding Persian string for a given key.
 *
 * @param string $key The key to translate.
 * @param array $replace An associative array of placeholders to replace in the translated string.
 * @return string The translated string, or the key itself if not found.
 */
function __(string $key, array $replace = []): string
{
    static $translations = null;

    if ($translations === null) {
        // The path is relative to the file that includes this helper,
        // which will be index.php in the public directory.
        $translations = require_once __DIR__ . '/../i18n/fa.php';
    }

    $text = $translations[$key] ?? $key;

    if (!empty($replace) && is_array($replace)) {
        foreach ($replace as $placeholder => $value) {
            $text = str_replace(':' . $placeholder, $value, $text);
        }
    }

    return $text;
}

/**
 * Converts a Jalali datetime string (e.g., "1404/08/01 15:30:00")
 * to a Gregorian datetime string for database storage.
 * @param string $jalaliDateTime
 * @return string|false Gregorian datetime in 'Y-m-d H:i:s' format, or false on failure.
 */
function jalaliToGregorian($jalaliDateTime) {
    if (empty($jalaliDateTime)) {
        return false;
    }

    $parts = explode(' ', $jalaliDateTime);
    $datePart = $parts[0];
    $timePart = $parts[1] ?? '00:00:00';

    $dateParts = explode('/', $datePart);
    if (count($dateParts) !== 3) {
        return false;
    }

    $j_y = intval($dateParts[0]);
    $j_m = intval($dateParts[1]);
    $j_d = intval($dateParts[2]);

    if ($j_y < 1000) return false; // Basic sanity check

    list($g_y, $g_m, $g_d) = jDateTime::toGregorian($j_y, $j_m, $j_d);

    return sprintf('%04d-%02d-%02d', $g_y, $g_m, $g_d) . ' ' . $timePart;
}
