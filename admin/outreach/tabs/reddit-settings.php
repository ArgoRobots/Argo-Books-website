<?php
/**
 * Reddit Settings sub-tab.
 *
 * - Watchlist subreddits CRUD (add/enable/disable/delete)
 * - Keywords CRUD
 * - Numeric tuning fields (scoring floors, post limits, auto-disable)
 * - Reddit account info card (account age, karma)
 * - Diagnostics card (last run timestamps, error)
 *
 * Exposes:
 *   reddit_settings_tab_handle_post($pdo)
 *   reddit_settings_tab_render($pdo)
 */

function reddit_settings_tab_handle_post($pdo)
{
    $action = $_POST['action'] ?? '';

    if ($action === 'reddit_set_enabled') {
        $enabled = ($_POST['enabled'] ?? '') === '1' ? 1 : 0;
        reddit_settings_tab_ensure_singleton($pdo);
        try {
            $stmt = $pdo->prepare("UPDATE reddit_settings SET enabled = ? WHERE id = 1");
            $stmt->execute([$enabled]);
            $_SESSION['message'] = 'Reddit discovery: ' . ($enabled === 1 ? 'ENABLED' : 'DISABLED');
            $_SESSION['message_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Could not update toggle. Run the latest schema migration (reddit_settings.enabled column) and try again.';
            $_SESSION['message_type'] = 'error';
        }
        header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
    }

    if ($action === 'reddit_add_subreddit') {
        $name = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($_POST['name'] ?? ''));
        if ($name === '') {
            $_SESSION['message'] = 'Subreddit name is required.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
        }
        $notes = mb_substr((string) ($_POST['notes'] ?? ''), 0, 255);
        try {
            $stmt = $pdo->prepare("INSERT INTO reddit_subreddits (name, enabled, notes) VALUES (?, 1, ?)
                ON DUPLICATE KEY UPDATE enabled = 1, notes = VALUES(notes)");
            $stmt->execute([$name, $notes]);
            $_SESSION['message'] = "Added r/$name to the watchlist.";
            $_SESSION['message_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Could not add subreddit: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
        header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
    }

    if ($action === 'reddit_toggle_subreddit') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE reddit_subreddits SET enabled = 1 - enabled WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
    }

    if ($action === 'reddit_reenable_subreddit') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE reddit_subreddits SET enabled = 1, auto_disabled_at = NULL, auto_disabled_reason = NULL WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Subreddit re-enabled.';
        $_SESSION['message_type'] = 'success';
        header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
    }

    if ($action === 'reddit_delete_subreddit') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM reddit_subreddits WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
    }

    if ($action === 'reddit_add_keyword') {
        $kw = trim((string) ($_POST['keyword'] ?? ''));
        if ($kw === '' || mb_strlen($kw) > 120) {
            $_SESSION['message'] = 'Keyword is required and must be ≤ 120 chars.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
        }
        $notes = mb_substr((string) ($_POST['notes'] ?? ''), 0, 255);
        try {
            $stmt = $pdo->prepare("INSERT INTO reddit_keywords (keyword, enabled, notes) VALUES (?, 1, ?)
                ON DUPLICATE KEY UPDATE enabled = 1, notes = VALUES(notes)");
            $stmt->execute([$kw, $notes]);
            $_SESSION['message'] = "Added keyword: $kw";
            $_SESSION['message_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Could not add keyword: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
        header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
    }

    if ($action === 'reddit_toggle_keyword') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE reddit_keywords SET enabled = 1 - enabled WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
    }

    if ($action === 'reddit_delete_keyword') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM reddit_keywords WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
    }

    if ($action === 'reddit_update_tuning') {
        $rulesFloor = max(0, min(100, (int) ($_POST['rules_score_floor'] ?? 30)));
        $aiFloor = max(0, min(10, (int) ($_POST['ai_relevance_floor'] ?? 6)));
        $dailyLimit = max(0, min(50, (int) ($_POST['daily_post_limit'] ?? 3)));
        $weeklyLimit = max(0, min(200, (int) ($_POST['weekly_post_limit'] ?? 12)));
        $autoDisableRate = max(0, min(100, (int) ($_POST['auto_disable_removal_rate'] ?? 60)));
        $autoDisableMin = max(1, min(50, (int) ($_POST['auto_disable_min_replies'] ?? 3)));

        reddit_settings_tab_ensure_singleton($pdo);
        $stmt = $pdo->prepare("UPDATE reddit_settings SET
            rules_score_floor = ?,
            ai_relevance_floor = ?,
            daily_post_limit = ?,
            weekly_post_limit = ?,
            auto_disable_removal_rate = ?,
            auto_disable_min_replies = ?
            WHERE id = 1");
        $stmt->execute([$rulesFloor, $aiFloor, $dailyLimit, $weeklyLimit, $autoDisableRate, $autoDisableMin]);
        $_SESSION['message'] = 'Tuning saved.';
        $_SESSION['message_type'] = 'success';
        header('Location: index.php?channel=reddit&tab=reddit-settings'); exit;
    }
}

function reddit_settings_tab_render($pdo)
{
    reddit_settings_tab_ensure_singleton($pdo);

    $subreddits = reddit_settings_tab_fetch_subreddits($pdo);
    $keywords = reddit_settings_tab_fetch_keywords($pdo);
    $settings = reddit_settings_tab_fetch_settings($pdo);
    $diagnostics = reddit_settings_tab_fetch_diagnostics($pdo);

    $csrfToken = $_SESSION['csrf_token'] ?? '';
    ?>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="flash-message flash-<?= htmlspecialchars($_SESSION['message_type'] ?? 'info') ?>">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <?php $redditEnabled = (int) $settings['enabled'] === 1; ?>

    <!-- Master enable/disable toggle for the Reddit discovery pipeline -->
    <div class="panel reddit-settings-panel">
        <div class="panel-header">
            <h2>Reddit discovery</h2>
        </div>
        <div class="panel-content">
            <p class="hint" style="margin-top:0;">
                Master switch for the daily Reddit discovery cron. When OFF, the cron exits without fetching threads, scoring, or generating drafts, even if it's still scheduled at the server level. Reply-status checks on already-posted threads continue regardless.
            </p>
            <div class="segmented-toggle">
                <form method="post" action="index.php?channel=reddit&tab=reddit-settings" style="display:contents;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="tab" value="reddit-settings">
                    <input type="hidden" name="action" value="reddit_set_enabled">
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit" class="segmented-option <?= $redditEnabled ? 'active' : '' ?>">
                        <span class="segmented-title">Enabled</span>
                        <span class="segmented-desc">Daily cron runs as scheduled. Threads are discovered, scored, and drafted automatically.</span>
                    </button>
                </form>
                <form method="post" action="index.php?channel=reddit&tab=reddit-settings" style="display:contents;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="tab" value="reddit-settings">
                    <input type="hidden" name="action" value="reddit_set_enabled">
                    <input type="hidden" name="enabled" value="0">
                    <button type="submit" class="segmented-option <?= !$redditEnabled ? 'active' : '' ?>">
                        <span class="segmented-title">Disabled</span>
                        <span class="segmented-desc">Cron exits immediately. No discovery, scoring, or drafting until re-enabled.</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Reddit account info card (loaded on demand) -->
    <div class="panel reddit-settings-panel">
        <div class="panel-header">
            <h2>Reddit account</h2>
        </div>
        <div class="panel-content">
            <div id="redditAccountInfo" class="reddit-account-info">
                <button class="btn btn-small btn-blue" onclick="loadRedditAccountInfo()">Check account status</button>
                <span class="text-muted" style="margin-left:8px; font-size:13px;">
                    Pulls account age + karma from Reddit. Use to decide whether to raise the daily post limit.
                </span>
            </div>
        </div>
    </div>

    <!-- Diagnostics card -->
    <div class="panel reddit-settings-panel">
        <div class="panel-header">
            <h2>Diagnostics</h2>
        </div>
        <div class="panel-content">
            <div class="reddit-diagnostics">
                <div><strong>Last discovery run:</strong> <?= htmlspecialchars($diagnostics['last_run_at'] ?? 'never') ?></div>
                <div><strong>Threads found:</strong> <?= (int) ($diagnostics['last_run_threads_found'] ?? 0) ?></div>
                <div><strong>Threads drafted:</strong> <?= (int) ($diagnostics['last_run_threads_drafted'] ?? 0) ?></div>
                <div><strong>Last status check:</strong> <?= htmlspecialchars($diagnostics['last_status_check_at'] ?? 'never') ?></div>
                <?php if (!empty($diagnostics['last_run_error'])): ?>
                    <div class="reddit-diagnostics-error">
                        <strong>Last error:</strong> <?= htmlspecialchars($diagnostics['last_run_error']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tuning fields -->
    <div class="panel reddit-settings-panel">
        <div class="panel-header">
            <h2>Tuning</h2>
        </div>
        <div class="panel-content">
            <form method="post" action="index.php?channel=reddit&tab=reddit-settings">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="tab" value="reddit-settings">
                <input type="hidden" name="action" value="reddit_update_tuning">

                <div class="reddit-tuning-grid">
                    <div class="form-group">
                        <label for="rulesScoreFloor">Rules score floor (0–100)</label>
                        <input type="number" id="rulesScoreFloor" name="rules_score_floor"
                               value="<?= (int) $settings['rules_score_floor'] ?>" min="0" max="100">
                        <p class="form-help">Threads scoring below this are auto-skipped without an AI call. Default 30.</p>
                    </div>
                    <div class="form-group">
                        <label for="aiRelevanceFloor">AI relevance floor (0–10)</label>
                        <input type="number" id="aiRelevanceFloor" name="ai_relevance_floor"
                               value="<?= (int) $settings['ai_relevance_floor'] ?>" min="0" max="10">
                        <p class="form-help">Threads scoring below this don't advance to draft generation. Default 6.</p>
                    </div>
                    <div class="form-group">
                        <label for="dailyPostLimit">Daily post limit</label>
                        <input type="number" id="dailyPostLimit" name="daily_post_limit"
                               value="<?= (int) $settings['daily_post_limit'] ?>" min="0" max="50">
                        <p class="form-help">Max product-mentioning replies per rolling 24h. Default 3.</p>
                    </div>
                    <div class="form-group">
                        <label for="weeklyPostLimit">Weekly post limit</label>
                        <input type="number" id="weeklyPostLimit" name="weekly_post_limit"
                               value="<?= (int) $settings['weekly_post_limit'] ?>" min="0" max="200">
                        <p class="form-help">Max product-mentioning replies per rolling 7d. Default 12.</p>
                    </div>
                    <div class="form-group">
                        <label for="autoDisableRemovalRate">Auto-disable removal rate (%)</label>
                        <input type="number" id="autoDisableRemovalRate" name="auto_disable_removal_rate"
                               value="<?= (int) $settings['auto_disable_removal_rate'] ?>" min="0" max="100">
                        <p class="form-help">If a subreddit's 30-day removal rate is ≥ this, auto-disable it. Default 60.</p>
                    </div>
                    <div class="form-group">
                        <label for="autoDisableMinReplies">Auto-disable min replies</label>
                        <input type="number" id="autoDisableMinReplies" name="auto_disable_min_replies"
                               value="<?= (int) $settings['auto_disable_min_replies'] ?>" min="1" max="50">
                        <p class="form-help">Minimum replies in last 30d before the auto-disable rule kicks in. Default 3.</p>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <button type="submit" class="btn btn-blue">Save tuning</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Subreddit watchlist -->
    <div class="panel reddit-settings-panel">
        <div class="panel-header">
            <h2>Watchlist subreddits</h2>
        </div>
        <div class="panel-content">
            <form method="post" action="index.php?channel=reddit&tab=reddit-settings" class="reddit-add-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="tab" value="reddit-settings">
                <input type="hidden" name="action" value="reddit_add_subreddit">
                <input type="text" name="name" placeholder="subreddit name (no r/)" required pattern="[A-Za-z0-9_]+" maxlength="64">
                <input type="text" name="notes" placeholder="notes (optional)" maxlength="255">
                <button type="submit" class="btn btn-small btn-blue">Add</button>
            </form>

            <table class="data-table reddit-config-table" data-paginate="25">
                <thead>
                    <tr>
                        <th>Subreddit</th>
                        <th>Enabled</th>
                        <th>Replies 30d</th>
                        <th>Removal rate 30d</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subreddits)): ?>
                        <tr><td colspan="6" class="empty-state">No subreddits configured. Add one above.</td></tr>
                    <?php else: ?>
                        <?php foreach ($subreddits as $row): ?>
                            <tr>
                                <td>r/<?= htmlspecialchars($row['name']) ?></td>
                                <td>
                                    <?php if (!empty($row['auto_disabled_at'])): ?>
                                        <span class="badge badge-red">Auto-disabled</span>
                                    <?php elseif ((int) $row['enabled'] === 1): ?>
                                        <span class="badge badge-green">Yes</span>
                                    <?php else: ?>
                                        <span class="badge badge-gray">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int) ($row['replies_30d'] ?? 0) ?></td>
                                <td>
                                    <?php
                                    $rate = (float) ($row['removal_rate_30d'] ?? 0);
                                    $rateClass = $rate >= 60 ? 'badge-red' : ($rate >= 30 ? 'badge-yellow' : 'badge-green');
                                    if (((int) ($row['replies_30d'] ?? 0)) === 0) {
                                        echo '<span class="text-muted">—</span>';
                                    } else {
                                        echo '<span class="badge ' . $rateClass . '">' . number_format($rate, 1) . '%</span>';
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars((string) ($row['notes'] ?? '')) ?></td>
                                <td class="row-actions">
                                    <?php if (!empty($row['auto_disabled_at'])): ?>
                                        <form method="post" action="index.php?channel=reddit&tab=reddit-settings" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <input type="hidden" name="tab" value="reddit-settings">
                                            <input type="hidden" name="action" value="reddit_reenable_subreddit">
                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                            <button type="submit" class="btn btn-small btn-blue">Re-enable</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="post" action="index.php?channel=reddit&tab=reddit-settings" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <input type="hidden" name="tab" value="reddit-settings">
                                            <input type="hidden" name="action" value="reddit_toggle_subreddit">
                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                            <button type="submit" class="btn btn-small btn-neutral">
                                                <?= (int) $row['enabled'] === 1 ? 'Disable' : 'Enable' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" action="index.php?channel=reddit&tab=reddit-settings" style="display:inline;"
                                          onsubmit="return confirm('Delete r/<?= htmlspecialchars($row['name']) ?>?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="tab" value="reddit-settings">
                                        <input type="hidden" name="action" value="reddit_delete_subreddit">
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button type="submit" class="btn btn-small btn-red">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Keywords -->
    <div class="panel reddit-settings-panel">
        <div class="panel-header">
            <h2>Search keywords</h2>
        </div>
        <div class="panel-content">
            <form method="post" action="index.php?channel=reddit&tab=reddit-settings" class="reddit-add-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="tab" value="reddit-settings">
                <input type="hidden" name="action" value="reddit_add_keyword">
                <input type="text" name="keyword" placeholder='keyword or phrase (use quotes for exact)' required maxlength="120">
                <input type="text" name="notes" placeholder="notes (optional)" maxlength="255">
                <button type="submit" class="btn btn-small btn-blue">Add</button>
            </form>

            <table class="data-table reddit-config-table" data-paginate="25">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Enabled</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($keywords)): ?>
                        <tr><td colspan="4" class="empty-state">No keywords configured. Add one above.</td></tr>
                    <?php else: ?>
                        <?php foreach ($keywords as $row): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($row['keyword']) ?></code></td>
                                <td>
                                    <?php if ((int) $row['enabled'] === 1): ?>
                                        <span class="badge badge-green">Yes</span>
                                    <?php else: ?>
                                        <span class="badge badge-gray">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars((string) ($row['notes'] ?? '')) ?></td>
                                <td class="row-actions">
                                    <form method="post" action="index.php?channel=reddit&tab=reddit-settings" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="tab" value="reddit-settings">
                                        <input type="hidden" name="action" value="reddit_toggle_keyword">
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button type="submit" class="btn btn-small btn-neutral">
                                            <?= (int) $row['enabled'] === 1 ? 'Disable' : 'Enable' ?>
                                        </button>
                                    </form>
                                    <form method="post" action="index.php?channel=reddit&tab=reddit-settings" style="display:inline;"
                                          onsubmit="return confirm('Delete keyword?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="tab" value="reddit-settings">
                                        <input type="hidden" name="action" value="reddit_delete_keyword">
                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                        <button type="submit" class="btn btn-small btn-red">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
}

