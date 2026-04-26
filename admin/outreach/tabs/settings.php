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

    header('Location: index.php?tab=settings'); exit;
}

function settings_tab_render($pdo)
{
    // Master enable flag for the whole outreach system — defaults ON so a
    // fresh install keeps behaving as before.
    $outreachEnabled = settings_tab_get_state($pdo, 'outreach_enabled', '1') === '1';

    // Current state. Use the same fallback as the cron pipeline so the UI
    // and the pipeline agree on the effective send mode before the admin
    // explicitly chooses one. cron/outreach_pipeline.php derives the default
    // from OUTREACH_AUTO_APPROVE (auto if truthy, review otherwise) — match
    // that here so a fresh install with OUTREACH_AUTO_APPROVE=false doesn't
    // show "Auto-send" in the UI while the cron actually behaves as Review.
    $autoApproveRaw = strtolower(trim((string) ($_ENV['OUTREACH_AUTO_APPROVE'] ?? 'true')));
    $defaultAutoSendMode = filter_var($autoApproveRaw, FILTER_VALIDATE_BOOLEAN) ? 'auto' : 'review';
    $autoSendMode = settings_tab_get_state($pdo, 'auto_send_mode', $defaultAutoSendMode);
    if (!in_array($autoSendMode, ['auto', 'review'], true)) $autoSendMode = $defaultAutoSendMode;

    $abAutoEnabled = settings_tab_get_state($pdo, 'ab_auto_enabled', '1') === '1';
    require_once __DIR__ . '/../../../cron/lib/outreach_helpers.php';
    $rotationOrder = ab_auto_rotation_order();
    $abNextType = settings_tab_get_state($pdo, 'ab_auto_next_type', $rotationOrder[0]);
    if (!in_array($abNextType, $rotationOrder, true)) {
        $abNextType = $rotationOrder[0];
    }

    $dailyLimit = (int) ($_ENV['OUTREACH_DAILY_SEND_LIMIT'] ?? 10);
    $ctrFloor = (float) settings_tab_get_state($pdo, 'ab_ctr_floor', '0.01');
    $replyFloor = (float) settings_tab_get_state($pdo, 'ab_reply_floor', '0.005');

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
        $totalSent = array_sum(array_column($variants, 'sent_count'));
        $totalClicks = array_sum(array_column($variants, 'clicked_count'));
        $totalReplies = array_sum(array_column($variants, 'replied_count'));
        $scoringMetric = $totalReplies > 0 ? 'reply_rate' : 'ctr';
        $leaderIdx = find_leader_idx($variants, $scoringMetric);
        $days = max(0, (int) floor((time() - strtotime($active['started_at'] ?: $active['created_at'])) / 86400));
        $activeStats = [
            'variants' => count($variants),
            'sent' => $totalSent,
            'clicked' => $totalClicks,
            'replied' => $totalReplies,
            'days' => $days,
            'metric' => $scoringMetric,
            'leader' => ($leaderIdx !== null) ? $variants[$leaderIdx]['label'] : null,
            'leader_ctr' => ($leaderIdx !== null && $variants[$leaderIdx]['sent_count'] > 0)
                ? $variants[$leaderIdx]['ctr'] : null,
            'leader_reply_rate' => ($leaderIdx !== null && $variants[$leaderIdx]['sent_count'] > 0)
                ? $variants[$leaderIdx]['reply_rate'] : null,
        ];
    }

    // Tail today's pipeline log for automation-related lines
    $logLines = [];
    $logPath = __DIR__ . '/../../../cron/logs/outreach_pipeline_' . date('Y-m-d') . '.log';
    if (is_readable($logPath)) {
        // Logs are date-rotated daily so a single file stays small (typically a few KB
        // per cron run). Load the day's file, take the last 200 lines, then filter
        // for A/B-related events.
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
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <input type="hidden" name="action" value="set_outreach_enabled">
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit" class="segmented-option <?php echo $outreachEnabled ? 'active' : ''; ?>">
                        <span class="segmented-title">Enabled</span>
                        <span class="segmented-desc">Pipeline runs as normal on its cron schedule.</span>
                    </button>
                </form>
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
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
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <input type="hidden" name="action" value="set_auto_send_mode">
                    <input type="hidden" name="mode" value="auto">
                    <button type="submit" class="segmented-option <?php echo $autoSendMode === 'auto' ? 'active' : ''; ?>">
                        <span class="segmented-title">Auto-send</span>
                        <span class="segmented-desc">Drafts are auto-approved and sent, up to <?php echo $dailyLimit; ?>/day.</span>
                    </button>
                </form>
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <input type="hidden" name="action" value="set_auto_send_mode">
                    <input type="hidden" name="mode" value="review">
                    <button type="submit" class="segmented-option <?php echo $autoSendMode === 'review' ? 'active' : ''; ?>">
                        <span class="segmented-title">Review before send</span>
                        <span class="segmented-desc">Pipeline generates drafts and stops. Review or edit them in the Leads tab, then click Send Email (or use bulk send).</span>
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
                When on, the pipeline runs A/B cycles by itself: it auto-generates variants each cycle (or uses the fixed pool for sender / format / personalization), promotes the winner when it has enough data (or after 14&ndash;28 days), and starts the next cycle &mdash; rotating across types so it tests one lever, then another, and so on.
                Promotion is scored on reply rate when any variant has a reply, falling back to CTR otherwise. It will self-pause if the winner's reply rate drops below <?php echo number_format($replyFloor * 100, 2); ?>% (when promoting on replies), or if CTR drops below <?php echo number_format($ctrFloor * 100, 1); ?>% (deliverability check, always evaluated).
            </p>
            <p class="hint">
                Rotation order:
            </p>
            <ol class="hint" style="margin-top:0;">
                <li><strong>Subject line</strong> &mdash; AI-generated subject styles.</li>
                <li><strong>Sender from-name</strong> &mdash; "Evan" vs "Evan from Argo Books" vs "Argo Books".</li>
                <li><strong>Format</strong> &mdash; full HTML email vs plain text.</li>
                <li><strong>Personalization</strong> &mdash; with vs without the AI-generated business summary.</li>
            </ol>
            <p class="hint">
                Body, CTA, and preheader tests aren't in the rotation: those need wording you write yourself, so they're always started by hand from the A/B Tests tab. Manual tests still work either way &mdash; they just delay the rotation while they run.
                <?php if ($abAutoEnabled): ?>
                    <br><br>The next type the cron will start is <strong><?php echo htmlspecialchars($abNextType); ?></strong> (assuming nothing else is running by then).
                <?php endif; ?>
            </p>
            <div class="segmented-toggle">
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <input type="hidden" name="action" value="set_ab_auto_enabled">
                    <input type="hidden" name="enabled" value="1">
                    <button type="submit" class="segmented-option <?php echo $abAutoEnabled ? 'active' : ''; ?>">
                        <span class="segmented-title">On</span>
                        <span class="segmented-desc">Runs in the daily outreach cron.</span>
                    </button>
                </form>
                <form method="POST" style="display:contents;">
                    <input type="hidden" name="tab" value="settings">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
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
                        <div class="meta-label">Sent / replied / clicked</div>
                        <div class="meta-value"><?php echo (int) $activeStats['sent']; ?> / <?php echo (int) $activeStats['replied']; ?> / <?php echo (int) $activeStats['clicked']; ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Current leader</div>
                        <div class="meta-value">
                            <?php if ($activeStats['leader']): ?>
                                <?php echo htmlspecialchars($activeStats['leader']); ?>
                                <span class="hint" style="margin:0; font-size:12px;">
                                    (<?php
                                        if ($activeStats['metric'] === 'reply_rate' && $activeStats['leader_reply_rate'] !== null) {
                                            echo number_format($activeStats['leader_reply_rate'] * 100, 1) . '% reply rate';
                                        } elseif ($activeStats['leader_ctr'] !== null) {
                                            echo number_format($activeStats['leader_ctr'] * 100, 1) . '% CTR';
                                        }
                                    ?>)
                                </span>
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
