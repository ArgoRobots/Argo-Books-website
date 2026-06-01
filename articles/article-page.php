<?php
// articles/article-page.php
//
// Shared template for every informational article under argorobots.com.
// Driven entirely by data files in articles/data/{slug}.php.
//
// Routing:
//   /how-to-invoice-clients/  -> articles/article-page.php?slug=how-to-invoice-clients
//   (etc., one RewriteRule per article slug in .htaccess)
//
// Heading structure (strict, for SEO):
//   <h1>            article title (from $data['h1'])
//   <h2> ...        one per section in $data['sections']
//   <h2>            Frequently asked questions (when $data['faqs'] non-empty)
//     <h3> ...        one per FAQ question
//   <h2>            Related guides
//   <h2>            Related articles (when $data['related_article_slugs'] non-empty)

require_once __DIR__ . '/../invoice-generator/_base.php';
require_once __DIR__ . '/../config/pricing.php';

// --- 1. Sanitize the slug -----------------------------------------------------

$slug_raw = $_GET['slug'] ?? '';
$slug = is_string($slug_raw) ? strtolower($slug_raw) : '';

if ($slug === '' || !preg_match('/^[a-z0-9-]+$/', $slug)) {
    article_render_404();
    exit;
}

// --- 2. Load the data file ----------------------------------------------------

$data_file = __DIR__ . '/data/' . $slug . '.php';
if (!is_file($data_file)) {
    article_render_404();
    exit;
}

$data = require $data_file;

if (!is_array($data) || empty($data['h1']) || empty($data['sections'])) {
    article_render_404();
    exit;
}

// --- 3. Server-side page view (post-404) --------------------------------------

if (PHP_SAPI !== 'cli') {
    // Referral tracking: capture ?source so a direct landing on this article
    // (from YouTube, an ad, a newsletter) is attributed in the funnel.
    require_once __DIR__ . '/../track_referral.php';
    require_once __DIR__ . '/../statistics.php';
    $safe_slug_for_event = preg_replace('/[^a-z0-9_-]/', '', $slug);
    track_page_view('invgen_article_' . $safe_slug_for_event);
}

$invgen_ref = 'guide-' . $slug;
$utm_qs = '?source=' . htmlspecialchars($invgen_ref)
        . '&amp;utm_source=invoice-generator&amp;utm_medium=article&amp;utm_campaign=phase1';

// --- 4. Page metadata ---------------------------------------------------------

$page_title = $data['meta_title'] ?? ($data['h1'] . ' | Argo Books');
$page_description = $data['meta_description'] ?? '';
$canonical_url = 'https://argorobots.com/' . $slug . '/';

// --- 5. JSON-LD ---------------------------------------------------------------

$schema_type = $data['schema_type'] ?? 'Article';
$published = $data['published'] ?? ($data['updated'] ?? date('Y-m-d'));
$updated = $data['updated'] ?? $published;

$base_schema = [
  '@context' => 'https://schema.org',
  '@type' => $schema_type,
  'headline' => $data['h1'],
  'description' => $data['meta_description'] ?? '',
  'datePublished' => $published,
  'dateModified' => $updated,
  'author' => ['@type' => 'Organization', 'name' => 'Argo Books', 'url' => 'https://argorobots.com/'],
  'publisher' => [
    '@type' => 'Organization',
    'name' => 'Argo Books',
    'logo' => [
      '@type' => 'ImageObject',
      'url' => 'https://argorobots.com/resources/images/argo-logo/argo-logo-black.png',
    ],
  ],
  'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical_url],
];

if ($schema_type === 'HowTo') {
    $steps = [];
    $position = 1;
    foreach ($data['sections'] as $section) {
        if (!empty($section['step_name'])) {
            $steps[] = [
                '@type' => 'HowToStep',
                'position' => $position++,
                'name' => $section['step_name'],
                'text' => $section['step_text'] ?? strip_tags($section['html'] ?? ''),
            ];
        }
    }
    if (!empty($steps)) {
        $base_schema['step'] = $steps;
    }
    if (!empty($data['total_time_iso8601'])) {
        $base_schema['totalTime'] = $data['total_time_iso8601'];
    }
}

