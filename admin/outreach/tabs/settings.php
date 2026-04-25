<?php
/**
 * Settings tab partial.
 *
 * Renders toggles for:
 *   - Auto-send vs Review-before-send (state: auto_send_mode)
 *   - A/B automation on/off (state: ab_auto_enabled)
 *
 * Plus read-only status: current A/B test, daily send limit,
 * and a tail of today's pipeline log filtered to automation lines.
 *
 * Exposes:
 *   settings_tab_handle_post($pdo)
 *   settings_tab_render($pdo)
 *
 * State values live in outreach_pipeline_state via getState/setState
 * (defined in cron/outreach_pipeline.php, but those helpers are only
 * loaded in CLI context — we define local equivalents here that use
 * the same table).
 */

/**
 * Local lightweight wrappers for outreach_pipeline_state.
 * Matches the CLI getState/setState behaviour.
 */
function settings_tab_get_state($pdo, $key, $default = null)
{
    try {
        $stmt = $pdo->prepare("SELECT state_value FROM outreach_pipeline_state WHERE state_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['state_value'] : $default;
    } catch (PDOException $e) {
        // Table may not exist yet on a fresh install — treat as default
        return $default;
    }
}

function settings_tab_set_state($pdo, $key, $value)
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS outreach_pipeline_state (
        id INT AUTO_INCREMENT PRIMARY KEY,
        state_key VARCHAR(100) NOT NULL UNIQUE,
        state_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $stmt = $pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)");
    $stmt->execute([$key, $value]);
}

function settings_tab_handle_post($pdo)
{
    $action = $_POST['action'] ?? '';

    if ($action === 'set_outreach_enabled') {
        $enabled = $_POST['enabled'] ?? '';
        $val = ($enabled === '1') ? '1' : '0';
        settings_tab_set_state($pdo, 'outreach_enabled', $val);
        $_SESSION['message'] = 'Outreach system: ' . ($val === '1' ? 'ENABLED' : 'DISABLED');
        $_SESSION['message_type'] = 'success';
        header('Location: index.php?tab=settings'); exit;
    }

    if ($action === 'set_auto_send_mode') {
        $mode = $_POST['mode'] ?? '';
        if (in_array($mode, ['auto', 'review'], true)) {
            settings_tab_set_state($pdo, 'auto_send_mode', $mode);
            $_SESSION['message'] = 'Send mode set to: ' . ($mode === 'auto' ? 'Auto-send' : 'Review before send');
            $_SESSION['message_type'] = 'success';
        }
        header('Location: index.php?tab=settings'); exit;
    }

    if ($action === 'set_ab_auto_enabled') {
        $enabled = $_POST['enabled'] ?? '';
        $val = ($enabled === '1') ? '1' : '0';
        settings_tab_set_state($pdo, 'ab_auto_enabled', $val);
        $_SESSION['message'] = 'A/B automation: ' . ($val === '1' ? 'ON' : 'OFF');
        $_SESSION['message_type'] = 'success';
        header('Location: index.php?tab=settings'); exit;
    }

    if ($action === 'set_ab_auto_rotation') {
        $enabled = $_POST['enabled'] ?? '';
        $val = ($enabled === '1') ? '1' : '0';
        settings_tab_set_state($pdo, 'ab_auto_rotation', $val);
        $_SESSION['message'] = 'A/B auto-rotation: ' . ($val === '1' ? 'ON' : 'OFF');
        $_SESSION['message_type'] = 'success';
        header('Location: index.php?tab=settings'); exit;
    }

    header('Location: index.php?tab=settings'); exit;
}

