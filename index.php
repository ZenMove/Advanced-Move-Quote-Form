<?php
// =============================================================================
//  Zen Move API Demo — Core Router & Frontend
// =============================================================================

// 1. Installation Check
if (!file_exists(__DIR__ . '/config.php')) {
    if (file_exists(__DIR__ . '/install.php')) {
        require __DIR__ . '/install.php';
        exit;
    }
    die('<strong>Error:</strong> Configuration file is missing and install.php could not be found.');
}

require_once __DIR__ . '/config.php';

// 2. Language Setup
session_start();
$langEnabled = defined('LANG_ENABLE') && LANG_ENABLE;
$langDefault  = (defined('LANG_DEFAULT') && in_array(LANG_DEFAULT, ['en', 'fr'])) ? LANG_DEFAULT : 'en';

if ($langEnabled) {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'fr'])) {
        $_SESSION['zm_lang'] = $_GET['lang'];
    }
    $lang = $_SESSION['zm_lang'] ?? $langDefault;
} else {
    $lang = $langDefault;
}
$langAlt = ($lang === 'en') ? 'fr' : 'en';

// 3. Translations
$strings = [
    'en' => [
        'page_title'       => 'Get a Quote',
        'hero_h1'          => 'Your Smooth Move Starts Here',
        'hero_sub'         => 'Get a free, no-obligation moving quote in minutes.',
        'form_title'       => 'Request a Quote',
        'form_sub'         => "Fill in your details below and we'll get back to you promptly.",
        'sec_move_type'    => 'Move Type',
        'sec_details'      => 'Your Details',
        'sec_move_details' => 'Move Details',
        'sec_origin'       => 'Origin',
        'sec_destination'  => 'Destination',
        'move_local'       => 'Local',
        'move_long'        => 'Long Distance',
        'move_intl'        => 'International',
        'first_name'       => 'First name',
        'last_name'        => 'Last name',
        'email'            => 'Email',
        'phone'            => 'Phone',
        'from_city'        => 'Moving from (City)',
        'prov_state'       => 'Province / State',
        'to_city'          => 'Moving to (City)',
        'home_size'        => 'Home size',
        'select'           => '— Select —',
        '1bed_dom'         => '1 Bedroom',
        '2bed_dom'         => '2 Bedrooms',
        '3bed_dom'         => '3 Bedrooms',
        '4bed_dom'         => '4+ Bedrooms',
        'move_date_dom'    => 'Preferred move date',
        'street'           => 'Street address',
        'city'             => 'City',
        'zip'              => 'Zip / Postal',
        'country'          => 'Country',
        'move_date_intl'   => 'Planned move date',
        'volume'           => 'Estimated volume (m³)',
        'move_size'        => 'Move size',
        'boxes'            => 'Boxes / bags only',
        '1bed_intl'        => '1-bedroom',
        '3bed_intl'        => '3-bedroom',
        'bedrooms'         => 'Bedrooms',
        'storage'          => 'Storage',
        'packing'          => 'Packing',
        'assembly'         => 'Assembly',
        'notes'            => 'Additional notes',
        'submit'           => 'Get My Free Quote',
        'privacy'          => 'Your data is secure and will only be used to provide your quote.',
        'thank_you'        => 'Thank you',
        'return_home'      => 'Return Home',
        'err_captcha'      => 'Security check failed. Please complete the captcha.',
        'err_name'         => 'Please enter your full name.',
        'err_email'        => 'Valid email required.',
        'err_phone'        => 'Phone required.',
        'err_intl_fields'  => 'Missing required international fields (address, cities, volume).',
        'err_countries'    => 'Please select valid origin and destination countries.',
        'note_movers'      => 'Your request has been sent to verified movers.',
        'note_specialist'  => 'A moving specialist will contact you shortly.',
        'note_both'        => 'Your request has been sent to verified movers and a specialist will follow up.',
        'lang_label'       => 'Français',
    ],
    'fr' => [
        'page_title'       => 'Obtenir une soumission',
        'hero_h1'          => 'Votre déménagement commence ici',
        'hero_sub'         => 'Obtenez une soumission gratuite et sans engagement en quelques minutes.',
        'form_title'       => 'Demander une soumission',
        'form_sub'         => 'Remplissez vos coordonnées ci-dessous et nous vous répondrons rapidement.',
        'sec_move_type'    => 'Type de déménagement',
        'sec_details'      => 'Vos coordonnées',
        'sec_move_details' => 'Détails du déménagement',
        'sec_origin'       => 'Origine',
        'sec_destination'  => 'Destination',
        'move_local'       => 'Local',
        'move_long'        => 'Longue distance',
        'move_intl'        => 'International',
        'first_name'       => 'Prénom',
        'last_name'        => 'Nom de famille',
        'email'            => 'Courriel',
        'phone'            => 'Téléphone',
        'from_city'        => 'Départ (ville)',
        'prov_state'       => 'Province / État',
        'to_city'          => 'Arrivée (ville)',
        'home_size'        => 'Taille du logement',
        'select'           => '— Sélectionner —',
        '1bed_dom'         => '1 chambre',
        '2bed_dom'         => '2 chambres',
        '3bed_dom'         => '3 chambres',
        '4bed_dom'         => '4+ chambres',
        'move_date_dom'    => 'Date de déménagement souhaitée',
        'street'           => 'Adresse',
        'city'             => 'Ville',
        'zip'              => 'Code postal',
        'country'          => 'Pays',
        'move_date_intl'   => 'Date de déménagement prévue',
        'volume'           => 'Volume estimé (m³)',
        'move_size'        => 'Taille du déménagement',
        'boxes'            => 'Boîtes / sacs seulement',
        '1bed_intl'        => '1 chambre',
        '3bed_intl'        => '3 chambres',
        'bedrooms'         => 'Chambres',
        'storage'          => 'Entreposage',
        'packing'          => 'Emballage',
        'assembly'         => 'Assemblage',
        'notes'            => 'Notes supplémentaires',
        'submit'           => 'Obtenir ma soumission gratuite',
        'privacy'          => 'Vos données sont sécurisées et ne seront utilisées que pour vous fournir une soumission.',
        'thank_you'        => 'Merci',
        'return_home'      => "Retour à l'accueil",
        'err_captcha'      => 'Échec de la vérification. Veuillez compléter le captcha.',
        'err_name'         => 'Veuillez entrer votre nom complet.',
        'err_email'        => 'Une adresse courriel valide est requise.',
        'err_phone'        => 'Le numéro de téléphone est requis.',
        'err_intl_fields'  => 'Champs requis manquants (adresse, villes, volume).',
        'err_countries'    => 'Veuillez sélectionner des pays d\'origine et de destination valides.',
        'note_movers'      => 'Votre demande a été envoyée à des déménageurs vérifiés.',
        'note_specialist'  => 'Un spécialiste du déménagement vous contactera sous peu.',
        'note_both'        => 'Votre demande a été envoyée à des déménageurs vérifiés et un spécialiste assurera un suivi.',
        'lang_label'       => 'English',
    ],
];