$page_schema_json = json_encode($base_schema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

$breadcrumb_items = [
  ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://argorobots.com/'],
  ['@type' => 'ListItem', 'position' => 2, 'name' => 'Guides', 'item' => 'https://argorobots.com/guides/'],
  ['@type' => 'ListItem', 'position' => 3, 'name' => $data['h1'], 'item' => $canonical_url],
];
$breadcrumb_schema_json = json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'BreadcrumbList',
  'itemListElement' => $breadcrumb_items,
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

// --- 6. Body ------------------------------------------------------------------

$callout_after = isset($data['callout_after_section_index']) ? (int)$data['callout_after_section_index'] : -1;
$tool_callout_text = $data['tool_callout_text'] ?? 'Open the free invoice generator and fill in your details now.';
$tool_callout_cta = $data['tool_callout_cta'] ?? 'Open the invoice generator';

// Optional site-relative callout target (e.g. '/features/receipt-scanning/').
// When unset, the callout points at the invoice generator. Either way it
// carries ?source so the funnel attributes the click to this article. The
// href is built with HTML-encoded ampersands and echoed raw (do not wrap in
// htmlspecialchars, or the &amp; entities double-encode).
$tool_callout_url = $data['tool_callout_url'] ?? null;
if ($tool_callout_url !== null) {
    $callout_sep = strpos($tool_callout_url, '?') !== false ? '&amp;' : '?';
    $tool_callout_href = INVGEN_BASE . $tool_callout_url . $callout_sep . 'source=' . htmlspecialchars($invgen_ref);
} else {
    $tool_callout_href = INVGEN_BASE . '/invoice-generator/' . $utm_qs . '&amp;placement=inline';
}

// Tag every internal link in the article body with ?source so a click through
// to downloads, features, or the generator is credited to this article.
// Leaves external links, anchors, and already-tagged links untouched.
if (!function_exists('article_tag_source')) {
    function article_tag_source(string $html, string $source): string
    {
        // Single-segment paths that are articles or the guides hub are content
        // navigation, not "main site" destinations, so they stay clean (no
        // tracking params on internal cross-links, which is better for SEO).
        static $skip = null;
        if ($skip === null) {
            $skip = ['guides' => true];
            foreach (glob(__DIR__ . '/data/*.php') as $af) {
                $as = basename($af, '.php');
                if ($as !== '_template') {
                    $skip[$as] = true;
                }
            }
        }
        return preg_replace_callback('/href="([^"]+)"/i', function ($m) use ($source, $skip) {
            $url = $m[1];
            $internal = (str_starts_with($url, '/') && !str_starts_with($url, '//'))
                || stripos($url, 'argorobots.com') !== false;
            if (!$internal || preg_match('/[?&](amp;)?source=/i', $url)) {
                return $m[0];
            }
            $path = parse_url($url, PHP_URL_PATH);
            $seg = $path !== null ? trim($path, '/') : '';
            if ($seg !== '' && strpos($seg, '/') === false && isset($skip[$seg])) {
                return $m[0];
            }
            $frag = '';
            if (($h = strpos($url, '#')) !== false) {
                $frag = substr($url, $h);
                $url = substr($url, 0, $h);
            }
            $sep = strpos($url, '?') !== false ? '&amp;' : '?';
            return 'href="' . $url . $sep . 'source=' . $source . $frag . '"';
        }, $html);
    }
}

ob_start();
?>
<article class="article-page">

  <nav class="article-breadcrumb" aria-label="Breadcrumb">
    <a class="article-breadcrumb-link" href="<?= INVGEN_BASE ?>/guides/">
      <span aria-hidden="true">&larr;</span> All guides
    </a>
  </nav>

  <header class="article-head">
    <h1><?= htmlspecialchars($data['h1']) ?></h1>
    <?php if (!empty($data['updated']) || !empty($data['reading_time_min'])): ?>
      <p class="article-meta">
        <?php if (!empty($data['updated'])): ?>
          <span class="article-updated">Updated <?= htmlspecialchars($data['updated']) ?></span>
        <?php endif; ?>
        <?php if (!empty($data['reading_time_min'])): ?>
          <span class="article-reading-time"><?= (int)$data['reading_time_min'] ?> min read</span>
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </header>

  <section class="article-intro">
    <?= article_tag_source(pricing_substitute($data['intro_html'] ?? ''), $invgen_ref) ?>
  </section>

  <?php foreach ($data['sections'] as $i => $section): ?>
    <section class="article-section" id="<?= htmlspecialchars($section['anchor'] ?? ('section-' . ($i + 1))) ?>">
      <?php if (!empty($section['h2'])): ?>
        <h2><?= htmlspecialchars($section['h2']) ?></h2>
      <?php endif; ?>
      <?= article_tag_source(pricing_substitute($section['html'] ?? ''), $invgen_ref) ?>
    </section>

    <?php if ($i === $callout_after): ?>
      <aside class="tool-callout" role="complementary">
        <p class="tool-callout-text"><?= htmlspecialchars($tool_callout_text) ?></p>
        <a class="tool-callout-link"
           data-pitch-placement="article-inline"
           href="<?= $tool_callout_href ?>">
          <?= htmlspecialchars($tool_callout_cta) ?> <span aria-hidden="true">&rarr;</span>
        </a>
      </aside>
    <?php endif; ?>
  <?php endforeach; ?>

  <?php if (!empty($data['faqs'])): ?>
    <section class="article-faqs">
      <h2>Frequently asked questions</h2>
      <div class="faq-grid">
        <?php foreach ($data['faqs'] as $faq): ?>
          <?php if (empty($faq['q']) || empty($faq['a'])) continue; ?>
          <div class="faq-item">
            <button type="button" class="faq-question" aria-expanded="false">
              <h3><?= htmlspecialchars(pricing_substitute($faq['q'])) ?></h3>
              <span class="faq-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6,9 12,15 18,9"/>
                </svg>
              </span>
            </button>
            <div class="faq-answer">
              <div class="faq-answer-content">
                <p><?= htmlspecialchars(pricing_substitute($faq['a'])) ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <section class="article-related-niches">
    <h2>Related guides</h2>
    <?php
      $related_niche_slugs = array_values(array_filter(
        $data['related_niche_slugs'] ?? [],
        fn($s) => is_string($s) && preg_match('/^[a-z0-9-]+$/', $s)
      ));
    ?>
    <?php if (count($related_niche_slugs) < 3 && function_exists('current_environment') && current_environment() === 'sandbox'): ?>
      <p class="article-dev-warning" style="border:2px solid #c00;background:#fff5f5;color:#900;padding:10px 14px;font-weight:600;">
        MISSING INTERNAL LINKS: <?= count($related_niche_slugs) ?>/3 (visible in sandbox only)
      </p>
    <?php endif; ?>
    <ul class="article-related-list">
      <?php foreach ($related_niche_slugs as $rs): ?>
        <li>
          <a href="<?= INVGEN_BASE ?>/free-invoice-generator/<?= htmlspecialchars($rs) ?>/">
            <?= htmlspecialchars(ucwords(str_replace('-', ' ', $rs))) ?> invoice generator
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>

  <?php
    $related_article_slugs = array_values(array_filter(
      $data['related_article_slugs'] ?? [],
      fn($s) => is_string($s) && preg_match('/^[a-z0-9-]+$/', $s)
    ));
  ?>
  <?php if (!empty($related_article_slugs)): ?>
    <section class="article-related-articles">
      <h2>Related articles</h2>
      <ul class="article-related-list">
        <?php foreach ($related_article_slugs as $as): ?>
          <li>
            <a href="<?= INVGEN_BASE ?>/<?= htmlspecialchars($as) ?>/">
              <?= htmlspecialchars(ucfirst(str_replace('-', ' ', $as))) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>

  <aside class="page-banner" role="complementary">
    <span class="page-banner-text">Argo Books is the accounting app behind these guides.</span>
    <a class="link page-banner-link"
       data-pitch-placement="article-footer"
       href="https://argorobots.com/<?= $utm_qs ?>&amp;placement=footer">
      Try it free <span aria-hidden="true">&rarr;</span>
    </a>
  </aside>

</article>
<?php
$body_content = article_apply_link_class(article_prefix_internal_links(ob_get_clean()));

// Collapsible FAQ click handler. Same pattern as niches/niche-page.php.
$extra_scripts = <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function () {
  var items = document.querySelectorAll('.article-faqs .faq-item');
  items.forEach(function (item) {
    var question = item.querySelector('.faq-question');
    if (!question) return;
    question.addEventListener('click', function () {
      var wasActive = item.classList.contains('active');
      items.forEach(function (other) {
        other.classList.remove('active');
        var btn = other.querySelector('.faq-question');
        if (btn) btn.setAttribute('aria-expanded', 'false');
      });
      if (!wasActive) {
        item.classList.add('active');
        question.setAttribute('aria-expanded', 'true');
      }
    });
  });
});
</script>
HTML;

