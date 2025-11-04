import type { QuickCheckData, UserAnswers, QuickCheckResult } from '../types/quickcheck';

export function calculateScore(
  data: QuickCheckData,
  answers: UserAnswers
): QuickCheckResult {
  let rawScore = 0;
  let weightedScore = 0;
  let totalPossibleWeightedScore = 0;
  const categoryScores: QuickCheckResult['categoryScores'] = {};

  // Calculate scores
  data.questions.forEach((question) => {
    const selectedOptionId = answers[question.id];
    if (!selectedOptionId) return;

    const selectedOption = question.options.find((opt) => opt.id === selectedOptionId);
    if (!selectedOption) return;

    const optionScore = selectedOption.score;
    const weight = question.weight;
    const maxOptionScore = Math.max(...question.options.map((opt) => opt.score));

    // Raw score (unweighted)
    rawScore += optionScore;

    // Weighted score
    const questionWeightedScore = optionScore * weight;
    weightedScore += questionWeightedScore;
    totalPossibleWeightedScore += maxOptionScore * weight;

    // Category scores
    if (!categoryScores[question.category]) {
      categoryScores[question.category] = {
        score: 0,
        maxScore: 0,
        percentage: 0,
      };
    }
    categoryScores[question.category].score += questionWeightedScore;
    categoryScores[question.category].maxScore += maxOptionScore * weight;
  });

  // Calculate category percentages
  Object.keys(categoryScores).forEach((category) => {
    const cat = categoryScores[category];
    cat.percentage = cat.maxScore > 0 ? (cat.score / cat.maxScore) * 100 : 0;
  });

  // Normalize score to 0-100 scale
  const normalizedScore = totalPossibleWeightedScore > 0
    ? (weightedScore / totalPossibleWeightedScore) * 100
    : 0;

  // Determine threshold
  const thresholds = data.scoringThresholds;
  let threshold: keyof typeof thresholds = 'explore';
  let thresholdData = thresholds.explore;

  if (normalizedScore >= thresholds.excellent.min) {
    threshold = 'excellent';
    thresholdData = thresholds.excellent;
  } else if (normalizedScore >= thresholds.good.min) {
    threshold = 'good';
    thresholdData = thresholds.good;
  } else if (normalizedScore >= thresholds.moderate.min) {
    threshold = 'moderate';
    thresholdData = thresholds.moderate;
  } else if (normalizedScore >= thresholds.fair.min) {
    threshold = 'fair';
    thresholdData = thresholds.fair;
  }

  return {
    rawScore,
    weightedScore,
    normalizedScore,
    threshold,
    label: thresholdData.label,
    description: thresholdData.description,
    answers,
    categoryScores,
  };
}

export function getCategoryName(category: string, language: 'de' | 'en' = 'de'): string {
  const categoryNames: Record<string, { de: string; en: string }> = {
    'business-situation': { de: 'Geschäftssituation', en: 'Business Situation' },
    'international': { de: 'Internationale Ausrichtung', en: 'International Orientation' },
    'residency': { de: 'Wohnsitz & Relocation', en: 'Residency & Relocation' },
    'substance': { de: 'Substanzanforderungen', en: 'Substance Requirements' },
    'investment': { de: 'Investment & Planung', en: 'Investment & Planning' },
  };

  return categoryNames[category]?.[language] || category;
}

export function getScoreColor(normalizedScore: number): string {
  if (normalizedScore >= 75) return 'text-green-600';
  if (normalizedScore >= 55) return 'text-blue-600';
  if (normalizedScore >= 35) return 'text-yellow-600';
  if (normalizedScore >= 20) return 'text-orange-600';
  return 'text-gray-600';
}

export function getScoreBgColor(normalizedScore: number): string {
  if (normalizedScore >= 75) return 'bg-green-100';
  if (normalizedScore >= 55) return 'bg-blue-100';
  if (normalizedScore >= 35) return 'bg-yellow-100';
  if (normalizedScore >= 20) return 'bg-orange-100';
  return 'bg-gray-100';
}
