<?php
/**
 * Hand-curated pain points per outreach category.
 *
 * The AI prompt in outreach_helpers.php uses these as generic industry-level
 * context the AI may gently allude to. They are NOT claims about any specific
 * business — the prompt enforces that framing.
 *
 * Keys must exactly match entries in OUTREACH_CATEGORY_POOL in outreach_helpers.php
 * (lowercase, plural form). Unknown categories fall back to '_default'.
 *
 * Keep each pain point short, concrete, and phrased as a typical day-to-day
 * bookkeeping/invoicing headache that Argo Books could help with.
 */

return [
    'restaurants' => [
        'matching tip splits and daily cash drops',
        'matching supplier invoices against rising food costs',
        'end-of-month sales tax without a finance person',
    ],
    'plumbers' => [
        'juggling estimates, invoices, and chasing late payments between jobs',
        'tracking parts receipts that pile up in the truck',
        'knowing at a glance what each job actually made',
    ],
    'electricians' => [
        'turning field notes into clean invoices after a long day',
        'tracking permits and material costs per job',
        'chasing payment from general contractors',
    ],
    'dentists' => [
        'matching insurance payments against what was billed',
        'tracking supply costs without a full accounting team',
        'month-end reports that do not take a whole evening',
    ],
    'lawyers' => [
        'tracking billable hours alongside expenses',
        'trust-account bookkeeping without a dedicated bookkeeper',
        'clean invoices clients actually understand',
    ],
    'accountants' => [
        'a lightweight tool to recommend to smaller clients who do not need full software',
        'keeping their own books as tidy as their clients expect',
    ],
    'real estate agents' => [
        'tracking expenses across listings and closings',
        'separating commission income from deductible costs at tax time',
        'invoicing referral fees and staging costs',
    ],
    'insurance agents' => [
        'tracking commissions against the expenses of running the office',
        'simple books that do not require an accounting background',
    ],
    'auto repair' => [
        'parts invoices piling up between jobs',
        'matching customer payments to work orders',
        'knowing the margin on each repair',
    ],
    'hair salons' => [
        'tracking product sales alongside service income',
        'managing booth-rental splits without a spreadsheet',
        'weekly cash-vs-card matching',
    ],
    'fitness gyms' => [
        'tracking recurring memberships alongside one-off sales',
        'equipment expenses and vendor invoices in one place',
    ],
    'chiropractors' => [
        'matching insurance claims against what patients paid',
        'tracking supplies and equipment without a full finance setup',
    ],
    'veterinarians' => [
        'tracking drug and supply costs per visit',
        'invoices that clients can actually read',
    ],
    'cleaning services' => [
        'invoicing recurring clients without forgetting anyone',
        'tracking supply costs and travel per crew',
        'chasing late payments from property managers',
    ],
    'landscaping' => [
        'switching between estimates and invoices fast during the busy season',
        'tracking fuel, equipment, and seasonal labour costs',
        'knowing which jobs actually turn a profit',
    ],
    'roofing contractors' => [
        'turning a deposit-progress-final schedule into clean invoices',
        'tracking crew costs and material runs per job',
    ],
    'hvac' => [
        'invoicing service calls without making customers wait for paperwork',
        'tracking parts and warranty work separately',
    ],
    'photographers' => [
        'deposit invoices, final invoices, and print-sale receipts in one place',
        'tracking gear and travel expenses at tax time',
    ],
    'florists' => [
        'tracking wholesale flower costs against retail arrangements',
        'event deposits and balances without double-booking the books',
    ],
    'bakeries' => [
        'tracking ingredient costs against retail prices',
        'wholesale orders and walk-in sales in one place',
    ],
    'coffee shops' => [
        'daily cash-vs-card matching',
        'tracking bean and supply costs without spreadsheets',
    ],
    'pet stores' => [
        'inventory costs and retail margins in one view',
        'grooming-service income separate from product sales',
    ],
    'daycare centers' => [
        'recurring invoices for parents without chasing every month',
        'tracking supplies, snacks, and staff costs simply',
    ],
    'tutoring services' => [
        'invoicing packages of sessions without getting confused about remaining hours',
        'tracking contractor tutors vs employee tutors',
    ],
    'martial arts studios' => [
        'recurring memberships alongside belt-test and gear fees',
        'tracking instructor pay-outs simply',
    ],
    'yoga studios' => [
        'class packs, memberships, and drop-ins in one clean ledger',
        'tracking instructor splits without a spreadsheet',
    ],
    'massage therapists' => [
        'tracking insurance-billed sessions vs direct-pay clients',
        'simple books for a solo practice',
    ],
    'optometrists' => [
        'matching insurance against the frames and exam charges patients actually owe',
        'tracking frame inventory simply',
    ],
    'pharmacies' => [
        'matching third-party payer reimbursements against dispensed prescriptions',
        'tracking front-shop retail sales alongside pharmacy income',
    ],
    'printing services' => [
        'quoting jobs and turning them into invoices without re-typing everything',
        'tracking paper and ink costs against job margins',
    ],
    'moving companies' => [
        'deposit-then-final invoices without losing track',
        'fuel, truck, and crew costs per move',
    ],
    'pest control' => [
        'recurring quarterly visits that invoice themselves',
        'tracking chemical and equipment costs per route',
    ],
    'locksmiths' => [
        'quick on-the-road invoicing after a call-out',
        'tracking parts and travel costs per job',
    ],
    'car dealerships' => [
        'tracking parts, service, and sales revenue separately',
        'matching trade-ins and financing on the books',
    ],
    'tire shops' => [
        'inventory costs vs sale price per tire',
        'service and retail income in one ledger',
    ],
    'furniture stores' => [
        'tracking delivery and setup fees alongside product revenue',
        'wholesale cost vs retail margin at a glance',
    ],
    'jewelry stores' => [
        'inventory valuation without a full accountant',
        'custom-order deposits and balances tracked cleanly',
    ],
    'clothing boutiques' => [
        'seasonal inventory costs against sales',
        'online and in-store income in one place',
    ],
    'tattoo parlors' => [
        'artist splits without a spreadsheet',
        'supply costs separate from session income',
    ],
    'breweries' => [
        'tracking ingredient batches against kegs and bottles sold',
        'taproom sales vs wholesale distribution',
    ],
    'catering' => [
        'event deposits, final invoices, and tips in one ledger',
        'food-cost-vs-quote margins per event',
    ],
    'wedding planners' => [
        'tracking vendor payments you pass through vs your own fee',
        'deposit schedules without spreadsheets',
    ],
    'interior designers' => [
        'client retainers, design fees, and procurement markups in one place',
        'tracking expenses you bill back vs those you eat',
    ],
    'architects' => [
        'billable hours and project expenses on one invoice',
        'multi-phase project billing without losing track',
    ],
    'surveyors' => [
        'tracking mileage, equipment, and crew time per job',
        'invoicing fast enough to keep cash flowing',
    ],
    'physiotherapists' => [
        'insurance-paid sessions matched against private pay',
        'simple books for a solo or small-team clinic',
    ],
    'psychologists' => [
        'insurance and direct-pay sessions tracked cleanly',
        'privacy-minded, simple books for a small practice',
    ],
    'counsellors' => [
        'session billing and sliding-scale income in one ledger',
        'minimal-fuss books for a solo practice',
    ],
    'notaries' => [
        'fast invoicing for one-off appointments',
        'tracking travel and supply expenses simply',
    ],
    'bookkeepers' => [
        'a lightweight tool to recommend to smaller clients who just need the basics',
        'keeping their own practice books as clean as their clients expect',
    ],
    'it support' => [
        'tracking hours, parts, and trip fees on one invoice',
        'recurring managed-services billing without chasing it',
    ],
    'web design' => [
        'deposit-then-final invoicing for project work',
        'recurring hosting or retainer income separate from project income',
    ],
    'marketing agencies' => [
        'retainer invoices, ad-spend pass-throughs, and project fees in one ledger',
        'clean books that do not require an accountant to decipher',
    ],
    'sign shops' => [
        'quote-to-invoice without re-typing job details',
        'material costs vs job price at a glance',
    ],
    'trophy shops' => [
        'small-batch orders invoiced without friction',
        'tracking engraving supplies against sales',
    ],
    'music schools' => [
        'recurring lesson packages billed automatically',
        'tracking instructor pay-outs simply',
    ],
    'dance studios' => [
        'monthly tuition plus costume and recital fees in one ledger',
        'instructor payments tracked cleanly',
    ],
    'dog groomers' => [
        'walk-in and recurring appointment income tracked together',
        'product sales separate from service income',
    ],
    'boarding kennels' => [
        'deposit and final invoices for multi-night stays',
        'tracking food, supplies, and staff costs simply',
    ],
    'farm equipment dealers' => [
        'parts, service, and sales revenue split cleanly',
        'tracking deposits on big-ticket orders',
    ],
    'hardware stores' => [
        'retail sales vs contractor accounts in one ledger',
        'inventory cost vs sale price at a glance',
    ],
    'building supplies' => [
        'contractor charge accounts and retail sales tracked together',
        'tracking delivery fees alongside product revenue',
    ],
    'appliance repair' => [
        'quick on-the-road invoicing after a service call',
        'tracking parts and trip fees per job',
    ],
    'upholstery services' => [
        'quote-then-invoice without re-typing job details',
        'tracking materials vs labour per job',
    ],
    'tailors' => [
        'alteration tickets invoiced cleanly',
        'tracking material and notion costs against small-ticket jobs',
    ],
    'dry cleaners' => [
        'daily ticket matching without a headache',
        'tracking supply costs against service revenue',
    ],
    'spas' => [
        'service income, product sales, and gift cards in one ledger',
        'tracking staff pay-outs and tips simply',
    ],
    'tanning salons' => [
        'memberships, single sessions, and product sales tracked together',
        'simple books for a small staff',
    ],
    'nail salons' => [
        'booth-rental vs commission income tracked cleanly',
        'product sales separate from service revenue',
    ],
    'barber shops' => [
        'chair-rental or commission income tracked cleanly',
        'product sales separate from service revenue',
    ],
    'optical stores' => [
        'frame inventory vs exam revenue tracked separately',
        'insurance reimbursements matched against patient charges',
    ],
    'hearing aid clinics' => [
        'device sales, fittings, and service fees in one ledger',
        'insurance and direct-pay matching',
    ],
    'home inspectors' => [
        'one-off report invoices that go out fast',
        'tracking mileage and equipment costs at tax time',
    ],
    'appraisers' => [
        'project-based invoicing without re-typing details each time',
        'tracking travel and research expenses per job',
    ],
    'property management' => [
        'recurring management-fee invoicing that runs itself',
        'tracking maintenance expenses you bill back to owners',
    ],
    'storage facilities' => [
        'recurring unit invoices that go out on time every month',
        'late fees handled without chasing each tenant',
    ],
    'courier services' => [
        'per-delivery invoicing that does not bog down the day',
        'tracking fuel and vehicle costs simply',
    ],
    'towing services' => [
        'quick on-the-road invoicing after a call-out',
        'tracking fuel, storage fees, and impound charges cleanly',
    ],
    'glass repair' => [
        'insurance-billed jobs vs direct-pay customers tracked cleanly',
        'parts and trip fees per job',
    ],
    'fencing contractors' => [
        'deposit-progress-final invoices without losing track',
        'tracking materials and crew time per job',
    ],
    'concrete contractors' => [
        'deposit-then-final invoicing per pour',
        'materials and crew time tracked per job',
    ],
    'paving contractors' => [
        'quote-to-invoice without re-typing job details',
        'tracking materials and equipment per job',
    ],
    'tree services' => [
        'fast invoicing after a day in the field',
        'tracking fuel, equipment, and crew costs simply',
    ],
    'snow removal' => [
        'seasonal contract invoicing that runs itself',
        'tracking fuel and equipment costs across the winter',
    ],
    'pool services' => [
        'recurring maintenance invoices that go out on time',
        'chemical and equipment costs tracked simply',
    ],
    'septic services' => [
        'quick on-the-road invoicing after a service call',
        'tracking truck, fuel, and disposal costs per job',
    ],
    'garage door repair' => [
        'fast on-site invoicing after a service call',
        'tracking parts and trip fees per job',
    ],
    'security companies' => [
        'recurring monitoring invoices that go out on time',
        'equipment and install costs tracked simply',
    ],
    'staffing agencies' => [
        'client invoicing and contractor pay-outs tracked together',
        'margin per placement at a glance',
    ],
    'travel agencies' => [
        'commission income tracked against the expenses of running the office',
        'client deposits and balances cleanly recorded',
    ],
    'event venues' => [
        'deposit-then-final invoicing per booking',
        'vendor pass-throughs separate from your own revenue',
    ],
    'food trucks' => [
        'daily cash-vs-card matching',
        'tracking food and fuel costs against sales',
    ],

    '_default' => [
        'keeping books up to date without losing an evening to it',
        'tracking receipts before they disappear',
        'pulling together numbers at tax time',
    ],
];
