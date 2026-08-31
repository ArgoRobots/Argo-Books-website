<?php
// compare/mockups/argo-books-vs-wave.php
//
// Decorative price chart for the Wave comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// price variables that compare/data/argo-books-vs-wave.php declares; both that
// file and this one are included at global scope by the template.
?>
<!-- Decorative cost comparison. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">Both free to start. Argo does more for less on paid.</text>

                                <!-- Argo Free -->
                                <text x="40" y="130" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="175" y="118" width="4" height="20" rx="2" fill="#cbd5e1"/>
                                <text x="188" y="133" font-size="13" font-weight="700" fill="#0f172a">$0</text>

                                <!-- Wave Starter -->
                                <text x="40" y="174" font-size="13" font-weight="600" fill="#0f172a">Wave Starter</text>
                                <rect x="175" y="162" width="4" height="20" rx="2" fill="#cbd5e1"/>
                                <text x="188" y="177" font-size="13" font-weight="700" fill="#0f172a">$0</text>

                                <!-- Argo Books Premium -->
                                <text x="40" y="218" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="175" y="206" width="<?= $argo_monthly * 13 ?>" height="20" rx="4" fill="#3f63e8"/>
                                <text x="<?= 175 + $argo_monthly * 13 + 10 ?>" y="221" font-size="13" font-weight="700" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- Wave Pro -->
                                <text x="40" y="262" font-size="13" font-weight="600" fill="#0f172a">Wave Pro</text>
                                <rect x="175" y="250" width="<?= $wave_pro * 13 ?>" height="20" rx="4" fill="#ef4444"/>
                                <text x="<?= 175 + $wave_pro * 13 + 10 ?>" y="265" font-size="13" font-weight="700" fill="#0f172a">$<?= $wave_pro ?></text>

                                <!-- Divider -->
                                <line x1="40" y1="300" x2="600" y2="300" stroke="#f1f5f9" stroke-width="1"/>

                                <!-- Receipt scanning row -->
                                <text x="40" y="338" font-size="14" font-weight="600" fill="#0f172a">Receipt scanning</text>
                                <rect x="40" y="352" width="132" height="28" rx="7" fill="#dcfce7"/>
                                <text x="106" y="371" text-anchor="middle" font-size="13" font-weight="600" fill="#15803d">Free on Argo</text>
                                <rect x="184" y="352" width="180" height="28" rx="7" fill="#fee2e2"/>
                                <text x="274" y="371" text-anchor="middle" font-size="13" font-weight="600" fill="#b91c1c">+$<?= $wave_receipt_mo ?>/mo on Wave</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
