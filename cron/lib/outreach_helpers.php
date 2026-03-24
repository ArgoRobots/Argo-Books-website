<?php
/**
 * Shared helper functions for outreach automation.
 * Used by both the admin API and the automated cron pipeline.
 */

// ─── Email Scraping ───

function scrape_email_from_website_cli($url)
{
    if (empty($url)) return null;

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0',
            'follow_location' => true,
            'max_redirects' => 3,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $falsePositives = ['example.com', 'sentry.io', 'wixpress.com', 'wordpress.org', 'w3.org', 'schema.org', 'googleapis.com', 'gravatar.com'];

    $cleanEmail = function ($email) {
        $email = urldecode($email);
        $email = preg_replace('/[^\x20-\x7E]/', '', $email);
        $email = trim($email);
        if (preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $email)) {
            return $email;
        }
        return null;
    };

    $extractEmail = function ($html) use ($falsePositives, $cleanEmail) {
        $decodedHtml = urldecode($html);

        if (preg_match_all('/mailto:\s*([^\s"\'<>]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,})/', $decodedHtml, $matches)) {
            foreach ($matches[1] as $raw) {
                $email = $cleanEmail($raw);
                if (!$email) continue;
                $skip = false;
                foreach ($falsePositives as $fp) {
                    if (str_contains(strtolower($email), $fp)) { $skip = true; break; }
                }
                if (!$skip) return $email;
            }
        }

        $text = strip_tags($decodedHtml);
        $text = preg_replace('/[^\x20-\x7E\n\r\t]/', ' ', $text);
        if (preg_match_all('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
            foreach ($matches[0] as $raw) {
                $email = $cleanEmail($raw);
                if (!$email) continue;
                $skip = false;
                foreach ($falsePositives as $fp) {
                    if (str_contains(strtolower($email), $fp)) { $skip = true; break; }
                }
                if (!$skip) return $email;
            }
        }
        return null;
    };

    $html = @file_get_contents($url, false, $context);
    if ($html) {
        $email = $extractEmail($html);
        if ($email) return $email;

        $parsed = parse_url($url);
        $origin = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');
        $basePath = rtrim($url, '/');
        $contactPaths = [];

        $contactKeywords = 'contact|about|about-us|contact-us|connect|get-in-touch|reach-us|reach out';
        if (preg_match_all('/<a\s[^>]*href=["\']([^"\'#][^"\']*)["\'][^>]*>(.*?)<\/a>/is', $html, $linkMatches, PREG_SET_ORDER)) {
            foreach ($linkMatches as $m) {
                $href = $m[1];
                $text = strip_tags($m[2]);
                if (!preg_match('/' . $contactKeywords . '/i', $href) && !preg_match('/' . $contactKeywords . '/i', $text)) continue;
                if (preg_match('/^(mailto:|tel:|javascript:)/i', $href)) continue;

                if (str_starts_with($href, 'http')) {
                    $contactPaths[] = $href;
                } elseif (str_starts_with($href, '/')) {
                    $contactPaths[] = $origin . $href;
                } else {
                    $contactPaths[] = $basePath . '/' . $href;
                }
            }
        }

        if (empty($contactPaths)) {
            $contactPaths = [
                $basePath . '/contact',
                $basePath . '/contact-us',
                $basePath . '/about',
            ];
        }

        foreach (array_unique(array_slice($contactPaths, 0, 3)) as $contactUrl) {
            $contactHtml = @file_get_contents($contactUrl, false, $context);
            if ($contactHtml) {
                $email = $extractEmail($contactHtml);
                if ($email) return $email;
            }
        }
    }

    return null;
}

// ─── Google Places Search (CLI version) ───

