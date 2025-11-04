<?php
/**
 * Test Script for Malta Assessment Evaluator
 *
 * This script validates the scoring logic without running a web server.
 * Run with: php test-evaluator.php
 */

// Include the evaluator
require_once __DIR__ . '/malta-assessment-evaluator.php';

// Suppress the automatic execution by capturing output
ob_start();

// Test cases
$testCases = [
    [
        'name' => 'Excellent Case - High scorer across all dimensions',
        'answers' => [
            'q001' => '4', // Größeres Business
            'q002' => '4', // Vollständig international
            'q003' => '4', // Langfristig bereit umzuziehen
            'q004' => '4', // Holding / Beteiligungsgesellschaft
            'q005' => '4', // Volle Substanz
            'q006' => '4', // Volle Compliance
            'q007' => '4', // Mehrere Niederlassungen
            'q008' => '4', // Sehr profitabel
            'q009' => '4', // EU-Marktzugang sehr wichtig
            'q010' => '4', // Umfangreiche Erfahrung
            'q011' => '1', // Transparenz OK
            'q012' => '4', // Sofort umsetzbar
        ],
        'expectedCategory' => 'excellent',
        'expectedMinPercentage' => 75,
    ],
    [
        'name' => 'Good Case - Strong candidate with minor concerns',
        'answers' => [
            'q001' => '3', // Etabliertes Business
            'q002' => '3', // Mix lokal/international
            'q003' => '3', // Vorübergehend bereit
            'q004' => '3', // SaaS
            'q005' => '3', // Moderate Substanz
            'q006' => '3', // Compliance bei Nutzen
            'q007' => '3', // Eine Niederlassung
            'q008' => '3', // Solide Profitabilität
            'q009' => '3', // EU wichtig
            'q010' => '3', // Gute Erfahrung
            'q011' => '2', // Etwas wichtig
            'q012' => '3', // Kurzfristig
        ],
        'expectedCategory' => 'good',
        'expectedMinPercentage' => 55,
    ],
    [
        'name' => 'Moderate Case - Needs adjustments',
        'answers' => [
            'q001' => '2', // Bestehendes Business unter 500k
            'q002' => '2', // Lokal, aber offen
            'q003' => '2', // Ungern umziehen
            'q004' => '2', // E-Commerce
            'q005' => '2', // Minimale Substanz
            'q006' => '2', // Unsicher Compliance
            'q007' => '2', // Geplant
            'q008' => '2', // Break-even
            'q009' => '2', // Etwas wichtig
            'q010' => '2', // Etwas Erfahrung
            'q011' => '2', // Etwas wichtig
            'q012' => '2', // Mittelfristig
        ],
        'expectedCategory' => 'moderate',
        'expectedMinPercentage' => 35,
    ],
    [
        'name' => 'Fair Case - Possible but challenging',
        'answers' => [
            'q001' => '1', // Neues Business starten
            'q002' => '1', // Plane international
            'q003' => '2', // Ungern umziehen
            'q004' => '2', // E-Commerce
            'q005' => '2', // Minimale Substanz
            'q006' => '1', // Minimale Compliance
            'q007' => '1', // Nicht geplant
            'q008' => '1', // Noch nicht profitabel
            'q009' => '1', // Nicht wichtig
            'q010' => '1', // Keine Erfahrung
            'q011' => '4', // Maximale Diskretion
            'q012' => '1', // Informationsphase
        ],
        'expectedCategory' => 'fair',
        'expectedMinPercentage' => 20,
    ],
    [
        'name' => 'Explore Case - Needs consultation',
        'answers' => [
            'q001' => '5', // Informieren
            'q002' => '5', // Noch in Planung
            'q003' => '1', // Auf keinen Fall umziehen
            'q004' => '1', // Lokale Dienstleistung
            'q005' => '1', // Briefkastenfirma
            'q006' => '1', // Minimale Compliance
            'q007' => '1', // Nicht geplant
            'q008' => '5', // Keine Angabe
            'q009' => '1', // Nicht wichtig
            'q010' => '1', // Keine Erfahrung
            'q011' => '4', // Maximale Diskretion
            'q012' => '1', // Informationsphase
        ],
        'expectedCategory' => 'explore',
        'expectedMinPercentage' => 0,
    ],
];

// Run tests
echo "=============================================================================\n";
echo "Malta Assessment Evaluator - Test Suite\n";
echo "=============================================================================\n\n";

$passed = 0;
$failed = 0;

foreach ($testCases as $testCase) {
    echo "Test: {$testCase['name']}\n";
    echo str_repeat('-', 77) . "\n";

    // Calculate score
    $scoreData = calculateScore($testCase['answers']);
    $interpretation = getInterpretation($scoreData['percentage']);

    // Check results
    $percentageMatch = $scoreData['percentage'] >= $testCase['expectedMinPercentage'];
    $categoryMatch = $interpretation['category'] === $testCase['expectedCategory'];

    // Display results
    echo "Expected Category: {$testCase['expectedCategory']}\n";
    echo "Actual Category:   {$interpretation['category']}\n";
    echo "Expected Min %:    {$testCase['expectedMinPercentage']}%\n";
    echo "Actual %:          {$scoreData['percentage']}%\n";
    echo "Interpretation:    {$interpretation['categoryLabel']}\n";

    // Verdict
    if ($percentageMatch && $categoryMatch) {
        echo "✅ PASSED\n";
        $passed++;
    } else {
        echo "❌ FAILED\n";
        if (!$categoryMatch) {
            echo "   - Category mismatch!\n";
        }
        if (!$percentageMatch) {
            echo "   - Percentage too low!\n";
        }
        $failed++;
    }

    echo "\n";
}

// Summary
echo "=============================================================================\n";
echo "Test Summary\n";
echo "=============================================================================\n";
echo "Total Tests:  " . count($testCases) . "\n";
echo "Passed:       $passed ✅\n";
echo "Failed:       $failed " . ($failed > 0 ? "❌" : "✅") . "\n";
echo "Success Rate: " . round(($passed / count($testCases)) * 100) . "%\n";
echo "\n";

if ($failed === 0) {
    echo "🎉 All tests passed! The evaluator is working correctly.\n";
    exit(0);
} else {
    echo "⚠️  Some tests failed. Please review the scoring logic.\n";
    exit(1);
}
