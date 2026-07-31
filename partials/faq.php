<?php
// partials/faq.php
//
// The site's one FAQ accordion. Before this, the same markup, the same toggle
// script, and the same FAQPage JSON-LD were hand-repeated across 26 pages, and
// on most of them the visible answers and the schema answers were separate
// copies that could drift apart without anyone noticing.
//
// Callers own the surrounding <section> and heading, because the wrapper class
// varies by page family (.faq, .ai-faq, .faq-section, .craft-faqs, .etsy-faqs).
// This file owns the grid, the items, and the schema.
//
// Usage:
//   require_once __DIR__ . '/../partials/faq.php';
//   $faqs = [
//     ['q' => 'Question?', 'a' => 'Plain answer, escaped for you.'],
//     ['q' => 'Question?', 'a_html' => '<p>Pre-built</p><p>multi-paragraph HTML.</p>'],
//   ];
//   echo argo_faq_grid($faqs);
//
// And in <head>, from the same array so the two can never disagree, echo
// argo_faq_schema($faqs) inside a script tag of type application/ld+json.
// (Written out in prose deliberately: a literal PHP close tag in a comment
// ends PHP mode and silently turns the rest of this file into HTML.)
//
// Entries missing a question, or missing both answer forms, are skipped.

require_once __DIR__ . '/../shared/_base.php';

/**
 * Render the .faq-grid. Emits the shared accordion script once per request,
 * so pages using the partial need no script wiring of their own.
 */
function argo_faq_grid(array $faqs): string
{
    $html = '';
    foreach ($faqs as $faq) {
        $question = argo_faq_question_html($faq);
        $answer = argo_faq_answer_html($faq);
        if ($question === '' || $answer === '') {
            continue;
        }

        $html .= '<div class="faq-item">'
            . '<button type="button" class="faq-question" aria-expanded="false">'
            . '<h3>' . $question . '</h3>'
            . '<span class="faq-icon" aria-hidden="true">' . argo_faq_icon() . '</span>'
            . '</button>'
            . '<div class="faq-answer"><div class="faq-answer-content">' . $answer . '</div></div>'
            . '</div>';
    }

    if ($html === '') {
        return '';
    }

    return '<div class="faq-grid">' . $html . '</div>' . argo_faq_script();
}

/**
 * FAQPage JSON-LD built from the same array as the visible accordion.
 * Answers given as HTML are flattened to text, which is what schema.org wants.
 */
function argo_faq_schema(array $faqs): string
{
    return json_encode(
        ['@context' => 'https://schema.org'] + argo_faq_schema_node($faqs),
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
}

/**
 * The bare FAQPage node, for pages that embed it in a schema @graph alongside
 * other types rather than emitting it as its own document.
 */
function argo_faq_schema_node(array $faqs): array
{
    $items = [];
    foreach ($faqs as $faq) {
        $question = argo_faq_question_html($faq);
        $answer = argo_faq_answer_html($faq);
        if ($question === '' || $answer === '') {
            continue;
        }
        $items[] = [
            '@type' => 'Question',
            'name' => argo_faq_to_text($question),
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => argo_faq_to_text($answer)],
        ];
    }

    return ['@type' => 'FAQPage', 'mainEntity' => $items];
}

/** 'q_html' is trusted markup from the page; 'q' is plain text we escape. */
function argo_faq_question_html(array $faq): string
{
    if (isset($faq['q_html']) && trim((string)$faq['q_html']) !== '') {
        return trim((string)$faq['q_html']);
    }
    $plain = trim((string)($faq['q'] ?? ''));
    return $plain === '' ? '' : htmlspecialchars($plain);
}

/** Flatten answer or question HTML to the plain text schema.org wants. */
function argo_faq_to_text(string $html): string
{
    // Paragraph and line breaks become spaces so sentences do not run together.
    $spaced = str_replace(['</p>', '<br>', '<br/>', '<br />', '</li>'], ' ', $html);
    $text = html_entity_decode(strip_tags($spaced), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(preg_replace('/\s+/u', ' ', $text));
}

/** 'a_html' is trusted markup from the page; 'a' is plain text we escape. */
function argo_faq_answer_html(array $faq): string
{
    if (isset($faq['a_html']) && trim((string)$faq['a_html']) !== '') {
        return trim((string)$faq['a_html']);
    }
    $plain = trim((string)($faq['a'] ?? ''));
    return $plain === '' ? '' : '<p>' . htmlspecialchars($plain) . '</p>';
}

/** Chevron. Uses the shared icon set when the page has loaded it. */
function argo_faq_icon(): string
{
    if (function_exists('svg_icon')) {
        return svg_icon('chevron-down');
    }
    return '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"'
        . ' stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'
        . '<polyline points="6,9 12,15 18,9"/></svg>';
}

/** The accordion script, emitted at most once per request. */
function argo_faq_script(): string
{
    static $emitted = false;
    if ($emitted) {
        return '';
    }
    $emitted = true;
    return '<script src="' . INVGEN_BASE . '/resources/scripts/faq-accordion.js" defer></script>';
}
