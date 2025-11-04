<?php
/**
 * Malta Assessment Evaluator - Server-Side Scoring Engine
 *
 * This script provides a secure, server-side evaluation endpoint for the Malta Assessment questionnaire.
 * It's designed to be WordPress-compatible but can run standalone on any PHP server.
 *
 * Security Features:
 * - Rate limiting (max 10 requests per IP per hour)
 * - Input sanitization and validation
 * - CORS protection (configurable allowed origins)
 * - Request method validation (POST only)
 * - JSON-only responses
 *
 * WordPress Deployment:
 * 1. Place this file in: /wp-content/themes/your-theme/malta-assessment-evaluator.php
 * 2. Or create a custom endpoint in functions.php using WordPress REST API
 *
 * Standalone Deployment:
 * 1. Upload to your server (e.g., /api/malta-evaluator.php)
 * 2. Ensure PHP 7.4+ is installed
 * 3. Configure ALLOWED_ORIGINS below
 *
 * @version 2.0
 * @author Dr. Werner & Partner
 * @license Proprietary
 */

// =============================================================================
// CONFIGURATION
// =============================================================================

// Allowed origins (domains that can call this endpoint)
// Add your domain(s) here
const ALLOWED_ORIGINS = [
    'https://www.drwerner.com',
    'https://drwerner.com',
    'http://localhost:8881', // Local development
    'http://localhost:3000', // Vite dev server
];

// Webhook URL for sending results (hidden from client)
// This keeps your webhook URL secure and not visible in JavaScript
const WEBHOOK_URL = 'https://brixon.app.n8n.cloud/webhook/malta-eignungscheck';

// Enable webhook sending (set to false to disable)
const WEBHOOK_ENABLED = true;

// Rate limiting configuration
const RATE_LIMIT_MAX_REQUESTS = 10; // Max requests per time window
const RATE_LIMIT_TIME_WINDOW = 3600; // Time window in seconds (3600 = 1 hour)

// Enable debug mode (set to false in production!)
const DEBUG_MODE = false;

// =============================================================================
// INITIALIZATION
// =============================================================================

// Start session for rate limiting
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Handle CORS
handleCORS();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST requests are allowed', 405);
}

// Check rate limiting
if (!checkRateLimit()) {
    sendError('Rate limit exceeded. Please try again later.', 429);
}

// =============================================================================
// MAIN LOGIC
// =============================================================================

try {
    // Get and validate input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError('Invalid JSON input', 400);
    }

    // Validate required fields
    if (!isset($input['answers']) || !is_array($input['answers'])) {
        sendError('Missing or invalid "answers" field', 400);
    }

    // Sanitize answers
    $answers = sanitizeAnswers($input['answers']);

    // Calculate score
    $scoreData = calculateScore($answers);

    // Get interpretation
    $interpretation = getInterpretation($scoreData['percentage']);

    // Build response
    $response = [
        'success' => true,
        'data' => [
            'percentage' => $scoreData['percentage'],
            'weightedScore' => $scoreData['weightedScore'],
            'totalPossibleWeightedScore' => $scoreData['totalPossibleWeightedScore'],
            'category' => $interpretation['category'],
            'categoryLabel' => $interpretation['categoryLabel'],
            'interpretation' => $interpretation['interpretation'],
            'detailedResults' => $scoreData['detailedResults'],
        ],
        'timestamp' => date('c'),
    ];

    // Send to webhook (if enabled)
    if (WEBHOOK_ENABLED) {
        $userContact = isset($input['userContact']) ? $input['userContact'] : [];
        sendWebhook($answers, $userContact, $scoreData, $interpretation);
    }

    // Log for debugging (only in debug mode)
    if (DEBUG_MODE) {
        error_log('[Malta Assessment] Score calculated: ' . $scoreData['percentage'] . '% for IP: ' . getClientIP());
    }

    sendSuccess($response['data']);

} catch (Exception $e) {
    if (DEBUG_MODE) {
        sendError('Internal error: ' . $e->getMessage(), 500);
    } else {
        sendError('Internal server error', 500);
    }
}

// =============================================================================
// CORE FUNCTIONS
// =============================================================================

/**
 * Calculate the weighted score based on answers
 *
 * @param array $answers User's answers (questionId => optionValue)
 * @return array Score data including percentage, weighted score, and detailed results
 */
