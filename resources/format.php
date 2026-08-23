<?php
/**
 * Shared display formatters.
 */

/**
 * Human-readable byte size, e.g. 5.4 MB. Caps at GB and clamps negatives to 0.
 *
 * Usage:
 *   echo formatFileSize(filesize($path));
 */
function formatFileSize($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, 1) . ' ' . $units[$pow];
}
