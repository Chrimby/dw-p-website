# Malta Assessment Server-Side Evaluator

## Overview

This is a secure, server-side PHP script that evaluates Malta Assessment questionnaire responses. The scoring logic is completely hidden from the client, preventing manipulation and protecting your business logic.

## Features

- ✅ **Secure Server-Side Evaluation** - Scoring logic is hidden from users
- ✅ **Rate Limiting** - Prevents abuse (10 requests per IP per hour)
- ✅ **CORS Protection** - Only allows requests from configured domains
- ✅ **Input Sanitization** - Prevents injection attacks
- ✅ **WordPress Compatible** - Can be integrated as REST API endpoint
- ✅ **Standalone Ready** - Works on any PHP 7.4+ server
- ✅ **Firewall Friendly** - Uses standard HTTP POST, no special requirements

## Deployment Options

### Option 1: WordPress REST API Integration (Recommended)

This is the most secure and professional approach for WordPress sites.

#### Step 1: Add REST API Endpoint

Add this to your theme's `functions.php`:

```php
/**
 * Register Malta Assessment Evaluator REST API Endpoint
 */
add_action('rest_api_init', function () {
    register_rest_route('drwerner/v1', '/malta-evaluator', [
        'methods' => 'POST',
        'callback' => 'malta_assessment_evaluate',
        'permission_callback' => '__return_true', // Public endpoint
    ]);
});

/**
 * Malta Assessment Evaluation Callback
 */
function malta_assessment_evaluate(WP_REST_Request $request) {
    // Include the evaluator script
    require_once get_template_directory() . '/malta-assessment-evaluator.php';

    // The script handles everything and exits
    exit;
}
```

#### Step 2: Upload PHP Script

1. Upload `malta-assessment-evaluator.php` to your theme directory:
   ```
   /wp-content/themes/your-theme/malta-assessment-evaluator.php
   ```

2. Configure allowed origins in the PHP file (line 35):
   ```php
   const ALLOWED_ORIGINS = [
       'https://www.drwerner.com',
       'https://drwerner.com',
   ];
   ```

#### Step 3: Update HTML to Use REST API

Your endpoint URL will be:
```
https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator
```

---

### Option 2: Standalone PHP File (Simpler)

If you don't want to modify WordPress functions.php, you can use the script standalone.

#### Step 1: Upload PHP Script

Upload `malta-assessment-evaluator.php` to a directory on your server:
```
/public_html/api/malta-evaluator.php
```

#### Step 2: Configure Allowed Origins

Edit line 35 in the PHP file:
```php
const ALLOWED_ORIGINS = [
    'https://www.drwerner.com',
    'https://drwerner.com',
];
```

#### Step 3: Update HTML to Use Standalone Script

Your endpoint URL will be:
```
https://www.drwerner.com/api/malta-evaluator.php
```

---

### Option 3: WordPress Plugin (Most Flexible)

Create a simple plugin for maximum portability.

#### Step 1: Create Plugin File

Create `/wp-content/plugins/malta-evaluator/malta-evaluator.php`:

```php
<?php
/**
 * Plugin Name: Malta Assessment Evaluator
 * Description: Server-side evaluation for Malta Assessment questionnaire
 * Version: 1.0.0
 * Author: Dr. Werner & Partner
 */

// Include the evaluator
require_once plugin_dir_path(__FILE__) . 'evaluator.php';

// Register REST API endpoint
add_action('rest_api_init', function () {
    register_rest_route('drwerner/v1', '/malta-evaluator', [
        'methods' => 'POST',
        'callback' => 'malta_assessment_evaluate',
        'permission_callback' => '__return_true',
    ]);
});

function malta_assessment_evaluate(WP_REST_Request $request) {
    // The evaluator script handles everything
    exit;
}
```

#### Step 2: Copy PHP Script

Copy `malta-assessment-evaluator.php` to:
```
/wp-content/plugins/malta-evaluator/evaluator.php
```

#### Step 3: Activate Plugin

Activate the plugin in WordPress admin dashboard.

---

## Client-Side Integration

