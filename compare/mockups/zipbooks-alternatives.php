<?php
// compare/mockups/zipbooks-alternatives.php
//
// Decorative price chart for the ZipBooks comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// price variables that compare/data/zipbooks-alternatives.php declares; both that
// file and this one are included at global scope by the template.
?>
<!-- Decorative price-comparison mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <?php
                                $barX0  = 205;
                                $barMax = 340; // px width for the $50 top of scale
                                $premW  = (int) round($argo_monthly     / 50 * $barMax);
                                $smartW = (int) round($zb_smarter        / 50 * $barMax);
                                $sophW  = (int) round($zb_sophisticated  / 50 * $barMax);
                            ?>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">Both free to start. Argo's paid plan costs less.</text>

                                <!-- Legend -->
                                <rect x="40" y="99" width="12" height="12" rx="3" fill="#3f63e8"/>
                                <text x="58" y="109" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="150" y="99" width="12" height="12" rx="3" fill="#ef4444"/>
                                <text x="168" y="109" font-size="13" fill="#0f172a">ZipBooks</text>

                                <!-- Row 1: Argo Free $0 -->
                                <rect x="205" y="145" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="162" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="205" y="145" width="4" height="26" rx="2" fill="#cbd5e1"/>
                                <text x="219" y="162" font-size="13" font-weight="700" fill="#64748b">$0</text>

                                <!-- Row 2: ZipBooks Starter $0 -->
                                <rect x="205" y="201" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="218" font-size="13" font-weight="600" fill="#0f172a">ZipBooks Starter</text>
                                <rect x="205" y="201" width="4" height="26" rx="2" fill="#cbd5e1"/>
                                <text x="219" y="218" font-size="13" font-weight="700" fill="#64748b">$0</text>

                                <!-- Row 3: Argo Books Premium $15 -->
                                <rect x="205" y="257" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="274" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="205" y="257" width="<?= $premW ?>" height="26" rx="5" fill="#3f63e8"/>
                                <text x="<?= 205 + $premW + 8 ?>" y="274" font-size="13" font-weight="700" fill="#3f63e8">$<?= $argo_monthly ?></text>

                                <!-- Row 4: ZipBooks Smarter $20 -->
                                <rect x="205" y="313" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="330" font-size="13" font-weight="600" fill="#0f172a">ZipBooks Smarter</text>
                                <rect x="205" y="313" width="<?= $smartW ?>" height="26" rx="5" fill="#ef4444"/>
                                <text x="<?= 205 + $smartW + 8 ?>" y="330" font-size="13" font-weight="700" fill="#ef4444">$<?= $zb_smarter ?></text>

                                <!-- Row 5: ZipBooks Sophisticated $50 -->
                                <rect x="205" y="369" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="386" font-size="13" font-weight="600" fill="#0f172a">ZipBooks Sophisticated</text>
                                <rect x="205" y="369" width="<?= $sophW ?>" height="26" rx="5" fill="#ef4444"/>
                                <text x="<?= 205 + $sophW + 8 ?>" y="386" font-size="13" font-weight="700" fill="#ef4444">$<?= $zb_sophisticated ?></text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
