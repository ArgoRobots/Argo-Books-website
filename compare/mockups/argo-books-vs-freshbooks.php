<?php
// compare/mockups/argo-books-vs-freshbooks.php
//
// Decorative price chart for the FreshBooks comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// price variables that compare/data/argo-books-vs-freshbooks.php declares; both that
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
                                <text x="40" y="80" font-size="14" fill="#0f172a">One flat Argo price vs FreshBooks' rising plans.</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="16" height="10" rx="2" fill="#3f63e8"/>
                                <text x="62" y="107" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="98" width="16" height="10" rx="2" fill="#ef4444"/>
                                <text x="188" y="107" font-size="13" fill="#0f172a">FreshBooks</text>

                                <!-- Bars: width scaled so $72 (widest) = 430px, i.e. ~5.97px per $1 -->
                                <!-- Argo Free $0 -->
                                <text x="40" y="152" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="175" y="140" width="2" height="18" rx="3" fill="#3f63e8"/>
                                <text x="187" y="153" font-size="13" font-weight="600" fill="#0f172a">$0</text>

                                <!-- Argo Books Premium $15 -->
                                <text x="40" y="196" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="175" y="184" width="90" height="18" rx="3" fill="#3f63e8"/>
                                <text x="275" y="197" font-size="13" font-weight="600" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- FreshBooks Lite $26 -->
                                <text x="40" y="256" font-size="13" font-weight="600" fill="#0f172a">FreshBooks Lite</text>
                                <rect x="175" y="244" width="155" height="18" rx="3" fill="#ef4444"/>
                                <text x="340" y="257" font-size="13" font-weight="600" fill="#0f172a">$<?= $fb_lite ?></text>

                                <!-- FreshBooks Plus $42 -->
                                <text x="40" y="300" font-size="13" font-weight="600" fill="#0f172a">FreshBooks Plus</text>
                                <rect x="175" y="288" width="251" height="18" rx="3" fill="#ef4444"/>
                                <text x="436" y="301" font-size="13" font-weight="600" fill="#0f172a">$<?= $fb_plus ?></text>

                                <!-- FreshBooks Premium $72 -->
                                <text x="40" y="344" font-size="13" font-weight="600" fill="#0f172a">FreshBooks Premium</text>
                                <rect x="175" y="332" width="430" height="18" rx="3" fill="#ef4444"/>
                                <text x="615" y="345" font-size="13" font-weight="600" fill="#0f172a">$<?= $fb_premium ?></text>

                                <!-- Baseline -->
                                <line x1="175" y1="372" x2="175" y2="128" stroke="#e2e8f0" stroke-width="1"/>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