function search_businesses_cli($city, $province, $category, $limit, $apiKey, $excludePlaceIds = [])
{
    $location = $province ? "$city, $province" : $city;
    $businesses = [];
    $seenPlaceIds = [];
    foreach ($excludePlaceIds as $id) {
        $seenPlaceIds[trim($id)] = true;
    }
    $maxRounds = 5;
    $roundsUsed = 0;

    $httpContext = stream_context_create(['http' => [
        'timeout' => 10,
        'ignore_errors' => true,
    ]]);

    $queries = [];
    if ($category) {
        $queries[] = "$category in $location";
        $queries[] = "$category near $location";
        $queries[] = "$category services in $location";
        $queries[] = "$category companies in $location";
        $queries[] = "best $category in $location";
    } else {
        $categoryPool = [
            'restaurants', 'plumbers', 'electricians', 'dentists', 'lawyers',
            'accountants', 'real estate agents', 'insurance agents', 'auto repair',
            'hair salons', 'fitness gyms', 'chiropractors', 'veterinarians',
            'cleaning services', 'landscaping', 'roofing contractors', 'HVAC',
            'photographers', 'florists', 'bakeries', 'coffee shops', 'pet stores',
            'daycare centers', 'tutoring services', 'martial arts studios',
            'yoga studios', 'massage therapists', 'optometrists', 'pharmacies',
            'printing services', 'moving companies', 'pest control', 'locksmiths',
            'car dealerships', 'tire shops', 'furniture stores', 'jewelry stores',
            'clothing boutiques', 'tattoo parlors', 'breweries', 'catering',
            'wedding planners', 'interior designers', 'architects', 'surveyors',
            'physiotherapists', 'psychologists', 'counsellors', 'notaries',
            'bookkeepers', 'IT support', 'web design', 'marketing agencies',
            'sign shops', 'trophy shops', 'music schools', 'dance studios',
            'dog groomers', 'boarding kennels', 'farm equipment dealers',
            'hardware stores', 'building supplies', 'appliance repair',
            'upholstery services', 'tailors', 'dry cleaners', 'spas',
            'tanning salons', 'nail salons', 'barber shops', 'optical stores',
            'hearing aid clinics', 'home inspectors', 'appraisers',
            'property management', 'storage facilities', 'courier services',
            'towing services', 'glass repair', 'fencing contractors',
            'concrete contractors', 'paving contractors', 'tree services',
            'snow removal', 'pool services', 'septic services',
            'garage door repair', 'security companies', 'staffing agencies',
            'travel agencies', 'event venues', 'food trucks',
        ];
        shuffle($categoryPool);
        for ($i = 0; $i < $maxRounds; $i++) {
            $queries[] = $categoryPool[$i] . " in $location";
        }
    }

    $queryCategories = [];
    if (!$category) {
        foreach ($queries as $q) {
            $queryCategories[] = ucwords(str_replace(" in $location", '', $q));
        }
    }

    $placeTypeMap = [
        'restaurant' => 'restaurant', 'plumber' => 'plumber',
        'electrician' => 'electrician', 'dentist' => 'dentist',
        'lawyer' => 'lawyer', 'accountant' => 'accounting',
        'gym' => 'gym', 'salon' => 'hair_care', 'veterinarian' => 'veterinary_care',
        'pharmacy' => 'pharmacy', 'car dealership' => 'car_dealer',
        'bakery' => 'bakery', 'cafe' => 'cafe', 'coffee' => 'cafe',
        'spa' => 'spa', 'florist' => 'florist', 'pet store' => 'pet_store',
        'furniture' => 'furniture_store', 'jewelry' => 'jewelry_store',
        'hardware' => 'hardware_store', 'barber' => 'hair_care',
        'locksmith' => 'locksmith', 'storage' => 'storage',
        'travel agenc' => 'travel_agency', 'insurance' => 'insurance_agency',
        'real estate' => 'real_estate_agency',
    ];

    for ($round = 0; $round < $maxRounds && count($businesses) < $limit; $round++) {
        $query = $queries[$round] ?? null;
        if (!$query) break;
        $countBefore = count($businesses);
        $roundsUsed++;

        $params = ['query' => $query, 'key' => $apiKey];
        foreach ($placeTypeMap as $keyword => $type) {
            if (stripos($query, $keyword) !== false) {
                $params['type'] = $type;
                break;
            }
        }
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?' . http_build_query($params);

        $resp = @file_get_contents($url, false, $httpContext);
        if ($resp === false) break;

        $data = json_decode($resp, true);
        $status = $data['status'] ?? '';
        if ($status !== 'OK' && $status !== 'ZERO_RESULTS') break;

        $candidates = $data['results'] ?? [];
        $nextPageToken = $data['next_page_token'] ?? null;
        $maxPages = 3;
        $pagesUsed = 1;

        while (count($businesses) < $limit) {
            foreach ($candidates as $place) {
                if (count($businesses) >= $limit) break;

                $placeId = $place['place_id'] ?? '';
                if ($placeId && isset($seenPlaceIds[$placeId])) continue;
                if ($placeId) $seenPlaceIds[$placeId] = true;

                $business = [
                    'places_id' => $placeId,
                    'business_name' => $place['name'] ?? '',
                    'address' => $place['formatted_address'] ?? '',
                    'category' => $category ?: ($queryCategories[$round] ?? (isset($place['types'][0]) ? ucfirst(str_replace('_', ' ', $place['types'][0])) : '')),
                    'city' => $city,
                    'phone' => null,
                    'website' => null,
                    'email' => null,
                ];

                if (!empty($placeId)) {
                    $detailUrl = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
                        'place_id' => $placeId,
                        'fields' => 'formatted_phone_number,website,url',
                        'key' => $apiKey,
                    ]);
                    $detailResp = @file_get_contents($detailUrl, false, $httpContext);
                    if ($detailResp) {
                        $detail = json_decode($detailResp, true);
                        $r = $detail['result'] ?? [];
                        $business['phone'] = $r['formatted_phone_number'] ?? null;
                        $business['website'] = $r['website'] ?? null;
                        $business['contact_page_url'] = $r['url'] ?? null;
                    }
                }

                if (empty($business['website'])) continue;

                $business['email'] = scrape_email_from_website_cli($business['website']);
                if (empty($business['email'])) continue;

                $businesses[] = $business;
            }

            if (count($businesses) >= $limit || empty($nextPageToken) || $pagesUsed >= $maxPages) break;

            sleep(2);

            $nextUrl = 'https://maps.googleapis.com/maps/api/place/textsearch/json?' . http_build_query([
                'pagetoken' => $nextPageToken,
                'key' => $apiKey,
            ]);
            $nextResp = @file_get_contents($nextUrl, false, $httpContext);
            if (!$nextResp) break;

            $nextData = json_decode($nextResp, true);
            if (($nextData['status'] ?? '') !== 'OK') break;

            $candidates = $nextData['results'] ?? [];
            $nextPageToken = $nextData['next_page_token'] ?? null;
            $pagesUsed++;
        }

        $newThisRound = count($businesses) - $countBefore;
        if ($newThisRound < 2 && $round > 0) break;
    }

    return ['businesses' => $businesses, 'count' => count($businesses), 'rounds' => $roundsUsed];
}

