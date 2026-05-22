<?php

/**
 * Telemetry allowlist filter.
 *
 * Server-side enforcement: rebuilds the uploaded payload from scratch using only
 * the fields listed in the allowlist. Anything not listed is silently dropped
 * before the file is written to admin/data-logs/. Applied to both free and
 * premium uploads, so the on-the-wire shape is identical across tiers.
 *
 * See docs/superpowers/specs/2026-05-21-free-tier-telemetry-design.md.
 */

const TELEMETRY_PLATFORMS = ['Windows', 'macOS', 'Linux', 'Other'];

const TELEMETRY_SESSION_ACTIONS = ['SessionStart', 'SessionEnd'];

const TELEMETRY_EXPORT_TYPES = ['Excel', 'GoogleSheets', 'Pdf', 'Csv', 'Backup', 'Receipts', 'ChartImage'];

const TELEMETRY_API_NAMES = ['Gemini', 'OpenExchangeRates', 'GoogleSheets', 'ReceiptScanProxy', 'MicrosoftTranslator'];

const TELEMETRY_ERROR_CATEGORIES = [
    'Unknown', 'Network', 'FileSystem', 'Parsing', 'Validation', 'UI', 'Api',
    'Export', 'Import', 'License', 'Authentication', 'Encryption'
];

const TELEMETRY_FEATURE_NAMES = [
    'ChartViewed', 'ChartTypeChanged',
    'ReportGenerated', 'ReportPrinted',
    'ReceiptScanned', 'ReceiptManualEntry',
    'DataImported', 'DataExported', 'BackupCreated', 'BackupRestored',
    'InvoiceCreated', 'ExpenseCreated', 'RevenueCreated', 'PaymentRecorded',
    'ProductCreated', 'StockAdjusted', 'PurchaseOrderCreated',
    'CustomerCreated', 'SupplierCreated',
    'RentalItemCreated', 'RentalRecordCreated',
    'AiSearchUsed', 'AiSuggestionAccepted',
    'ThemeChanged', 'LanguageChanged'
];

/**
 * Bound a string field: must be a string, length-cap at $maxLen, drop control chars.
 * Returns null if not a usable string.
 */
function telemetry_clean_string($value, int $maxLen = 64): ?string
{
    if (!is_string($value) || $value === '') {
        return null;
    }
    // Drop control characters except tab/newline/CR
    $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
    if ($clean === null || $clean === '') {
        return null;
    }
    return mb_substr($clean, 0, $maxLen);
}

/**
 * Validate a value against an allowed-enum list. Returns "Unknown" if invalid.
 */
function telemetry_validate_enum($value, array $allowed): string
{
    if (is_string($value) && in_array($value, $allowed, true)) {
        return $value;
    }
    return 'Unknown';
}

/**
 * Cast to int with a max bound. Returns null if not numeric.
 */
function telemetry_clean_int($value, int $max = PHP_INT_MAX): ?int
{
    if (!is_numeric($value)) {
        return null;
    }
    $i = (int)$value;
    if ($i < 0) {
        return 0;
    }
    return min($i, $max);
}

/**
 * Validate an ISO 8601 timestamp string; return it back if usable, else server-now.
 */
function telemetry_clean_timestamp($value): string
{
    if (is_string($value)) {
        $ts = strtotime($value);
        if ($ts !== false) {
            return gmdate('Y-m-d\TH:i:s\Z', $ts);
        }
    }
    return gmdate('Y-m-d\TH:i:s\Z');
}

/**
 * Filter a single telemetry event to its allowlist.
 * Returns null if the dataType is unknown.
 */
function filter_telemetry_event(array $event): ?array
{
    $dataType = $event['dataType'] ?? null;
    if (!is_string($dataType)) {
        return null;
    }

    // Base fields for every event type
    $base = [
        'dataId' => telemetry_clean_string($event['dataId'] ?? null, 32),
        'timestamp' => telemetry_clean_timestamp($event['timestamp'] ?? null),
        'dataType' => $dataType,
    ];

    switch ($dataType) {
        case 'Session':
            return $base + [
                'action' => telemetry_validate_enum($event['action'] ?? null, TELEMETRY_SESSION_ACTIONS),
                'durationSeconds' => telemetry_clean_int($event['durationSeconds'] ?? null),
            ];

        case 'FeatureUsage':
            return $base + [
                'featureName' => telemetry_validate_enum($event['featureName'] ?? null, TELEMETRY_FEATURE_NAMES),
                'durationMs' => telemetry_clean_int($event['durationMs'] ?? null),
            ];

        case 'Error':
            return $base + [
                'errorCategory' => telemetry_validate_enum($event['errorCategory'] ?? null, TELEMETRY_ERROR_CATEGORIES),
                'errorCode' => telemetry_clean_string($event['errorCode'] ?? null, 128),
                'sourceFile' => telemetry_clean_string($event['sourceFile'] ?? null, 128),
                'lineNumber' => telemetry_clean_int($event['lineNumber'] ?? null),
                'methodName' => telemetry_clean_string($event['methodName'] ?? null, 128),
            ];

        case 'Export':
            return $base + [
                'exportType' => telemetry_validate_enum($event['exportType'] ?? null, TELEMETRY_EXPORT_TYPES),
                'durationMs' => telemetry_clean_int($event['durationMs'] ?? null),
                'fileSize' => telemetry_clean_int($event['fileSize'] ?? null),
            ];

        case 'ApiUsage':
            return $base + [
                'apiName' => telemetry_validate_enum($event['apiName'] ?? null, TELEMETRY_API_NAMES),
                'durationMs' => telemetry_clean_int($event['durationMs'] ?? null),
                'success' => isset($event['success']) ? (bool)$event['success'] : null,
            ];

        default:
            return null;
    }
}

/**
 * Filter the full upload payload to the allowlist.
 * Rebuilds the payload from scratch. Fields not listed are dropped.
 */
function filter_telemetry_payload(array $payload): array
{
    $events = [];
    if (isset($payload['events']) && is_array($payload['events'])) {
        foreach ($payload['events'] as $event) {
            if (!is_array($event)) {
                continue;
            }
            $filtered = filter_telemetry_event($event);
            if ($filtered !== null) {
                $events[] = $filtered;
            }
        }
    }

    $out = [
        'uploadTime' => telemetry_clean_timestamp($payload['uploadTime'] ?? null),
        'appVersion' => null,
        'platform' => telemetry_validate_enum($payload['platform'] ?? null, TELEMETRY_PLATFORMS),
        'eventCount' => count($events),
        'events' => $events,
    ];

    // appVersion: strict charset (alphanumerics, dot, dash, underscore)
    $appVersion = $payload['appVersion'] ?? null;
    if (is_string($appVersion) && preg_match('/^[\w.\-]{1,32}$/', $appVersion)) {
        $out['appVersion'] = $appVersion;
    }

    // Geo: country/countryCode/region/timezone only. No city, no hashedIp.
    $geo = $payload['geoLocation'] ?? null;
    if (is_array($geo)) {
        $out['geoLocation'] = [
            'country' => telemetry_clean_string($geo['country'] ?? null, 64),
            'countryCode' => telemetry_clean_string($geo['countryCode'] ?? null, 8),
            'region' => telemetry_clean_string($geo['region'] ?? null, 64),
            'timezone' => telemetry_clean_string($geo['timezone'] ?? null, 64),
        ];
    }

    return $out;
}