/**
 * Insert the singleton settings row if missing. Defaults are spec-aligned.
 * Tolerates missing table on a fresh install (silent: Phase 1 schema must be run).
 */
function reddit_settings_tab_ensure_singleton($pdo): void
{
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO reddit_settings
            (id, enabled, rules_score_floor, ai_relevance_floor, daily_post_limit, weekly_post_limit,
             auto_disable_removal_rate, auto_disable_min_replies)
            VALUES (1, 1, 30, 6, 3, 12, 60, 3)");
        $stmt->execute();
    } catch (PDOException $e) {
        // Table not created yet; render code will fall back to defaults.
    }
}

function reddit_settings_tab_fetch_subreddits($pdo): array
{
    try {
        $stmt = $pdo->prepare("SELECT id, name, enabled, notes, replies_30d, removal_rate_30d, auto_disabled_at, auto_disabled_reason
                               FROM reddit_subreddits ORDER BY enabled DESC, name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function reddit_settings_tab_fetch_keywords($pdo): array
{
    try {
        $stmt = $pdo->prepare("SELECT id, keyword, enabled, notes FROM reddit_keywords ORDER BY enabled DESC, keyword ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function reddit_settings_tab_fetch_settings($pdo): array
{
    $defaults = [
        'enabled' => 1,
        'rules_score_floor' => 30,
        'ai_relevance_floor' => 6,
        'daily_post_limit' => 3,
        'weekly_post_limit' => 12,
        'auto_disable_removal_rate' => 60,
        'auto_disable_min_replies' => 3,
    ];
    // `enabled` column was added later; try selecting it, fall back to the
    // pre-migration schema (and treat the channel as enabled) if it's missing.
    try {
        $stmt = $pdo->prepare("SELECT enabled, rules_score_floor, ai_relevance_floor, daily_post_limit, weekly_post_limit,
                                      auto_disable_removal_rate, auto_disable_min_replies
                               FROM reddit_settings WHERE id = 1");
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? array_merge($defaults, $row) : $defaults;
    } catch (PDOException $e) {
        try {
            $stmt = $pdo->prepare("SELECT rules_score_floor, ai_relevance_floor, daily_post_limit, weekly_post_limit,
                                          auto_disable_removal_rate, auto_disable_min_replies
                                   FROM reddit_settings WHERE id = 1");
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ? array_merge($defaults, $row) : $defaults;
        } catch (PDOException $e2) {
            return $defaults;
        }
    }
}

function reddit_settings_tab_fetch_diagnostics($pdo): array
{
    try {
        $stmt = $pdo->prepare("SELECT last_run_at, last_run_threads_found, last_run_threads_drafted,
                                      last_run_error, last_status_check_at
                               FROM reddit_settings WHERE id = 1");
        $stmt->execute();
        return $stmt->fetch() ?: [];
    } catch (PDOException $e) {
        return [];
    }
}
