// payroll/calculator.js
//
// Recomputes the provider cost table when the headcount changes. The table is
// already rendered server-side at the default headcount, so with JavaScript off
// the page still makes its argument, just at one fixed size.
//
// Provider figures come from config/competitors.json through the data-calc
// attribute, so the arithmetic here and the arithmetic in PHP read the same
// numbers and a price edit lands in both.

(function () {
    'use strict';

    var root = document.querySelector('.pr-calc');
    if (!root) return;

    var data;
    try {
        data = JSON.parse(root.getAttribute('data-calc'));
    } catch (e) {
        return; // Leave the server-rendered table alone rather than blanking it.
    }
    if (!data || !Array.isArray(data.providers)) return;

    var number = document.getElementById('pr-headcount');
    var range = document.getElementById('pr-headcount-range');
    var savingCount = root.querySelector('[data-saving-count]');
    var savingNoun = root.querySelector('[data-saving-noun]');
    var savingAmount = root.querySelector('[data-saving-amount]');
    var rows = root.querySelectorAll('tbody tr');

    var MIN = 1;
    var MAX = 50;

    // Whole dollars: the cents on a per-employee fee are noise next to the
    // point the table is making, and a column of round numbers is readable.
    function money(value) {
        return '$' + Math.round(value).toLocaleString('en-CA');
    }

    function clamp(value) {
        if (isNaN(value)) return MIN;
        return Math.min(MAX, Math.max(MIN, value));
    }

    function render(count) {
        var cheapest = null;

        rows.forEach(function (row) {
            var monthly;

            if (row.hasAttribute('data-argo')) {
                monthly = data.argo;
            } else {
                var name = row.getAttribute('data-provider');
                var provider = data.providers.filter(function (p) { return p.name === name; })[0];
                if (!provider) return;
                monthly = provider.base + provider.per * count;
                if (cheapest === null || monthly < cheapest) cheapest = monthly;
            }

            var monthlyCell = row.querySelector('[data-cell="monthly"]');
            var yearlyCell = row.querySelector('[data-cell="yearly"]');
            if (monthlyCell) monthlyCell.textContent = money(monthly);
            if (yearlyCell) yearlyCell.textContent = money(monthly * 12);
        });

        if (savingCount) savingCount.textContent = String(count);
        // The minimum is 1, so "1 people" is reachable and the noun has to move.
        if (savingNoun) savingNoun.textContent = count === 1 ? 'person' : 'people';
        if (savingAmount && cheapest !== null) {
            savingAmount.textContent = money((cheapest - data.argo) * 12);
        }
    }

    function set(value, syncNumber, syncRange) {
        var count = clamp(parseInt(value, 10));
        if (syncNumber && number) number.value = count;
        if (syncRange && range) range.value = count;
        render(count);
    }

    if (number) {
        number.addEventListener('input', function () { set(number.value, false, true); });
        // An empty or out-of-range box is only corrected on blur, so typing
        // "12" does not get rewritten to "1" after the first keystroke.
        number.addEventListener('blur', function () { set(number.value, true, true); });
    }

    if (range) {
        range.addEventListener('input', function () { set(range.value, true, false); });
    }

    root.querySelectorAll('.pr-step').forEach(function (button) {
        button.addEventListener('click', function () {
            var step = parseInt(button.getAttribute('data-step'), 10) || 0;
            var current = parseInt(number ? number.value : range.value, 10);
            set((isNaN(current) ? MIN : current) + step, true, true);
        });
    });
})();
