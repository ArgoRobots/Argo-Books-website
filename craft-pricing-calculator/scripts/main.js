// craft-pricing-calculator/scripts/main.js
// Wires the pricing form to the pure calc and renders live. Vanilla ES module.
//
// Note: unlike the invoice/tax tools, this calculator is currency-agnostic, so
// it shows a plain "$" with no ISO code (reusing formatMoney would append
// "USD", implying a US-only tool, which this isn't).

import { computeCraftPrice } from './calc.js';

const $ = (sel) => document.querySelector(sel);
const el = {
  material: $('[data-cc="material"]'),
  labor: $('[data-cc="labor"]'),
  markup: $('[data-cc="markup"]'),
  price: $('[data-cc="price"]'),
  cost: $('[data-cc="cost"]'),
  profit: $('[data-cc="profit"]'),
  margin: $('[data-cc="margin"]'),
};

const moneyFmt = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' });
const money = (n) => moneyFmt.format(Number.isFinite(n) ? n : 0);
const pct = (rate) => `${Math.round(rate * 100)}%`;

function render() {
  const r = computeCraftPrice({
    materialCost: el.material.value,
    laborCost: el.labor.value,
    markupPercent: el.markup.value,
  });
  el.price.textContent = money(r.sellingPrice);
  el.cost.textContent = money(r.totalCost);
  el.profit.textContent = money(r.profit);
  el.margin.textContent = pct(r.margin);
}

function wirePresets() {
  const buttons = document.querySelectorAll('[data-cc-preset]');
  buttons.forEach((btn) => {
    btn.addEventListener('click', () => {
      el.markup.value = btn.getAttribute('data-cc-preset');
      buttons.forEach((b) => b.classList.toggle('is-active', b === btn));
      render();
    });
  });
  // Typing a custom markup clears the active preset highlight.
  if (el.markup) {
    el.markup.addEventListener('input', () => {
      buttons.forEach((b) => {
        if (b.getAttribute('data-cc-preset') !== el.markup.value) b.classList.remove('is-active');
      });
    });
  }
}

// Collapsible FAQ accordion, matching the invoice-template pages. Reuses the
// shared .faq-item / .faq-question / .faq-answer styling from tool.css.
function wireFaq() {
  const items = document.querySelectorAll('.craft-faqs .faq-item');
  items.forEach((item) => {
    const question = item.querySelector('.faq-question');
    if (!question) return;
    question.addEventListener('click', () => {
      const wasActive = item.classList.contains('active');
      items.forEach((other) => {
        other.classList.remove('active');
        const btn = other.querySelector('.faq-question');
        if (btn) btn.setAttribute('aria-expanded', 'false');
      });
      if (!wasActive) {
        item.classList.add('active');
        question.setAttribute('aria-expanded', 'true');
      }
    });
  });
}

function init() {
  wireFaq();
  if (!el.material) return;
  const form = el.material.closest('form') || document;
  form.addEventListener('input', render);
  wirePresets();
  render();
}

init();