function settings_tab_render($pdo)
{
    // Master enable flag for the whole outreach system — defaults ON so a
    // fresh install keeps behaving as before.
    $outreachEnabled = settings_tab_get_state($pdo, 'outreach_enabled', '1') === '1';

    // Current state — default to 'auto' for send mode and '1' for A/B automation
    $autoSendMode = settings_tab_get_state($pdo, 'auto_send_mode', 'auto');
    if (!in_array($autoSendMode, ['auto', 'review'], true)) $autoSendMode = 'auto';

    $abAutoEnabled = settings_tab_get_state($pdo, 'ab_auto_enabled', '1') === '1';
    $abAutoRotation = settings_tab_get_state($pdo, 'ab_auto_rotation', '0') === '1';
    require_once __DIR__ . '/../../../cron/lib/outreach_helpers.php';
    $rotationOrder = ab_auto_rotation_order();
    $abNextType = settings_tab_get_state($pdo, 'ab_auto_next_type', $rotationOrder[0]);
    if (!in_array($abNextType, $rotationOrder, true)) {
        $abNextType = $rotationOrder[0];
    }

    $dailyLimit = (int) ($_ENV['OUTREACH_DAILY_SEND_LIMIT'] ?? 10);
    $ctrFloor = (float) settings_tab_get_state($pdo, 'ab_ctr_floor', '0.01');

    // Active A/B test snapshot — show whichever variant_type is currently
    // running (only one is active at a time, regardless of type).
    $active = null;
    try {
        $stmt = $pdo->prepare("SELECT * FROM outreach_ab_tests WHERE status = 'active' ORDER BY started_at DESC, id DESC LIMIT 1");
        $stmt->execute();
        $active = $stmt->fetch();
    } catch (PDOException $e) {
        $active = null; // Tables may not exist yet
    }

    $activeStats = null;
    if ($active) {
        require_once __DIR__ . '/../../../cron/lib/ab_helpers.php';
        $variants = load_variants_with_stats($pdo, (int) $active['id']);
        $leaderIdx = find_leader_idx($variants);
        $totalSent = array_sum(array_column($variants, 'sent_count'));
        $totalClicks = array_sum(array_column($variants, 'clicked_count'));
        $days = max(0, (int) floor((time() - strtotime($active['started_at'] ?: $active['created_at'])) / 86400));
        $activeStats = [
            'variants' => count($variants),
            'sent' => $totalSent,
            'clicked' => $totalClicks,
            'days' => $days,
            'leader' => ($leaderIdx !== null) ? $variants[$leaderIdx]['label'] : null,
            'leader_ctr' => ($leaderIdx !== null && $variants[$leaderIdx]['sent_count'] > 0)
                ? $variants[$leaderIdx]['ctr'] : null,
        ];
    }

    // Tail today's pipeline log for automation-related lines
    $logLines = [];
    $logPath = __DIR__ . '/../../../cron/logs/outreach_pipeline_' . date('Y-m-d') . '.log';
    if (is_readable($logPath)) {
        // Read last ~200 lines then filter — avoid loading an enormous file entirely
        $lines = @file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (is_array($lines)) {
            $tail = array_slice($lines, -200);
            foreach ($tail as $line) {
                if (preg_match('/A\/B|ab_auto|stepManageAbTests|auto-cycle|auto-rotation|Promoted variant/i', $line)) {
                    $logLines[] = $line;
                }
            }
            $logLines = array_slice($logLines, -40);
        }
    }
    ?>

    <?php if ($abAutoEnabled === false): ?>
        <div class="panel" style="border-left:3px solid #f59e0b;">
            <div class="panel-content">
                <strong>A/B automation is off.</strong>
                <?php if (settings_tab_get_state($pdo, 'ab_auto_last_pause_reason')): ?>
                    The last automated run paused itself: <em><?php echo htmlspecialchars((string) settings_tab_get_state($pdo, 'ab_auto_last_pause_reason')); ?></em>
                <?php else: ?>
                    Flip the toggle below to let the pipeline create and promote tests on its own.
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="panel">
        <div class="panel-header">
            <h2>Outreach system</h2>
        </div>
        <div class="panel-content">
            <p class="hint" style="margin-top:0;">
                Master switch for the whole pipeline. When OFF, the cron exits without touching anything — no discovery, no drafts, no sends — even if it's still scheduled at the server level. Use this when you want a full pause without deleting the cron job.
            </p>
            <div class="segmented-toggle">
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="action" value="set_outreach_enabled">
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit" class="segmented-option <?php echo $outreachEnabled ? 'active' : ''; ?>">
                        <span class="segmented-title">Enabled</span>
                        <span class="segmented-desc">Pipeline runs as normal on its cron schedule.</span>
                    </button>
                </form>
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="action" value="set_outreach_enabled">
                    <input type="hidden" name="enabled" value="0">
                    <button type="submit" class="segmented-option <?php echo !$outreachEnabled ? 'active' : ''; ?>">
                        <span class="segmented-title">Disabled</span>
                        <span class="segmented-desc">Cron exits immediately. Nothing runs until re-enabled.</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Send mode</h2>
        </div>
        <div class="panel-content">
            <p class="hint" style="margin-top:0;">
                Controls whether the hourly pipeline auto-approves and sends drafts, or stops at draft generation for you to review.
            </p>
            <div class="segmented-toggle">
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="action" value="set_auto_send_mode">
                    <input type="hidden" name="mode" value="auto">
                    <button type="submit" class="segmented-option <?php echo $autoSendMode === 'auto' ? 'active' : ''; ?>">
                        <span class="segmented-title">Auto-send</span>
                        <span class="segmented-desc">Drafts are auto-approved and sent, up to <?php echo $dailyLimit; ?>/day.</span>
                    </button>
                </form>
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="action" value="set_auto_send_mode">
                    <input type="hidden" name="mode" value="review">
                    <button type="submit" class="segmented-option <?php echo $autoSendMode === 'review' ? 'active' : ''; ?>">
                        <span class="segmented-title">Review before send</span>
                        <span class="segmented-desc">Pipeline generates drafts and stops. You approve each from the Leads tab.</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>A/B automation</h2>
        </div>
        <div class="panel-content">
            <p class="hint" style="margin-top:0;">
                When on, the pipeline runs A/B cycles by itself: it auto-generates variants each cycle (or uses the
                fixed pool for sender / format / personalization), promotes the winner when it has enough data (or
                after 14&ndash;28 days), and starts the next cycle. By default it cycles only subject-line tests
                &mdash; flip the auto-rotation toggle below to cycle across other types too.
                It will self-pause if winner CTR drops below <?php echo number_format($ctrFloor * 100, 1); ?>%.
            </p>
            <div class="segmented-toggle">
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="action" value="set_ab_auto_enabled">
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit" class="segmented-option <?php echo $abAutoEnabled ? 'active' : ''; ?>">
                        <span class="segmented-title">On</span>
                        <span class="segmented-desc">Runs on every hourly cron.</span>
                    </button>
                </form>
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="action" value="set_ab_auto_enabled">
                    <input type="hidden" name="enabled" value="0">
                    <button type="submit" class="segmented-option <?php echo !$abAutoEnabled ? 'active' : ''; ?>">
                        <span class="segmented-title">Off</span>
                        <span class="segmented-desc">Active test keeps running; no new cycles start.</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>A/B auto-rotation</h2>
        </div>
        <div class="panel-content">
            <p class="hint" style="margin-top:0;">
                When on, completed cycles trigger the next type in rotation:
                <strong><?php echo htmlspecialchars(implode(' &rarr; ', $rotationOrder)); ?></strong> &rarr; (loop).
                When off, only subject-line cycles auto-create. Body / CTA / preheader stay admin-initiated either way.
                <?php if ($abAutoRotation): ?>
                    <br>Next type the pipeline will start: <strong><?php echo htmlspecialchars($abNextType); ?></strong>.
                <?php endif; ?>
            </p>
            <div class="segmented-toggle">
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="action" value="set_ab_auto_rotation">
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit" class="segmented-option <?php echo $abAutoRotation ? 'active' : ''; ?>">
                        <span class="segmented-title">On</span>
                        <span class="segmented-desc">Cycle across subject, sender, format, personalization.</span>
                    </button>
                </form>
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="action" value="set_ab_auto_rotation">
                    <input type="hidden" name="enabled" value="0">
                    <button type="submit" class="segmented-option <?php echo !$abAutoRotation ? 'active' : ''; ?>">
                        <span class="segmented-title">Off</span>
                        <span class="segmented-desc">Subject-only auto-cycles (default).</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Current A/B status</h2>
        </div>
        <div class="panel-content">
            <?php if ($active && $activeStats): ?>
                <div class="test-meta">
                    <div class="meta-item">
                        <div class="meta-label">Active test</div>
                        <div class="meta-value">
                            <a href="?tab=ab-tests&test_id=<?php echo (int) $active['id']; ?>" class="link-strong">
                                <?php echo htmlspecialchars($active['name']); ?>
                            </a>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Type</div>
                        <div class="meta-value"><?php echo htmlspecialchars($active['variant_type']); ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Running for</div>
                        <div class="meta-value"><?php echo (int) $activeStats['days']; ?> day<?php echo $activeStats['days'] === 1 ? '' : 's'; ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Variants</div>
                        <div class="meta-value"><?php echo (int) $activeStats['variants']; ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Sent / clicked</div>
                        <div class="meta-value"><?php echo (int) $activeStats['sent']; ?> / <?php echo (int) $activeStats['clicked']; ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Current leader</div>
                        <div class="meta-value">
                            <?php if ($activeStats['leader']): ?>
                                <?php echo htmlspecialchars($activeStats['leader']); ?>
                                <?php if ($activeStats['leader_ctr'] !== null): ?>
                                    <span class="hint" style="margin:0; font-size:12px;">(<?php echo number_format($activeStats['leader_ctr'] * 100, 1); ?>% CTR)</span>
                                <?php endif; ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="empty-state">No active A/B test.
                    <?php if ($abAutoEnabled): ?>
                        The next pipeline run will create one.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Today's automation log</h2>
        </div>
        <div class="panel-content">
            <?php if (!empty($logLines)): ?>
                <pre class="log-tail"><?php echo htmlspecialchars(implode("\n", $logLines)); ?></pre>
            <?php else: ?>
                <p class="empty-state">No automation entries in today's log yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