function t(string $key): string {
    global $strings, $lang;
    return $strings[$lang][$key] ?? $strings['en'][$key] ?? $key;
}

// 4. Helpers
function h(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function zm_verify_turnstile(string $token): bool {
    $siteKey   = defined('TURNSTILE_SITE_KEY')   ? TURNSTILE_SITE_KEY   : '';
    $secretKey = defined('TURNSTILE_SECRET_KEY') ? TURNSTILE_SECRET_KEY : '';

    if ($siteKey === '' || $secretKey === '') return true;
    if ($token === '') return false;

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query(['secret' => $secretKey, 'response' => $token]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($raw ?: '', true);
    return !empty($data['success']);
}

function zm_api_post(string $endpoint, array $payload): array {
    $url       = rtrim(ZM_API_BASE, '/') . '/' . ltrim($endpoint, '/');
    $verifySsl = defined('ZM_API_VERIFY_SSL') ? (bool)ZM_API_VERIFY_SSL : true;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => $verifySsl,
        CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . ZM_API_TOKEN,
            'Accept: application/json',
        ],
    ]);
    $raw      = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $body = json_decode((string)$raw, true);
    if (!is_array($body)) return [false, ['error' => ['message' => "API Error HTTP {$httpCode}"]]];
    return [$httpCode >= 200 && $httpCode < 300 && !empty($body['success']), $body];
}

