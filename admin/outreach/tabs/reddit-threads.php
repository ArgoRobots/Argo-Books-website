<?php
/**
 * Reddit Threads sub-tab.
 *
 * Renders the discovery queue: stats row, safety meter (daily/weekly post
 * limits), filters, and a paginated table populated via api.php Reddit
 * endpoints. Default filter is status IN (drafted, drafted_pending) sorted
 * by ai_relevance DESC so the actionable queue is front and center.
 *
 * Exposes:
 *   reddit_threads_tab_render($pdo)
 */

function reddit_threads_tab_render($pdo)
{
    // Resolve a few server-side bits we want available at page load. Heavy
    // data (the threads list) loads via JS from api.php.
    $limits = reddit_threads_tab_get_post_limits($pdo);
    $dailyUsed = reddit_threads_tab_count_recent_mentions($pdo, '1 DAY');
    $weeklyUsed = reddit_threads_tab_count_recent_mentions($pdo, '7 DAY');
    ?>

    <!-- Pipeline-running / progress banner (live-updated by outreach.js) -->
    <div id="redditPipelineBanner" class="reddit-progress-banner" style="display:none;"></div>

    <!-- Safety meter -->
    <div class="reddit-safety-meter" id="redditSafetyMeter">
        <div class="safety-meter-row">
            <div class="safety-meter-cell">
                <div class="safety-meter-label">Today (product mentions)</div>
                <div class="safety-meter-value">
                    <span id="redditDailyUsed"><?= (int) $dailyUsed ?></span>
                    <span class="safety-meter-divider">/</span>
                    <span id="redditDailyLimit"><?= (int) $limits['daily'] ?></span>
                </div>
                <div class="safety-meter-bar">
                    <div class="safety-meter-fill" id="redditDailyFill" data-used="<?= (int) $dailyUsed ?>" data-limit="<?= (int) $limits['daily'] ?>"></div>
                </div>
            </div>
            <div class="safety-meter-cell">
                <div class="safety-meter-label">This week (product mentions)</div>
                <div class="safety-meter-value">
                    <span id="redditWeeklyUsed"><?= (int) $weeklyUsed ?></span>
                    <span class="safety-meter-divider">/</span>
                    <span id="redditWeeklyLimit"><?= (int) $limits['weekly'] ?></span>
                </div>
                <div class="safety-meter-bar">
                    <div class="safety-meter-fill" id="redditWeeklyFill" data-used="<?= (int) $weeklyUsed ?>" data-limit="<?= (int) $limits['weekly'] ?>"></div>
                </div>
            </div>
            <span class="safety-meter-help" tabindex="0" aria-label="What do these limits mean?">
                <span class="safety-meter-help-icon" aria-hidden="true">?</span>
                <span class="safety-meter-help-tooltip" role="tooltip">Heuristic limits to reduce shadowban risk. Counts only replies where you marked &lsquo;Mentioned Argo Books&rsquo;. Tune in Settings as the account ages.</span>
            </span>
        </div>
    </div>

    <!-- Secondary stats row -->
    <div class="stats-row" id="redditStatsRow">
        <div class="stat-card">
            <div class="stat-label">Total Threads</div>
            <div class="stat-value" id="redditStatTotal">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Drafted (ready)</div>
            <div class="stat-value stat-new" id="redditStatDrafted">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Drafted-pending</div>
            <div class="stat-value stat-pending" id="redditStatPending">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Replied (7d)</div>
            <div class="stat-value" id="redditStatReplied7d">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Reply Survival</div>
            <div class="stat-value" id="redditStatSurvival">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Upvotes / Reply</div>
            <div class="stat-value" id="redditStatAvgUpvotes">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Profile-Link Clicks (30d)</div>
            <div class="stat-value" id="redditStatProfileClicks">0</div>
        </div>
    </div>

    <!-- Filters + Run Now -->
    <div class="filters-bar">
        <div class="filters-container">
            <div class="filters-row">
                <div class="filter-group">
                    <label for="redditFilterStatus">Status</label>
                    <select id="redditFilterStatus" onchange="loadRedditThreads()">
                        <option value="actionable">Actionable (drafted)</option>
                        <option value="all">All</option>
                        <option value="drafted">Drafted</option>
                        <option value="drafted_pending">Drafted-pending</option>
                        <option value="replied">Replied</option>
                        <option value="reply_removed">Reply Removed</option>
                        <option value="skipped">Skipped</option>
                        <option value="not_fit">Not Fit</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="redditFilterSubreddit">Subreddit</label>
                    <select id="redditFilterSubreddit" onchange="loadRedditThreads()">
                        <option value="">All</option>
                        <?php foreach (reddit_threads_tab_list_subreddits($pdo) as $sub): ?>
                            <option value="<?= htmlspecialchars($sub) ?>">r/<?= htmlspecialchars($sub) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="redditFilterSource">Discovery</label>
                    <select id="redditFilterSource" onchange="loadRedditThreads()">
                        <option value="">Any</option>
                        <option value="watchlist">Watchlist</option>
                        <option value="keyword">Keyword</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="redditFilterDays">Discovered</label>
                    <select id="redditFilterDays" onchange="loadRedditThreads()">
                        <option value="7">Last 7 days</option>
                        <option value="14">Last 14 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="0">All time</option>
                    </select>
                </div>
                <div class="filter-group form-group-btn">
                    <button class="btn btn-blue" onclick="runRedditDiscoveryNow()" id="redditRunNowBtn">Run discovery now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Threads table -->
    <div class="reddit-table-wrapper">
        <table class="data-table reddit-table">
            <thead>
                <tr>
                    <th>Subreddit</th>
                    <th>Title</th>
                    <th>AI</th>
                    <th>Status</th>
                    <th>Reply</th>
                    <th>Upvotes</th>
                    <th>Discovered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="redditThreadsTableBody">
                <tr><td colspan="8" class="empty-state">Loading…</td></tr>
            </tbody>
        </table>
    </div>

    <?php
}

/**
 * Read post-limit settings from `reddit_settings`. Falls back to spec defaults
 * if the row hasn't been created yet (fresh install before Phase 2 schema ran).
 */
function reddit_threads_tab_get_post_limits($pdo): array
{
    $defaults = ['daily' => 3, 'weekly' => 12];
    try {
        $stmt = $pdo->prepare("SELECT daily_post_limit, weekly_post_limit FROM reddit_settings WHERE id = 1");
        $stmt->execute();
        $row = $stmt->fetch();
        if (!$row) return $defaults;
        return [
            'daily' => (int) ($row['daily_post_limit'] ?? $defaults['daily']),
            'weekly' => (int) ($row['weekly_post_limit'] ?? $defaults['weekly']),
        ];
    } catch (PDOException $e) {
        return $defaults;
    }
}

/**
 * Count product-mentioning replies posted in the given rolling window
 * (e.g. '1 DAY', '7 DAY'). Used to render the safety meter.
 */
function reddit_threads_tab_count_recent_mentions($pdo, string $interval): int
{
    try {
        $sql = "SELECT COUNT(*) FROM reddit_threads
                WHERE status = 'replied'
                  AND mentioned_product = 1
                  AND reply_posted_at > DATE_SUB(NOW(), INTERVAL $interval)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Distinct subreddits seen in the threads table, for the filter dropdown.
 */
function reddit_threads_tab_list_subreddits($pdo): array
{
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT subreddit FROM reddit_threads ORDER BY subreddit ASC");
        $stmt->execute();
        return array_map(fn($r) => $r['subreddit'], $stmt->fetchAll());
    } catch (PDOException $e) {
        return [];
    }
}