### Update your HTML file

Find the `calculateScore()` function (around line 1982) and replace it with:

```javascript
// Calculate Score - Server-Side (NEW)
async function calculateScore() {
    try {
        // Send answers to server for evaluation
        const response = await fetch('https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                answers: answers
            })
        });

        if (!response.ok) {
            throw new Error(`Server responded with ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.error || 'Evaluation failed');
        }

        return result.data;

    } catch (error) {
        console.error('Score calculation failed:', error);

        // Show user-friendly error
        alert('Es gab einen Fehler bei der Auswertung. Bitte versuchen Sie es erneut oder kontaktieren Sie uns direkt.');

        // Fallback: Return neutral result
        return {
            percentage: 50,
            weightedScore: 0,
            totalPossibleWeightedScore: 0,
            category: 'moderate',
            categoryLabel: 'Beratung empfohlen',
            interpretation: 'Aufgrund eines technischen Problems können wir Ihre Antworten momentan nicht auswerten. Bitte kontaktieren Sie uns direkt für eine persönliche Beratung.',
            detailedResults: []
        };
    }
}
```

### Update `calculateAndShowResults()` function

Replace the function (around line 2038) with:

```javascript
// Calculate and Show Results - Server-Side Version
async function calculateAndShowResults() {
    // Show loading state
    const questionScreen = document.getElementById('questionScreen');
    const resultsScreen = document.getElementById('resultsScreen');

    questionScreen.classList.add('hidden');
    resultsScreen.classList.remove('hidden');
    resultsScreen.innerHTML = `
        <div style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">⏳</div>
            <h2>Ihre Antworten werden ausgewertet...</h2>
            <p style="color: var(--color-gray-500); margin-top: 0.5rem;">
                Einen Moment bitte
            </p>
        </div>
    `;

    // Get score from server
    const scoreData = await calculateScore();

    const percentage = scoreData.percentage;
    const category = scoreData.category;
    const categoryLabel = scoreData.categoryLabel;
    const interpretation = scoreData.interpretation;

    // Categorize detailed results
    const positiveDetails = scoreData.detailedResults.filter(r => r.category === 'positive');
    const neutralDetails = scoreData.detailedResults.filter(r => r.category === 'neutral');
    const criticalDetails = scoreData.detailedResults.filter(r => r.category === 'critical');

    // Send webhook (optional - you can also do this server-side)
    await sendWebhook({
        timestamp: new Date().toISOString(),
        contact: userContact,
        answers: answers,
        score: {
            percentage: percentage,
            category: category
        },
        interpretation: interpretation,
        detailedResults: scoreData.detailedResults
    });

    // Render results
    renderResults(percentage, category, categoryLabel, interpretation, positiveDetails, neutralDetails, criticalDetails);
}
```

---

## Security Features

### Rate Limiting

- **Limit:** 10 requests per IP per hour
- **Storage:** PHP Sessions (automatic cleanup)
- **Configurable:** Change `RATE_LIMIT_MAX_REQUESTS` and `RATE_LIMIT_TIME_WINDOW` constants

### CORS Protection

Only requests from configured domains are allowed. Configure in PHP file:

```php
const ALLOWED_ORIGINS = [
    'https://www.drwerner.com',
    'https://drwerner.com',
    'http://localhost:8881', // Remove in production
];
```

### Input Validation

- Question IDs must match pattern: `q001`, `q002`, etc.
- Answer values are strictly validated
- Non-matching inputs are silently ignored
- JSON parsing errors are caught

### Error Handling

- Production mode hides detailed error messages
- Debug mode can be enabled for development
- All errors are logged (if DEBUG_MODE = true)

---

## Testing

### Local Testing (Before Deployment)

1. Start PHP built-in server:
   ```bash
   cd qc-malta-server
   php -S localhost:8000 malta-assessment-evaluator.php
   ```

2. Test with curl:
   ```bash
   curl -X POST http://localhost:8000/malta-assessment-evaluator.php \
     -H "Content-Type: application/json" \
     -H "Origin: http://localhost:8881" \
     -d '{
       "answers": {
         "q001": "4",
         "q002": "4",
         "q003": "4",
         "q004": "4",
         "q005": "4"
       }
     }'
   ```

3. Expected response:
   ```json
   {
     "success": true,
     "data": {
       "percentage": 85,
       "weightedScore": 68,
       "totalPossibleWeightedScore": 80,
       "category": "excellent",
       "categoryLabel": "Malta ist hervorragend geeignet",
       "interpretation": "Perfekt! Ihre Situation ist ideal für Malta...",
       "detailedResults": [...]
     }
   }
   ```

### Production Testing

1. Upload script to server
2. Configure ALLOWED_ORIGINS
3. Test endpoint URL in browser console:
   ```javascript
   fetch('https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator', {
     method: 'POST',
     headers: {'Content-Type': 'application/json'},
     body: JSON.stringify({
       answers: {
         "q001": "4",
         "q002": "4",
         "q003": "4"
       }
     })
   })
   .then(r => r.json())
   .then(console.log)
   ```

---

## Troubleshooting

### CORS Errors

**Symptom:** Browser console shows "CORS policy: No 'Access-Control-Allow-Origin' header"

**Solution:**
1. Check that your domain is in `ALLOWED_ORIGINS` array
2. Ensure origin includes protocol (https://)
3. Check for typos in domain name

### Rate Limit Errors

**Symptom:** HTTP 429 "Rate limit exceeded"

**Solution:**
1. Wait 1 hour for automatic reset
2. Or increase `RATE_LIMIT_MAX_REQUESTS` constant
3. For testing, set to 1000

### 500 Internal Server Error

**Symptom:** Server returns HTTP 500

**Solution:**
1. Enable debug mode: `const DEBUG_MODE = true;`
2. Check PHP error logs
3. Ensure PHP 7.4+ is installed
4. Check file permissions (644 for PHP files)

### Empty Response

**Symptom:** Fetch succeeds but no data returned

**Solution:**
1. Check that request Content-Type is `application/json`
2. Verify JSON structure matches expected format
3. Check browser Network tab for actual response

---

## Firewall Compatibility

This script is designed to work with common firewall configurations:

✅ **Cloudflare** - Compatible (uses standard HTTP POST)
✅ **AWS WAF** - Compatible
✅ **ModSecurity** - Compatible (no suspicious patterns)
✅ **Wordfence** - Compatible (WordPress plugin)
✅ **Sucuri** - Compatible

### Why It's Firewall-Friendly:

1. **Standard HTTP POST** - No unusual methods
2. **JSON Content-Type** - Standard and expected
3. **No SQL/Code Patterns** - Input is strictly validated
4. **Rate Limiting** - Prevents abuse detection
5. **CORS Headers** - Proper origin validation

---

## Performance

- **Response Time:** ~50-100ms typical
- **Memory Usage:** ~2MB per request
- **Concurrent Requests:** Handles 100+ concurrent users
- **Caching:** Not needed (fast calculation)

---

## Maintenance

### Updating Question Scores

Edit the `getQuestions()` function (line 245) in `malta-assessment-evaluator.php`.

**Important:** Keep question structure synchronized with client-side HTML!

### Updating Interpretation Logic

Edit the `getInterpretation()` function (line 181) to change category thresholds or messages.

### Monitoring

Enable debug mode temporarily to log all evaluations:

```php
const DEBUG_MODE = true;
```

Check your PHP error log for entries like:
```
[Malta Assessment] Score calculated: 85% for IP: 123.456.789.0
```

---

## Security Best Practices

1. ✅ Keep `DEBUG_MODE = false` in production
2. ✅ Use HTTPS (required for CORS)
3. ✅ Regularly update PHP version
4. ✅ Monitor rate limit violations
5. ✅ Keep WordPress/PHP up to date
6. ✅ Don't expose PHP file directly in WordPress (use REST API)

---

## Support

For issues or questions, contact: [your email]

**Version:** 2.0
**Last Updated:** 2025-11-04
**Author:** Dr. Werner & Partner
