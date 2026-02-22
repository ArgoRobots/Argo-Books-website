<?php
session_start();
require_once '../../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Set page variables for the header
$page_title = "Website Statistics";
$page_description = "View comprehensive analytics, user statistics, and performance metrics";

/**
 * Get SQL period formatting based on period type
 * @param string $period Period type (day, week, month, year)
 * @return array [sql_period, display_format]
 */
function get_period_formatting($period)
{
    $formats = [
        'day' => ['DATE(created_at)', 'DATE(created_at)'],
        'week' => ['YEARWEEK(created_at)', 'CONCAT("Week ", WEEK(created_at), ", ", YEAR(created_at))'],
        'month' => ['DATE_FORMAT(created_at, "%Y-%m")', 'DATE_FORMAT(created_at, "%b %Y")'],
        'year' => ['YEAR(created_at)', 'YEAR(created_at)']
    ];
    return $formats[$period] ?? $formats['month'];
}

/**
 * Generic function to get statistics by period from any table
 * @param string $table Table name
 * @param string $period Period type
 * @param int $limit Number of results
 * @param string $where_clause Optional WHERE clause (without WHERE keyword)
 * @return array Statistics data
 */
function get_stats_by_period($table, $period = 'month', $limit = 12, $where_clause = '')
{
    $db = get_db_connection();
    list($sql_period, $display_format) = get_period_formatting($period);

    $where = $where_clause ? "WHERE $where_clause" : '';
    $query = "
        SELECT
            $sql_period as period,
            $display_format as display_period,
            COUNT(*) as count
        FROM $table
        $where
        GROUP BY period, display_period
        ORDER BY period DESC
        LIMIT ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

// Function to get download statistics by period
function get_downloads_by_period($period = 'month', $limit = 12)
{
    return get_stats_by_period('statistics', $period, $limit, "event_type = 'download'");
}

// Function to get user registrations by period
function get_registrations_by_period($period = 'month', $limit = 12)
{
    return get_stats_by_period('community_users', $period, $limit);
}

// Function to get page view statistics
function get_page_views_by_period($period = 'month', $limit = 12)
{
    $db = get_db_connection();

    $sql_period = '';
    $display_format = '';
    switch ($period) {
        case 'day':
            $sql_period = 'DATE(created_at)';
            $display_format = 'DATE(created_at)';
            break;
        case 'week':
            $sql_period = 'YEARWEEK(created_at)';
            $display_format = 'CONCAT("Week ", WEEK(created_at), ", ", YEAR(created_at))';
            break;
        case 'month':
            $sql_period = 'DATE_FORMAT(created_at, "%Y-%m")';
            $display_format = 'DATE_FORMAT(created_at, "%b %Y")';
            break;
        case 'year':
            $sql_period = 'YEAR(created_at)';
            $display_format = 'YEAR(created_at)';
            break;
    }

    $query = "
        SELECT
            $sql_period as period,
            $display_format as display_period,
            COUNT(*) as count
        FROM statistics
        WHERE event_type = 'page_view'
        GROUP BY period, display_period
        ORDER BY period DESC
        LIMIT ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

// Function to get community post views
function get_community_post_views()
{
    $db = get_db_connection();
    $query = "
        SELECT 
            SUM(views) as total_views,
            AVG(views) as avg_views_per_post,
            MAX(views) as most_viewed
        FROM community_posts";

    $result = $db->query($query);
    $data = $result->fetch_assoc();

    return $data;
}

// Function to get community activity by post type
function get_community_post_types()
{
    $db = get_db_connection();
    $query = "
        SELECT 
            post_type,
            COUNT(*) as count,
            SUM(views) as total_views
        FROM community_posts
        GROUP BY post_type";

    $result = $db->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

// Function to get geographic distribution of users by page views
function get_user_countries($limit = 10)
{
    $db = get_db_connection();
    $query = "
        SELECT 
            country_code,
            COUNT(DISTINCT ip_address) as count
        FROM statistics
        WHERE country_code IS NOT NULL AND country_code != ''
        GROUP BY country_code
        ORDER BY count DESC
        LIMIT ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();

    return $data;
}

// Function to get downloads by country
function get_downloads_by_country($limit = 10)
{
    $db = get_db_connection();
    $query = "
        SELECT 
            country_code,
            COUNT(*) as download_count
        FROM statistics
        WHERE event_type = 'download' AND country_code IS NOT NULL AND country_code != ''
        GROUP BY country_code
        ORDER BY download_count DESC
        LIMIT ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();

    return $data;
}

// Function to get browser/platform statistics
function get_user_agents()
{
    $db = get_db_connection();
    $query = "
        SELECT 
            CASE
                WHEN user_agent LIKE '%Chrome%' THEN 'Chrome'
                WHEN user_agent LIKE '%Firefox%' THEN 'Firefox'
                WHEN user_agent LIKE '%Safari%' THEN 'Safari'
                WHEN user_agent LIKE '%Edge%' THEN 'Edge'
                WHEN user_agent LIKE '%MSIE%' OR user_agent LIKE '%Trident%' THEN 'Internet Explorer'
                ELSE 'Other'
            END as browser,
            COUNT(*) as count
        FROM statistics
        WHERE user_agent IS NOT NULL
        GROUP BY browser
        ORDER BY count DESC";

    $result = $db->query($query);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

// Function to get conversion rate data
function get_conversion_data()
{
    $db = get_db_connection();

    // Get total downloads (from statistics table)
    $download_query = "SELECT COUNT(*) as count FROM statistics WHERE event_type = 'download'";
    $download_result = $db->query($download_query);
    $downloads = $download_result->fetch_assoc()['count'];

    // Get total registrations
    $reg_query = "SELECT COUNT(*) as count FROM community_users";
    $reg_result = $db->query($reg_query);
    $registrations = $reg_result->fetch_assoc()['count'];

    return [
        'downloads' => $downloads,
        'registrations' => $registrations,
    ];
}

// Function to get most active users
function get_most_active_users($limit = 5)
{
    $db = get_db_connection();
    $query = "
        SELECT 
            u.username,
            u.email,
            COUNT(DISTINCT p.id) as post_count,
            COUNT(DISTINCT c.id) as comment_count,
            SUM(p.views) as total_views,
            (COUNT(DISTINCT p.id) + COUNT(DISTINCT c.id)) as activity_score
        FROM community_users u
        LEFT JOIN community_posts p ON u.id = p.user_id
        LEFT JOIN community_comments c ON u.id = c.user_id
        GROUP BY u.id, u.username, u.email
        ORDER BY activity_score DESC
        LIMIT ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

// Get statistics by period (default to month)
$period = isset($_GET['period']) ? $_GET['period'] : 'month';
$allowed_periods = ['day', 'week', 'month', 'year'];
if (!in_array($period, $allowed_periods)) {
    $period = 'month';
}

$downloads = get_downloads_by_period($period);
$registrations = get_registrations_by_period($period);
$page_views = get_page_views_by_period($period);
$post_views = get_community_post_views();
$post_types = get_community_post_types();
$user_countries = get_user_countries();
$downloads_by_country = get_downloads_by_country();
$user_agents = get_user_agents();
$conversion_data = get_conversion_data();
$active_users = get_most_active_users();

// Prepare data for charts
$chart_labels = [];
$downloads_data = [];
$registrations_data = [];
$page_views_data = [];

// Reverse arrays to show chronological order
$downloads = array_reverse($downloads);
$registrations = array_reverse($registrations);
$page_views = array_reverse($page_views);

foreach ($downloads as $item) {
    $chart_labels[] = isset($item['display_period']) ? $item['display_period'] : $item['period'];
    $downloads_data[] = $item['count'];
}

$reg_data = [];
foreach ($registrations as $item) {
    $period_key = $item['period'];
    $reg_data[$period_key] = $item['count'];
}

// Align registration data with download periods
foreach ($downloads as $index => $item) {
    $period_key = $item['period'];
    $registrations_data[] = isset($reg_data[$period_key]) ? $reg_data[$period_key] : 0;
}

// Align page view data with download periods
$view_data = [];
foreach ($page_views as $item) {
    $period_key = $item['period'];
    $view_data[$period_key] = $item['count'];
}

foreach ($downloads as $index => $item) {
    $period_key = $item['period'];
    $page_views_data[] = isset($view_data[$period_key]) ? $view_data[$period_key] : 0;
}

// Calculate growth rate
$latest_growth = 0;
if (count($downloads_data) >= 2) {
    $latest = end($downloads_data);
    $previous = prev($downloads_data);
    if ($previous > 0) {
        $latest_growth = round((($latest - $previous) / $previous) * 100, 1);
    }
}

// Format post views numbers
$total_post_views = isset($post_views['total_views']) ? number_format($post_views['total_views']) : 0;
$avg_post_views = isset($post_views['avg_views_per_post']) ? round($post_views['avg_views_per_post'], 1) : 0;
$most_viewed = isset($post_views['most_viewed']) ? number_format($post_views['most_viewed']) : 0;

// Prepare post type data for charts
$post_type_labels = [];
$post_type_counts = [];
$post_type_views = [];

foreach ($post_types as $type) {
    $post_type_labels[] = ucfirst($type['post_type']);
    $post_type_counts[] = (int)$type['count'];
    $post_type_views[] = (int)$type['total_views'];
}

// Prepare country data for charts
$country_labels = [];
$country_counts = [];

foreach ($user_countries as $country) {
    $country_labels[] = $country['country_code'];
    $country_counts[] = $country['count'];
}

// Prepare downloads by country data
$downloads_country_labels = [];
$downloads_country_counts = [];

foreach ($downloads_by_country as $country) {
    $downloads_country_labels[] = $country['country_code'];
    $downloads_country_counts[] = $country['download_count'];
}

// Prepare browser data for charts
$browser_labels = [];
$browser_counts = [];

foreach ($user_agents as $browser) {
    $browser_labels[] = $browser['browser'];
    $browser_counts[] = (int)$browser['count'];
}

// Map all ISO 3166-1 alpha-2 codes to full country names
$country_name_map = [
    'AF' => 'Afghanistan',
    'AX' => 'Åland Islands',
    'AL' => 'Albania',
    'DZ' => 'Algeria',
    'AS' => 'American Samoa',
    'AD' => 'Andorra',
    'AO' => 'Angola',
    'AI' => 'Anguilla',
    'AQ' => 'Antarctica',
    'AG' => 'Antigua and Barbuda',
    'AR' => 'Argentina',
    'AM' => 'Armenia',
    'AW' => 'Aruba',
    'AU' => 'Australia',
    'AT' => 'Austria',
    'AZ' => 'Azerbaijan',
    'BS' => 'Bahamas',
    'BH' => 'Bahrain',
    'BD' => 'Bangladesh',
    'BB' => 'Barbados',
    'BY' => 'Belarus',
    'BE' => 'Belgium',
    'BZ' => 'Belize',
    'BJ' => 'Benin',
    'BM' => 'Bermuda',
    'BT' => 'Bhutan',
    'BO' => 'Bolivia',
    'BQ' => 'Bonaire',
    'BA' => 'Bosnia and Herzegovina',
    'BW' => 'Botswana',
    'BV' => 'Bouvet Island',
    'BR' => 'Brazil',
    'IO' => 'British Indian Ocean Territory',
    'BN' => 'Brunei',
    'BG' => 'Bulgaria',
    'BF' => 'Burkina Faso',
    'BI' => 'Burundi',
    'KH' => 'Cambodia',
    'CM' => 'Cameroon',
    'CA' => 'Canada',
    'CV' => 'Cape Verde',
    'KY' => 'Cayman Islands',
    'CF' => 'Central African Republic',
    'TD' => 'Chad',
    'CL' => 'Chile',
    'CN' => 'China',
    'CX' => 'Christmas Island',
    'CC' => 'Cocos (Keeling) Islands',
    'CO' => 'Colombia',
    'KM' => 'Comoros',
    'CD' => 'Congo (DRC)',
    'CG' => 'Congo (Republic)',
    'CK' => 'Cook Islands',
    'CR' => 'Costa Rica',
    'CI' => "Côte d'Ivoire",
    'HR' => 'Croatia',
    'CU' => 'Cuba',
    'CW' => 'Curaçao',
    'CY' => 'Cyprus',
    'CZ' => 'Czech Republic',
    'DK' => 'Denmark',
    'DJ' => 'Djibouti',
    'DM' => 'Dominica',
    'DO' => 'Dominican Republic',
    'EC' => 'Ecuador',
    'EG' => 'Egypt',
    'SV' => 'El Salvador',
    'GQ' => 'Equatorial Guinea',
    'ER' => 'Eritrea',
    'EE' => 'Estonia',
    'SZ' => 'Eswatini',
    'ET' => 'Ethiopia',
    'FK' => 'Falkland Islands',
    'FO' => 'Faroe Islands',
    'FJ' => 'Fiji',
    'FI' => 'Finland',
    'FR' => 'France',
    'GF' => 'French Guiana',
    'PF' => 'French Polynesia',
    'TF' => 'French Southern Territories',
    'GA' => 'Gabon',
    'GM' => 'Gambia',
    'GE' => 'Georgia',
    'DE' => 'Germany',
    'GH' => 'Ghana',
    'GI' => 'Gibraltar',
    'GR' => 'Greece',
    'GL' => 'Greenland',
    'GD' => 'Grenada',
    'GP' => 'Guadeloupe',
    'GU' => 'Guam',
    'GT' => 'Guatemala',
    'GG' => 'Guernsey',
    'GN' => 'Guinea',
    'GW' => 'Guinea-Bissau',
    'GY' => 'Guyana',
    'HT' => 'Haiti',
    'HM' => 'Heard Island and McDonald Islands',
    'VA' => 'Vatican City',
    'HN' => 'Honduras',
    'HK' => 'Hong Kong',
    'HU' => 'Hungary',
    'IS' => 'Iceland',
    'IN' => 'India',
    'ID' => 'Indonesia',
    'IR' => 'Iran',
    'IQ' => 'Iraq',
    'IE' => 'Ireland',
    'IM' => 'Isle of Man',
    'IL' => 'Israel',
    'IT' => 'Italy',
    'JM' => 'Jamaica',
    'JP' => 'Japan',
    'JE' => 'Jersey',
    'JO' => 'Jordan',
    'KZ' => 'Kazakhstan',
    'KE' => 'Kenya',
    'KI' => 'Kiribati',
    'KP' => 'North Korea',
    'KR' => 'South Korea',
    'KW' => 'Kuwait',
    'KG' => 'Kyrgyzstan',
    'LA' => 'Laos',
    'LV' => 'Latvia',
    'LB' => 'Lebanon',
    'LS' => 'Lesotho',
    'LR' => 'Liberia',
    'LY' => 'Libya',
    'LI' => 'Liechtenstein',
    'LT' => 'Lithuania',
    'LU' => 'Luxembourg',
    'MO' => 'Macau',
    'MG' => 'Madagascar',
    'MW' => 'Malawi',
    'MY' => 'Malaysia',
    'MV' => 'Maldives',
    'ML' => 'Mali',
    'MT' => 'Malta',
    'MH' => 'Marshall Islands',
    'MQ' => 'Martinique',
    'MR' => 'Mauritania',
    'MU' => 'Mauritius',
    'YT' => 'Mayotte',
    'MX' => 'Mexico',
    'FM' => 'Micronesia',
    'MD' => 'Moldova',
    'MC' => 'Monaco',
    'MN' => 'Mongolia',
    'ME' => 'Montenegro',
    'MS' => 'Montserrat',
    'MA' => 'Morocco',
    'MZ' => 'Mozambique',
    'MM' => 'Myanmar',
    'NA' => 'Namibia',
    'NR' => 'Nauru',
    'NP' => 'Nepal',
    'NL' => 'Netherlands',
    'NC' => 'New Caledonia',
    'NZ' => 'New Zealand',
    'NI' => 'Nicaragua',
    'NE' => 'Niger',
    'NG' => 'Nigeria',
    'NU' => 'Niue',
    'NF' => 'Norfolk Island',
    'MK' => 'North Macedonia',
    'MP' => 'Northern Mariana Islands',
    'NO' => 'Norway',
    'OM' => 'Oman',
    'PK' => 'Pakistan',
    'PW' => 'Palau',
    'PS' => 'Palestine',
    'PA' => 'Panama',
    'PG' => 'Papua New Guinea',
    'PY' => 'Paraguay',
    'PE' => 'Peru',
    'PH' => 'Philippines',
    'PN' => 'Pitcairn Islands',
    'PL' => 'Poland',
    'PT' => 'Portugal',
    'PR' => 'Puerto Rico',
    'QA' => 'Qatar',
    'RE' => 'Réunion',
    'RO' => 'Romania',
    'RU' => 'Russia',
    'RW' => 'Rwanda',
    'BL' => 'Saint Barthélemy',
    'SH' => 'Saint Helena',
    'KN' => 'Saint Kitts and Nevis',
    'LC' => 'Saint Lucia',
    'MF' => 'Saint Martin',
    'PM' => 'Saint Pierre and Miquelon',
    'VC' => 'Saint Vincent and the Grenadines',
    'WS' => 'Samoa',
    'SM' => 'San Marino',
    'ST' => 'São Tomé and Príncipe',
    'SA' => 'Saudi Arabia',
    'SN' => 'Senegal',
    'RS' => 'Serbia',
    'SC' => 'Seychelles',
    'SL' => 'Sierra Leone',
    'SG' => 'Singapore',
    'SX' => 'Sint Maarten',
    'SK' => 'Slovakia',
    'SI' => 'Slovenia',
    'SB' => 'Solomon Islands',
    'SO' => 'Somalia',
    'ZA' => 'South Africa',
    'GS' => 'South Georgia and the South Sandwich Islands',
    'SS' => 'South Sudan',
    'ES' => 'Spain',
    'LK' => 'Sri Lanka',
    'SD' => 'Sudan',
    'SR' => 'Suriname',
    'SJ' => 'Svalbard and Jan Mayen',
    'SE' => 'Sweden',
    'CH' => 'Switzerland',
    'SY' => 'Syria',
    'TW' => 'Taiwan',
    'TJ' => 'Tajikistan',
    'TZ' => 'Tanzania',
    'TH' => 'Thailand',
    'TL' => 'Timor-Leste',
    'TG' => 'Togo',
    'TK' => 'Tokelau',
    'TO' => 'Tonga',
    'TT' => 'Trinidad and Tobago',
    'TN' => 'Tunisia',
    'TR' => 'Turkey',
    'TM' => 'Turkmenistan',
    'TC' => 'Turks and Caicos Islands',
    'TV' => 'Tuvalu',
    'UG' => 'Uganda',
    'UA' => 'Ukraine',
    'AE' => 'United Arab Emirates',
    'GB' => 'United Kingdom',
    'US' => 'United States',
    'UY' => 'Uruguay',
    'UZ' => 'Uzbekistan',
    'VU' => 'Vanuatu',
    'VE' => 'Venezuela',
    'VN' => 'Vietnam',
    'VG' => 'British Virgin Islands',
    'VI' => 'U.S. Virgin Islands',
    'WF' => 'Wallis and Futuna',
    'EH' => 'Western Sahara',
    'YE' => 'Yemen',
    'ZM' => 'Zambia',
    'ZW' => 'Zimbabwe'
];

// Prepare country data for charts
$country_labels = [];
$country_counts = [];

foreach ($user_countries as $country) {
    $code = $country['country_code'];
    $country_labels[] = $country_name_map[$code] ?? $code;
    $country_counts[] = $country['count'];
}

// Prepare downloads by country data
$downloads_country_labels = [];
$downloads_country_counts = [];

foreach ($downloads_by_country as $country) {
    $code = $country['country_code'];
    $downloads_country_labels[] = $country_name_map[$code] ?? $code;
    $downloads_country_counts[] = $country['download_count'];
}

include '../admin_header.php';
?>

<div class="container">
    <!-- Period selection -->
    <div class="period-selection">
        <span>Time Period:</span>
        <div class="period-buttons">
            <?php
            // Define all periods with their display names
            $periods = [
                'day' => 'Daily',
                'week' => 'Weekly',
                'month' => 'Monthly',
                'year' => 'Yearly'
            ];

            // Loop through periods and create buttons
            foreach ($periods as $periodKey => $periodName) {
                $activeClass = ($period === $periodKey) ? 'active' : '';
                echo "<a href=\"?period={$periodKey}\" class=\"period-btn {$activeClass}\">{$periodName}</a>";
            }
            ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid" id="statsGrid">
        <!-- Will be populated by JavaScript -->
    </div>

    <!-- Charts -->
    <div class="chart-row">
        <div class="chart-container">
            <h2>Downloads Over Time</h2>
            <canvas id="downloadsChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Page Views Over Time</h2>
            <canvas id="viewsChart"></canvas>
        </div>
    </div>

    <div class="chart-row">
        <div class="chart-container">
            <h2>Community Post Types</h2>
            <canvas id="postTypeChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Post Views by Type</h2>
            <canvas id="postViewsChart"></canvas>
        </div>
    </div>

    <div class="chart-row">
        <div class="chart-container">
            <h2>Top 10 User Countries (Page Views)</h2>
            <canvas id="countryChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Browser Distribution</h2>
            <canvas id="browserChart"></canvas>
        </div>
    </div>

    <div class="chart-row">
        <div class="chart-container">
            <h2>Downloads by Country</h2>
            <canvas id="downloadsCountryChart"></canvas>
        </div>
    </div>

    <!-- Most active users table -->
    <div class="table-container">
        <h2>Most Active Community Users</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Posts</th>
                        <th>Comments</th>
                        <th>Total Views</th>
                        <th>Activity Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($active_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['post_count']; ?></td>
                            <td><?php echo $user['comment_count']; ?></td>
                            <td><?php echo number_format($user['total_views']); ?></td>
                            <td><?php echo $user['activity_score']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Helper function to sum arrays since array_sum is a PHP function
    const sumArray = (arr) => arr.reduce((sum, val) => sum + (Number(val) || 0), 0);

    document.addEventListener('DOMContentLoaded', function() {
        // Chart data
        const chartLabels = <?php echo json_encode($chart_labels); ?>;
        const downloadsData = <?php echo json_encode($downloads_data); ?>;
        const registrationsData = <?php echo json_encode($registrations_data); ?>;
        const pageViewsData = <?php echo json_encode($page_views_data); ?>;
        const postTypeLabels = <?php echo json_encode($post_type_labels); ?>;
        const postTypeCounts = <?php echo json_encode($post_type_counts); ?>;
        const postTypeViews = <?php echo json_encode($post_type_views); ?>;
        const countryLabels = <?php echo json_encode($country_labels); ?>;
        const countryCounts = <?php echo json_encode($country_counts); ?>;
        const downloadsCountryLabels = <?php echo json_encode($downloads_country_labels); ?>;
        const downloadsCountryCounts = <?php echo json_encode($downloads_country_counts); ?>;
        const browserLabels = <?php echo json_encode($browser_labels); ?>;
        const browserCounts = <?php echo json_encode($browser_counts); ?>;
        const conversionData = <?php echo json_encode([
                                    $conversion_data['downloads'],
                                    $conversion_data['registrations']
                                ]); ?>;

        generateStatistics();

        function generateStatistics() {
            const statsGrid = document.getElementById('statsGrid');

            const totalDownloads = sumArray(downloadsData);
            const totalRegistrations = sumArray(registrationsData);
            const totalPageViews = sumArray(pageViewsData);
            const downloadGrowthRate = <?php echo $latest_growth; ?>;

            // Calculate view growth rate
            let viewGrowthRate = 0;
            if (pageViewsData.length >= 2) {
                const latestViews = pageViewsData[pageViewsData.length - 1];
                const previousViews = pageViewsData[pageViewsData.length - 2];
                if (previousViews > 0) {
                    viewGrowthRate = Math.round(((latestViews - previousViews) / previousViews) * 100 * 10) / 10;
                }
            }

            const stats = [{
                    title: 'Total Downloads',
                    value: totalDownloads.toLocaleString(),
                    subtext: 'operations'
                },
                {
                    title: 'Registrations',
                    value: totalRegistrations.toLocaleString(),
                    subtext: 'users'
                },
                {
                    title: 'Views Growth Rate',
                    value: (viewGrowthRate >= 0 ? '+' : '') + viewGrowthRate + '%',
                    subtext: 'period over period'
                },
                {
                    title: 'Downloads Growth Rate',
                    value: (downloadGrowthRate >= 0 ? '+' : '') + downloadGrowthRate + '%',
                    subtext: 'period over period'
                },
                {
                    title: 'Page Views',
                    value: totalPageViews.toLocaleString(),
                    subtext: 'total views'
                }
            ];

            statsGrid.innerHTML = stats.map(stat => `
                <div class="stat-card">
                    <h3>${stat.title}</h3>
                    <div class="value">${stat.value}</div>
                    ${stat.subtext ? `<div class="subtext">${stat.subtext}</div>` : ''}
                </div>
            `).join('');
        }

        // Downloads chart
        const ctxDownloads = document.getElementById('downloadsChart').getContext('2d');
        new Chart(ctxDownloads, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Downloads',
                    data: downloadsData,
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(37, 99, 235, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        ticks: {
                            padding: 10
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Downloads: ${context.raw}`;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Page Views chart
        const ctxViews = document.getElementById('viewsChart').getContext('2d');
        new Chart(ctxViews, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Page Views',
                    data: pageViewsData,
                    backgroundColor: 'rgba(245, 158, 11, 0.2)',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(245, 158, 11, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        ticks: {
                            padding: 10
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Page Views: ${context.raw}`;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Post Type chart
        const ctxPostType = document.getElementById('postTypeChart').getContext('2d');
        new Chart(ctxPostType, {
            type: 'pie',
            data: {
                labels: postTypeLabels,
                datasets: [{
                    data: postTypeCounts,
                    backgroundColor: [
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(99, 102, 241, 0.8)'
                    ],
                    borderColor: [
                        'rgba(37, 99, 235, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(99, 102, 241, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = Number(context.raw) || 0;
                                const total = context.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} posts (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Post Views by Type chart
        const ctxPostViews = document.getElementById('postViewsChart').getContext('2d');
        new Chart(ctxPostViews, {
            type: 'bar',
            data: {
                labels: postTypeLabels,
                datasets: [{
                    label: 'Views',
                    data: postTypeViews,
                    backgroundColor: [
                        'rgba(37, 99, 235, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(99, 102, 241, 0.7)'
                    ],
                    borderColor: [
                        'rgba(37, 99, 235, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(99, 102, 241, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = Number(context.raw) || 0;
                                const total = context.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value.toLocaleString()} views (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Country chart (Page Views)
        const ctxCountry = document.getElementById('countryChart').getContext('2d');
        new Chart(ctxCountry, {
            type: 'bar',
            data: {
                labels: countryLabels,
                datasets: [{
                    label: 'Page Views',
                    data: countryCounts,
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = Number(context.raw) || 0;
                                const total = context.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} page views (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Downloads by Country chart
        const ctxDownloadsCountry = document.getElementById('downloadsCountryChart').getContext('2d');
        new Chart(ctxDownloadsCountry, {
            type: 'bar',
            data: {
                labels: downloadsCountryLabels,
                datasets: [{
                    label: 'Downloads',
                    data: downloadsCountryCounts,
                    backgroundColor: 'rgba(37, 99, 235, 0.7)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = Number(context.raw) || 0;
                                const total = context.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} downloads (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Browser chart
        const ctxBrowser = document.getElementById('browserChart').getContext('2d');
        new Chart(ctxBrowser, {
            type: 'pie',
            data: {
                labels: browserLabels,
                datasets: [{
                    data: browserCounts,
                    backgroundColor: [
                        'rgba(37, 99, 235, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(99, 102, 241, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(107, 114, 128, 0.7)'
                    ],
                    borderColor: [
                        'rgba(37, 99, 235, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(99, 102, 241, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(107, 114, 128, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });


        // Restore scroll position if it exists in sessionStorage
        if (sessionStorage.getItem('scrollPosition')) {
            window.scrollTo(0, sessionStorage.getItem('scrollPosition'));
            sessionStorage.removeItem('scrollPosition');
        }

        // Save scroll position when clicking links
        const links = document.querySelectorAll('a[href^="?period="]');
        links.forEach(link => {
            link.addEventListener('click', function() {
                sessionStorage.setItem('scrollPosition', window.scrollY);
            });
        });
    });
</script>