function calculateScore(array $answers): array
{
    $questions = getQuestions();

    $weightedScore = 0;
    $totalPossibleWeightedScore = 0;
    $detailedResults = [];

    foreach ($questions as $question) {
        if ($question['type'] !== 'single_choice' || !isset($question['options'])) {
            continue;
        }

        $questionId = $question['id'];
        $weight = $question['weight'] ?? 1.0;

        // Find max score for this question
        $maxOptionScore = max(array_column($question['options'], 'score'));
        $totalPossibleWeightedScore += $maxOptionScore * $weight;

        // Check if user answered this question
        if (!isset($answers[$questionId])) {
            continue;
        }

        $selectedValue = $answers[$questionId];

        // Find selected option
        $selectedOption = null;
        foreach ($question['options'] as $option) {
            if ($option['value'] === $selectedValue) {
                $selectedOption = $option;
                break;
            }
        }

        if ($selectedOption === null) {
            continue;
        }

        // Calculate weighted score for this question
        $score = $selectedOption['score'];
        $questionWeightedScore = $score * $weight;
        $weightedScore += $questionWeightedScore;

        // Categorize based on score
        $category = 'neutral';
        if ($score >= 8) {
            $category = 'positive';
        } elseif ($score <= 4) {
            $category = 'critical';
        }

        $detailedResults[] = [
            'questionId' => $questionId,
            'questionText' => $question['text'],
            'answer' => $selectedOption['label'],
            'answerDescription' => $selectedOption['description'] ?? '',
            'score' => $score,
            'category' => $category,
        ];
    }

    // Convert to percentage (0-100)
    $percentage = $totalPossibleWeightedScore > 0
        ? round(($weightedScore / $totalPossibleWeightedScore) * 100)
        : 0;

    return [
        'percentage' => $percentage,
        'weightedScore' => $weightedScore,
        'totalPossibleWeightedScore' => $totalPossibleWeightedScore,
        'detailedResults' => $detailedResults,
    ];
}

/**
 * Get interpretation based on percentage score
 *
 * @param int $percentage Score percentage (0-100)
 * @return array Category, label, and interpretation text
 */
function getInterpretation(int $percentage): array
{
    if ($percentage < 20) {
        return [
            'category' => 'explore',
            'categoryLabel' => 'Lassen Sie uns sprechen',
            'interpretation' => 'Ihre Situation erfordert eine individuelle Beratung. Kontaktieren Sie uns für ein persönliches Gespräch über Ihre Möglichkeiten. Malta bietet flexible Lösungen für verschiedenste Situationen.',
        ];
    } elseif ($percentage < 35) {
        return [
            'category' => 'fair',
            'categoryLabel' => 'Malta ist möglich',
            'interpretation' => 'Malta könnte für Sie funktionieren. Lassen Sie uns gemeinsam herausfinden, wie wir dies optimal gestalten. Mit einigen Anpassungen können Sie von Maltas Vorteilen profitieren.',
        ];
    } elseif ($percentage < 55) {
        return [
            'category' => 'moderate',
            'categoryLabel' => 'Malta ist gut geeignet',
            'interpretation' => 'Malta bietet gute Möglichkeiten für Sie. Mit einigen Anpassungen können Sie optimal profitieren. Die Kombination aus niedrigen Steuern, EU-Mitgliedschaft und hoher Lebensqualität macht Malta einzigartig.',
        ];
    } elseif ($percentage < 75) {
        return [
            'category' => 'good',
            'categoryLabel' => 'Malta ist sehr gut geeignet',
            'interpretation' => 'Großartig! Malta bietet signifikante Vorteile für Ihre Situation. Mit der richtigen Planung wird dies ein Erfolg. Wir helfen Ihnen, die optimale Struktur für Ihre spezifische Situation zu finden.',
        ];
    } else {
        return [
            'category' => 'excellent',
            'categoryLabel' => 'Malta ist hervorragend geeignet',
            'interpretation' => 'Perfekt! Ihre Situation ist ideal für Malta. Sie können von allen Vorteilen profitieren - lassen Sie uns die Details besprechen! Hohe Erfolgswahrscheinlichkeit bei korrekter Umsetzung.',
        ];
    }
}

/**
 * Get questions configuration
 * This mirrors the client-side questions structure
 *
 * @return array Questions with options and scores
 */