function zm_build_lead_email(array $fields): string {
    $rows = '';
    foreach ($fields as $label => $value) {
        if ($value === '' || $value === null) continue;
        $rows .= '<tr>'
            . '<td style="padding:6px 12px;border-bottom:1px solid #eee;color:#666;font-weight:600;">' . h($label) . '</td>'
            . '<td style="padding:6px 12px;border-bottom:1px solid #eee;">' . h((string)$value) . '</td>'
            . '</tr>';
    }
    return '<div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;border:1px solid #ddd;border-radius:8px;">'
        . '<div style="background:#0d6efd;color:#fff;padding:15px;"><h2>New Moving Lead</h2></div>'
        . '<table style="width:100%;border-collapse:collapse;font-size:14px;">' . $rows . '</table></div>';
}

function zm_send_email(string $to, string $subject, string $html): string {
    $host = defined('SMTP_HOST') ? SMTP_HOST : '';
    $port = defined('SMTP_PORT') ? (int)SMTP_PORT : 587;
    $enc  = defined('SMTP_ENCRYPTION') ? strtolower(SMTP_ENCRYPTION) : 'tls';

    if ($host === '') return 'SMTP is not configured.';

    $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]]);
    $socket = ($enc === 'ssl' || $enc === 'smtps')
        ? @stream_socket_client("ssl://{$host}:{$port}", $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx)
        : @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 15);

    if (!$socket) return "Connection failed: {$errstr}";

    $read = function () use ($socket): string { return (string)fgets($socket, 1024); };
    $send = function (string $cmd) use ($socket): void { fwrite($socket, $cmd . "\r\n"); };

    $read(); $send('EHLO ' . (gethostname() ?: 'localhost')); while ($line = $read()) { if ($line[3] === ' ') break; }
    if ($enc === 'tls') {
        $send('STARTTLS'); $read(); stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $send('EHLO ' . (gethostname() ?: 'localhost')); while ($line = $read()) { if ($line[3] === ' ') break; }
    }
    $send('AUTH LOGIN'); $read(); $send(base64_encode(SMTP_USERNAME)); $read(); $send(base64_encode(SMTP_PASSWORD));
    if (substr(trim($read()), 0, 3) !== '235') { fclose($socket); return 'SMTP auth failed.'; }

    $send('MAIL FROM:<' . SMTP_FROM_EMAIL . '>'); $read();
    $send('RCPT TO:<' . $to . '>'); $read(); $send('DATA'); $read();

    $boundary = md5(uniqid('zm', true));
    $plain = strip_tags(str_replace(['<br>', '<br/>'], "\n", $html));
    $msg = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n"
        . "To: <{$to}>\r\n"
        . "Subject: {$subject}\r\n"
        . "MIME-Version: 1.0\r\n"
        . "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n\r\n"
        . "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n{$plain}\r\n\r\n"
        . "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n{$html}\r\n\r\n"
        . "--{$boundary}--";

    $send($msg); $send('.'); $read(); $send('QUIT'); fclose($socket);
    return '';
}

// Country List
$countries = [
    'CA' => 'Canada', 'US' => 'United States', 'GB' => 'United Kingdom', 'AU' => 'Australia',
    'FR' => 'France', 'DE' => 'Germany', 'NL' => 'Netherlands', 'ES' => 'Spain',
    'IT' => 'Italy', 'PT' => 'Portugal', 'CH' => 'Switzerland', 'AE' => 'UAE',
    'SG' => 'Singapore', 'NZ' => 'New Zealand', 'ZA' => 'South Africa', 'IE' => 'Ireland',
];

