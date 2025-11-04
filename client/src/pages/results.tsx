import { useEffect, useState } from 'react';
import { useLocation } from 'wouter';
import { quickCheckV2Data } from '@/data/quickcheck-v2';
import { calculateScore, getCategoryName, getScoreColor, getScoreBgColor } from '@/lib/scoring';
import type { QuickCheckResult, UserAnswers } from '@/types/quickcheck';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { CheckCircle2, ArrowLeft, Mail, Phone } from 'lucide-react';

export default function Results() {
  const [, setLocation] = useLocation();
  const [result, setResult] = useState<QuickCheckResult | null>(null);
  const [language, setLanguage] = useState<'de' | 'en'>('de');

  useEffect(() => {
    // Get answers from URL query params
    const params = new URLSearchParams(window.location.search);
    const answersStr = params.get('answers');

    if (!answersStr) {
      setLocation('/');
      return;
    }

    try {
      const answers: UserAnswers = JSON.parse(answersStr);
      const calculatedResult = calculateScore(quickCheckV2Data, answers);
      setResult(calculatedResult);
    } catch (error) {
      console.error('Error parsing answers:', error);
      setLocation('/');
    }
  }, [setLocation]);

  if (!result) {
    return (
      <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">Loading results...</p>
        </div>
      </div>
    );
  }

  const handleLanguageToggle = () => {
    setLanguage(language === 'de' ? 'en' : 'de');
  };

  const handleRestart = () => {
    setLocation('/');
  };

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white py-8 px-4">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-2">
            {language === 'de' ? 'Ihre Auswertung' : 'Your Results'}
          </h1>
          <p className="text-lg text-gray-600">
            {quickCheckV2Data.title}
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

        {/* Overall Score Card */}
        <Card className={`shadow-lg mb-8 border-2 ${getScoreBgColor(result.normalizedScore)} border-transparent`}>
          <CardHeader className="text-center pb-4">
            <div className="flex justify-center mb-4">
              <div className={`w-32 h-32 rounded-full ${getScoreBgColor(result.normalizedScore)} flex items-center justify-center border-4 ${getScoreColor(result.normalizedScore)} border-current`}>
                <div className="text-center">
                  <div className={`text-4xl font-bold ${getScoreColor(result.normalizedScore)}`}>
                    {Math.round(result.normalizedScore)}
                  </div>
                  <div className={`text-sm font-medium ${getScoreColor(result.normalizedScore)}`}>
                    / 100
                  </div>
                </div>
              </div>
            </div>
            <CardTitle className="text-3xl mb-2">
              {result.label}
            </CardTitle>
            <CardDescription className="text-lg">
              {result.description}
            </CardDescription>
          </CardHeader>
        </Card>

        {/* Category Scores */}
        <Card className="shadow-lg mb-8">
          <CardHeader>
            <CardTitle>
              {language === 'de' ? 'Detaillierte Bewertung nach Kategorien' : 'Detailed Category Assessment'}
            </CardTitle>
            <CardDescription>
              {language === 'de'
                ? 'So schneiden Sie in den verschiedenen Bereichen ab'
                : 'How you score across different areas'}
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-6">
            {Object.entries(result.categoryScores)
              .sort(([, a], [, b]) => b.percentage - a.percentage)
              .map(([category, data]) => (
              <div key={category}>
                <div className="flex justify-between items-center mb-2">
                  <span className="font-medium text-gray-700">
                    {getCategoryName(category, language)}
                  </span>
                  <span className="text-sm font-semibold text-gray-600">
                    {Math.round(data.percentage)}%
                  </span>
                </div>
                <Progress value={data.percentage} className="h-3" />
              </div>
            ))}
          </CardContent>
        </Card>

        {/* Additional Notes */}
        <Card className="shadow-lg mb-8 bg-blue-50 border-blue-200">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <CheckCircle2 className="w-5 h-5 text-blue-600" />
              {language === 'de' ? 'Wichtige Hinweise' : 'Important Notes'}
            </CardTitle>
          </CardHeader>
          <CardContent>
            <ul className="space-y-3">
              {quickCheckV2Data.additionalNotes
                .filter((_, index) => language === 'de' ? index % 2 === 0 : index % 2 === 1)
                .map((note, index) => (
                  <li key={index} className="flex items-start gap-2">
                    <CheckCircle2 className="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" />
                    <span className="text-gray-700">{note}</span>
                  </li>
                ))}
            </ul>
          </CardContent>
        </Card>

        {/* CTA Section */}
        <Card className="shadow-lg mb-8 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
          <CardHeader>
            <CardTitle className="text-white text-2xl">
              {language === 'de' ? 'Nächste Schritte' : 'Next Steps'}
            </CardTitle>
            <CardDescription className="text-blue-100">
              {language === 'de'
                ? 'Lassen Sie uns gemeinsam Ihre Malta-Strategie entwickeln'
                : "Let's develop your Malta strategy together"}
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <p className="text-blue-50">
              {language === 'de'
                ? 'Vereinbaren Sie jetzt ein kostenloses Erstgespräch mit unseren Malta-Experten. Wir analysieren Ihre individuelle Situation und zeigen Ihnen konkrete Umsetzungsmöglichkeiten.'
                : 'Schedule a free initial consultation with our Malta experts. We will analyze your individual situation and show you concrete implementation options.'}
            </p>
            <div className="flex flex-col sm:flex-row gap-4">
              <Button
                className="bg-white text-blue-600 hover:bg-blue-50 flex items-center gap-2"
                size="lg"
              >
                <Mail className="w-4 h-4" />
                {language === 'de' ? 'Beratungstermin vereinbaren' : 'Schedule Consultation'}
              </Button>
              <Button
                variant="outline"
                className="border-white text-white hover:bg-blue-800 flex items-center gap-2"
                size="lg"
              >
                <Phone className="w-4 h-4" />
                {language === 'de' ? 'Rückruf anfordern' : 'Request Callback'}
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Navigation */}
        <div className="flex justify-center">
          <Button
            variant="outline"
            onClick={handleRestart}
            className="flex items-center gap-2"
          >
            <ArrowLeft className="w-4 h-4" />
            {language === 'de' ? 'QuickCheck neu starten' : 'Restart QuickCheck'}
          </Button>
        </div>
      </div>
    </div>
  );
}
