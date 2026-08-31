<?php
// compare/mockups/sage-50-alternatives.php
//
// Decorative price chart for the Sage 50 comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// price variables that compare/data/sage-50-alternatives.php declares; both that
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
                                <text x="40" y="80" font-size="14" fill="#0f172a">One flat Argo price vs Sage 50's plans (billed annually).</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="16" height="10" rx="2" fill="#3f63e8"/>
                                <text x="62" y="107" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="98" width="16" height="10" rx="2" fill="#ef4444"/>
                                <text x="188" y="107" font-size="13" fill="#0f172a">Sage 50</text>

                                <!-- Bars: width scaled so $<?= $sage_quantum ?> (widest) = 430px, i.e. ~0.915px per $1 -->
                                <!-- Argo Free $0 -->
                                <text x="40" y="152" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="205" y="140" width="2" height="18" rx="3" fill="#3f63e8"/>
                                <text x="217" y="153" font-size="13" font-weight="600" fill="#0f172a">$0</text>

                                <!-- Argo Books Premium $<?= $argo_monthly ?> -->
                                <text x="40" y="192" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="205" y="180" width="14" height="18" rx="3" fill="#3f63e8"/>
                                <text x="229" y="193" font-size="13" font-weight="600" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- Sage Pro $<?= $sage_pro ?> -->
                                <text x="40" y="240" font-size="13" font-weight="600" fill="#0f172a">Sage Pro</text>
                                <rect x="205" y="228" width="62" height="18" rx="3" fill="#ef4444"/>
                                <text x="277" y="241" font-size="13" font-weight="600" fill="#0f172a">$<?= $sage_pro ?></text>

                                <!-- Sage Premium $<?= $sage_premium ?> -->
                                <text x="40" y="284" font-size="13" font-weight="600" fill="#0f172a">Sage Premium</text>
                                <rect x="205" y="272" width="93" height="18" rx="3" fill="#ef4444"/>
                                <text x="308" y="285" font-size="13" font-weight="600" fill="#0f172a">$<?= $sage_premium ?></text>

                                <!-- Sage Quantum $<?= $sage_quantum ?> -->
                                <text x="40" y="328" font-size="13" font-weight="600" fill="#0f172a">Sage Quantum</text>
                                <rect x="205" y="316" width="430" height="18" rx="3" fill="#ef4444"/>
                                <text x="597" y="329" font-size="13" font-weight="600" fill="#ffffff" text-anchor="end">$<?= $sage_quantum ?></text>

                                <!-- Baseline -->
                                <line x1="205" y1="356" x2="205" y2="128" stroke="#e2e8f0" stroke-width="1"/>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
