<?php
// profit-analyzer/lib/import/categorize.php
//
// WEBSITE-ONLY enrichment (not part of the desktop importer).
//
// The desktop importer produces expense rows with a description but no category
// (in Argo, an expense is only categorized via linked line-items -> product ->
// category, which a bulk spreadsheet upload rarely has). The Profit Analyzer's
// signature "follow your money" Sankey breaks spending down by category, so we add
// one Gemini pass that buckets each unique expense description into a fixed list.
// One call over distinct descriptions keeps the cost negligible.

require_once __DIR__ . '/gemini.php';

/** The fixed expense-category vocabulary the model must choose from. */
function pa_expense_categories(): array
{
    // Note: 'Cost of goods', 'Advertising', and 'Fees' are the three buckets the
    // money-flow Sankey names explicitly (analytics.php) — keep those spellings.
    return [
        'Cost of goods', 'Advertising', 'Fees', 'Shipping', 'Software & subscriptions',
        'Rent', 'Utilities', 'Vehicle & fuel', 'Supplies', 'Wages & contractors',
        'Insurance', 'Professional services', 'Travel & meals', 'Bank charges',
        'Taxes & licenses', 'Repairs & maintenance', 'Other',
    ];
}

/**
 * Assigns a category to each expense (in place) by asking Gemini to map each unique
 * description to one of pa_expense_categories(). Expenses that already carry a
 * category are left as-is. Falls back to a keyword heuristic if the AI call fails,
 * so the Sankey always has something to show.
 *
 * @param array $expenses  list of expense entities (importer-shape, with 'description')
 * @return array  the same list, each with a 'category' set
 */
function pa_categorize_expenses(array $expenses): array
{
    if (count($expenses) === 0) {
        return $expenses;
    }

    // Collect distinct descriptions still needing a category.
    $unique = [];
    foreach ($expenses as $e) {
        if (!empty($e['category'])) {
            continue;
        }
        $desc = trim((string)($e['description'] ?? ''));
        if ($desc !== '') {
            $unique[$desc] = true;
        }
    }
    $descriptions = array_keys($unique);

    $map = [];
    if (count($descriptions) > 0) {
        // Cap to keep the prompt bounded; the rest fall back to the heuristic.
        $forAi = array_slice($descriptions, 0, 200);
        $map = pa_categorize_with_ai($forAi);
    }

    $valid = array_flip(pa_expense_categories());
    foreach ($expenses as &$e) {
        if (!empty($e['category'])) {
            continue;
        }
        $desc = trim((string)($e['description'] ?? ''));
        $cat = $map[$desc] ?? null;
        if ($cat === null || !isset($valid[$cat])) {
            $cat = pa_categorize_heuristic($desc);
        }
        $e['category'] = $cat;
    }
    unset($e);

    return $expenses;
}

/** Ask Gemini to map each description to a category. Returns [description => category]. */
function pa_categorize_with_ai(array $descriptions): array
{
    $categories = pa_expense_categories();
    $system = "You categorize small-business expenses. Given a list of expense descriptions, assign each to EXACTLY ONE category from this fixed list:\n"
        . '- ' . implode("\n- ", $categories) . "\n\n"
        . "Respond with a JSON object mapping each input description (verbatim) to its category name. "
        . "Use only the category names from the list above. No markdown, JSON object only.";

    $lines = '';
    foreach ($descriptions as $d) {
        $lines .= '- ' . str_replace(["\n", "\r"], ' ', $d) . "\n";
    }
    $user = "Categorize these expense descriptions:\n" . $lines;

    $response = pa_gemini_chat($system, $user, 8000, 0.0);
    if ($response === null) {
        return [];
    }
    $doc = json_decode(pa_strip_markdown_json($response), true);
    return is_array($doc) ? $doc : [];
}

/** Keyword fallback so the Sankey still has categories if the AI call fails. */
function pa_categorize_heuristic(string $desc): string
{
    $d = strtolower($desc);
    $rules = [
        'Advertising' => ['ad', 'ads', 'advert', 'facebook', 'instagram', 'google ads', 'marketing', 'campaign'],
        'Fees' => ['stripe', 'paypal', 'processing fee', 'merchant fee', 'square fee', 'transaction fee'],
        'Shipping' => ['shipping', 'courier', 'postage', 'freight', 'delivery', 'fedex', 'ups', 'usps'],
        'Software & subscriptions' => ['software', 'subscription', 'saas', 'license', 'hosting', 'domain', 'app'],
        'Rent' => ['rent', 'lease'],
        'Utilities' => ['electric', 'water', 'gas bill', 'internet', 'phone', 'utility', 'hydro'],
        'Vehicle & fuel' => ['fuel', 'gas', 'mileage', 'vehicle', 'car', 'truck', 'parking'],
        'Supplies' => ['supplies', 'stationery', 'paper', 'printer', 'office'],
        'Wages & contractors' => ['wage', 'salary', 'payroll', 'contractor', 'freelance', 'labor', 'labour'],
        'Insurance' => ['insurance', 'premium'],
        'Professional services' => ['legal', 'lawyer', 'accountant', 'bookkeep', 'consult', 'attorney'],
        'Travel & meals' => ['travel', 'flight', 'hotel', 'meal', 'restaurant', 'lodging', 'airfare'],
        'Bank charges' => ['bank fee', 'bank charge', 'overdraft', 'wire fee', 'interest'],
        'Taxes & licenses' => ['tax', 'license', 'permit', 'registration'],
        'Repairs & maintenance' => ['repair', 'maintenance', 'fix', 'service'],
        'Cost of goods' => ['raw', 'material', 'inventory', 'stock', 'cogs', 'wholesale', 'parts', 'cotton', 'ceramic', 'wax', 'wick', 'blank'],
    ];
    foreach ($rules as $category => $keywords) {
        foreach ($keywords as $kw) {
            if (strpos($d, $kw) !== false) {
                return $category;
            }
        }
    }
    return 'Other';
}
