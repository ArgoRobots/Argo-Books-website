<?php
// shared/data/mileage-rates.php
//
// Statutory mileage / per-kilometre rates. Rates verified 2026-07-30. When a
// tax authority changes one, edit here only and bump 'verified'; the page
// prints that date and renders its rate table from this array.
//
// Two things most mileage calculators get wrong, both handled here:
//   1. The US rate changed mid-year in 2026 (72.5c to 76c on 1 July), so a
//      full-year claim needs both rates.
//   2. Canada and the UK are tiered: a lower rate applies after the first
//      5,000 km / 10,000 miles.
//
// Sources:
//   https://www.irs.gov/newsroom/irs-sets-2026-business-standard-mileage-rate-at-725-cents-per-mile-up-25-cents
//   https://www.journalofaccountancy.com/news/2026/jul/irs-raises-standard-mileage-rates-for-remainder-of-2026/
//   https://www.canada.ca/en/department-finance/news/2026/01/government-announces-the-2026-automobile-deduction-limits-and-expense-benefit-rates-for-businesses.html
//   https://www.gov.uk/expenses-and-benefits-business-travel-mileage/rules-for-tax
//   https://www.ato.gov.au/

return [
    'verified' => '2026-07-30',

    'regions' => [
        'US' => [
            'name' => 'United States',
            'authority' => 'IRS',
            'currency' => 'USD',
            'locale' => 'en-US',
            'unit' => 'mile',
            'unit_plural' => 'miles',
            'period_label' => 'Tax year 2026',
            // Split rather than tiered: which rate applies depends on WHEN the
            // trip happened, not how far you have driven.
            'split' => true,
            'periods' => [
                ['label' => 'Miles driven 1 Jan to 30 Jun 2026', 'rate' => 0.725],
                ['label' => 'Miles driven 1 Jul to 31 Dec 2026', 'rate' => 0.76],
            ],
            'note' => 'The IRS raised the rate mid-year, so a full-year claim uses two rates. Split your log at 30 June.',
        ],
        'CA' => [
            'name' => 'Canada (provinces)',
            'authority' => 'CRA',
            'currency' => 'CAD',
            'locale' => 'en-CA',
            'unit' => 'kilometre',
            'unit_plural' => 'kilometres',
            'period_label' => '2026',
            'split' => false,
            'tiers' => [
                ['upTo' => 5000, 'rate' => 0.73],
                ['upTo' => null, 'rate' => 0.67],
            ],
            'note' => 'The rate drops after the first 5,000 km in the year. Territories are higher: 77 cents then 71 cents.',
        ],
        'CA-TERR' => [
            'name' => 'Canada (territories)',
            'authority' => 'CRA',
            'currency' => 'CAD',
            'locale' => 'en-CA',
            'unit' => 'kilometre',
            'unit_plural' => 'kilometres',
            'period_label' => '2026',
            'split' => false,
            'tiers' => [
                ['upTo' => 5000, 'rate' => 0.77],
                ['upTo' => null, 'rate' => 0.71],
            ],
            'note' => 'Yukon, Northwest Territories, and Nunavut carry a higher rate than the provinces.',
        ],
        'GB' => [
            'name' => 'United Kingdom',
            'authority' => 'HMRC',
            'currency' => 'GBP',
            'locale' => 'en-GB',
            'unit' => 'mile',
            'unit_plural' => 'miles',
            'period_label' => 'From 6 April 2026',
            'split' => false,
            'tiers' => [
                ['upTo' => 10000, 'rate' => 0.55],
                ['upTo' => null, 'rate' => 0.25],
            ],
            'note' => 'The first-10,000-mile rate rose from 45p to 55p on 6 April 2026, its first change since 2011. Use 45p for travel before that date.',
        ],
        'AU' => [
            'name' => 'Australia',
            'authority' => 'ATO',
            'currency' => 'AUD',
            'locale' => 'en-AU',
            'unit' => 'kilometre',
            'unit_plural' => 'kilometres',
            'period_label' => '2025-26 income year',
            'split' => false,
            'tiers' => [
                ['upTo' => 5000, 'rate' => 0.88],
                ['upTo' => null, 'rate' => 0.0],
            ],
            'cap' => 5000,
            'note' => 'The cents-per-kilometre method is capped at 5,000 km per car per year. Beyond that you must use the logbook method instead. The rate rises to 91 cents for 2026-27.',
        ],
    ],
];
