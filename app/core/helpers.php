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

/**
 * Prepares an array of proposal objects for rendering in a time-slot grid.
 * Adds grid_column, grid_row_start, and grid_row_span properties to each object.
 *
 * @param array $proposals The array of proposal objects from the database.
 * @param string $weekStartDate The start date of the week being displayed ('Y-m-d').
 * @param int $intervalMinutes The duration of each time slot in minutes (e.g., 30).
 * @param string $dayStartTime The start time of the grid's day (e.g., '07:00').
 * @return array The processed array of proposals.
 */
function prepareProposalsForGrid($proposals, $weekStartDate, $intervalMinutes = 30, $dayStartTime = '04:00') {
    // This function now assumes a grid starting at 04:00, with a 1-row header.
    $gridStartHour = 4;

    foreach ($proposals as $proposal) {
        $eventStart = new DateTime($proposal->event_datetime);
        $eventEnd = !empty($proposal->event_end_datetime) ? new DateTime($proposal->event_end_datetime) : (clone $eventStart)->modify('+1 hour');

        // Calculate grid_column (1-7 for Sat-Fri)
        $dayOfWeek = (int)$eventStart->format('w'); // 0=Sun, 6=Sat
        $proposal->grid_column = (($dayOfWeek + 1) % 7) + 1; // 1=Sat, 2=Sun, ..., 7=Fri

        // Calculate grid_row_start (based on a 04:00 start)
        $startMinutesIntoDay = ($eventStart->format('G') * 60) + (int)$eventStart->format('i');
        $gridStartMinutesIntoDay = ($gridStartHour * 60);

        $minutesFromGridStart = $startMinutesIntoDay - $gridStartMinutesIntoDay;
        // +2 because grid rows are 1-based and we have a header row.
        $proposal->grid_row_start = floor($minutesFromGridStart / $intervalMinutes) + 2;

        // Calculate grid_row_span
        $durationMinutes = ($eventEnd->getTimestamp() - $eventStart->getTimestamp()) / 60;
        $proposal->grid_row_span = max(1, ceil($durationMinutes / $intervalMinutes));

        // Handle events that start before the grid's visible time
        if ($proposal->grid_row_start < 2) {
            $proposal->grid_row_span -= (2 - $proposal->grid_row_start);
            $proposal->grid_row_start = 2;
        }
    }
    return $proposals;
}