// 5. Form Processing
$success    = false;
$error      = '';
$posted     = [];
$resultNote = '';
$submittedFirstName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p  = function($k) use (&$posted) { $posted[$k] = trim($_POST[$k] ?? ''); return $posted[$k]; };
    $pi = function($k) use (&$posted) { $posted[$k] = (int)($_POST[$k] ?? 0); return (int)$posted[$k]; };

    if (!zm_verify_turnstile($p('cf-turnstile-response'))) $error = t('err_captcha');

    $move_type = $p('move_type');
    $isIntl    = ($move_type === 'international');

    $first_name = $p('first_name'); $last_name = $p('last_name');
    $email = $p('email'); $phone = $p('phone'); $move_date = $p('move_date'); $notes = $p('notes');

    // Domestic
    $from_city = $p('from_city'); $from_province = $p('from_province');
    $to_city   = $p('to_city');   $to_province   = $p('to_province'); $home_size = $p('home_size');

    // International
    $street_from  = $p('street_from');  $zipcode_from = $p('zipcode_from');
    $city_from    = $p('city_from');    $country_from = strtoupper($p('country_from'));
    $city_to      = $p('city_to');      $country_to   = strtoupper($p('country_to'));
    $moving_size  = $pi('moving_size'); $volume       = $pi('volume'); $bedrooms = $p('bedrooms');
    $storage      = $pi('storage');     $packing      = $pi('packing'); $assembly = $pi('assembly');

    // Validation
    if ($error === '') {
        if ($first_name === '' || $last_name === '')      $error = t('err_name');
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = t('err_email');
        elseif ($phone === '')                              $error = t('err_phone');
        elseif ($isIntl) {
            if ($street_from === '' || $city_from === '' || $city_to === '' || $volume <= 0)
                $error = t('err_intl_fields');
            elseif (strlen($country_from) !== 2 || strlen($country_to) !== 2)
                $error = t('err_countries');
        }
    }

    if ($error === '') {
        $mode = defined('LEAD_MODE') ? LEAD_MODE : 'sell_all';

        $sendToApi = in_array($mode, ['sell_all', 'both']) || ($mode === 'sell_international' && $isIntl);
        $sendEmail = in_array($mode, ['email_only', 'both']);

        $apiOk = false; $apiErrorMsg = '';
        $emailOk = false; $emailErrorMsg = '';

        // --- API submission ---
        if ($sendToApi) {
            if ($isIntl) {
                $payload = [
                    'first_name'   => $first_name, 'family_name'  => $last_name,
                    'email'        => $email,       'telephone1'   => $phone,
                    'street_from'  => $street_from, 'zipcode_from' => $zipcode_from,
                    'city_from'    => $city_from,   'country_from' => $country_from,
                    'city_to'      => $city_to,     'country_to'   => $country_to,
                    'moving_date'  => $move_date,   'moving_size'  => $moving_size,
                    'volume'       => $volume,       'bedrooms'     => $bedrooms !== '' ? (int)$bedrooms : null,
                    'storage'      => $storage,      'packing'      => $packing,
                    'assembly'     => $assembly,     'remarks'      => $notes,
                    'source'       => SITE_NAME,
                ];
                [$ok, $data] = zm_api_post('leads-international.php', array_filter($payload, fn($v) => $v !== null && $v !== ''));
            } else {
                $payload = [
                    'first_name'    => $first_name, 'last_name'     => $last_name,
                    'email'         => $email,       'phone'         => $phone,
                    'from_city'     => $from_city,   'from_province' => $from_province,
                    'to_city'       => $to_city,     'to_province'   => $to_province,
                    'move_type'     => $move_type,   'move_date'     => $move_date,
                    'home_size'     => $home_size,   'special_items' => $notes,
                    'source'        => SITE_NAME,
                ];
                [$ok, $data] = zm_api_post('leads.php', array_filter($payload, fn($v) => $v !== ''));
            }
            if ($ok) {
                $apiOk = true;
            } else {
                $errFields   = implode(', ', (array)($data['error']['fields'] ?? []));
                $apiErrorMsg = 'API: ' . ($data['error']['message'] ?? 'Unknown') . ($errFields ? " ({$errFields})" : '');
            }
        }

        // --- Email notification ---
        if ($sendEmail) {
            $emailFields = [
                'Name'      => $first_name . ' ' . $last_name,
                'Email'     => $email,
                'Phone'     => $phone,
                'Move Type' => ucfirst($move_type),
                'Move Date' => $move_date,
                'From'      => $isIntl ? "{$street_from}, {$city_from}, {$country_from}" : "{$from_city}, {$from_province}",
                'To'        => $isIntl ? "{$city_to}, {$country_to}" : "{$to_city}, {$to_province}",
                'Notes'     => $notes,
            ];
            $e = zm_send_email(SMTP_TO_EMAIL, "New Moving Lead — {$first_name} {$last_name}", zm_build_lead_email($emailFields));
            if ($e === '') $emailOk = true;
            else $emailErrorMsg = $e;
        }

        // --- Determine outcome ---
        if ($mode === 'both') {
            if ($apiOk || $emailOk) {
                $success    = true;
                $resultNote = $apiOk ? ($emailOk ? t('note_both') : t('note_movers')) : t('note_specialist');
            } else {
                $error = implode(' | ', array_filter([$apiErrorMsg, $emailErrorMsg]));
            }
        } elseif ($sendToApi) {
            if ($apiOk) { $success = true; $resultNote = t('note_movers'); }
            else { $error = $apiErrorMsg; }
        } else {
            if ($emailOk) { $success = true; $resultNote = t('note_specialist'); }
            else { $error = 'Email error: ' . $emailErrorMsg; }
        }

        if ($success) {
            $submittedFirstName = $first_name;
            $posted = [];
        }
    }
}

