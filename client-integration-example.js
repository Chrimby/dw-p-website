/**
 * Malta Assessment - Client-Side Integration Example
 *
 * This file shows how to update your HTML file to use the server-side evaluator
 * instead of client-side scoring logic.
 *
 * INSTRUCTIONS:
 * 1. Replace the calculateScore() function in your HTML (around line 1982)
 * 2. Update the calculateAndShowResults() function (around line 2038)
 * 3. Update the API_ENDPOINT constant below with your actual endpoint URL
 */

// =============================================================================
// CONFIGURATION
// =============================================================================

// Your server-side evaluator endpoint
// Choose one based on your deployment method:

// Option 1: WordPress REST API
const API_ENDPOINT = 'https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator';

// Option 2: Standalone PHP file
// const API_ENDPOINT = 'https://www.drwerner.com/api/malta-evaluator.php';

// Option 3: Local testing
// const API_ENDPOINT = 'http://localhost:8000/malta-assessment-evaluator.php';

// =============================================================================
// REPLACE THESE FUNCTIONS IN YOUR HTML
// =============================================================================

/**
 * Calculate Score - Server-Side Version
 *
 * REPLACE your existing calculateScore() function with this one
 * Location: Around line 1982 in malta-assessment-v2-dwp/index.html
 */
