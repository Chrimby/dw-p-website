#!/usr/bin/env python3
import re

# Read the file
with open('/Users/christoph/Brixon Dropbox/Christoph Sauerborn/Downloads/quickcheck20/qc/public/malta-assessment-v2/index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the calculateScore function with weighted scoring
old_calc_score = r'// Calculate Score\s+function calculateScore\(\) \{.*?\n\s+\}'
new_calc_score = '''// Calculate Score
        function calculateScore() {
            let weightedScore = 0;
            let totalPossibleWeightedScore = 0;
            let detailedResults = [];

            questions.forEach(q => {
                if (q.type === 'single_choice' && q.options) {
                    const selectedValue = answers[q.id];
                    const selectedOption = q.options.find(opt => opt.value === selectedValue);

                    if (selectedOption) {
                        const score = selectedOption.score;
                        const weight = q.weight || 1.0;
                        const maxOptionScore = Math.max(...q.options.map(opt => opt.score));

                        // Weighted scoring
                        const questionWeightedScore = score * weight;
                        weightedScore += questionWeightedScore;
                        totalPossibleWeightedScore += maxOptionScore * weight;

                        // Categorize based on score
                        let category = 'neutral';
                        if (score >= 8) category = 'positive';
                        else if (score <= 4) category = 'critical';

                        detailedResults.push({
                            questionId: q.id,
                            questionText: q.text,
                            answer: selectedOption.label,
                            answerDescription: selectedOption.description || '',
                            score: score,
                            category: category
                        });
                    } else {
                        // Add max possible to total even if not answered
                        const weight = q.weight || 1.0;
                        const maxOptionScore = Math.max(...q.options.map(opt => opt.score));
                        totalPossibleWeightedScore += maxOptionScore * weight;
                    }
                }
            });

            // Convert to percentage (0-100)
            const percentage = totalPossibleWeightedScore > 0
                ? Math.round((weightedScore / totalPossibleWeightedScore) * 100)
                : 0;

            return {
                percentage,
                weightedScore,
                totalPossibleWeightedScore,
                detailedResults
            };
        }'''

content = re.sub(old_calc_score, new_calc_score, content, flags=re.DOTALL)

# Update the threshold logic - change from 30/60 to 20/35/55/75
old_thresholds = r"if \(percentage < 30\) \{.*?categoryLabel = 'Eher ungeeignet'.*?\} else if \(percentage < 60\) \{.*?categoryLabel = 'Bedingt geeignet'.*?\} else \{.*?categoryLabel = 'Sehr geeignet'.*?\}"

new_thresholds = '''if (percentage < 20) {
                category = 'explore';
                categoryLabel = 'Lassen Sie uns sprechen';
                interpretation = 'Ihre Situation erfordert eine individuelle Beratung. Kontaktieren Sie uns für ein persönliches Gespräch über Ihre Möglichkeiten. Malta bietet flexible Lösungen für verschiedenste Situationen.';
            } else if (percentage < 35) {
                category = 'fair';
                categoryLabel = 'Malta ist möglich';
                interpretation = 'Malta könnte für Sie funktionieren. Lassen Sie uns gemeinsam herausfinden, wie wir dies optimal gestalten. Mit einigen Anpassungen können Sie von Maltas Vorteilen profitieren.';
            } else if (percentage < 55) {
                category = 'moderate';
                categoryLabel = 'Malta ist gut geeignet';
                interpretation = 'Malta bietet gute Möglichkeiten für Sie. Mit einigen Anpassungen können Sie optimal profitieren. Die Kombination aus niedrigen Steuern, EU-Mitgliedschaft und hoher Lebensqualität macht Malta einzigartig.';
            } else if (percentage < 75) {
                category = 'good';
                categoryLabel = 'Malta ist sehr gut geeignet';
                interpretation = 'Großartig! Malta bietet signifikante Vorteile für Ihre Situation. Mit der richtigen Planung wird dies ein Erfolg. Wir helfen Ihnen, die optimale Struktur für Ihre spezifische Situation zu finden.';
            } else {
                category = 'excellent';
                categoryLabel = 'Malta ist hervorragend geeignet';
                interpretation = 'Perfekt! Ihre Situation ist ideal für Malta. Sie können von allen Vorteilen profitieren - lassen Sie uns die Details besprechen! Hohe Erfolgswahrscheinlichkeit bei korrekter Umsetzung.';
            }'''

content = re.sub(old_thresholds, new_thresholds, content, flags=re.DOTALL)

# Write back
with open('/Users/christoph/Brixon Dropbox/Christoph Sauerborn/Downloads/quickcheck20/qc/public/malta-assessment-v2/index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("✅ Scoring logic and thresholds updated successfully!")
