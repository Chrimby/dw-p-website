import type { QuickCheckData } from '../types/quickcheck';

export const quickCheckV2Data: QuickCheckData = {
  "version": "2.0",
  "title": "Malta Qualification QuickCheck",
  "description": "Dieser QuickCheck zeigt Ihnen, wie gut Malta als attraktiver Unternehmensstandort zu Ihrer Situation passt. Malta bietet als EU-Mitglied einzigartige steuerliche Vorteile bei hoher Lebensqualität.",
  "descriptionEN": "This QuickCheck shows you how well Malta fits your situation as an attractive business location. As an EU member, Malta offers unique tax advantages combined with high quality of life.",
  "questions": [
    {
      "id": "q1",
      "category": "business-situation",
      "question": "Was beschreibt Ihre geschäftliche Situation am besten?",
      "questionEN": "What best describes your business situation?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Ich plane, in Malta ein komplett neues Business zu starten",
          "textEN": "I plan to start a completely new business in Malta",
          "score": 8,
          "reasoning": "Fresh start in Malta is ideal - can structure optimally from day one"
        },
        {
          "id": "b",
          "text": "Ich habe ein bestehendes Business (unter 500k EUR Umsatz)",
          "textEN": "I have an existing business (below 500k EUR revenue)",
          "score": 6,
          "reasoning": "Good foundation - Malta provides growth platform"
        },
        {
          "id": "c",
          "text": "Ich habe ein etabliertes Business (500k - 2 Mio. EUR)",
          "textEN": "I have an established business (500k - 2M EUR)",
          "score": 8,
          "reasoning": "Excellent size - Malta tax benefits provide significant value"
        },
        {
          "id": "d",
          "text": "Ich habe ein größeres Business (über 2 Mio. EUR)",
          "textEN": "I have a larger business (above 2M EUR)",
          "score": 10,
          "reasoning": "Perfect for Malta - maximum optimization potential"
        },
        {
          "id": "e",
          "text": "Ich möchte mich erstmal informieren / keine Angabe",
          "textEN": "I want to inform myself first / prefer not to say",
          "score": 7,
          "reasoning": "Understandable - we can discuss specifics in personal consultation"
        }
      ],
      "weight": 2.0,
      "reasoning": "Business situation is a key indicator for Malta suitability"
    },
    {
      "id": "q2",
      "category": "international",
      "question": "Wie international ist Ihr Business ausgerichtet (oder soll es sein)?",
      "questionEN": "How international is your business oriented (or should it be)?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Neues Business - plane internationale Ausrichtung",
          "textEN": "New business - planning international orientation",
          "score": 8,
          "reasoning": "Perfect - Malta is ideal launchpad for international business"
        },
        {
          "id": "b",
          "text": "Hauptsächlich lokal, aber offen für internationale Expansion",
          "textEN": "Mainly local, but open to international expansion",
          "score": 6,
          "reasoning": "Malta provides excellent platform for future growth"
        },
        {
          "id": "c",
          "text": "Mix aus lokalen und internationalen Kunden",
          "textEN": "Mix of local and international clients",
          "score": 8,
          "reasoning": "Good balance - Malta's tax structure provides clear advantages"
        },
        {
          "id": "d",
          "text": "Vollständig international / digitales Business",
          "textEN": "Fully international / digital business",
          "score": 10,
          "reasoning": "Perfect match - Malta maximizes advantages for location-independent business"
        },
        {
          "id": "e",
          "text": "Noch in Planung / keine Angabe",
          "textEN": "Still planning / prefer not to say",
          "score": 7,
          "reasoning": "We can help you plan the optimal international structure"
        }
      ],
      "weight": 1.5,
      "reasoning": "International orientation is key for Malta benefits"
    },
    {
      "id": "q3",
      "category": "residency",
      "question": "Sind Sie bereit, nach Malta umzuziehen und dort mindestens 183 Tage pro Jahr zu verbringen?",
      "questionEN": "Are you willing to relocate to Malta and spend at least 183 days per year there?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Nein, auf keinen Fall",
          "textEN": "No, absolutely not",
          "score": 3,
          "reasoning": "Malta structures can work with creative solutions even without full relocation"
        },
        {
          "id": "b",
          "text": "Ungern, nur wenn unbedingt nötig",
          "textEN": "Reluctantly, only if absolutely necessary",
          "score": 6,
          "reasoning": "Malta's lifestyle and climate often win people over - worth exploring"
        },
        {
          "id": "c",
          "text": "Ja, aber nur vorübergehend (2-3 Jahre)",
          "textEN": "Yes, but only temporarily (2-3 years)",
          "score": 8,
          "reasoning": "Even short-term relocation can provide significant tax benefits"
        },
        {
          "id": "d",
          "text": "Ja, langfristig bereit",
          "textEN": "Yes, willing long-term",
          "score": 10,
          "reasoning": "Long-term commitment allows full utilization of Malta's tax system"
        }
      ],
      "weight": 2.0,
      "reasoning": "Personal tax residency is important for most Malta strategies"
    },
    {
      "id": "q4",
      "category": "business-situation",
      "question": "Welches Geschäftsmodell beschreibt Ihr Unternehmen am besten?",
      "questionEN": "Which business model best describes your company?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Lokale Dienstleistung mit persönlichem Kundenkontakt",
          "textEN": "Local services with personal customer contact",
          "score": 4,
          "reasoning": "Even local services can benefit from Malta's quality of life and EU market access"
        },
        {
          "id": "b",
          "text": "E-Commerce / Handel",
          "textEN": "E-Commerce / Trading",
          "score": 7,
          "reasoning": "E-commerce works well in Malta due to flexible location and EU market access"
        },
        {
          "id": "c",
          "text": "SaaS / Digitale Produkte",
          "textEN": "SaaS / Digital Products",
          "score": 9,
          "reasoning": "Digital products are ideal for Malta - location-independent with high margins"
        },
        {
          "id": "d",
          "text": "Holding / Beteiligungsgesellschaft",
          "textEN": "Holding / Investment Company",
          "score": 10,
          "reasoning": "Malta's participation exemption makes it excellent for holding structures"
        },
        {
          "id": "e",
          "text": "Beratung / Professional Services (ortsunabhängig)",
          "textEN": "Consulting / Professional Services (location-independent)",
          "score": 8,
          "reasoning": "Location-independent services fit Malta well, especially with international clients"
        }
      ],
      "weight": 1.5,
      "reasoning": "Business model determines how well it can leverage Malta's structure"
    },
    {
      "id": "q5",
      "category": "substance",
      "question": "Können Sie echte wirtschaftliche Substanz in Malta aufbauen (Büro, Mitarbeiter, Management)?",
      "questionEN": "Can you build genuine economic substance in Malta (office, employees, management)?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Nein, nur Briefkastenfirma ohne Aktivität",
          "textEN": "No, only mailbox company without activity",
          "score": 3,
          "reasoning": "Minimal setup is possible for certain business models - we can help you find compliant solutions"
        },
        {
          "id": "b",
          "text": "Minimale Substanz (Virtual Office, keine Mitarbeiter)",
          "textEN": "Minimal substance (virtual office, no employees)",
          "score": 5,
          "reasoning": "Good starting point - can be expanded as your business grows in Malta"
        },
        {
          "id": "c",
          "text": "Moderate Substanz (kleines Büro, 1-2 lokale Teilzeitmitarbeiter)",
          "textEN": "Moderate substance (small office, 1-2 local part-time employees)",
          "score": 8,
          "reasoning": "Excellent level of substance that satisfies requirements for most business types"
        },
        {
          "id": "d",
          "text": "Volle Substanz (eigenes Büro, mehrere Vollzeitmitarbeiter, Management vor Ort)",
          "textEN": "Full substance (own office, multiple full-time employees, management on-site)",
          "score": 10,
          "reasoning": "Gold standard - fully compliant with all substance requirements"
        }
      ],
      "weight": 2.0,
      "reasoning": "Substance requirements are important for compliance"
    },
    {
      "id": "q6",
      "category": "investment",
      "question": "Welches Budget haben Sie für Setup und jährliche Compliance-Kosten eingeplant?",
      "questionEN": "What budget have you allocated for setup and annual compliance costs?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Unter 10.000 EUR/Jahr",
          "textEN": "Below 10,000 EUR/year",
          "score": 4,
          "reasoning": "Lean setup possible - Malta can work even with modest budgets for simple structures"
        },
        {
          "id": "b",
          "text": "10.000 - 25.000 EUR/Jahr",
          "textEN": "10,000 - 25,000 EUR/year",
          "score": 6,
          "reasoning": "Good budget for streamlined Malta setup - covers essentials comfortably"
        },
        {
          "id": "c",
          "text": "25.000 - 50.000 EUR/Jahr",
          "textEN": "25,000 - 50,000 EUR/year",
          "score": 8,
          "reasoning": "Very comfortable budget for professional Malta structure with good substance"
        },
        {
          "id": "d",
          "text": "50.000 - 100.000 EUR/Jahr",
          "textEN": "50,000 - 100,000 EUR/year",
          "score": 9,
          "reasoning": "Comfortable budget allowing for proper substance and premium advisory"
        },
        {
          "id": "e",
          "text": "Über 100.000 EUR/Jahr",
          "textEN": "Above 100,000 EUR/year",
          "score": 10,
          "reasoning": "Ample budget for comprehensive setup with full substance and optimization"
        }
      ],
      "weight": 1.5,
      "reasoning": "Budget is important for realistic planning"
    },
    {
      "id": "q7",
      "category": "international",
      "question": "Haben Sie bereits internationale Strukturen oder planen Sie welche?",
      "questionEN": "Do you already have international structures or are you planning some?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Neugründung - plane internationale Struktur von Anfang an",
          "textEN": "New venture - planning international structure from the start",
          "score": 9,
          "reasoning": "Excellent - building correctly from day one maximizes Malta benefits"
        },
        {
          "id": "b",
          "text": "Noch keine, aber plane zukünftige Expansion",
          "textEN": "Not yet, but planning future expansion",
          "score": 7,
          "reasoning": "Smart planning - Malta provides perfect foundation for growth"
        },
        {
          "id": "c",
          "text": "Einzelne internationale Kunden/Märkte",
          "textEN": "Individual international clients/markets",
          "score": 7,
          "reasoning": "Good foundation - Malta can optimize your international activities"
        },
        {
          "id": "d",
          "text": "Mehrere Märkte oder Tochtergesellschaften",
          "textEN": "Multiple markets or subsidiaries",
          "score": 10,
          "reasoning": "Perfect for Malta holding structure - significant optimization potential"
        },
        {
          "id": "e",
          "text": "Noch unklar / keine Angabe",
          "textEN": "Still unclear / prefer not to say",
          "score": 6,
          "reasoning": "We can help you explore the best structure for your plans"
        }
      ],
      "weight": 1.5,
      "reasoning": "International structures benefit most from Malta's advantages"
    },
    {
      "id": "q8",
      "category": "business-situation",
      "question": "Wie würden Sie Ihre Profitabilität einschätzen?",
      "questionEN": "How would you assess your profitability?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Neugründung - noch keine Einnahmen",
          "textEN": "New venture - no revenue yet",
          "score": 7,
          "reasoning": "Perfect timing - structure correctly from the start for maximum benefit"
        },
        {
          "id": "b",
          "text": "Moderate Margen (10-25%)",
          "textEN": "Moderate margins (10-25%)",
          "score": 7,
          "reasoning": "Good profitability - Malta benefits clearly worthwhile"
        },
        {
          "id": "c",
          "text": "Hohe Margen (25-50%)",
          "textEN": "High margins (25-50%)",
          "score": 9,
          "reasoning": "Excellent - Malta's tax efficiency provides substantial savings"
        },
        {
          "id": "d",
          "text": "Sehr hohe Margen (über 50%)",
          "textEN": "Very high margins (above 50%)",
          "score": 10,
          "reasoning": "Perfect for Malta - maximum tax optimization potential"
        },
        {
          "id": "e",
          "text": "Möchte ich nicht angeben / weiß noch nicht",
          "textEN": "Prefer not to say / don't know yet",
          "score": 7,
          "reasoning": "No problem - we can discuss specifics in personal consultation"
        }
      ],
      "weight": 1.5,
      "reasoning": "Profitability affects the value of Malta's tax benefits"
    },
    {
      "id": "q9",
      "category": "substance",
      "question": "Wo wird die tatsächliche Geschäftsführung und strategische Entscheidungsfindung stattfinden?",
      "questionEN": "Where will actual management and strategic decision-making take place?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Vollständig in meinem aktuellen Heimatland",
          "textEN": "Entirely in my current home country",
          "score": 4,
          "reasoning": "Can be managed with proper structuring - local directors and advisors can help"
        },
        {
          "id": "b",
          "text": "Überwiegend im Heimatland, teilweise Malta",
          "textEN": "Predominantly home country, partially Malta",
          "score": 6,
          "reasoning": "Good hybrid approach - Malta offers flexible solutions for this setup"
        },
        {
          "id": "c",
          "text": "Ausgeglichen zwischen Malta und anderen Ländern",
          "textEN": "Balanced between Malta and other countries",
          "score": 8,
          "reasoning": "Excellent balance - provides strong substance while maintaining flexibility"
        },
        {
          "id": "d",
          "text": "Vollständig in Malta (Board Meetings, strategische Entscheidungen)",
          "textEN": "Entirely in Malta (board meetings, strategic decisions)",
          "score": 10,
          "reasoning": "Gold standard - establishes clear place of effective management in Malta"
        }
      ],
      "weight": 2.0,
      "reasoning": "Place of effective management is a key substance requirement"
    },
    {
      "id": "q10",
      "category": "investment",
      "question": "Planen Sie, über Ihre Malta-Gesellschaft Beteiligungen zu halten oder zu erwerben?",
      "questionEN": "Do you plan to hold or acquire shareholdings through your Malta company?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Nein, keine Beteiligungen geplant",
          "textEN": "No, no shareholdings planned",
          "score": 5,
          "reasoning": "Malta still viable for operating business but not leveraging its strongest advantage"
        },
        {
          "id": "b",
          "text": "Möglicherweise in der Zukunft",
          "textEN": "Possibly in the future",
          "score": 7,
          "reasoning": "Good forward planning - Malta provides flexibility for future holding structure"
        },
        {
          "id": "c",
          "text": "Ja, 1-2 Beteiligungen",
          "textEN": "Yes, 1-2 shareholdings",
          "score": 9,
          "reasoning": "Good use case - Malta's participation exemption provides tax-free dividend income"
        },
        {
          "id": "d",
          "text": "Ja, mehrere Beteiligungen / komplexe Holding-Struktur",
          "textEN": "Yes, multiple shareholdings / complex holding structure",
          "score": 10,
          "reasoning": "Perfect match - Malta is one of Europe's best jurisdictions for holding structures"
        }
      ],
      "weight": 1.5,
      "reasoning": "Malta's participation exemption is excellent for holding structures"
    },
    {
      "id": "q11",
      "category": "business-situation",
      "question": "Wie wichtig ist Ihnen der Zugang zum EU-Binnenmarkt und EU-rechtliche Anerkennung?",
      "questionEN": "How important is access to the EU single market and EU legal recognition?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Unwichtig, habe keine EU-Kunden",
          "textEN": "Not important, no EU customers",
          "score": 4,
          "reasoning": "Malta less attractive if EU benefits aren't needed - other jurisdictions may be cheaper"
        },
        {
          "id": "b",
          "text": "Etwas wichtig, einige EU-Geschäfte",
          "textEN": "Somewhat important, some EU business",
          "score": 7,
          "reasoning": "Malta's EU membership provides value but not critical"
        },
        {
          "id": "c",
          "text": "Sehr wichtig, hauptsächlich EU-Geschäft",
          "textEN": "Very important, mainly EU business",
          "score": 10,
          "reasoning": "Malta perfect - low-tax EU jurisdiction with full single market access"
        },
        {
          "id": "d",
          "text": "Kritisch, benötige EU-Passporting (z.B. FinTech)",
          "textEN": "Critical, need EU passporting (e.g. FinTech)",
          "score": 10,
          "reasoning": "Malta excellent for regulated industries requiring EU license"
        }
      ],
      "weight": 1.5,
      "reasoning": "Malta's EU membership is a key advantage"
    },
    {
      "id": "q12",
      "category": "residency",
      "question": "Wie ist Ihre familiäre Situation in Bezug auf einen Malta-Umzug?",
      "questionEN": "What is your family situation regarding a Malta relocation?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Familie ist strikt gegen Umzug / unmöglich",
          "textEN": "Family strictly against move / impossible",
          "score": 4,
          "reasoning": "Alternative structures possible - doesn't have to be a dealbreaker"
        },
        {
          "id": "b",
          "text": "Familie skeptisch, könnte aber überzeugt werden",
          "textEN": "Family skeptical but could be convinced",
          "score": 7,
          "reasoning": "Many families fall in love with Malta's lifestyle, schools, and safety"
        },
        {
          "id": "c",
          "text": "Familie neutral / flexibel",
          "textEN": "Family neutral / flexible",
          "score": 8,
          "reasoning": "Good situation - family flexibility allows for successful relocation"
        },
        {
          "id": "d",
          "text": "Familie unterstützt Umzug / Single ohne Verpflichtungen",
          "textEN": "Family supports move / Single without commitments",
          "score": 10,
          "reasoning": "Ideal - no family obstacles to genuine relocation"
        }
      ],
      "weight": 1.5,
      "reasoning": "Family situation often determines relocation success"
    },
    {
      "id": "q13",
      "category": "investment",
      "question": "Haben Sie bereits professionelle Beratung zu Malta-Strukturen eingeholt?",
      "questionEN": "Have you already obtained professional advice on Malta structures?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Nein, erst am Anfang der Recherche",
          "textEN": "No, just starting research",
          "score": 5,
          "reasoning": "Normal starting point but need to engage advisors before committing"
        },
        {
          "id": "b",
          "text": "Teilweise, einige Gespräche geführt",
          "textEN": "Partially, had some discussions",
          "score": 7,
          "reasoning": "Good progress - continuing with proper due diligence"
        },
        {
          "id": "c",
          "text": "Ja, umfassende Beratung durch Malta-Spezialisten",
          "textEN": "Yes, comprehensive advice from Malta specialists",
          "score": 10,
          "reasoning": "Excellent - proper planning with qualified advisors maximizes success"
        },
        {
          "id": "d",
          "text": "Ja, aber durch nicht-spezialisierte Berater",
          "textEN": "Yes, but from non-specialized advisors",
          "score": 6,
          "reasoning": "Good start - we can complement with Malta-specific expertise"
        }
      ],
      "weight": 1.0,
      "reasoning": "Quality of advisory is important for success"
    },
    {
      "id": "q14",
      "category": "business-situation",
      "question": "Wie würden Sie Ihre Branche in Bezug auf Regulierung beschreiben?",
      "questionEN": "How would you describe your industry regarding regulation?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "Stark reguliert (FinTech, Pharma, Glücksspiel, etc.)",
          "textEN": "Highly regulated (FinTech, pharma, gambling, etc.)",
          "score": 9,
          "reasoning": "Malta has strong regulatory frameworks and licensing for many regulated industries"
        },
        {
          "id": "b",
          "text": "Moderat reguliert",
          "textEN": "Moderately regulated",
          "score": 7,
          "reasoning": "Malta handles standard regulatory requirements well"
        },
        {
          "id": "c",
          "text": "Kaum reguliert",
          "textEN": "Lightly regulated",
          "score": 6,
          "reasoning": "Malta still suitable but regulatory framework less of an advantage"
        },
        {
          "id": "d",
          "text": "Grauzone / rechtlich problematisch",
          "textEN": "Gray area / legally problematic",
          "score": 3,
          "reasoning": "Malta has clear regulatory frameworks - we can help assess if your business model fits"
        }
      ],
      "weight": 1.0,
      "reasoning": "Malta has strong regulatory frameworks for certain industries"
    },
    {
      "id": "q15",
      "category": "investment",
      "question": "Was ist Ihr Zeithorizont für die Malta-Struktur?",
      "questionEN": "What is your time horizon for the Malta structure?",
      "type": "single-choice",
      "options": [
        {
          "id": "a",
          "text": "1-2 Jahre (kurzfristig)",
          "textEN": "1-2 years (short-term)",
          "score": 5,
          "reasoning": "Even short-term can provide significant benefits - good for testing the waters"
        },
        {
          "id": "b",
          "text": "3-5 Jahre (mittelfristig)",
          "textEN": "3-5 years (medium-term)",
          "score": 7,
          "reasoning": "Reasonable timeframe for Malta structure to pay off"
        },
        {
          "id": "c",
          "text": "5-10 Jahre",
          "textEN": "5-10 years",
          "score": 9,
          "reasoning": "Good planning horizon - allows for full optimization and ROI"
        },
        {
          "id": "d",
          "text": "10+ Jahre / unbegrenzt",
          "textEN": "10+ years / unlimited",
          "score": 10,
          "reasoning": "Ideal commitment - maximizes value of Malta structure"
        }
      ],
      "weight": 1.5,
      "reasoning": "Longer commitment allows for better ROI"
    }
  ],
  "scoringThresholds": {
    "excellent": {
      "min": 75,
      "label": "Malta ist hervorragend geeignet",
      "labelEN": "Malta is an excellent match",
      "description": "Perfekt! Ihre Situation ist ideal für Malta. Sie können von allen Vorteilen profitieren - lassen Sie uns die Details besprechen!",
      "descriptionEN": "Perfect! Your situation is ideal for Malta. You can benefit from all advantages - let's discuss the details!"
    },
    "good": {
      "min": 55,
      "label": "Malta ist sehr gut geeignet",
      "labelEN": "Malta is very well suited",
      "description": "Großartig! Malta bietet signifikante Vorteile für Ihre Situation. Mit der richtigen Planung wird dies ein Erfolg.",
      "descriptionEN": "Great! Malta offers significant advantages for your situation. With proper planning, this will be a success."
    },
    "moderate": {
      "min": 35,
      "label": "Malta ist gut geeignet",
      "labelEN": "Malta is well suited",
      "description": "Malta bietet gute Möglichkeiten für Sie. Mit einigen Anpassungen können Sie optimal profitieren.",
      "descriptionEN": "Malta offers good opportunities for you. With some adjustments, you can benefit optimally."
    },
    "fair": {
      "min": 20,
      "label": "Malta ist möglich",
      "labelEN": "Malta is possible",
      "description": "Malta könnte für Sie funktionieren. Lassen Sie uns gemeinsam herausfinden, wie wir dies optimal gestalten.",
      "descriptionEN": "Malta could work for you. Let's find out together how we can optimize this."
    },
    "explore": {
      "min": 0,
      "label": "Lassen Sie uns sprechen",
      "labelEN": "Let's talk",
      "description": "Ihre Situation erfordert eine individuelle Beratung. Kontaktieren Sie uns für ein persönliches Gespräch über Ihre Möglichkeiten.",
      "descriptionEN": "Your situation requires individual consultation. Contact us for a personal discussion about your options."
    }
  },
  "additionalNotes": [
    "Malta bietet flexible Lösungen für verschiedenste Situationen - auch wenn nicht alle Kriterien perfekt passen",
    "Malta offers flexible solutions for various situations - even if not all criteria match perfectly",
    "Die Kombination aus niedrigen Steuern, EU-Mitgliedschaft und hoher Lebensqualität macht Malta einzigartig",
    "The combination of low taxes, EU membership, and high quality of life makes Malta unique",
    "Wir helfen Ihnen, die optimale Struktur für Ihre spezifische Situation zu finden",
    "We help you find the optimal structure for your specific situation",
    "Jede Situation ist individuell - kontaktieren Sie uns für eine persönliche Beratung",
    "Every situation is individual - contact us for personal consultation"
  ]
};