function getQuestions(): array
{
    return [
        [
            'id' => 'q001',
            'text' => 'Was beschreibt Ihre geschäftliche Situation am besten?',
            'type' => 'single_choice',
            'weight' => 2.0,
            'options' => [
                ['value' => '1', 'label' => 'Ich plane, in Malta ein komplett neues Business zu starten', 'score' => 8],
                ['value' => '2', 'label' => 'Ich habe ein bestehendes Business (unter 500k EUR Umsatz)', 'score' => 6],
                ['value' => '3', 'label' => 'Ich habe ein etabliertes Business (500k - 2 Mio. EUR)', 'score' => 8],
                ['value' => '4', 'label' => 'Ich habe ein größeres Business (über 2 Mio. EUR)', 'score' => 10],
                ['value' => '5', 'label' => 'Ich möchte mich erstmal informieren / keine Angabe', 'score' => 7],
            ],
        ],
        [
            'id' => 'q002',
            'text' => 'Wie international ist Ihr Business ausgerichtet (oder soll es sein)?',
            'type' => 'single_choice',
            'weight' => 1.5,
            'options' => [
                ['value' => '1', 'label' => 'Neues Business - plane internationale Ausrichtung', 'score' => 8],
                ['value' => '2', 'label' => 'Hauptsächlich lokal, aber offen für internationale Expansion', 'score' => 6],
                ['value' => '3', 'label' => 'Mix aus lokalen und internationalen Kunden', 'score' => 8],
                ['value' => '4', 'label' => 'Vollständig international / digitales Business', 'score' => 10],
                ['value' => '5', 'label' => 'Noch in Planung / keine Angabe', 'score' => 7],
            ],
        ],
        [
            'id' => 'q003',
            'text' => 'Sind Sie bereit, nach Malta umzuziehen und dort mindestens 183 Tage pro Jahr zu verbringen?',
            'type' => 'single_choice',
            'weight' => 2.0,
            'options' => [
                ['value' => '1', 'label' => 'Nein, auf keinen Fall', 'score' => 3],
                ['value' => '2', 'label' => 'Ungern, nur wenn unbedingt nötig', 'score' => 6],
                ['value' => '3', 'label' => 'Ja, aber nur vorübergehend (2-3 Jahre)', 'score' => 8],
                ['value' => '4', 'label' => 'Ja, langfristig bereit', 'score' => 10],
            ],
        ],
        [
            'id' => 'q004',
            'text' => 'Welches Geschäftsmodell beschreibt Ihr Unternehmen am besten?',
            'type' => 'single_choice',
            'weight' => 1.5,
            'options' => [
                ['value' => '1', 'label' => 'Lokale Dienstleistung mit persönlichem Kundenkontakt', 'score' => 4],
                ['value' => '2', 'label' => 'E-Commerce / Handel', 'score' => 7],
                ['value' => '3', 'label' => 'SaaS / Digitale Produkte', 'score' => 9],
                ['value' => '4', 'label' => 'Holding / Beteiligungsgesellschaft', 'score' => 10],
                ['value' => '5', 'label' => 'Beratung / Professional Services (ortsunabhängig)', 'score' => 8],
            ],
        ],
        [
            'id' => 'q005',
            'text' => 'Können Sie echte wirtschaftliche Substanz in Malta aufbauen (Büro, Mitarbeiter, Management)?',
            'type' => 'single_choice',
            'weight' => 2.0,
            'options' => [
                ['value' => '1', 'label' => 'Nein, nur Briefkastenfirma ohne Aktivität', 'score' => 3],
                ['value' => '2', 'label' => 'Minimale Substanz (Virtual Office, keine Mitarbeiter)', 'score' => 5],
                ['value' => '3', 'label' => 'Moderate Substanz (kleines Büro, 1-2 lokale Teilzeitmitarbeiter)', 'score' => 8],
                ['value' => '4', 'label' => 'Volle Substanz (eigenes Büro, mehrere Vollzeitmitarbeiter, Management vor Ort)', 'score' => 10],
            ],
        ],
        [
            'id' => 'q006',
            'text' => 'Sind Sie bereit, höhere Compliance-Anforderungen auf sich zu nehmen?',
            'type' => 'single_choice',
            'weight' => 1.5,
            'options' => [
                ['value' => '1', 'label' => 'Nein, ich bevorzuge minimale Compliance', 'score' => 4],
                ['value' => '2', 'label' => 'Unsicher / möchte mehr erfahren', 'score' => 6],
                ['value' => '3', 'label' => 'Ja, bei angemessenem Nutzen', 'score' => 8],
                ['value' => '4', 'label' => 'Ja, volle Compliance ist mir wichtig', 'score' => 10],
            ],
        ],
        [
            'id' => 'q007',
            'text' => 'Haben Sie bereits Niederlassungen in anderen Ländern oder planen Sie diese?',
            'type' => 'single_choice',
            'weight' => 1.5,
            'options' => [
                ['value' => '1', 'label' => 'Nein, und nicht geplant', 'score' => 6],
                ['value' => '2', 'label' => 'Noch nicht, aber für die Zukunft geplant', 'score' => 7],
                ['value' => '3', 'label' => 'Ja, eine Niederlassung in einem weiteren Land', 'score' => 8],
                ['value' => '4', 'label' => 'Ja, mehrere Niederlassungen / Tochtergesellschaften', 'score' => 10],
                ['value' => '5', 'label' => 'Unsicher / keine Angabe', 'score' => 6],
            ],
        ],
        [
            'id' => 'q008',
            'text' => 'Wie würden Sie Ihre Profitabilität einschätzen?',
            'type' => 'single_choice',
            'weight' => 1.5,
            'options' => [
                ['value' => '1', 'label' => 'Noch nicht profitabel / Start-up Phase', 'score' => 5],
                ['value' => '2', 'label' => 'Break-even oder leicht profitabel', 'score' => 7],
                ['value' => '3', 'label' => 'Solide Profitabilität', 'score' => 9],
                ['value' => '4', 'label' => 'Sehr profitabel', 'score' => 10],
                ['value' => '5', 'label' => 'Keine Angabe', 'score' => 6],
            ],
        ],
        [
            'id' => 'q009',
            'text' => 'Wie wichtig ist Ihnen EU-Marktzugang?',
            'type' => 'single_choice',
            'weight' => 1.5,
            'options' => [
                ['value' => '1', 'label' => 'Nicht wichtig / fokussiere auf Nicht-EU', 'score' => 6],
                ['value' => '2', 'label' => 'Etwas wichtig / nice to have', 'score' => 7],
                ['value' => '3', 'label' => 'Wichtig / plane EU-Expansion', 'score' => 9],
                ['value' => '4', 'label' => 'Sehr wichtig / kritisch für mein Geschäftsmodell', 'score' => 10],
            ],
        ],
        [
            'id' => 'q010',
            'text' => 'Haben Sie bereits Erfahrung mit internationalen Unternehmensstrukturen?',
            'type' => 'single_choice',
            'weight' => 1.0,
            'options' => [
                ['value' => '1', 'label' => 'Nein, vollständig neu für mich', 'score' => 6],
                ['value' => '2', 'label' => 'Etwas Erfahrung', 'score' => 7],
                ['value' => '3', 'label' => 'Gute Erfahrung mit internationalen Strukturen', 'score' => 9],
                ['value' => '4', 'label' => 'Umfangreiche Erfahrung', 'score' => 10],
            ],
        ],
        [
            'id' => 'q011',
            'text' => 'Wie wichtig ist Ihnen Privatsphäre / Diskretion?',
            'type' => 'single_choice',
            'weight' => 1.0,
            'options' => [
                ['value' => '1', 'label' => 'Nicht wichtig / volle Transparenz ist OK', 'score' => 8],
                ['value' => '2', 'label' => 'Etwas wichtig', 'score' => 7],
                ['value' => '3', 'label' => 'Wichtig / möchte diskrete Strukturen', 'score' => 7],
                ['value' => '4', 'label' => 'Sehr wichtig / maximale Diskretion gewünscht', 'score' => 6],
            ],
        ],
        [
            'id' => 'q012',
            'text' => 'Welche Zeitschiene haben Sie für die Umsetzung?',
            'type' => 'single_choice',
            'weight' => 1.0,
            'options' => [
                ['value' => '1', 'label' => 'Informationsphase / über 12 Monate', 'score' => 7],
                ['value' => '2', 'label' => 'Mittelfristig (6-12 Monate)', 'score' => 8],
                ['value' => '3', 'label' => 'Kurzfristig (3-6 Monate)', 'score' => 9],
                ['value' => '4', 'label' => 'Sofort / unter 3 Monaten', 'score' => 10],
            ],
        ],
    ];
}

