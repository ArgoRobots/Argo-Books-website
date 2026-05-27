<?php
// free-invoice-generator/index.php
// Seed landing page for /free-invoice-generator/. Delegates to the niche
// template using the "generic" data file. The niche template canonicalizes
// this URL to https://argorobots.com/free-invoice-generator/ (no slug).
$_GET['slug'] = 'generic';
require __DIR__ . '/../niches/niche-page.php';
