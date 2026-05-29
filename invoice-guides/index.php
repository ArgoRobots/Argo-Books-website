<?php
// invoice-guides/index.php
//
// Editorial hub for the Phase D informational articles. Served at
// argorobots.com/invoice-guides/ via Apache DirectoryIndex (no rewrite
// needed). Loads each article's metadata from articles/data/*.php and
// renders a curated reading order grouped into four sections.
//
// Heading structure (strict, for SEO):
//   <h1>     Invoice guides for small businesses
//   no H2s on the index entries themselves (each list item is a link, not
//   a section). Group dividers carry small-caps labels but no headings.

require_once __DIR__ . '/../invoice-generator/_base.php';

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../statistics.php';
    track_page_view('invgen_articles_hub');
}

// Curated reading order: from the basics, through terms and tax, to
// chasing payment, to deciding which tools to use long term. The PHP
// hardcodes the order so it does not depend on filesystem glob order.
// Each entry's `group` label is set positionally in the parallel array
// below, so it stays in sync with the order.
$order = [
    'how-to-invoice-clients',
    'what-to-include-on-an-invoice',
    'invoice-numbering-best-practices',
    'net-30-vs-due-on-receipt',
    'late-fees-when-and-how-to-charge',
    'tax-on-invoices-country-guide',
    'how-to-follow-up-on-unpaid-invoices',
    'what-to-do-when-a-client-does-not-pay',
    'recurring-invoices-when-to-use-them',
    'free-vs-paid-invoicing-tools',
];

$groups = [
    'Start here',
    'Start here',
    'Start here',
    'Terms and tax',
    'Terms and tax',
    'Terms and tax',
    'Getting paid',
    'Getting paid',
    'Going further',
    'Going further',
];

$articles = [];
foreach ($order as $i => $slug) {
    $file = __DIR__ . '/../articles/data/' . $slug . '.php';
    if (!is_file($file)) {
        continue;
    }
    $d = require $file;
    $articles[] = [
        'slug' => $slug,
        'h1' => $d['h1'] ?? $slug,
        'meta_description' => $d['meta_description'] ?? '',
        'reading_time_min' => $d['reading_time_min'] ?? null,
        'schema_type' => $d['schema_type'] ?? 'Article',
        'group' => $groups[$i] ?? 'Guides',
    ];
}

$page_title = 'Invoice Guides for Small Businesses | Argo Books';
$page_description = 'Ten plain-language guides on invoicing, payment terms, late fees, taxes, and getting paid. Written for small businesses doing their own books.';
$canonical_url = 'https://argorobots.com/invoice-guides/';

$item_list = [];
foreach ($articles as $i => $a) {
    $item_list[] = [
        '@type' => 'ListItem',
        'position' => $i + 1,
        'url' => 'https://argorobots.com/' . $a['slug'] . '/',
        'name' => $a['h1'],
    ];
}

$page_schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'CollectionPage',
            '@id' => $canonical_url,
            'name' => 'Invoice Guides for Small Businesses',
            'url' => $canonical_url,
            'description' => $page_description,
        ],
        [
            '@type' => 'ItemList',
            'itemListElement' => $item_list,
            'numberOfItems' => count($item_list),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

$breadcrumb_schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://argorobots.com/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Invoice Guides', 'item' => $canonical_url],
    ],
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

// Fraunces serif is loaded for this page only. CSP already permits
// fonts.googleapis.com (style-src) and fonts.gstatic.com (font-src) per
// the project's .htaccess.
$extra_head = '<link rel="preconnect" href="https://fonts.googleapis.com">'
    . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
    . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600&display=swap">'
    . '<link rel="stylesheet" href="' . INVGEN_BASE . '/invoice-guides/styles/hub.css">';

$invgen_ref = 'invgen-articles-hub';

ob_start();
?>
<main class="guides-hub">

  <header class="guides-hub-head">
    <p class="guides-hub-eyebrow">Argo Books <span aria-hidden="true">&middot;</span> Guides</p>
    <h1 class="guides-hub-title">Invoice <em>guides</em> for small businesses.</h1>
    <p class="guides-hub-lede">Ten plain-language guides on getting an invoice out the door, picking payment terms, charging late fees, handling tax, and following up when a client goes quiet. Written for people who do their own books.</p>
  </header>

  <ol class="guides-hub-list" role="list">
    <?php
      $prev_group = null;
      foreach ($articles as $i => $a):
        $position = $i + 1;
        $show_group = $a['group'] !== $prev_group;
        $prev_group = $a['group'];
    ?>
      <?php if ($show_group): ?>
        <li class="guides-hub-group" role="presentation">
          <span class="guides-hub-group-label"><?= htmlspecialchars($a['group']) ?></span>
          <span class="guides-hub-group-rule" aria-hidden="true"></span>
        </li>
      <?php endif; ?>

      <li class="guides-hub-entry">
        <a class="guides-hub-link"
           href="<?= INVGEN_BASE ?>/<?= htmlspecialchars($a['slug']) ?>/"
           style="--entry-delay: <?= ($i * 45) ?>ms;">
          <span class="guides-hub-num" aria-hidden="true"><?= sprintf('%02d', $position) ?></span>
          <span class="guides-hub-body">
            <span class="guides-hub-headline"><?= htmlspecialchars($a['h1']) ?></span>
            <span class="guides-hub-desc"><?= htmlspecialchars($a['meta_description']) ?></span>
            <span class="guides-hub-meta">
              <?php if (!empty($a['reading_time_min'])): ?>
                <span class="guides-hub-chip"><?= (int)$a['reading_time_min'] ?> min read</span>
              <?php endif; ?>
              <?php if ($a['schema_type'] === 'HowTo'): ?>
                <span class="guides-hub-chip guides-hub-chip--howto">Step by step</span>
              <?php endif; ?>
            </span>
          </span>
          <span class="guides-hub-arrow" aria-hidden="true">&rarr;</span>
        </a>
      </li>
    <?php endforeach; ?>
  </ol>

  <aside class="guides-hub-banner" role="complementary">
    <div class="guides-hub-banner-copy">
      <p class="guides-hub-banner-eyebrow">From the guides into your books</p>
      <p class="guides-hub-banner-text">If you want to handle payments, refunds, and track everything, Argo Books is the accounting app these guides are based on.</p>
    </div>
    <a class="guides-hub-banner-link"
       data-pitch-placement="guides-hub-footer"
       href="https://argorobots.com/?source=<?= htmlspecialchars($invgen_ref) ?>&amp;utm_source=invoice-generator&amp;utm_medium=hub&amp;utm_campaign=phase1&amp;placement=footer">
      Visit Argo Books <span aria-hidden="true">&rarr;</span>
    </a>
  </aside>

</main>
<?php
$body_content = ob_get_clean();

include __DIR__ . '/../invoice-generator/layout.php';