include __DIR__ . '/../invoice-generator/layout.php';

// -----------------------------------------------------------------------------

/**
 * Add `class="link"` to any inline <a> tag that has no class attribute
 * yet. Article data files write plain anchors like
 * `<a href="/foo/">bar</a>`; this brings them into the project's standard
 * link styling without forcing each anchor to be authored with the class.
 * Anchors that already carry a class (breadcrumb, tool callout, page
 * banner, etc.) are left untouched.
 */
function article_apply_link_class(string $html): string
{
    return preg_replace_callback(
        '/<a (?![^>]*\bclass=)([^>]*?)>/i',
        fn($m) => '<a class="link" ' . $m[1] . '>',
        $html
    );
}

/**
 * Prefix INVGEN_BASE onto any root-absolute internal href that does not
 * already carry it. Lets article data files write friendly root paths
 * like `<a href="/net-30-vs-due-on-receipt/">` and have them resolve
 * correctly under Laragon's `/argo-books-website/...` mount point. On
 * production INVGEN_BASE is empty so this function is a no-op.
 *
 * Skipped: protocol-relative (`//cdn...`), absolute (`https://`),
 * fragment (`#x`), mailto, tel, and already-prefixed paths.
 */
function article_prefix_internal_links(string $html): string
{
    if (INVGEN_BASE === '') {
        return $html;
    }
    return preg_replace_callback(
        '/\bhref="(\/[^"\/][^"]*)"/i',
        function ($m) {
            $path = $m[1];
            if (strpos($path, INVGEN_BASE . '/') === 0 || $path === INVGEN_BASE) {
                return $m[0];
            }
            return 'href="' . INVGEN_BASE . $path . '"';
        },
        $html
    );
}

function article_render_404(): void
{
    invgen_render_404(
        'Article not found',
        '<p>The page you asked for does not exist. Try the <a href="' . INVGEN_BASE . '/free-invoice-generator/">free invoice generator</a>.</p>'
    );
}
