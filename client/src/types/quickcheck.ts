export interface QuestionOption {
  id: string;
  text: string;
  textEN: string;
  score: number;
  reasoning: string;
}

export interface Question {
  id: string;
  category: string;
  question: string;
  questionEN: string;
  type: 'single-choice';
  options: QuestionOption[];
  weight: number;
  reasoning: string;
}

export interface ScoringThreshold {
  min: number;
  label: string;
  labelEN: string;
  description: string;
  descriptionEN: string;
}

export interface QuickCheckData {
  version: string;
  title: string;
  description: string;
  descriptionEN: string;
  questions: Question[];
  scoringThresholds: {
    excellent: ScoringThreshold;
    good: ScoringThreshold;
    moderate: ScoringThreshold;
    fair: ScoringThreshold;
    explore: ScoringThreshold;
  };
  additionalNotes: string[];
}

export interface UserAnswers {
  [questionId: string]: string; // questionId -> optionId
}

export interface QuickCheckResult {
  rawScore: number;
  weightedScore: number;
  normalizedScore: number; // 0-100
  threshold: keyof QuickCheckData['scoringThresholds'];
  label: string;
  description: string;
  answers: UserAnswers;
  categoryScores: {
    [category: string]: {
      score: number;
      maxScore: number;
      percentage: number;
    };
  };
}
