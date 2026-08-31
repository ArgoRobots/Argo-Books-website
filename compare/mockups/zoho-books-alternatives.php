<?php
// compare/mockups/zoho-books-alternatives.php
//
// Decorative price chart for the Zoho Books comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// price variables that compare/data/zoho-books-alternatives.php declares; both that
// file and this one are included at global scope by the template.
?>
<!-- Decorative price-comparison mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 540" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="538" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="540" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">About the same to start, but Zoho's tiers climb to $<?= $zoho_ultimate ?>.</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="16" height="10" rx="2" fill="#3f63e8"/>
                                <text x="62" y="107" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="98" width="16" height="10" rx="2" fill="#ef4444"/>
                                <text x="188" y="107" font-size="13" fill="#0f172a">Zoho Books</text>

                                <!-- Bars: width scaled so $290 (widest) = 400px, i.e. ~1.38px per $1 -->
                                <!-- Argo Free $0 -->
                                <text x="40" y="153" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="175" y="140" width="2" height="18" rx="3" fill="#3f63e8"/>
                                <text x="189" y="153" font-size="13" font-weight="600" fill="#0f172a">$0</text>

                                <!-- Argo Books Premium $15 -->
                                <text x="40" y="199" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="175" y="186" width="21" height="18" rx="3" fill="#3f63e8"/>
                                <text x="208" y="199" font-size="13" font-weight="600" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- Zoho Free $0 -->
                                <text x="40" y="245" font-size="13" font-weight="600" fill="#0f172a">Zoho Free</text>
                                <rect x="175" y="232" width="2" height="18" rx="3" fill="#ef4444"/>
                                <text x="189" y="245" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_free ?></text>

                                <!-- Zoho Standard $15 -->
                                <text x="40" y="291" font-size="13" font-weight="600" fill="#0f172a">Zoho Standard</text>
                                <rect x="175" y="278" width="21" height="18" rx="3" fill="#ef4444"/>
                                <text x="208" y="291" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_standard ?></text>

                                <!-- Zoho Professional $30 -->
                                <text x="40" y="337" font-size="13" font-weight="600" fill="#0f172a">Zoho Professional</text>
                                <rect x="175" y="324" width="41" height="18" rx="3" fill="#ef4444"/>
                                <text x="228" y="337" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_professional ?></text>

                                <!-- Zoho Premium $40 -->
                                <text x="40" y="383" font-size="13" font-weight="600" fill="#0f172a">Zoho Premium</text>
                                <rect x="175" y="370" width="55" height="18" rx="3" fill="#ef4444"/>
                                <text x="242" y="383" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_premium ?></text>

                                <!-- Zoho Elite $165 -->
                                <text x="40" y="429" font-size="13" font-weight="600" fill="#0f172a">Zoho Elite</text>
                                <rect x="175" y="416" width="228" height="18" rx="3" fill="#ef4444"/>
                                <text x="415" y="429" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_elite ?></text>

                                <!-- Zoho Ultimate $290 -->
                                <text x="40" y="475" font-size="13" font-weight="600" fill="#0f172a">Zoho Ultimate</text>
                                <rect x="175" y="462" width="400" height="18" rx="3" fill="#ef4444"/>
                                <text x="587" y="475" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_ultimate ?></text>

                                <!-- Baseline -->
                                <line x1="175" y1="486" x2="175" y2="128" stroke="#e2e8f0" stroke-width="1"/>
                            </g>
                            <rect x="1" y="1" width="638" height="538" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