// 6. Dynamic Theming
$brandColor = defined('BRAND_COLOR') && BRAND_COLOR !== '' ? BRAND_COLOR : '#0d6efd';
function adjustBrightness($hex, $steps) {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) $hex = str_repeat(substr($hex,0,1),2).str_repeat(substr($hex,1,1),2).str_repeat(substr($hex,2,1),2);
    $r = max(0,min(255,hexdec(substr($hex,0,2))+$steps));
    $g = max(0,min(255,hexdec(substr($hex,2,2))+$steps));
    $b = max(0,min(255,hexdec(substr($hex,4,2))+$steps));
    return '#'.str_pad(dechex($r),2,'0',STR_PAD_LEFT).str_pad(dechex($g),2,'0',STR_PAD_LEFT).str_pad(dechex($b),2,'0',STR_PAD_LEFT);
}
$brandHover = adjustBrightness($brandColor, -20);
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h(t('page_title')) ?> — <?= h(SITE_NAME) ?></title>

<?php if (defined('BRAND_FAVICON') && BRAND_FAVICON): ?>
<link rel="icon" href="<?= h(BRAND_FAVICON) ?>">
<?php endif; ?>

<?php if (defined('SOCIAL_ENABLE') && SOCIAL_ENABLE): ?>
<meta property="og:title" content="<?= h(defined('SOCIAL_TITLE') && SOCIAL_TITLE ? SOCIAL_TITLE : t('page_title') . ' — ' . SITE_NAME) ?>">
<meta property="og:description" content="<?= h(defined('SOCIAL_DESC') ? SOCIAL_DESC : t('hero_sub')) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= h(defined('SITE_URL') ? SITE_URL : '') ?>">
<?php if (defined('SOCIAL_IMAGE') && SOCIAL_IMAGE): ?>
<meta property="og:image" content="<?= h(SOCIAL_IMAGE) ?>">
<?php endif; ?>
<?php endif; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<?php if (defined('TURNSTILE_SITE_KEY') && TURNSTILE_SITE_KEY): ?>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php endif; ?>
<style>
  :root { --zm-primary: <?= h($brandColor) ?>; --zm-primary-hover: <?= h($brandHover) ?>; }
  body { background: #f8f9fa; }
  .navbar-brand { font-weight: 800; color: var(--zm-primary) !important; }
  .btn-primary { background-color: var(--zm-primary); border-color: var(--zm-primary); }
  .btn-primary:hover { background-color: var(--zm-primary-hover); border-color: var(--zm-primary-hover); }
  .btn-outline-primary { color: var(--zm-primary); border-color: var(--zm-primary); }
  .btn-outline-primary:hover, .btn-check:checked+.btn-outline-primary { background-color: var(--zm-primary); color: #fff; }
  .hero { background: linear-gradient(135deg, var(--zm-primary) 0%, #111 150%); color: #fff; padding: 60px 0 40px; }
  .hero h1 { font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 800; letter-spacing: -0.02em; }
  .form-card { border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,.08); margin-top: -20px; }
  .section-divider { font-size:.7rem; text-transform:uppercase; letter-spacing:.1em; color:#6c757d; font-weight:700; border-bottom:2px solid #f1f3f5; padding-bottom:8px; margin-bottom:20px; margin-top:30px; }
  .form-control, .form-select { background-color: #f8f9fa; }
  .form-control:focus, .form-select:focus { background-color: #fff; border-color: var(--zm-primary); box-shadow: 0 0 0 0.25rem rgba(13,110,253,.15); }
  .brand-logo { max-height: 40px; width: auto; }
  .lang-toggle { font-size: .8rem; font-weight: 600; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="?">
      <?php if (defined('BRAND_LOGO') && BRAND_LOGO): ?>
        <img src="<?= h(BRAND_LOGO) ?>" alt="<?= h(SITE_NAME) ?>" class="brand-logo">
      <?php else: ?>
        <i class="bi bi-box-seam me-2"></i><?= h(SITE_NAME) ?>
      <?php endif; ?>
    </a>
    <?php if ($langEnabled): ?>
    <a href="?lang=<?= h($langAlt) ?>" class="btn btn-outline-secondary btn-sm lang-toggle ms-auto">
      <i class="bi bi-translate me-1"></i><?= h(t('lang_label')) ?>
    </a>
    <?php endif; ?>
  </div>
</nav>

<div class="hero text-center">
  <div class="container pb-4">
    <h1><?= h(t('hero_h1')) ?></h1>
    <p class="lead opacity-75"><?= h(t('hero_sub')) ?></p>
  </div>
</div>

<div class="container pb-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">

      <?php if ($success): ?>
        <div class="card form-card p-5 text-center">
          <div class="mb-3" style="font-size:4rem;color:var(--zm-primary);"><i class="bi bi-check-circle-fill"></i></div>
          <h2 class="fw-bold mb-2"><?= h(t('thank_you'))?><?= $submittedFirstName !== '' ? ', ' . h($submittedFirstName) : '' ?>!</h2>
          <p class="text-muted mb-4"><?= h($resultNote) ?></p>
          <a href="?" class="btn btn-primary px-4 py-2"><i class="bi bi-arrow-left me-2"></i><?= h(t('return_home')) ?></a>
        </div>
      <?php else: ?>
        <div class="card form-card p-4 p-md-5">
          <h3 class="fw-bold mb-1"><?= h(t('form_title')) ?></h3>
          <p class="text-muted small mb-4"><?= h(t('form_sub')) ?></p>

          <?php if ($error): ?>
            <div class="alert alert-danger small fw-semibold"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= h($error) ?></div>
          <?php endif; ?>

          <form method="POST" action="" id="quoteForm">
            <div class="section-divider mt-0"><?= h(t('sec_move_type')) ?></div>
            <div class="btn-group w-100 mb-2" role="group">
              <?php foreach (['local' => t('move_local'), 'long-distance' => t('move_long'), 'international' => t('move_intl')] as $val => $label): ?>
                <input type="radio" class="btn-check" name="move_type" id="mt_<?= $val ?>" value="<?= $val ?>" <?= (($posted['move_type'] ?? 'local') === $val) ? 'checked' : '' ?>>
                <label class="btn btn-outline-primary" for="mt_<?= $val ?>"><?= h($label) ?></label>
              <?php endforeach; ?>
            </div>

            <div class="section-divider"><?= h(t('sec_details')) ?></div>
            <div class="row g-3 mb-3">
              <div class="col-sm-6">
                <label class="form-label small fw-semibold"><?= h(t('first_name')) ?> <span class="text-danger">*</span></label>
                <input type="text" name="first_name" class="form-control" value="<?= h($posted['first_name'] ?? '') ?>" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label small fw-semibold"><?= h(t('last_name')) ?> <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control" value="<?= h($posted['last_name'] ?? '') ?>" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label small fw-semibold"><?= h(t('email')) ?> <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="<?= h($posted['email'] ?? '') ?>" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label small fw-semibold"><?= h(t('phone')) ?> <span class="text-danger">*</span></label>
                <input type="tel" name="phone" class="form-control" value="<?= h($posted['phone'] ?? '') ?>" required>
              </div>
            </div>

            <!-- Domestic fields -->
            <div id="domFields">
              <div class="section-divider"><?= h(t('sec_move_details')) ?></div>
              <div class="row g-3 mb-3">
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('from_city')) ?> <span class="text-danger">*</span></label>
                  <input type="text" name="from_city" class="form-control" value="<?= h($posted['from_city'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('prov_state')) ?></label>
                  <input type="text" name="from_province" class="form-control" value="<?= h($posted['from_province'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('to_city')) ?> <span class="text-danger">*</span></label>
                  <input type="text" name="to_city" class="form-control" value="<?= h($posted['to_city'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('prov_state')) ?></label>
                  <input type="text" name="to_province" class="form-control" value="<?= h($posted['to_province'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('home_size')) ?></label>
                  <select name="home_size" class="form-select">
                    <option value=""><?= h(t('select')) ?></option>
                    <option value="1-bedroom" <?= ($posted['home_size'] ?? '') == '1-bedroom' ? 'selected' : '' ?>><?= h(t('1bed_dom')) ?></option>
                    <option value="2-bedroom" <?= ($posted['home_size'] ?? '') == '2-bedroom' ? 'selected' : '' ?>><?= h(t('2bed_dom')) ?></option>
                    <option value="3-bedroom" <?= ($posted['home_size'] ?? '') == '3-bedroom' ? 'selected' : '' ?>><?= h(t('3bed_dom')) ?></option>
                    <option value="4-bedroom" <?= ($posted['home_size'] ?? '') == '4-bedroom' ? 'selected' : '' ?>><?= h(t('4bed_dom')) ?></option>
                  </select>
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('move_date_dom')) ?></label>
                  <input type="date" name="move_date" class="form-control" value="<?= h($posted['move_date'] ?? '') ?>">
                </div>
              </div>
            </div>

            <!-- International fields -->
            <div id="intlFields" style="display:none;">
              <div class="section-divider"><?= h(t('sec_origin')) ?></div>
              <div class="row g-3 mb-3">
                <div class="col-12">
                  <label class="form-label small fw-semibold"><?= h(t('street')) ?> <span class="text-danger">*</span></label>
                  <input type="text" name="street_from" class="form-control" value="<?= h($posted['street_from'] ?? '') ?>">
                </div>
                <div class="col-sm-5">
                  <label class="form-label small fw-semibold"><?= h(t('city')) ?> <span class="text-danger">*</span></label>
                  <input type="text" name="city_from" class="form-control" value="<?= h($posted['city_from'] ?? '') ?>">
                </div>
                <div class="col-sm-3">
                  <label class="form-label small fw-semibold"><?= h(t('zip')) ?> <span class="text-danger">*</span></label>
                  <input type="text" name="zipcode_from" class="form-control" value="<?= h($posted['zipcode_from'] ?? '') ?>">
                </div>
                <div class="col-sm-4">
                  <label class="form-label small fw-semibold"><?= h(t('country')) ?> <span class="text-danger">*</span></label>
                  <select name="country_from" class="form-select">
                    <option value=""><?= h(t('select')) ?></option>
                    <?php foreach ($countries as $code => $name): ?>
                      <option value="<?= $code ?>" <?= (($posted['country_from'] ?? '') === $code) ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="section-divider"><?= h(t('sec_destination')) ?></div>
              <div class="row g-3 mb-3">
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('city')) ?> <span class="text-danger">*</span></label>
                  <input type="text" name="city_to" class="form-control" value="<?= h($posted['city_to'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('country')) ?> <span class="text-danger">*</span></label>
                  <select name="country_to" class="form-select">
                    <option value=""><?= h(t('select')) ?></option>
                    <?php foreach ($countries as $code => $name): ?>
                      <option value="<?= $code ?>" <?= (($posted['country_to'] ?? '') === $code) ? 'selected' : '' ?>><?= h($name) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="section-divider"><?= h(t('sec_move_details')) ?></div>
              <div class="row g-3 mb-3">
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('move_date_intl')) ?> <span class="text-danger">*</span></label>
                  <input type="date" name="move_date" class="form-control" value="<?= h($posted['move_date'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('volume')) ?> <span class="text-danger">*</span></label>
                  <input type="number" name="volume" class="form-control" min="1" value="<?= h($posted['volume'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('move_size')) ?></label>
                  <select name="moving_size" class="form-select">
                    <option value="0"><?= h(t('select')) ?></option>
                    <option value="1" <?= ($posted['moving_size'] ?? '') == '1' ? 'selected' : '' ?>><?= h(t('boxes')) ?></option>
                    <option value="3" <?= ($posted['moving_size'] ?? '') == '3' ? 'selected' : '' ?>><?= h(t('1bed_intl')) ?></option>
                    <option value="5" <?= ($posted['moving_size'] ?? '') == '5' ? 'selected' : '' ?>><?= h(t('3bed_intl')) ?></option>
                  </select>
                </div>
                <div class="col-sm-6">
                  <label class="form-label small fw-semibold"><?= h(t('bedrooms')) ?></label>
                  <input type="number" name="bedrooms" class="form-control" value="<?= h($posted['bedrooms'] ?? '') ?>">
                </div>
                <div class="col-12">
                  <div class="d-flex flex-wrap gap-3">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="storage"  value="1" <?= !empty($posted['storage'])  ? 'checked' : '' ?> id="chk_storage"> <label class="form-check-label small" for="chk_storage"><?=  h(t('storage'))  ?></label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="packing"  value="1" <?= !empty($posted['packing'])  ? 'checked' : '' ?> id="chk_packing"> <label class="form-check-label small" for="chk_packing"><?=  h(t('packing'))  ?></label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="assembly" value="1" <?= !empty($posted['assembly']) ? 'checked' : '' ?> id="chk_assembly"><label class="form-check-label small" for="chk_assembly"><?= h(t('assembly')) ?></label></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-4 mt-3">
              <label class="form-label small fw-semibold"><?= h(t('notes')) ?></label>
              <textarea name="notes" class="form-control" rows="2"><?= h($posted['notes'] ?? '') ?></textarea>
            </div>

            <?php if (defined('TURNSTILE_SITE_KEY') && TURNSTILE_SITE_KEY): ?>
            <div class="cf-turnstile mb-3" data-sitekey="<?= h(TURNSTILE_SITE_KEY) ?>"></div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary w-100 py-3 mt-2 fw-bold text-uppercase" style="letter-spacing:.05em;">
              <i class="bi bi-send-fill me-2"></i><?= h(t('submit')) ?>
            </button>
            <p class="text-center text-muted mt-3 mb-0" style="font-size:.7rem;"><?= h(t('privacy')) ?></p>
          </form>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script>
(function () {
  const domFields  = document.getElementById('domFields');
  const intlFields = document.getElementById('intlFields');
  const radios     = document.querySelectorAll('input[name="move_type"]');

  function toggleFields() {
    const isIntl = document.querySelector('input[name="move_type"]:checked')?.value === 'international';
    domFields.style.display  = isIntl ? 'none' : 'block';
    intlFields.style.display = isIntl ? 'block' : 'none';

    domFields.querySelectorAll('input, select').forEach(el => {
      if (el.name === 'from_city' || el.name === 'to_city') el.required = !isIntl;
    });
    intlFields.querySelectorAll('input, select').forEach(el => {
      if (['street_from','city_from','zipcode_from','country_from','city_to','country_to','volume'].includes(el.name)) el.required = isIntl;
    });
  }

  radios.forEach(r => r.addEventListener('change', toggleFields));
  toggleFields();
})();
</script>
</body>
</html>
