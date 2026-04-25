<?php
/**
 * A/B Tests tab partial.
 *
 * Renders inside admin/outreach/index.php. Handles list view + detail view
 * in a single partial, dispatching on $_GET['test_id'].
 *
 * Exposes two functions:
 *   ab_tests_tab_handle_post($pdo) — process POST submissions for this tab
 *   ab_tests_tab_render($pdo, $testId) — output HTML body of the tab
 *
 * Assumes $pdo is the global PDO connection and the admin session is active.
 */

require_once __DIR__ . '/../../../cron/lib/ab_helpers.php';

/**
 * Handle POST submissions posted with hidden input tab=ab-tests.
 * Every branch ends with header() + exit, so callers should not continue after.
 */
function ab_tests_tab_handle_post($pdo)
{
    $action = $_POST['action'] ?? '';
    $testId = (int) ($_POST['test_id'] ?? 0);

    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $variantType = $_POST['variant_type'] ?? 'subject';
        $notes = trim($_POST['notes'] ?? '');
        $variantLabels = $_POST['variant_label'] ?? [];
        $variantContents = $_POST['variant_content'] ?? [];
        $defaultIdx = isset($_POST['default_variant']) ? (int) $_POST['default_variant'] : 0;

        if ($name === '') {
            $_SESSION['message'] = 'Test name is required.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?tab=ab-tests'); exit;
        }

        if (!in_array($variantType, ['subject', 'body', 'sender', 'cta', 'preheader', 'format', 'personalization'], true)) {
            $variantType = 'subject';
        }

        if ($variantType === 'format') {
            // Format tests are fixed: html (control) vs plain. Ignore any
            // variant rows the form posted — the values are not user-authored.
            $rows = [
                ['label' => 'A', 'content' => 'html',  'is_default' => 1],
                ['label' => 'B', 'content' => 'plain', 'is_default' => 0],
            ];
        } elseif ($variantType === 'personalization') {
            // Personalization tests are fixed: on (current behaviour, includes
            // the AI-generated business_summary) vs off (skip the summary call
            // entirely). Variant content is the literal string read by
            // generate_draft_for_lead.
            $rows = [
                ['label' => 'A', 'content' => 'on',  'is_default' => 1],
                ['label' => 'B', 'content' => 'off', 'is_default' => 0],
            ];
        } else {
            $rows = [];
            foreach ($variantLabels as $i => $label) {
                $label = trim((string) $label);
                $content = trim((string) ($variantContents[$i] ?? ''));
                if ($label === '' || $content === '') continue;
                $rows[] = ['label' => $label, 'content' => $content, 'is_default' => ($i === $defaultIdx) ? 1 : 0];
            }

            if (count($rows) < 2) {
                $_SESSION['message'] = 'A/B tests need at least 2 variants (both label and content filled).';
                $_SESSION['message_type'] = 'error';
                header('Location: index.php?tab=ab-tests'); exit;
            }
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO outreach_ab_tests (name, variant_type, status, notes) VALUES (?, ?, 'draft', ?)");
            $stmt->execute([$name, $variantType, $notes !== '' ? $notes : null]);
            $newId = (int) $pdo->lastInsertId();

            $vStmt = $pdo->prepare("INSERT INTO outreach_ab_variants (test_id, label, content, is_default) VALUES (?, ?, ?, ?)");
            foreach ($rows as $r) {
                $vStmt->execute([$newId, $r['label'], $r['content'], $r['is_default']]);
            }
            $pdo->commit();

            $_SESSION['message'] = 'Test created as draft. Click "Start" when ready.';
            $_SESSION['message_type'] = 'success';
            header('Location: index.php?tab=ab-tests&test_id=' . $newId); exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['message'] = 'Failed to create test: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?tab=ab-tests'); exit;
        }
    }

    if ($action === 'status_change') {
        $newStatus = $_POST['status'] ?? '';
        if ($testId && in_array($newStatus, ['draft', 'active', 'paused', 'completed'], true)) {
            if ($newStatus === 'active') {
                // Only one test can be active at a time across the whole
                // framework — the draft generator picks the first active
                // test it finds, so a second active test of any other type
                // would silently starve. Pause every other active test.
                $pdo->prepare("UPDATE outreach_ab_tests SET status = 'paused' WHERE status = 'active' AND id <> ?")
                    ->execute([$testId]);
                $pdo->prepare("UPDATE outreach_ab_tests SET status = 'active', started_at = COALESCE(started_at, NOW()) WHERE id = ?")
                    ->execute([$testId]);
            } elseif ($newStatus === 'completed') {
                $pdo->prepare("UPDATE outreach_ab_tests SET status = 'completed', completed_at = NOW() WHERE id = ?")
                    ->execute([$testId]);
            } else {
                $pdo->prepare("UPDATE outreach_ab_tests SET status = ? WHERE id = ?")
                    ->execute([$newStatus, $testId]);
            }
            $_SESSION['message'] = 'Test status updated.';
            $_SESSION['message_type'] = 'success';
        }
        $redirect = $testId ? ('index.php?tab=ab-tests&test_id=' . $testId) : 'index.php?tab=ab-tests';
        header('Location: ' . $redirect); exit;
    }

    if ($action === 'promote') {
        $variantId = (int) ($_POST['variant_id'] ?? 0);
        if ($testId > 0 && $variantId > 0) {
            $check = $pdo->prepare("SELECT id FROM outreach_ab_variants WHERE id = ? AND test_id = ?");
            $check->execute([$variantId, $testId]);
            if ($check->fetch()) {
                $pdo->prepare("UPDATE outreach_ab_tests
                    SET winner_variant_id = ?, status = 'completed', completed_at = NOW()
                    WHERE id = ?")
                    ->execute([$variantId, $testId]);
                $_SESSION['message'] = 'Variant promoted. Test completed; the pipeline will start a new cycle on its next run if automation is on.';
                $_SESSION['message_type'] = 'success';
            }
        }
        header('Location: index.php?tab=ab-tests&test_id=' . $testId); exit;
    }

    if ($action === 'update_notes') {
        if ($testId > 0) {
            $notes = trim($_POST['notes'] ?? '');
            $pdo->prepare("UPDATE outreach_ab_tests SET notes = ? WHERE id = ?")
                ->execute([$notes !== '' ? $notes : null, $testId]);
            $_SESSION['message'] = 'Notes saved.';
            $_SESSION['message_type'] = 'success';
        }
        header('Location: index.php?tab=ab-tests&test_id=' . $testId); exit;
    }

    // Unknown action — just bounce back to the tab
    header('Location: index.php?tab=ab-tests'); exit;
}

