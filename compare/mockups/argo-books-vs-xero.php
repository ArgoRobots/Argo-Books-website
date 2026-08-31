<?php
// compare/mockups/argo-books-vs-xero.php
//
// Decorative price chart for the Xero comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// price variables that compare/data/argo-books-vs-xero.php declares; both that
// file and this one are included at global scope by the template.
?>
<!-- Decorative price-comparison mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">One flat Argo price vs Xero's tiers.</text>

                                <!-- Bars: width proportional to price, $80 = widest.
                                     Track starts at x=200, max width 380 (=$80). -->
                                <!-- Argo Free $0 -->
                                <text x="40" y="132" font-size="14" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="200" y="120" width="4" height="18" rx="4" fill="#3f63e8"/>
                                <text x="214" y="134" font-size="14" font-weight="700" fill="#3f63e8">$0</text>

                                <!-- Argo Books Premium $15 -->
                                <text x="40" y="182" font-size="14" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="200" y="170" width="71" height="18" rx="4" fill="#3f63e8"/>
                                <text x="281" y="184" font-size="14" font-weight="700" fill="#3f63e8">$<?= $argo_monthly ?></text>

                                <!-- Xero Starter $25 -->
                                <text x="40" y="232" font-size="14" font-weight="600" fill="#0f172a">Xero Starter</text>
                                <rect x="200" y="220" width="119" height="18" rx="4" fill="#ef4444"/>
                                <text x="329" y="234" font-size="14" font-weight="700" fill="#ef4444">$<?= $xero_starter ?></text>

                                <!-- Xero Standard $60 -->
                                <text x="40" y="282" font-size="14" font-weight="600" fill="#0f172a">Xero Standard</text>
                                <rect x="200" y="270" width="285" height="18" rx="4" fill="#ef4444"/>
                                <text x="495" y="284" font-size="14" font-weight="700" fill="#ef4444">$<?= $xero_standard ?></text>

                                <!-- Xero Premium $80 -->
                                <text x="40" y="332" font-size="14" font-weight="600" fill="#0f172a">Xero Premium</text>
                                <rect x="200" y="320" width="380" height="18" rx="4" fill="#ef4444"/>
                                <text x="590" y="334" font-size="14" font-weight="700" fill="#ef4444" text-anchor="end">$<?= $xero_premium ?></text>

                                <!-- Baseline -->
                                <line x1="200" y1="366" x2="580" y2="366" stroke="#f1f5f9" stroke-width="1"/>

                                <!-- Legend -->
                                <rect x="40" y="398" width="14" height="14" rx="3" fill="#3f63e8"/>
                                <text x="62" y="410" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="398" width="14" height="14" rx="3" fill="#ef4444"/>
                                <text x="188" y="410" font-size="13" fill="#0f172a">Xero</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