async function calculateScore() {
    try {
        // Show a subtle loading indicator (optional)
        console.log('Calculating score on server...');

        // Send answers to server for evaluation
        const response = await fetch(API_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                answers: answers // Global variable from your HTML
            })
        });

        // Check response status
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Server responded with ${response.status}: ${errorText}`);
        }

        // Parse JSON response
        const result = await response.json();

        // Check if evaluation was successful
        if (!result.success) {
            throw new Error(result.error || 'Evaluation failed');
        }

        // Return the score data
        // Structure matches the old client-side return value
        return {
            percentage: result.data.percentage,
            weightedScore: result.data.weightedScore,
            totalPossibleWeightedScore: result.data.totalPossibleWeightedScore,
            detailedResults: result.data.detailedResults,
            // Additional server-provided fields
            category: result.data.category,
            categoryLabel: result.data.categoryLabel,
            interpretation: result.data.interpretation
        };

    } catch (error) {
        console.error('Score calculation failed:', error);

        // Show user-friendly error message
        alert('Es gab einen Fehler bei der Auswertung. Bitte versuchen Sie es erneut oder kontaktieren Sie uns direkt.');

        // Fallback: Return neutral result so the user isn't completely stuck
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

/**
 * Calculate and Show Results - Updated Version
 *
 * REPLACE your existing calculateAndShowResults() function with this one
 * Location: Around line 2038 in malta-assessment-v2-dwp/index.html
 */
async function calculateAndShowResults() {
    // Get screen elements
    const questionScreen = document.getElementById('questionScreen');
    const resultsScreen = document.getElementById('resultsScreen');

    // Hide question screen, show results screen
    questionScreen.classList.add('hidden');
    resultsScreen.classList.remove('hidden');

    // Show loading state while server calculates
    resultsScreen.innerHTML = `
        <div style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem; animation: pulse 1.5s ease-in-out infinite;">⏳</div>
            <h2 style="font-family: var(--font-heading); color: var(--color-primary); margin-bottom: 0.5rem;">
                Ihre Antworten werden ausgewertet...
            </h2>
            <p style="color: var(--color-gray-500); margin-top: 0.5rem;">
                Einen Moment bitte
            </p>
        </div>
        <style>
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
        </style>
    `;

    try {
        // Get score from server (this is now async)
        const scoreData = await calculateScore();

        // Extract data from server response
        const percentage = scoreData.percentage;
        const category = scoreData.category;
        const categoryLabel = scoreData.categoryLabel;
        const interpretation = scoreData.interpretation;

        // Categorize detailed results
        const positiveDetails = scoreData.detailedResults.filter(r => r.category === 'positive');
        const neutralDetails = scoreData.detailedResults.filter(r => r.category === 'neutral');
        const criticalDetails = scoreData.detailedResults.filter(r => r.category === 'critical');

        // Send webhook (optional - you could also move this to server-side)
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

        // Render results (this function stays the same)
        renderResults(percentage, category, categoryLabel, interpretation, positiveDetails, neutralDetails, criticalDetails);

    } catch (error) {
        console.error('Failed to show results:', error);

        // Show error state
        resultsScreen.innerHTML = `
            <div style="text-align: center; padding: 4rem 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                <h2 style="font-family: var(--font-heading); color: var(--color-danger); margin-bottom: 1rem;">
                    Fehler bei der Auswertung
                </h2>
                <p style="color: var(--color-gray-600); margin-bottom: 2rem; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Es gab einen technischen Fehler bei der Auswertung Ihrer Antworten.
                    Bitte versuchen Sie es erneut oder kontaktieren Sie uns direkt.
                </p>
                <button class="btn-submit-dwp" onclick="location.reload()">
                    Seite neu laden
                </button>
            </div>
        `;
    }
}

// =============================================================================
// OPTIONAL: ENHANCED ERROR HANDLING
// =============================================================================

/**
 * Add this to your HTML to provide better error messages
 * Location: Add to <script> section, before calculateScore()
 */
function getErrorMessage(error) {
    if (error.message.includes('429')) {
        return 'Sie haben zu viele Anfragen gesendet. Bitte warten Sie einen Moment und versuchen Sie es erneut.';
    }
    if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
        return 'Keine Verbindung zum Server möglich. Bitte überprüfen Sie Ihre Internetverbindung.';
    }
    if (error.message.includes('500')) {
        return 'Ein Serverfehler ist aufgetreten. Wir wurden benachrichtigt und arbeiten an einer Lösung.';
    }
    return 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
}

// Then update the catch block in calculateScore() to use it:
// alert(getErrorMessage(error));

// =============================================================================
// OPTIONAL: RETRY LOGIC
// =============================================================================

/**
 * Add retry logic for transient network errors
 * Location: Add to <script> section
 */
async function calculateScoreWithRetry(maxRetries = 2) {
    let lastError;

    for (let attempt = 0; attempt <= maxRetries; attempt++) {
        try {
            return await calculateScore();
        } catch (error) {
            lastError = error;

            // Don't retry on rate limit or validation errors
            if (error.message.includes('429') || error.message.includes('400')) {
                throw error;
            }

            // Wait before retrying (exponential backoff)
            if (attempt < maxRetries) {
                const delay = Math.pow(2, attempt) * 1000; // 1s, 2s, 4s
                console.log(`Retry attempt ${attempt + 1} after ${delay}ms`);
                await new Promise(resolve => setTimeout(resolve, delay));
            }
        }
    }

    throw lastError;
}

// Then in calculateAndShowResults(), use:
// const scoreData = await calculateScoreWithRetry();

// =============================================================================
// TESTING
// =============================================================================

/**
 * Test your integration in browser console
 *
 * 1. Open your Malta Assessment page
 * 2. Answer a few questions
 * 3. Open browser console (F12)
 * 4. Paste and run this code:
 */

// Test function (paste in console)
async function testServerEvaluation() {
    const testAnswers = {
        'q001': '4',
        'q002': '4',
        'q003': '4',
        'q004': '3',
        'q005': '4'
    };

    try {
        const response = await fetch(API_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ answers: testAnswers })
        });

        const result = await response.json();
        console.log('✅ Server evaluation successful!');
        console.log('Score:', result.data.percentage + '%');
        console.log('Category:', result.data.category);
        console.log('Full response:', result);
    } catch (error) {
        console.error('❌ Server evaluation failed:', error);
    }
}

// Run test
// testServerEvaluation();
