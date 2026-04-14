<?php
// =============================================================================
//  Zen Move API Demo — Installation Wizard
// =============================================================================

$configFile = __DIR__ . '/config.php';
$lockFile   = __DIR__ . '/installed.lock';

// 1. Security Lockdown: If already installed, block access immediately.
if (file_exists($configFile) || file_exists($lockFile)) {
    http_response_code(403);
    die('<strong>Security Error:</strong> The script is already configured. To run this wizard again, you must manually delete <code>config.php</code> and <code>installed.lock</code> from your server.');
}

// 2. Pre-flight Checks
$errors = [];
$isWritable = is_writable(__DIR__);
$hasCurl    = extension_loaded('curl');
$hasJson    = extension_loaded('json');
$phpVersion = version_compare(PHP_VERSION, '7.4.0', '>=');

if (!$isWritable) {
    $errors[] = 'The current directory is not writable. Please update folder permissions (e.g., chmod 755) so the script can generate the config.php file.';
}
if (!$hasCurl) $errors[] = 'The PHP cURL extension is missing. Please enable it in your hosting control panel.';
if (!$hasJson) $errors[] = 'The PHP JSON extension is missing. Please enable it in your hosting control panel.';
if (!$phpVersion) $errors[] = 'Your PHP version is too old. Please upgrade to PHP 7.4 or higher (PHP 8.x recommended).';

$canInstall = empty($errors);

// 3. Process Form Submission
$success = false;
$setupError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canInstall) {
    // Core & API
    $siteName     = trim($_POST['site_name'] ?? '');
    $siteUrl      = rtrim(trim($_POST['site_url'] ?? ''), '/');
    $apiToken     = trim($_POST['api_token'] ?? '');
    
    // Branding
    $brandColor   = trim($_POST['brand_color'] ?? '#0d6efd');
    $brandLogo    = trim($_POST['brand_logo'] ?? '');
    $brandFavicon = trim($_POST['brand_favicon'] ?? '');

    // Social / SEO
    $socialEnable = isset($_POST['social_enable']);
    $socialTitle  = trim($_POST['social_title'] ?? '');
    $socialDesc   = trim($_POST['social_desc'] ?? '');
    $socialImg    = trim($_POST['social_image'] ?? '');

    // Routing
    $leadMode     = trim($_POST['lead_mode'] ?? 'sell_all');
    
    // SMTP
    $smtpHost     = trim($_POST['smtp_host'] ?? '');
    $smtpPort     = (int)($_POST['smtp_port'] ?? 587);
    $smtpEnc      = trim($_POST['smtp_encryption'] ?? 'tls');
    $smtpUser     = trim($_POST['smtp_username'] ?? '');
    $smtpPass     = trim($_POST['smtp_password'] ?? '');
    $smtpTo       = trim($_POST['smtp_to_email'] ?? '');

    // Security
    $turnstileSiteKey   = trim($_POST['turnstile_site_key'] ?? '');
    $turnstileSecretKey = trim($_POST['turnstile_secret_key'] ?? '');

    if ($siteName === '' || $siteUrl === '' || $apiToken === '') {
        $setupError = 'Please fill out all required core settings (Name, URL, API Token).';
    } else {
        // Generate the config.php content
        $configContent = "<?php\n"
            . "// =============================================================================\n"
            . "// Zen Move — Auto-Generated Configuration\n"
            . "// Generated on: " . date('Y-m-d H:i:s') . "\n"
            . "// =============================================================================\n\n"
            . "define('SITE_NAME', '" . addslashes($siteName) . "');\n"
            . "define('SITE_URL', '" . addslashes($siteUrl) . "');\n"
            . "define('BRAND_COLOR', '" . addslashes($brandColor) . "');\n"
            . "define('BRAND_LOGO', '" . addslashes($brandLogo) . "');\n"
            . "define('BRAND_FAVICON', '" . addslashes($brandFavicon) . "');\n\n"
            
            . "define('SOCIAL_ENABLE', " . ($socialEnable ? 'true' : 'false') . ");\n"
            . "define('SOCIAL_TITLE', '" . addslashes($socialTitle) . "');\n"
            . "define('SOCIAL_DESC', '" . addslashes($socialDesc) . "');\n"
            . "define('SOCIAL_IMAGE', '" . addslashes($socialImg) . "');\n\n"

            . "define('ZM_API_BASE', 'https://zenmove.ca/api/v1');\n"
            . "define('ZM_API_TOKEN', '" . addslashes($apiToken) . "');\n"
            . "define('ZM_API_VERIFY_SSL', true);\n\n"
            . "define('LEAD_MODE', '" . addslashes($leadMode) . "');\n\n"

            . "define('TURNSTILE_SITE_KEY', '" . addslashes($turnstileSiteKey) . "');\n"
            . "define('TURNSTILE_SECRET_KEY', '" . addslashes($turnstileSecretKey) . "');\n\n"

            . "define('SMTP_HOST', '" . addslashes($smtpHost) . "');\n"
            . "define('SMTP_PORT', {$smtpPort});\n"
            . "define('SMTP_ENCRYPTION', '" . addslashes($smtpEnc) . "');\n"
            . "define('SMTP_USERNAME', '" . addslashes($smtpUser) . "');\n"
            . "define('SMTP_PASSWORD', '" . addslashes($smtpPass) . "');\n"
            . "define('SMTP_FROM_EMAIL', '" . addslashes($smtpUser) . "');\n"
            . "define('SMTP_FROM_NAME', SITE_NAME);\n"
            . "define('SMTP_TO_EMAIL', '" . addslashes($smtpTo) . "');\n";

        // Write files
        if (file_put_contents($configFile, $configContent) !== false) {
            file_put_contents($lockFile, 'Installed on ' . date('Y-m-d H:i:s'));
            $success = true;
        } else {
            $setupError = 'Failed to write config.php. Please check file permissions.';
        }
    }
}