// ─── OpenAI Call (CLI version) ───

function call_openai_cli($systemPrompt, $userPrompt)
{
    $apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
    if (empty($apiKey)) {
        return ['error' => 'OpenAI API key not configured'];
    }

    $model = $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini';

    $payload = json_encode([
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ],
        'temperature' => 0.7,
        'max_tokens' => 2000,
    ]);

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = $errorData['error']['message'] ?? 'OpenAI request failed';
        return ['error' => $errorMsg];
    }

    $result = json_decode($response, true);
    return ['content' => $result['choices'][0]['message']['content'] ?? ''];
}

// ─── Business Summarization (CLI version) ───

function summarize_business_cli($website)
{
    if (empty($website)) return null;

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0',
            'follow_location' => true,
            'max_redirects' => 3,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $html = @file_get_contents($website, false, $context);
    if (!$html) return null;

    $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
    $text = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $text);
    $text = strip_tags($text);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim(mb_substr($text, 0, 3000));

    if (strlen($text) < 50) return null;

    $result = call_openai_cli(
        "You summarize businesses based on their website content. Respond with ONLY a concise summary (3-5 sentences) covering:
1. What specific services or products they offer
2. Who their typical customers are
3. How they likely handle billing (e.g. do they invoice clients, do project quotes, charge hourly, sell products, etc.)
4. Any pain points a simple bookkeeping/invoicing tool could solve for them (e.g. tracking job expenses, sending invoices, managing payments)
Be specific and factual based on the website content. Do not include any other text or preamble.",
        "Website content from $website:\n\n$text"
    );

    return $result['content'] ?? null;
}

