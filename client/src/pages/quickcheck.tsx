import { useState } from 'react';
import { useLocation } from 'wouter';
import { quickCheckV2Data } from '@/data/quickcheck-v2';
import type { UserAnswers } from '@/types/quickcheck';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Label } from '@/components/ui/label';
import { Progress } from '@/components/ui/progress';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export default function QuickCheck() {
  const [, setLocation] = useLocation();
  const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
  const [answers, setAnswers] = useState<UserAnswers>({});
  const [language, setLanguage] = useState<'de' | 'en'>('de');

  const currentQuestion = quickCheckV2Data.questions[currentQuestionIndex];
  const totalQuestions = quickCheckV2Data.questions.length;
  const progress = ((currentQuestionIndex + 1) / totalQuestions) * 100;
  const isLastQuestion = currentQuestionIndex === totalQuestions - 1;
  const canGoNext = answers[currentQuestion.id] !== undefined;

  const handleAnswerChange = (value: string) => {
    setAnswers({
      ...answers,
      [currentQuestion.id]: value,
    });
  };

  const handleNext = () => {
    if (isLastQuestion) {
      // Navigate to results page with answers
      const queryParams = new URLSearchParams();
      queryParams.set('answers', JSON.stringify(answers));
      setLocation(`/results?${queryParams.toString()}`);
    } else {
      setCurrentQuestionIndex(currentQuestionIndex + 1);
    }
  };

  const handlePrevious = () => {
    if (currentQuestionIndex > 0) {
      setCurrentQuestionIndex(currentQuestionIndex - 1);
    }
  };

  const handleLanguageToggle = () => {
    setLanguage(language === 'de' ? 'en' : 'de');
  };

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white py-8 px-4">
      <div className="max-w-3xl mx-auto">
        {/* Header */}
        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-2">
            {quickCheckV2Data.title}
          </h1>
          <p className="text-lg text-gray-600 max-w-2xl mx-auto">
            {language === 'de' ? quickCheckV2Data.description : quickCheckV2Data.descriptionEN}
          </p>
          <Button
            variant="outline"
            size="sm"
            onClick={handleLanguageToggle}
            className="mt-4"
          >
            {language === 'de' ? '🇬🇧 English' : '🇩🇪 Deutsch'}
          </Button>
        </div>

        {/* Progress */}
        <div className="mb-8">
          <div className="flex justify-between items-center mb-2">
            <span className="text-sm font-medium text-gray-600">
              {language === 'de' ? 'Fortschritt' : 'Progress'}: {currentQuestionIndex + 1} / {totalQuestions}
            </span>
            <span className="text-sm font-medium text-gray-600">
              {Math.round(progress)}%
            </span>
          </div>
          <Progress value={progress} className="h-2" />
        </div>

        {/* Question Card */}
        <Card className="shadow-lg">
          <CardHeader>
            <div className="flex items-start justify-between">
              <div className="flex-1">
                <CardTitle className="text-2xl mb-2">
                  {language === 'de' ? currentQuestion.question : currentQuestion.questionEN}
                </CardTitle>
                <CardDescription>
                  {language === 'de' ? 'Frage' : 'Question'} {currentQuestionIndex + 1} {language === 'de' ? 'von' : 'of'} {totalQuestions}
                </CardDescription>
              </div>
              <div className="ml-4">
                <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {currentQuestion.category}
                </span>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <RadioGroup
              value={answers[currentQuestion.id]}
              onValueChange={handleAnswerChange}
              className="space-y-4"
            >
              {currentQuestion.options.map((option) => (
                <div
                  key={option.id}
                  className="flex items-start space-x-3 p-4 rounded-lg border-2 hover:border-blue-300 hover:bg-blue-50 transition-all cursor-pointer"
                  onClick={() => handleAnswerChange(option.id)}
                >
                  <RadioGroupItem value={option.id} id={option.id} className="mt-1" />
                  <Label
                    htmlFor={option.id}
                    className="flex-1 cursor-pointer text-base leading-relaxed"
                  >
                    {language === 'de' ? option.text : option.textEN}
                  </Label>
                </div>
              ))}
            </RadioGroup>
          </CardContent>
        </Card>

        {/* Navigation */}
        <div className="flex justify-between items-center mt-8">
          <Button
            variant="outline"
            onClick={handlePrevious}
            disabled={currentQuestionIndex === 0}
            className="flex items-center gap-2"
          >
            <ChevronLeft className="w-4 h-4" />
            {language === 'de' ? 'Zurück' : 'Back'}
          </Button>

          <Button
            onClick={handleNext}
            disabled={!canGoNext}
            className="flex items-center gap-2 bg-blue-600 hover:bg-blue-700"
          >
            {isLastQuestion ? (
              <>
                {language === 'de' ? 'Ergebnis anzeigen' : 'Show Results'}
                <ChevronRight className="w-4 h-4" />
              </>
            ) : (
              <>
                {language === 'de' ? 'Weiter' : 'Next'}
                <ChevronRight className="w-4 h-4" />
              </>
            )}
          </Button>
        </div>

        {/* Question Counter Dots */}
        <div className="flex justify-center mt-8 gap-2">
          {quickCheckV2Data.questions.map((_, index) => (
            <button
              key={index}
              onClick={() => setCurrentQuestionIndex(index)}
              className={`w-3 h-3 rounded-full transition-all ${
                index === currentQuestionIndex
                  ? 'bg-blue-600 w-8'
                  : answers[quickCheckV2Data.questions[index].id]
                  ? 'bg-green-400'
                  : 'bg-gray-300'
              }`}
              title={`${language === 'de' ? 'Frage' : 'Question'} ${index + 1}`}
            />
          ))}
        </div>
      </div>
    </div>
  );
}
