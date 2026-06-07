// self-employed-tax-calculator/scripts/calc.js
//
// Pure tax-estimate functions. No DOM, no globals: take net profit (and a
// province for Canada) and return a structured breakdown. All rates live in
// data/tax-rates-2026.js. Kept side-effect-free so calc.test.js can verify the
// numbers against hand-worked examples.
//
// See the data file's header for the simplifying assumptions (single/basic
// filer, standard deduction / basic personal amount only, estimate errs high).

import { US, CANADA_FEDERAL, CANADA_PENSION, PROVINCES } from '../data/tax-rates-2026.js';

// Progressive tax over marginal brackets. Each bracket taxes the income slice
// between the previous bracket's `upTo` and its own `upTo` at `rate`.
export function bracketTax(income, brackets) {
  let tax = 0;
  let lower = 0;
  for (const b of brackets) {
    if (income <= lower) break;
    const slice = Math.min(income, b.upTo) - lower;
    if (slice > 0) tax += slice * b.rate;
    lower = b.upTo;
  }
  return tax;
}

// --- United States (federal only) ---
export function computeUS(profit) {
  const netProfit = Math.max(0, Number(profit) || 0);

  // Self-employment tax: 15.3% on 92.35% of net profit, Social Security
  // portion capped at the wage base, Medicare uncapped.
  const seBase = netProfit * US.se.netFactor;
  let selfEmploymentTax = 0;
  if (seBase >= US.se.minNetEarnings) {
    const ss = Math.min(seBase, US.se.socialSecurityWageBase) * US.se.socialSecurityRate;
    const medicare = seBase * US.se.medicareRate;
    selfEmploymentTax = ss + medicare;
  }

  // Income tax on profit minus half the SE tax minus the standard deduction.
  const taxableIncome = Math.max(0, netProfit - selfEmploymentTax / 2 - US.standardDeduction);
  const incomeTax = bracketTax(taxableIncome, US.brackets);

  const total = selfEmploymentTax + incomeTax;
  return {
    country: 'US',
    currency: 'USD',
    netProfit,
    contributionLabel: 'Self-employment tax',
    contribution: selfEmploymentTax,
    incomeTax,
    taxableIncome,
    total,
    effectiveRate: netProfit > 0 ? total / netProfit : 0,
    quarterly: total / 4,
  };
}

// Federal basic personal amount, phasing from max down to min across the
// top-bracket income range.
function federalBpa(income) {
  const f = CANADA_FEDERAL;
  if (income <= f.bpaPhaseStart) return f.bpaMax;
  if (income >= f.bpaPhaseEnd) return f.bpaMin;
  const frac = (income - f.bpaPhaseStart) / (f.bpaPhaseEnd - f.bpaPhaseStart);
  return f.bpaMax - (f.bpaMax - f.bpaMin) * frac;
}

// --- Canada (federal + provincial + CPP/QPP) ---
export function computeCanada(profit, provinceCode) {
  const netProfit = Math.max(0, Number(profit) || 0);
  const prov = PROVINCES[provinceCode] || PROVINCES.ON;
  const cp = CANADA_PENSION;
  const rates = prov.usesQpp ? cp.qpp : cp.cpp;

  // CPP / QPP. Self-employed pay both the employee and employer portions.
  const baseEarnings = Math.max(0, Math.min(netProfit, cp.ympe) - cp.basicExemption);
  const secondEarnings = Math.max(0, Math.min(netProfit, cp.yampe) - cp.ympe);
  const contribution = baseEarnings * rates.baseSelfRate + secondEarnings * rates.secondSelfRate;

  // Federal income tax: bracket tax minus the basic-personal-amount credit
  // (given at the lowest rate). Quebec residents get the 16.5% abatement.
  let federalTax = bracketTax(netProfit, CANADA_FEDERAL.brackets)
    - federalBpa(netProfit) * CANADA_FEDERAL.lowestRate;
  federalTax = Math.max(0, federalTax);
  if (prov.usesQpp) federalTax *= (1 - CANADA_FEDERAL.quebecAbatement);

  // Provincial income tax: bracket tax minus the provincial BPA credit, then
  // the Ontario surtax (the only province with one) on the result.
  let provincialTax = bracketTax(netProfit, prov.brackets) - prov.bpa * prov.lowestRate;
  provincialTax = Math.max(0, provincialTax);
  if (prov.surtax) {
    const s = prov.surtax;
    provincialTax += Math.max(0, provincialTax - s.t1) * s.r1
      + Math.max(0, provincialTax - s.t2) * s.r2;
  }

  const incomeTax = federalTax + provincialTax;
  const total = contribution + incomeTax;
  return {
    country: 'CA',
    currency: 'CAD',
    netProfit,
    contributionLabel: prov.usesQpp ? 'QPP contributions' : 'CPP contributions',
    contribution,
    federalTax,
    provincialTax,
    incomeTax,
    total,
    effectiveRate: netProfit > 0 ? total / netProfit : 0,
    quarterly: total / 4,
  };
}

// Dispatch helper used by the UI.
export function computeTax({ country, profit, province }) {
  return country === 'CA' ? computeCanada(profit, province) : computeUS(profit);
}