// ─── Draft Generation (CLI version) ───

function generate_draft_for_lead_cli($pdo, $lead)
{
    $id = $lead['id'];

    // Generate business summary if missing
    $summary = $lead['business_summary'] ?? null;
    if (empty($summary) && !empty($lead['website'])) {
        $summary = summarize_business_cli($lead['website']);
        if ($summary) {
            $stmt = $pdo->prepare("UPDATE outreach_leads SET business_summary = ? WHERE id = ?");
            $stmt->execute([$summary, $id]);
        }
    }

    $isLocal = false;
    $city = strtolower(trim($lead['city'] ?? ''));
    $province = strtolower(trim($lead['province'] ?? ''));
    if ($province === 'saskatchewan' || $province === 'sk' || in_array($city, ['saskatoon','regina','prince albert','moose jaw','swift current','yorkton','north battleford','estevan','weyburn','martensville','warman','humboldt','melfort','meadow lake','lloydminster'])) {
        $isLocal = true;
    }

    $localInstruction = $isLocal
        ? "- The business is in Saskatchewan. Evan is a local Saskatchewan software developer based in Saskatoon. ALWAYS mention being local, e.g. \"I'm a local Saskatoon software developer\" or \"As a fellow Saskatchewan business\". This local connection is important, make it feel personal."
        : "- Evan is an independent software developer based in Saskatoon, Saskatchewan. Mention this briefly for context.";

    $systemPrompt = "You are helping write a brief, personal outreach email from Evan, the developer behind Argo Books, to a small business. The goal is to get honest product feedback on Argo Books, a bookkeeping and invoicing app for small businesses.

About Argo Books:
- It is like QuickBooks but way simpler, designed so you do not need any accounting knowledge at all
- Built specifically for small businesses, not a bloated enterprise tool
- Features include invoicing, expense tracking, and simple bookkeeping
- Evan is a local independent software developer based in Saskatoon building this specifically for small businesses

Rules:
- Keep it very short (2-3 short paragraphs max, under 100 words ideally)
- Sound human, friendly, and genuine, not like marketing spam
$localInstruction
- Do NOT refer to a \"team\", Evan is a solo developer
- Get to the point quickly in the first sentence - say why you are emailing. Do NOT open with generic filler like \"I hope this message finds you well\" or vague flattery like \"I admire your work\"
- Use the business name in the greeting (e.g. \"Hi LVM Landscaping\" or \"Hi [contact name]\" if available)

PERSONALIZATION (this is critical):
- If a business summary is provided, you MUST use it to make the email specific to their business. Do not write a generic email when you have summary info
- Connect Argo Books features directly to their business needs. Examples:
  - If they do services/contracting: mention how easy it is to invoice clients after a job
  - If they sell products: mention simple expense tracking and bookkeeping
  - If they likely deal with quotes/estimates: mention invoicing features
  - If they have multiple revenue streams: mention how it keeps everything organized without accounting knowledge
- Reference their actual business type naturally (e.g. \"I know running a landscaping business means a lot of invoicing\" not just \"I see you run a business\")
- Only reference Argo Books features that are relevant to what they do. Do not list every feature
- Do NOT invent details about the business you do not have
- If no summary is available, keep it more general but still mention their industry/category if known

- Briefly describe Argo Books as a simpler alternative to QuickBooks that requires no accounting knowledge. Do NOT just say \"check it out\" without explaining what it is
- Mention you are looking for honest feedback from small business owners
- If appropriate, mention offering a free 1-year premium license in exchange for feedback
- Use a casual but professional tone
- NEVER use placeholders like [Your Name], [Your Title], [Your Company], etc.
- ALWAYS include the website link https://argorobots.com/ in the email body. This is required in every single email, no exceptions
- NEVER use em dashes in the email. Use commas, periods, or regular hyphens instead
- The subject line should be about the recipient's business, NOT about Argo Books. Make it feel personal and curiosity-driven (e.g. \"Quick question about [business name]\", \"Thought of you guys\")
- You MUST include the line \"You can check it out here: https://argorobots.com/\" (or similar natural phrasing with that exact URL) somewhere in the email body, ideally after mentioning what Argo Books is
- End the email body with a line like \"Feel free to reply to this email if you have any questions!\" or similar, before the sign-off
- Always sign off with three separate lines: \"All the best,\" then \"Evan\" then \"Argo Books\" (each on its own line, separated by \\n)

Return your response as JSON with two fields:
{\"subject\": \"the email subject line\", \"body\": \"the email body text (plain text, use \\n for line breaks)\"}

Return ONLY the JSON, no other text.";

    $details = "Business: {$lead['business_name']}";
    if (!empty($lead['category'])) $details .= "\nCategory/Industry: {$lead['category']}";
    if (!empty($lead['city'])) $details .= "\nCity: {$lead['city']}";
    if ($isLocal) $details .= "\nLocal: Yes, this business is in Saskatchewan (same province as Evan)";
    if (!empty($lead['website'])) $details .= "\nWebsite: {$lead['website']}";
    if (!empty($lead['contact_name'])) $details .= "\nContact person: {$lead['contact_name']}";
    if ($summary) $details .= "\nBusiness summary: $summary";

    $result = call_openai_cli($systemPrompt, $details);

    if (isset($result['error'])) {
        return ['error' => $result['error']];
    }

    $content = trim($result['content']);
    $content = preg_replace('/^```json\s*/i', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);

    $parsed = json_decode($content, true);
    if (!$parsed || !isset($parsed['subject']) || !isset($parsed['body'])) {
        $parsed = [
            'subject' => "Quick question for {$lead['business_name']}",
            'body' => $content,
        ];
    }

    // Ensure the website URL is in the body
    if (stripos($parsed['body'], 'argorobots.com') === false) {
        $parsed['body'] = preg_replace(
            '/(Feel free to|Don\'t hesitate|Let me know|Reply to this)/i',
            "You can check it out here: https://argorobots.com/\n\n$1",
            $parsed['body'],
            1
        );
        if (stripos($parsed['body'], 'argorobots.com') === false) {
            $parsed['body'] = preg_replace(
                '/(\nAll the best)/i',
                "\n\nYou can check it out here: https://argorobots.com/\n$1",
                $parsed['body'],
                1
            );
        }
    }

    // Save draft
    $stmt = $pdo->prepare("UPDATE outreach_leads SET draft_subject = ?, draft_body = ?, drafted_at = NOW(), status = CASE WHEN status IN ('new','awaiting_approval','approved') THEN 'draft_generated' ELSE status END WHERE id = ?");
    $stmt->execute([$parsed['subject'], $parsed['body'], $id]);

    return ['success' => true, 'subject' => $parsed['subject'], 'body' => $parsed['body']];
}