// Helper to safely echo variables
function h($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Zen Move Partner Script</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .hero { background: linear-gradient(135deg, #2b32b2 0%, #1488cc 100%); color: #fff; padding: 40px 0; }
        .setup-card { border: none; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); margin-top: -30px; background: #fff; padding: 2rem; }
        .section-divider { font-size: .75rem; text-transform: uppercase; letter-spacing: .1em; color: #6c757d; font-weight: 700; border-bottom: 2px solid #f1f3f5; padding-bottom: 8px; margin-bottom: 20px; margin-top: 30px;}
        .color-preview { width: 38px; height: 38px; border-radius: 6px; border: 1px solid #dee2e6; cursor: pointer; padding: 0; }
        .optional-badge { font-size: 0.65rem; background: #e9ecef; color: #6c757d; padding: 2px 6px; border-radius: 4px; vertical-align: middle; margin-left: 6px; font-weight: normal; }
    </style>
</head>
<body>

<div class="hero text-center">
    <div class="container">
        <h1 class="h3 fw-bold"><i class="bi bi-box-seam me-2"></i>Zen Move Partner Setup</h1>
        <p class="opacity-75 mb-4">Let's get your moving lead portal online.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="setup-card">
                
                <?php if ($success): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h2 class="mt-3 fw-bold">Installation Complete!</h2>
                        <p class="text-muted mb-4">Your configuration has been saved and the setup directory is now locked for your security.</p>
                        <a href="index.php" class="btn btn-primary px-4 py-2 fw-semibold">Go to Your Site <i class="bi bi-arrow-right ms-2"></i></a>
                    </div>
                <?php else: ?>

                    <?php if (!$canInstall): ?>
                        <div class="alert alert-danger">
                            <h5 class="alert-heading"><i class="bi bi-exclamation-octagon-fill me-2"></i>Server Requirements Not Met</h5>
                            <p class="mb-2">Your server is missing some required features to run this script:</p>
                            <ul class="mb-0">
                                <?php foreach ($errors as $err): ?>
                                    <li><?= $err ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <hr>
                            <p class="mb-0 small">Please resolve these issues and refresh the page.</p>
                        </div>
                    <?php else: ?>

                        <?php if ($setupError): ?>
                            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= h($setupError) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="section-divider mt-0">1. Core Settings</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="site_name" class="form-control" required placeholder="e.g. Apex Moving Group" value="<?= h($_POST['site_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Website URL <span class="text-danger">*</span></label>
                                    <input type="url" name="site_url" class="form-control" required placeholder="https://..." value="<?= h($_POST['site_url'] ?? 'https://' . $_SERVER['HTTP_HOST']) ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold">Zen Move API Token <span class="text-danger">*</span></label>
                                    <input type="text" name="api_token" class="form-control text-monospace" required placeholder="Paste your API token here" value="<?= h($_POST['api_token'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="section-divider">2. Branding & Assets</div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold">Brand Primary Color</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" name="brand_color" class="form-control color-preview" value="<?= h($_POST['brand_color'] ?? '#0d6efd') ?>">
                                        <span class="text-muted small">Themes your buttons and headers.</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Logo URL <span class="optional-badge">Optional</span></label>
                                    <input type="text" name="brand_logo" class="form-control" placeholder="https://.../logo.png" value="<?= h($_POST['brand_logo'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Favicon URL <span class="optional-badge">Optional</span></label>
                                    <input type="text" name="brand_favicon" class="form-control" placeholder="https://.../favicon.ico" value="<?= h($_POST['brand_favicon'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="section-divider">3. Lead Routing & Email Config</div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Routing Strategy</label>
                                <select name="lead_mode" class="form-select" id="leadModeSelect">
                                    <option value="sell_all" <?= ($_POST['lead_mode'] ?? '') === 'sell_all' ? 'selected' : '' ?>>Marketplace Only (Send all leads to Zen Move)</option>
                                    <option value="sell_international" <?= ($_POST['lead_mode'] ?? '') === 'sell_international' ? 'selected' : '' ?>>Hybrid (Intl to Marketplace, Domestic to Email)</option>
                                    <option value="email_only" <?= ($_POST['lead_mode'] ?? '') === 'email_only' ? 'selected' : '' ?>>Email Only (Send all leads to my inbox)</option>
                                </select>
                            </div>

                            <div id="smtpSettings" class="bg-light p-3 border rounded" style="display: <?= in_array(($_POST['lead_mode'] ?? ''), ['sell_international', 'email_only']) ? 'block' : 'none' ?>;">
                                <div class="alert alert-info small py-2 mb-3"><i class="bi bi-info-circle me-1"></i> SMTP is required for your selected routing mode to send emails.</div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-semibold">Receive Leads At</label>
                                        <input type="email" name="smtp_to_email" class="form-control" placeholder="leads@yourdomain.com" value="<?= h($_POST['smtp_to_email'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label small fw-semibold">SMTP Host</label>
                                        <input type="text" name="smtp_host" class="form-control" placeholder="smtp.yourdomain.com" value="<?= h($_POST['smtp_host'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Port</label>
                                        <input type="number" name="smtp_port" class="form-control" placeholder="587" value="<?= h($_POST['smtp_port'] ?? '587') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">SMTP Username</label>
                                        <input type="text" name="smtp_username" class="form-control" value="<?= h($_POST['smtp_username'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">SMTP Password</label>
                                        <input type="password" name="smtp_password" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Encryption</label>
                                        <select name="smtp_encryption" class="form-select">
                                            <option value="tls" <?= ($_POST['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                            <option value="ssl" <?= ($_POST['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                            <option value="" <?= ($_POST['smtp_encryption'] ?? '') === '' ? 'selected' : '' ?>>None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider">4. Security (Cloudflare Turnstile)</div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Site Key <span class="optional-badge">Optional</span></label>
                                    <input type="text" name="turnstile_site_key" class="form-control" placeholder="0x4AAAAAA..." value="<?= h($_POST['turnstile_site_key'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Secret Key <span class="optional-badge">Optional</span></label>
                                    <input type="password" name="turnstile_secret_key" class="form-control" placeholder="Your Turnstile Secret Key">
                                </div>
                                <div class="col-12 mt-1">
                                    <div class="form-text small">Leave these blank to disable the captcha. Get free keys at <a href="https://dash.cloudflare.com/" target="_blank">cloudflare.com</a>.</div>
                                </div>
                            </div>

                            <div class="section-divider">5. Social Media & SEO</div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="socialToggle" name="social_enable" <?= isset($_POST['social_enable']) ? 'checked' : '' ?>>
                                <label class="form-check-label small fw-semibold" for="socialToggle">Enable Social Sharing Cards (Open Graph)</label>
                            </div>

                            <div id="socialSettings" class="bg-light p-3 border rounded" style="display: <?= isset($_POST['social_enable']) ? 'block' : 'none' ?>;">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small fw-semibold">Social Title</label>
                                        <input type="text" name="social_title" class="form-control" placeholder="e.g. Get a Free Moving Quote" value="<?= h($_POST['social_title'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-semibold">Social Description</label>
                                        <textarea name="social_desc" class="form-control" rows="2" placeholder="e.g. Fast, reliable moving quotes from top-rated movers..."><?= h($_POST['social_desc'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-semibold">Sharing Image URL</label>
                                        <input type="text" name="social_image" class="form-control" placeholder="https://.../social-banner.jpg" value="<?= h($_POST['social_image'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold text-uppercase" style="letter-spacing: .05em;">
                                    Install & Configure Site
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle SMTP settings
    const leadSelect = document.getElementById('leadModeSelect');
    const smtpDiv = document.getElementById('smtpSettings');
    
    if (leadSelect && smtpDiv) {
        leadSelect.addEventListener('change', function() {
            smtpDiv.style.display = (this.value === 'sell_international' || this.value === 'email_only') ? 'block' : 'none';
        });
    }

    // Toggle Social Settings
    const socialToggle = document.getElementById('socialToggle');
    const socialDiv = document.getElementById('socialSettings');

    if (socialToggle && socialDiv) {
        socialToggle.addEventListener('change', function() {
            socialDiv.style.display = this.checked ? 'block' : 'none';
        });
    }
});
</script>
</body>
</html>