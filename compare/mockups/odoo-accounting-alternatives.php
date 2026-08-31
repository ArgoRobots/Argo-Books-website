<?php
// compare/mockups/odoo-accounting-alternatives.php
//
// Decorative price chart for the Odoo comparison, included by
// compare/compare-page.php inside .diff-mockup. Reads $argo_monthly and the
// price variables that compare/data/odoo-accounting-alternatives.php declares; both that
// file and this one are included at global scope by the template.
?>
<!-- Decorative cost mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">Argo is one flat price. Odoo bills per user.</text>

                                <!-- Legend -->
                                <rect x="40" y="100" width="14" height="10" rx="2" fill="#3f63e8"/>
                                <text x="60" y="109" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="100" width="14" height="10" rx="2" fill="#ef4444"/>
                                <text x="186" y="109" font-size="13" fill="#0f172a">Odoo Standard</text>

                                <!-- Bars: width proportional to price, max ($220) = 380px wide -->
                                <!-- Argo Books Premium (flat) $15 -->
                                <text x="40" y="156" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium (flat)</text>
                                <rect x="40" y="166" width="26" height="26" rx="5" fill="#3f63e8"/>
                                <text x="76" y="184" font-size="14" font-weight="700" fill="#0f172a">$15</text>

                                <!-- Odoo, 1 user $44 -->
                                <text x="40" y="216" font-size="13" font-weight="600" fill="#0f172a">Odoo, 1 user</text>
                                <rect x="40" y="226" width="76" height="26" rx="5" fill="#ef4444"/>
                                <text x="126" y="244" font-size="14" font-weight="700" fill="#0f172a">$44</text>

                                <!-- Odoo, 3 users $132 -->
                                <text x="40" y="276" font-size="13" font-weight="600" fill="#0f172a">Odoo, 3 users</text>
                                <rect x="40" y="286" width="228" height="26" rx="5" fill="#ef4444"/>
                                <text x="278" y="304" font-size="14" font-weight="700" fill="#0f172a">$132</text>

                                <!-- Odoo, 5 users $220 -->
                                <text x="40" y="336" font-size="13" font-weight="600" fill="#0f172a">Odoo, 5 users</text>
                                <rect x="40" y="346" width="380" height="26" rx="5" fill="#ef4444"/>
                                <text x="430" y="364" font-size="14" font-weight="700" fill="#0f172a">$220</text>

                                <!-- Flat-price reminder -->
                                <rect x="40" y="402" width="26" height="18" rx="5" fill="#eef2fe"/>
                                <line x1="53" y1="406" x2="53" y2="416" stroke="#3f63e8" stroke-width="2.4" stroke-linecap="round"/>
                                <text x="76" y="416" font-size="13" font-weight="600" fill="#3f63e8">Argo stays $15 for the whole team</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