// =============================================================================
// SECURITY & UTILITY FUNCTIONS
// =============================================================================

/**
 * Handle CORS (Cross-Origin Resource Sharing)
 */
function handleCORS(): void
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, ALLOWED_ORIGINS, true)) {
        header("Access-Control-Allow-Origin: $origin");
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept');
        header('Access-Control-Max-Age: 86400'); // 24 hours
    }

    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Check rate limiting
 *
 * @return bool True if request is allowed, false if rate limit exceeded
 */
function checkRateLimit(): bool
{
    $ip = getClientIP();
    $key = 'rate_limit_' . md5($ip);

    // Get current request count from session
    $requests = $_SESSION[$key] ?? [];

    // Remove old requests outside the time window
    $currentTime = time();
    $requests = array_filter($requests, function ($timestamp) use ($currentTime) {
        return ($currentTime - $timestamp) < RATE_LIMIT_TIME_WINDOW;
    });

    // Check if limit exceeded
    if (count($requests) >= RATE_LIMIT_MAX_REQUESTS) {
        return false;
    }

    // Add current request
    $requests[] = $currentTime;
    $_SESSION[$key] = $requests;

    return true;
}

/**
 * Get client IP address (handles proxies and load balancers)
 *
 * @return string Client IP address
 */
function getClientIP(): string
{
    $ipKeys = [
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR',
    ];

    foreach ($ipKeys as $key) {
        if (isset($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            // Get first IP if comma-separated list
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '0.0.0.0';
}

/**
 * Sanitize answers array
 *
 * @param array $answers Raw answers from user input
 * @return array Sanitized answers
 */
function sanitizeAnswers(array $answers): array
{
    $sanitized = [];

    foreach ($answers as $questionId => $value) {
        // Only allow alphanumeric question IDs
        if (!preg_match('/^q[0-9]{3}$/', $questionId)) {
            continue;
        }

        // Only allow simple alphanumeric values
        if (!is_string($value) || !preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
            continue;
        }

        $sanitized[$questionId] = $value;
    }

    return $sanitized;
}

/**
 * Send success response
 *
 * @param array $data Response data
 */
function sendSuccess(array $data): void
{
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Send error response
 *
 * @param string $message Error message
 * @param int $statusCode HTTP status code
 */
function sendError(string $message, int $statusCode = 400): void
{
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'error' => $message,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Send data to webhook
 * This keeps the webhook URL hidden from the client
 *
 * @param array $answers User's answers
 * @param array $userContact User contact information
 * @param array $scoreData Score calculation results
 * @param array $interpretation Interpretation data
 * @return bool True if successful, false otherwise
 */
function sendWebhook(array $answers, array $userContact, array $scoreData, array $interpretation): bool
{
    if (!WEBHOOK_ENABLED || empty(WEBHOOK_URL)) {
        return false;
    }

    try {
        // Build webhook payload
        $payload = [
            'timestamp' => date('c'),
            'contact' => $userContact,
            'answers' => $answers,
            'score' => [
                'percentage' => $scoreData['percentage'],
                'weightedScore' => $scoreData['weightedScore'],
                'totalPossibleWeightedScore' => $scoreData['totalPossibleWeightedScore'],
                'category' => $interpretation['category'],
            ],
            'interpretation' => $interpretation['interpretation'],
            'detailedResults' => $scoreData['detailedResults'],
            'metadata' => [
                'ip' => getClientIP(),
                'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                'serverTime' => date('Y-m-d H:i:s'),
            ],
        ];

        // Send to webhook using cURL
        $ch = curl_init(WEBHOOK_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10, // 10 second timeout
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Check if webhook was successful
        if ($httpCode >= 200 && $httpCode < 300) {
            if (DEBUG_MODE) {
                error_log('[Malta Assessment] Webhook sent successfully: ' . WEBHOOK_URL);
            }
            return true;
        } else {
            if (DEBUG_MODE) {
                error_log('[Malta Assessment] Webhook failed: HTTP ' . $httpCode . ' - ' . $error);
            }
            return false;
        }

    } catch (Exception $e) {
        if (DEBUG_MODE) {
            error_log('[Malta Assessment] Webhook exception: ' . $e->getMessage());
        }
        return false;
    }
}
