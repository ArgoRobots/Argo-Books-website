<?php
// ---------------------------------------------------------------------------
// Shared date-range selection for admin dashboards.
//
// Mirrors the desktop app (ArgoBooks): a single date-range selection drives the
// page, and where a chart needs a bucket (day / week / month) that bucket is
// derived from the range length rather than chosen manually. See
// ReportChartDataService.GetTimeBucket and ChartSettingsService in the Avalonia
// repo.
//
// This file owns preset naming and range resolution only, so every page offers
// the same dropdown and interprets "Last Quarter" identically. Bucketing and
// the SQL that groups by bucket stay with the pages that query the database
// (admin/website-stats/index.php); pages reading JSON telemetry don't need it.
//
// "All Time" has no universal floor: each page knows its own earliest record,
// so callers pass it in via $all_time_start. Without one, All Time collapses to
// today, which is the same fallback used when a page has no records at all.
// ---------------------------------------------------------------------------

/** Preset display names, in dropdown order (matches DateRangePreset.GetStandardOptions()). */
function date_range_presets()
{
    return [
        'This Month', 'Last Month', 'Last 30 Days', 'Last 100 Days', 'Last 365 Days',
        'This Quarter', 'Last Quarter', 'This Year', 'Last Year', 'All Time', 'Custom Range',
    ];
}

/**
 * Read ?range from the request and validate it against the preset list.
 *
 * @param string $default Preset to fall back to when absent or unrecognised.
 * @return string A name guaranteed to appear in date_range_presets().
 */
function selected_date_range_preset($default = 'Last 30 Days')
{
    $selected = isset($_GET['range']) ? $_GET['range'] : $default;
    return in_array($selected, date_range_presets(), true) ? $selected : $default;
}

/**
 * Resolve a preset (plus optional custom start/end) to concrete [start, end]
 * DateTime bounds. Mirrors ChartSettingsService.UpdateDateRangeFromSelection():
 * the end is always end-of-day so records saved later in the day aren't filtered out.
 *
 * @param string        $preset          One of date_range_presets().
 * @param string|null   $custom_start    'Y-m-d', only read for 'Custom Range'.
 * @param string|null   $custom_end      'Y-m-d', only read for 'Custom Range'.
 * @param DateTime|null $all_time_start  The caller's earliest record, for 'All Time'.
 * @return array{start: DateTime, end: DateTime}
 */
function resolve_date_range($preset, $custom_start = null, $custom_end = null, DateTime $all_time_start = null)
{
    $now   = new DateTime('now');
    $today = (new DateTime('now'))->setTime(0, 0, 0);
    $year  = (int)$now->format('Y');

    // Defaults (used as-is for "Custom Range" with missing/invalid input).
    $start = clone $today;
    $end   = (new DateTime('now'))->setTime(23, 59, 59);

    switch ($preset) {
        case 'This Month':
            $start = (new DateTime('first day of this month'))->setTime(0, 0, 0);
            break;

        case 'Last Month':
            $start = (new DateTime('first day of last month'))->setTime(0, 0, 0);
            $end   = (new DateTime('last day of last month'))->setTime(23, 59, 59);
            break;

        case 'Last 30 Days':
            $start = (clone $today)->modify('-29 days');
            break;

        case 'Last 100 Days':
            $start = (clone $today)->modify('-99 days');
            break;

        case 'Last 365 Days':
            $start = (clone $today)->modify('-364 days');
            break;

        case 'This Quarter':
            $qm = intdiv((int)$now->format('n') - 1, 3) * 3 + 1;
            $start = (new DateTime())->setDate($year, $qm, 1)->setTime(0, 0, 0);
            break;

        case 'Last Quarter':
            $qm = intdiv((int)$now->format('n') - 1, 3) * 3 + 1;
            $this_q_start = (new DateTime())->setDate($year, $qm, 1)->setTime(0, 0, 0);
            $last_q_end   = (clone $this_q_start)->modify('-1 day')->setTime(23, 59, 59);
            $lqm = intdiv((int)$last_q_end->format('n') - 1, 3) * 3 + 1;
            $start = (new DateTime())->setDate((int)$last_q_end->format('Y'), $lqm, 1)->setTime(0, 0, 0);
            $end   = $last_q_end;
            break;

        case 'This Year':
            $start = (new DateTime())->setDate($year, 1, 1)->setTime(0, 0, 0);
            break;

        case 'Last Year':
            $start = (new DateTime())->setDate($year - 1, 1, 1)->setTime(0, 0, 0);
            $end   = (new DateTime())->setDate($year - 1, 12, 31)->setTime(23, 59, 59);
            break;

        case 'All Time':
            $start = $all_time_start ? (clone $all_time_start)->setTime(0, 0, 0) : clone $today;
            break;

        case 'Custom Range':
            $s = $custom_start ? DateTime::createFromFormat('Y-m-d', $custom_start) : false;
            $e = $custom_end ? DateTime::createFromFormat('Y-m-d', $custom_end) : false;
            if ($s && $e) {
                if ($s > $e) {
                    $tmp = $s;
                    $s = $e;
                    $e = $tmp;
                }
                $start = $s->setTime(0, 0, 0);
                $end   = $e->setTime(23, 59, 59);
            }
            break;
    }

    return ['start' => $start, 'end' => $end];
}

/** Human-readable "Jan 1, 2026 - Jan 31, 2026" label for the control bar. */
function format_date_range(DateTime $start, DateTime $end)
{
    return $start->format('M j, Y') . ' – ' . $end->format('M j, Y');
}