function ab_tests_tab_render($pdo, $testId = 0)
{
    if ($testId > 0) {
        ab_tests_tab_render_detail($pdo, $testId);
    } else {
        ab_tests_tab_render_list($pdo);
    }
}

function ab_tests_tab_render_list($pdo)
{
    $testsQuery = $pdo->query("
        SELECT
            t.*,
            (SELECT COUNT(*) FROM outreach_ab_variants v WHERE v.test_id = t.id) AS variant_count,
            (SELECT COUNT(*) FROM outreach_leads ol WHERE ol.ab_test_id = t.id) AS assigned_count,
            (SELECT COUNT(*) FROM outreach_leads ol WHERE ol.ab_test_id = t.id AND ol.sent_at IS NOT NULL) AS sent_count,
            (SELECT COUNT(DISTINCT ol.id)
                FROM outreach_leads ol
                JOIN referral_visits rv
                  ON rv.source_code = CONCAT('outreach-', ol.id, '-v', ol.ab_variant_id)
                WHERE ol.ab_test_id = t.id) AS clicked_count
        FROM outreach_ab_tests t
        ORDER BY
            FIELD(t.status, 'active', 'paused', 'draft', 'completed'),
            COALESCE(t.started_at, t.created_at) DESC
    ");
    $tests = $testsQuery->fetchAll();

    $stats = ['active' => 0, 'completed' => 0, 'assigned' => 0, 'clicked' => 0];
    foreach ($tests as $t) {
        if ($t['status'] === 'active') $stats['active']++;
        if ($t['status'] === 'completed') $stats['completed']++;
        $stats['assigned'] += (int) $t['assigned_count'];
        $stats['clicked']  += (int) $t['clicked_count'];
    }
    ?>

    <div class="stats-row" style="margin-top:4px;">
        <div class="stat-card">
            <div class="stat-label">Active Tests</div>
            <div class="stat-value stat-active"><?php echo (int) $stats['active']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Completed</div>
            <div class="stat-value stat-completed"><?php echo (int) $stats['completed']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Leads Assigned</div>
            <div class="stat-value"><?php echo (int) $stats['assigned']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Leads Clicked</div>
            <div class="stat-value stat-clicked-ab"><?php echo (int) $stats['clicked']; ?></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header" onclick="togglePanel('abCreatePanelContent')">
            <h2>New A/B Test (manual)</h2>
            <span class="panel-toggle" id="abCreatePanelToggle">&#9654;</span>
        </div>
        <div class="panel-content" id="abCreatePanelContent" style="display:none;">
            <p class="hint" style="margin-top:0;">
                A/B automation creates tests for you when turned on. Use this form if you want to run a specific test by hand.
            </p>
            <form method="POST" id="abCreateForm">
                <input type="hidden" name="tab" value="ab-tests">
                <input type="hidden" name="action" value="create">

                <div class="form-row">
                    <div class="form-group" style="flex:2; min-width:220px;">
                        <label for="abTestName">Test name</label>
                        <input type="text" id="abTestName" name="name" placeholder="e.g. Curiosity vs direct subject line" required>
                    </div>
                    <div class="form-group" style="flex:1; min-width:140px;">
                        <label for="abVariantType">Variant type</label>
                        <select id="abVariantType" name="variant_type">
                            <option value="subject" selected>Subject line</option>
                            <option value="body">Email body</option>
                            <option value="cta">CTA / offer</option>
                            <option value="sender">Sender from-name</option>
                            <option value="preheader">Preheader (inbox preview)</option>
                            <option value="format">Format (HTML vs plain text)</option>
                            <option value="personalization">Personalization (with vs without business summary)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label for="abTestNotes">Notes (optional)</label>
                    <textarea id="abTestNotes" name="notes" rows="2" placeholder="What are you trying to learn?"></textarea>
                </div>

                <p class="hint">
                    <strong>Variant content:</strong> Either a literal value (used verbatim) OR a directive prefixed with
                    <code>directive:</code> (e.g. <code>directive: Ask a curiosity question referencing the business's city</code>).
                    Directives let the AI generate the value each time while staying in a style.
                    <span id="senderHintNote" style="display:none;"><br><strong>Sender variants are literal-only</strong> — the from-name string is used as-is in the email envelope, with no AI interpretation. Skip the <code>directive:</code> prefix here. Try things like <code>Evan</code> vs <code>Evan from Argo Books</code> vs <code>Argo Books</code>.</span>
                    <span id="preheaderHintNote" style="display:none;"><br><strong>Preheader variants are literal-only</strong> — this is the snippet most inboxes show next to the subject. Use short, scannable text. Try things like <code>Quick question about your business</code> vs <code>Free 1-year license inside</code> vs leave one variant blank to test the &ldquo;no preheader&rdquo; baseline.</span>
                    <span id="formatHintNote" style="display:none;"><br><strong>Format is a fixed two-variant test</strong> — Variant A sends the full styled HTML email (current behaviour); Variant B sends the same content as plain text (no template, no logo, bare URLs). The variant rows below are not used for this test type.</span>
                    <span id="personalizationHintNote" style="display:none;"><br><strong>Personalization is a fixed two-variant test</strong> — Variant A keeps the AI-generated <code>business_summary</code> (current behaviour, costs an OpenAI call per lead). Variant B skips the summary entirely. Use it to find out whether the extra call actually moves CTR.</span>
                </p>

                <div id="abVariantRows"></div>

                <div id="abFormatFixedNotice" class="hint" style="display:none; padding:10px; border:1px dashed var(--border-color, #d1d5db); border-radius:6px; margin-top:8px;">
                    Will create two variants automatically: <strong>A &mdash; <code>html</code></strong> (default) and <strong>B &mdash; <code>plain</code></strong>.
                </div>

                <div id="abPersonalizationFixedNotice" class="hint" style="display:none; padding:10px; border:1px dashed var(--border-color, #d1d5db); border-radius:6px; margin-top:8px;">
                    Will create two variants automatically: <strong>A &mdash; <code>on</code></strong> (default, summary included) and <strong>B &mdash; <code>off</code></strong> (summary skipped).
                </div>

                <div id="abVariantControls" style="display:flex; gap:8px; margin-top:8px; align-items:center;">
                    <button type="button" class="btn btn-small btn-neutral" onclick="abAddVariantRow()">+ Add variant</button>
                    <span class="hint" id="abVariantCountHint">2 of up to 4 variants</span>
                </div>

                <div style="margin-top:16px; display:flex; gap:8px;">
                    <button type="submit" class="btn btn-blue">Create as draft</button>
                </div>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>All tests</h2>
        </div>
        <div class="panel-content">
            <?php if (empty($tests)): ?>
                <p class="empty-state">No A/B tests yet. Turn on automation in the Settings tab, or create one above.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Variants</th>
                                <th>Assigned</th>
                                <th>Sent</th>
                                <th>Clicked</th>
                                <th>CTR</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tests as $t): ?>
                                <tr>
                                    <td>
                                        <a href="?tab=ab-tests&test_id=<?php echo (int) $t['id']; ?>" class="link-strong">
                                            <?php echo htmlspecialchars($t['name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($t['variant_type']); ?></td>
                                    <td>
                                        <span class="status-pill status-<?php echo htmlspecialchars($t['status']); ?>">
                                            <?php echo htmlspecialchars(ucfirst($t['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo (int) $t['variant_count']; ?></td>
                                    <td><?php echo (int) $t['assigned_count']; ?></td>
                                    <td><?php echo (int) $t['sent_count']; ?></td>
                                    <td><?php echo (int) $t['clicked_count']; ?></td>
                                    <td><?php echo format_ctr((int) $t['sent_count'], (int) $t['clicked_count']); ?></td>
                                    <td class="actions-cell">
                                        <?php if ($t['status'] === 'draft' || $t['status'] === 'paused'): ?>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Activate this test? Any other currently active test will be paused (only one A/B test can be active at a time).');">
                                                <input type="hidden" name="tab" value="ab-tests">
                                                <input type="hidden" name="action" value="status_change">
                                                <input type="hidden" name="test_id" value="<?php echo (int) $t['id']; ?>">
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="btn btn-small btn-green">Start</button>
                                            </form>
                                        <?php elseif ($t['status'] === 'active'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="tab" value="ab-tests">
                                                <input type="hidden" name="action" value="status_change">
                                                <input type="hidden" name="test_id" value="<?php echo (int) $t['id']; ?>">
                                                <input type="hidden" name="status" value="paused">
                                                <button type="submit" class="btn btn-small">Pause</button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="?tab=ab-tests&test_id=<?php echo (int) $t['id']; ?>" class="btn btn-small">Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        (function() {
            if (window.__abVariantRowsInit) return;
            window.__abVariantRowsInit = true;

            var container = document.getElementById('abVariantRows');
            if (!container) return;
            var hint = document.getElementById('abVariantCountHint');
            var MAX = 4;

            function renderLabel(idx) { return String.fromCharCode(65 + idx); }

            function makeRow(idx) {
                var row = document.createElement('div');
                row.className = 'variant-row';
                row.innerHTML =
                    '<div class="variant-row-header">' +
                        '<label><input type="radio" name="default_variant" value="' + idx + '"' + (idx === 0 ? ' checked' : '') + '> Default</label>' +
                        '<button type="button" class="btn btn-small btn-neutral variant-remove" title="Remove variant">&times;</button>' +
                    '</div>' +
                    '<div class="form-row">' +
                        '<div class="form-group" style="flex:0 0 110px;">' +
                            '<label>Label</label>' +
                            '<input type="text" name="variant_label[]" value="' + renderLabel(idx) + '" required>' +
                        '</div>' +
                        '<div class="form-group" style="flex:1; min-width:240px;">' +
                            '<label>Subject or <code>directive:</code> prompt</label>' +
                            '<input type="text" name="variant_content[]" placeholder="e.g. Thought of you guys  OR  directive: Ask a curiosity question referencing the city" required>' +
                        '</div>' +
                    '</div>';
                row.querySelector('.variant-remove').addEventListener('click', function() {
                    if (container.children.length <= 2) return;
                    row.remove();
                    refreshIndices();
                });
                return row;
            }

            function refreshIndices() {
                var rows = container.querySelectorAll('.variant-row');
                rows.forEach(function(r, i) {
                    var radio = r.querySelector('input[name="default_variant"]');
                    if (radio) radio.value = i;
                    var labelInput = r.querySelector('input[name="variant_label[]"]');
                    if (labelInput && labelInput.dataset.autoLabel !== 'false') {
                        labelInput.value = renderLabel(i);
                    }
                });
                if (hint) hint.textContent = rows.length + ' of up to ' + MAX + ' variants';
            }

            window.abAddVariantRow = function() {
                var count = container.children.length;
                if (count >= MAX) return;
                container.appendChild(makeRow(count));
                refreshIndices();
            };

            container.appendChild(makeRow(0));
            container.appendChild(makeRow(1));
            refreshIndices();

            // Show type-specific notes when the matching type is selected.
            var typeSelect = document.getElementById('abVariantType');
            var senderNote = document.getElementById('senderHintNote');
            var preheaderNote = document.getElementById('preheaderHintNote');
            var formatNote = document.getElementById('formatHintNote');
            var personalizationNote = document.getElementById('personalizationHintNote');
            var formatFixedNotice = document.getElementById('abFormatFixedNotice');
            var personalizationFixedNotice = document.getElementById('abPersonalizationFixedNotice');
            var variantRows = document.getElementById('abVariantRows');
            var variantControls = document.getElementById('abVariantControls');
            if (typeSelect) {
                var syncTypeNotes = function () {
                    var v = typeSelect.value;
                    if (senderNote) senderNote.style.display = v === 'sender' ? 'inline' : 'none';
                    if (preheaderNote) preheaderNote.style.display = v === 'preheader' ? 'inline' : 'none';
                    if (formatNote) formatNote.style.display = v === 'format' ? 'inline' : 'none';
                    if (personalizationNote) personalizationNote.style.display = v === 'personalization' ? 'inline' : 'none';
                    var isFixed = (v === 'format' || v === 'personalization');
                    if (variantRows) variantRows.style.display = isFixed ? 'none' : '';
                    if (variantControls) variantControls.style.display = isFixed ? 'none' : 'flex';
                    if (formatFixedNotice) formatFixedNotice.style.display = v === 'format' ? 'block' : 'none';
                    if (personalizationFixedNotice) personalizationFixedNotice.style.display = v === 'personalization' ? 'block' : 'none';
                };
                typeSelect.addEventListener('change', syncTypeNotes);
                syncTypeNotes();
            }
        })();
    </script>
    <?php
}

function ab_tests_tab_render_detail($pdo, $testId)
{
    $testStmt = $pdo->prepare("SELECT * FROM outreach_ab_tests WHERE id = ?");
    $testStmt->execute([$testId]);
    $test = $testStmt->fetch();

    if (!$test) {
        echo '<p class="empty-state">Test not found. <a href="?tab=ab-tests" class="link-strong">Back to all tests</a></p>';
        return;
    }

    $variants = load_variants_with_stats($pdo, $testId);
    $leaderIdx = find_leader_idx($variants);

    $chartLabels = array_map(fn($v) => $v['label'], $variants);
    $chartCtrs   = array_map(fn($v) => round($v['ctr'] * 100, 2), $variants);
    $chartClicks = array_map(fn($v) => (int) $v['clicked_count'], $variants);
    $chartSent   = array_map(fn($v) => (int) $v['sent_count'], $variants);
    ?>

    <p style="margin-top:0;"><a href="?tab=ab-tests" class="link-strong">&larr; All tests</a></p>

    <div class="panel">
        <div class="panel-header">
            <h2>
                <?php echo htmlspecialchars($test['name']); ?>
                <span class="status-pill status-<?php echo htmlspecialchars($test['status']); ?>" style="margin-left:8px; font-size:11px; vertical-align:middle;">
                    <?php echo htmlspecialchars(ucfirst($test['status'])); ?>
                </span>
            </h2>
            <div>
                <?php if ($test['status'] === 'draft' || $test['status'] === 'paused'): ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Activate this test? Any other currently active test will be paused (only one A/B test can be active at a time).');">
                        <input type="hidden" name="tab" value="ab-tests">
                        <input type="hidden" name="action" value="status_change">
                        <input type="hidden" name="test_id" value="<?php echo (int) $test['id']; ?>">
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="btn btn-small btn-green">Start</button>
                    </form>
                <?php elseif ($test['status'] === 'active'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="tab" value="ab-tests">
                        <input type="hidden" name="action" value="status_change">
                        <input type="hidden" name="test_id" value="<?php echo (int) $test['id']; ?>">
                        <input type="hidden" name="status" value="paused">
                        <button type="submit" class="btn btn-small">Pause</button>
                    </form>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('End this test without picking a winner? Existing leads keep their variant; new leads stop getting assigned.');">
                        <input type="hidden" name="tab" value="ab-tests">
                        <input type="hidden" name="action" value="status_change">
                        <input type="hidden" name="test_id" value="<?php echo (int) $test['id']; ?>">
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-small">End</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <div class="panel-content">
            <div class="test-meta">
                <div class="meta-item">
                    <div class="meta-label">Variant type</div>
                    <div class="meta-value"><?php echo htmlspecialchars($test['variant_type']); ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Created</div>
                    <div class="meta-value"><?php echo htmlspecialchars(date('M j, Y', strtotime($test['created_at']))); ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Started</div>
                    <div class="meta-value"><?php echo $test['started_at'] ? htmlspecialchars(date('M j, Y', strtotime($test['started_at']))) : '—'; ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Completed</div>
                    <div class="meta-value"><?php echo $test['completed_at'] ? htmlspecialchars(date('M j, Y', strtotime($test['completed_at']))) : '—'; ?></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Winner</div>
                    <div class="meta-value">
                        <?php
                            if ($test['winner_variant_id']) {
                                $winnerLabel = '';
                                foreach ($variants as $v) {
                                    if ((int) $v['id'] === (int) $test['winner_variant_id']) {
                                        $winnerLabel = $v['label'];
                                        break;
                                    }
                                }
                                echo htmlspecialchars($winnerLabel !== '' ? $winnerLabel : '#' . $test['winner_variant_id']);
                            } else {
                                echo '—';
                            }
                        ?>
                    </div>
                </div>
            </div>

            <form method="POST" style="margin-top:4px;">
                <input type="hidden" name="tab" value="ab-tests">
                <input type="hidden" name="action" value="update_notes">
                <input type="hidden" name="test_id" value="<?php echo (int) $test['id']; ?>">
                <div class="form-group">
                    <label for="abNotesField">Notes</label>
                    <textarea id="abNotesField" name="notes" rows="2" placeholder="Why you ran this test, any context for later."><?php echo htmlspecialchars((string) $test['notes']); ?></textarea>
                </div>
                <div style="margin-top:8px;">
                    <button type="submit" class="btn btn-small">Save notes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Variants</h2>
        </div>
        <div class="panel-content">
            <?php if (empty($variants)): ?>
                <p class="empty-state">No variants on this test.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Content</th>
                                <th>Assigned</th>
                                <th>Sent</th>
                                <th>Clicked</th>
                                <th>CTR</th>
                                <th>Confidence vs leader</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($variants as $i => $v): ?>
                                <?php
                                    $isLeader = ($i === $leaderIdx);
                                    $confidence = ['tag' => 'insufficient', 'label' => '—'];
                                    if ($leaderIdx !== null && !$isLeader) {
                                        $confidence = confidence_vs_leader(
                                            $variants[$leaderIdx]['sent_count'],
                                            $variants[$leaderIdx]['clicked_count'],
                                            $v['sent_count'],
                                            $v['clicked_count']
                                        );
                                    }
                                    $isDirective = stripos(trim((string) $v['content']), 'directive:') === 0;
                                    $isWinner = ((int) $test['winner_variant_id'] === (int) $v['id']);
                                ?>
                                <tr class="<?php echo $isLeader ? 'conf-leader-row' : ''; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($v['label']); ?></strong>
                                        <?php if ($v['is_default']): ?><span class="hint" style="display:block; margin:0; font-size:11px;">default</span><?php endif; ?>
                                        <?php if ($isWinner): ?><span class="status-pill status-completed" style="font-size:10px; margin-top:2px;">Winner</span><?php endif; ?>
                                    </td>
                                    <td class="variant-content-cell">
                                        <?php if ($isDirective): ?><span class="directive-pill">directive</span><?php endif; ?>
                                        <?php echo htmlspecialchars($isDirective ? trim(substr($v['content'], strlen('directive:'))) : $v['content']); ?>
                                    </td>
                                    <td><?php echo (int) $v['assigned_count']; ?></td>
                                    <td><?php echo (int) $v['sent_count']; ?></td>
                                    <td><?php echo (int) $v['clicked_count']; ?></td>
                                    <td><?php echo format_ctr((int) $v['sent_count'], (int) $v['clicked_count']); ?></td>
                                    <td>
                                        <?php if ($isLeader): ?>
                                            <span class="hint" style="margin:0;">leader</span>
                                        <?php else: ?>
                                            <span class="confidence-tag conf-<?php echo htmlspecialchars($confidence['tag']); ?>">
                                                <?php echo htmlspecialchars($confidence['label']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions-cell">
                                        <?php if ($test['status'] !== 'completed'): ?>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Promote this variant and end the test? No more new leads will be assigned.');">
                                                <input type="hidden" name="tab" value="ab-tests">
                                                <input type="hidden" name="action" value="promote">
                                                <input type="hidden" name="test_id" value="<?php echo (int) $test['id']; ?>">
                                                <input type="hidden" name="variant_id" value="<?php echo (int) $v['id']; ?>">
                                                <button type="submit" class="btn btn-small btn-blue">Promote to winner</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="chart-card">
                    <h3>CTR by variant (% of sent emails that got a click)</h3>
                    <div class="chart-wrap">
                        <canvas id="abCtrChart"></canvas>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    (function() {
        var el = document.getElementById('abCtrChart');
        if (!el || typeof Chart === 'undefined') return;

        var ctrs = <?php echo json_encode($chartCtrs); ?>;
        var clicks = <?php echo json_encode($chartClicks); ?>;
        var sent = <?php echo json_encode($chartSent); ?>;
        var leaderIdx = <?php echo $leaderIdx === null ? '-1' : (int) $leaderIdx; ?>;

        new Chart(el.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'CTR %',
                    data: ctrs,
                    backgroundColor: ctrs.map(function(_, i) {
                        return i === leaderIdx ? '#22c55e' : '#3b82f6';
                    }),
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                var i = ctx.dataIndex;
                                return 'CTR: ' + ctrs[i].toFixed(1) + '% (' + clicks[i] + ' clicks of ' + sent[i] + ' sent)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: function(v) { return v + '%'; } }
                    }
                }
            }
        });
    })();
    </script>
    <?php
}
