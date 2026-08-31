<?php
// compare/mockups/honeybook-alternatives.php
//
// Decorative price chart for the HoneyBook comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// price variables that compare/data/honeybook-alternatives.php declares; both that
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
                                <text x="40" y="80" font-size="14" fill="#0f172a">One flat Argo price vs HoneyBook's rising plans.</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="16" height="10" rx="2" fill="#3f63e8"/>
                                <text x="62" y="107" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="98" width="16" height="10" rx="2" fill="#ef4444"/>
                                <text x="188" y="107" font-size="13" fill="#0f172a">HoneyBook</text>

                                <!-- Bars: width scaled so $149 (widest) = 387px, i.e. ~2.6px per $1.
                                     Label column runs to x=200 so no label truncates. -->
                                <!-- Argo Free $0 -->
                                <text x="40" y="152" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="200" y="140" width="2" height="18" rx="3" fill="#3f63e8"/>
                                <text x="212" y="153" font-size="13" font-weight="600" fill="#0f172a">$0</text>

                                <!-- Argo Books Premium $15 -->
                                <text x="40" y="196" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="200" y="184" width="39" height="18" rx="3" fill="#3f63e8"/>
                                <text x="249" y="197" font-size="13" font-weight="600" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- HoneyBook Starter $40 -->
                                <text x="40" y="256" font-size="13" font-weight="600" fill="#0f172a">HoneyBook Starter</text>
                                <rect x="200" y="244" width="104" height="18" rx="3" fill="#ef4444"/>
                                <text x="314" y="257" font-size="13" font-weight="600" fill="#0f172a">$<?= $hb_starter ?></text>

                                <!-- HoneyBook Essentials $67 -->
                                <text x="40" y="300" font-size="13" font-weight="600" fill="#0f172a">HoneyBook Essentials</text>
                                <rect x="200" y="288" width="174" height="18" rx="3" fill="#ef4444"/>
                                <text x="384" y="301" font-size="13" font-weight="600" fill="#0f172a">$<?= $hb_essentials ?></text>

                                <!-- HoneyBook Premium $149 -->
                                <text x="40" y="344" font-size="13" font-weight="600" fill="#0f172a">HoneyBook Premium</text>
                                <rect x="200" y="332" width="387" height="18" rx="3" fill="#ef4444"/>
                                <text x="597" y="345" font-size="13" font-weight="600" fill="#0f172a">$<?= $hb_premium ?></text>

                                <!-- Baseline -->
                                <line x1="200" y1="372" x2="200" y2="128" stroke="#e2e8f0" stroke-width="1"/>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
