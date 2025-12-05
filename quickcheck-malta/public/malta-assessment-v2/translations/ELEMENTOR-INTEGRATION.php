<?php
/**
 * Malta Assessment - Elementor Integration
 *
 * Add this code to your theme's functions.php or to a custom plugin.
 * This code injects the language variable into the page so that
 * the Malta Assessment can detect the correct language when embedded
 * directly in Elementor.
 *
 * @package Malta_Assessment
 * @version 1.0
 */

/**
 * Inject language variables into pages that contain Malta Assessment
 *
 * This function runs on wp_head and checks if the current page
 * is in a language-specific URL path (/de/, /en/, /nl/).
 * It then injects JavaScript variables that the Malta Assessment
 * can use to detect the correct language.
 */
function malta_assessment_inject_language_vars() {
    // Get current URL path
    $current_path = $_SERVER['REQUEST_URI'] ?? '';

    // Detect language from URL
    $language = 'de'; // Default fallback

    if (strpos($current_path, '/en/') !== false) {
        $language = 'en';
    } elseif (strpos($current_path, '/nl/') !== false) {
        $language = 'nl';
    } elseif (strpos($current_path, '/de/') !== false) {
        $language = 'de';
    }

    // Alternative: Use WPML/Polylang if installed
    // Uncomment these lines if you use WPML or Polylang:
    /*
    if (function_exists('pll_current_language')) {
        $language = pll_current_language(); // Polylang
    } elseif (function_exists('icl_get_current_language')) {
        $language = icl_get_current_language(); // WPML
    }
    */

    // Get WordPress uploads directory URL
    $upload_dir = wp_upload_dir();
    $translations_path = $upload_dir['baseurl'] . '/malta-assessment-v2/translations';

    // Inject JavaScript variables
    ?>
    <script>
    // Set language for Malta Assessment
    window.qcMaltaLanguage = '<?php echo esc_js($language); ?>';

    // Set translations path
    window.qcMaltaTranslationsPath = '<?php echo esc_js($translations_path); ?>';

    console.log('🌐 WordPress detected language:', window.qcMaltaLanguage);
    </script>
    <?php
}
add_action('wp_head', 'malta_assessment_inject_language_vars');


/**
 * Alternative Method: Inject language via Elementor HTML Widget
 *
 * If you want more control, you can manually add this HTML
 * BEFORE embedding the Malta Assessment in your Elementor HTML widget:
 *
 * <script>
 * window.qcMaltaLanguage = 'en'; // Change to: 'de', 'en', or 'nl'
 * </script>
 *
 * Then add your Malta Assessment HTML below.
 */


/**
 * AJAX Handler Extension: Save language field with submission
 *
 * Add this to your existing AJAX handler to capture the language
 * when the user submits the contact form.
 */
add_action('wp_ajax_nopriv_submit_malta_assessment', 'handle_malta_assessment_submission');
add_action('wp_ajax_submit_malta_assessment', 'handle_malta_assessment_submission');

function handle_malta_assessment_submission() {
    // Check nonce (adjust nonce name to match your existing handler)
    check_ajax_referer('malta_assessment_nonce', 'nonce');

    // Sanitize all input data
    $data = [
        'geschlecht' => sanitize_text_field($_POST['geschlecht'] ?? ''),
        'vorname' => sanitize_text_field($_POST['vorname'] ?? ''),
        'nachname' => sanitize_text_field($_POST['nachname'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'telefon' => sanitize_text_field($_POST['telefon'] ?? ''),
        'unternehmen' => sanitize_text_field($_POST['unternehmen'] ?? ''),
        'language' => sanitize_text_field($_POST['language'] ?? 'de'), // ← NEW: Language field
        'answers' => json_decode(stripslashes($_POST['answers'] ?? '{}'), true),
        'score' => absint($_POST['score'] ?? 0),
        'percentage' => floatval($_POST['percentage'] ?? 0),
    ];

    // Validate required fields
    if (empty($data['email']) || empty($data['vorname']) || empty($data['nachname'])) {
        wp_send_json_error([
            'message' => 'Please fill in all required fields.'
        ]);
    }

    // Process the submission (send to CRM, save to database, etc.)
    // ... your existing logic here ...

    // Example: Send to webhook
    $webhook_url = 'https://your-crm-webhook.com/endpoint';
    $response = wp_remote_post($webhook_url, [
        'body' => json_encode($data),
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error([
            'message' => 'Failed to submit. Please try again.'
        ]);
    }

    wp_send_json_success([
        'message' => 'Submission successful!',
        'language' => $data['language'],
    ]);
}


/**
 * Optional: Cache Exclusion for WP Rocket
 *
 * Exclude translation JSON files from aggressive caching
 * to allow quick updates without cache busting.
 */
add_filter('rocket_cache_reject_uri', function($urls) {
    $urls[] = '/wp-content/uploads/malta-assessment-v2/translations/(.*)';
    return $urls;
});


/**
 * Optional: Set proper MIME type for JSON files
 *
 * Ensures that .json files are served with correct Content-Type header.
 */
add_filter('upload_mimes', function($mimes) {
    $mimes['json'] = 'application/json';
    return $mimes;
});
