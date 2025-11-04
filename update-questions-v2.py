#!/usr/bin/env python3
import re

# Read the v2 questions JSON
v2_questions_js = """        // Questions Data
        const questions = [
            {
                id: 'q001',
                text: 'Was beschreibt Ihre geschäftliche Situation am besten?',
                helper: 'Wählen Sie die Option, die am besten auf Sie zutrifft.',
                type: 'single_choice',
                required: true,
                weight: 2.0,
                options: [
                    { value: '1', label: 'Ich plane, in Malta ein komplett neues Business zu starten', score: 8 },
                    { value: '2', label: 'Ich habe ein bestehendes Business (unter 500k EUR Umsatz)', score: 6 },
                    { value: '3', label: 'Ich habe ein etabliertes Business (500k - 2 Mio. EUR)', score: 8 },
                    { value: '4', label: 'Ich habe ein größeres Business (über 2 Mio. EUR)', score: 10 },
                    { value: '5', label: 'Ich möchte mich erstmal informieren / keine Angabe', score: 7 }
                ]
            },
            {
                id: 'q002',
                text: 'Wie international ist Ihr Business ausgerichtet (oder soll es sein)?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 1.5,
                options: [
                    { value: '1', label: 'Neues Business - plane internationale Ausrichtung', score: 8 },
                    { value: '2', label: 'Hauptsächlich lokal, aber offen für internationale Expansion', score: 6 },
                    { value: '3', label: 'Mix aus lokalen und internationalen Kunden', score: 8 },
                    { value: '4', label: 'Vollständig international / digitales Business', score: 10 },
                    { value: '5', label: 'Noch in Planung / keine Angabe', score: 7 }
                ]
            },
            {
                id: 'q003',
                text: 'Sind Sie bereit, nach Malta umzuziehen und dort mindestens 183 Tage pro Jahr zu verbringen?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 2.0,
                options: [
                    { value: '1', label: 'Nein, auf keinen Fall', score: 3 },
                    { value: '2', label: 'Ungern, nur wenn unbedingt nötig', score: 6 },
                    { value: '3', label: 'Ja, aber nur vorübergehend (2-3 Jahre)', score: 8 },
                    { value: '4', label: 'Ja, langfristig bereit', score: 10 }
                ]
            },
            {
                id: 'q004',
                text: 'Welches Geschäftsmodell beschreibt Ihr Unternehmen am besten?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 1.5,
                options: [
                    { value: '1', label: 'Lokale Dienstleistung mit persönlichem Kundenkontakt', score: 4 },
                    { value: '2', label: 'E-Commerce / Handel', score: 7 },
                    { value: '3', label: 'SaaS / Digitale Produkte', score: 9 },
                    { value: '4', label: 'Holding / Beteiligungsgesellschaft', score: 10 },
                    { value: '5', label: 'Beratung / Professional Services (ortsunabhängig)', score: 8 }
                ]
            },
            {
                id: 'q005',
                text: 'Können Sie echte wirtschaftliche Substanz in Malta aufbauen (Büro, Mitarbeiter, Management)?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 2.0,
                options: [
                    { value: '1', label: 'Nein, nur Briefkastenfirma ohne Aktivität', score: 3 },
                    { value: '2', label: 'Minimale Substanz (Virtual Office, keine Mitarbeiter)', score: 5 },
                    { value: '3', label: 'Moderate Substanz (kleines Büro, 1-2 lokale Teilzeitmitarbeiter)', score: 8 },
                    { value: '4', label: 'Volle Substanz (eigenes Büro, mehrere Vollzeitmitarbeiter, Management vor Ort)', score: 10 }
                ]
            },
            {
                id: 'q006',
                text: 'Welches Budget haben Sie für Setup und jährliche Compliance-Kosten eingeplant?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 1.5,
                options: [
                    { value: '1', label: 'Unter 10.000 EUR/Jahr', score: 4 },
                    { value: '2', label: '10.000 - 25.000 EUR/Jahr', score: 6 },
                    { value: '3', label: '25.000 - 50.000 EUR/Jahr', score: 8 },
                    { value: '4', label: '50.000 - 100.000 EUR/Jahr', score: 9 },
                    { value: '5', label: 'Über 100.000 EUR/Jahr', score: 10 }
                ]
            },
            {
                id: 'q007',
                text: 'Haben Sie bereits internationale Strukturen oder planen Sie welche?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 1.5,
                options: [
                    { value: '1', label: 'Neugründung - plane internationale Struktur von Anfang an', score: 9 },
                    { value: '2', label: 'Noch keine, aber plane zukünftige Expansion', score: 7 },
                    { value: '3', label: 'Einzelne internationale Kunden/Märkte', score: 7 },
                    { value: '4', label: 'Mehrere Märkte oder Tochtergesellschaften', score: 10 },
                    { value: '5', label: 'Noch unklar / keine Angabe', score: 6 }
                ]
            },
            {
                id: 'q008',
                text: 'Wie würden Sie Ihre Profitabilität einschätzen?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 1.5,
                options: [
                    { value: '1', label: 'Neugründung - noch keine Einnahmen', score: 7 },
                    { value: '2', label: 'Moderate Margen (10-25%)', score: 7 },
                    { value: '3', label: 'Hohe Margen (25-50%)', score: 9 },
                    { value: '4', label: 'Sehr hohe Margen (über 50%)', score: 10 },
                    { value: '5', label: 'Möchte ich nicht angeben / weiß noch nicht', score: 7 }
                ]
            },
            {
                id: 'q009',
                text: 'Wo wird die tatsächliche Geschäftsführung und strategische Entscheidungsfindung stattfinden?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 2.0,
                options: [
                    { value: '1', label: 'Vollständig in meinem aktuellen Heimatland', score: 4 },
                    { value: '2', label: 'Überwiegend im Heimatland, teilweise Malta', score: 6 },
                    { value: '3', label: 'Ausgeglichen zwischen Malta und anderen Ländern', score: 8 },
                    { value: '4', label: 'Vollständig in Malta (Board Meetings, strategische Entscheidungen)', score: 10 }
                ]
            },
            {
                id: 'q010',
                text: 'Planen Sie, über Ihre Malta-Gesellschaft Beteiligungen zu halten oder zu erwerben?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 1.5,
                options: [
                    { value: '1', label: 'Nein, keine Beteiligungen geplant', score: 5 },
                    { value: '2', label: 'Möglicherweise in der Zukunft', score: 7 },
                    { value: '3', label: 'Ja, 1-2 Beteiligungen', score: 9 },
                    { value: '4', label: 'Ja, mehrere Beteiligungen / komplexe Holding-Struktur', score: 10 }
                ]
            },
            {
                id: 'q011',
                text: 'Wie wichtig ist Ihnen der Zugang zum EU-Binnenmarkt und EU-rechtliche Anerkennung?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 1.5,
                options: [
                    { value: '1', label: 'Unwichtig, habe keine EU-Kunden', score: 4 },
                    { value: '2', label: 'Etwas wichtig, einige EU-Geschäfte', score: 7 },
                    { value: '3', label: 'Sehr wichtig, hauptsächlich EU-Geschäft', score: 10 },
                    { value: '4', label: 'Kritisch, benötige EU-Passporting (z.B. FinTech)', score: 10 }
                ]
            },
            {
                id: 'q012',
                text: 'Wie ist Ihre familiäre Situation in Bezug auf einen Malta-Umzug?',
                helper: '',
                type: 'single_choice',
                required: true,
                weight: 1.5,
                options: [
                    { value: '1', label: 'Familie ist strikt gegen Umzug / unmöglich', score: 4 },
                    { value: '2', label: 'Familie skeptisch, könnte aber überzeugt werden', score: 7 },
                    { value: '3', label: 'Familie neutral / flexibel', score: 8 },
                    { value: '4', label: 'Familie unterstützt Umzug / Single ohne Verpflichtungen', score: 10 }
                ]
            },
            {
                id: 'q013',
                text: 'Optional: Beschreiben Sie Ihre Situation kurz mit einigen Worten!',
                helper: '',
                type: 'textarea',
                required: false,
                score: 0
            },
            {
                id: 'q014',
                text: 'Optional: Was Sie uns sonst noch wissen lassen möchten',
                helper: '',
                type: 'textarea',
                required: false,
                score: 0
            },
            {
                id: 'q015_contact',
                text: 'Der letzte Schritt',
                helper: 'Geben Sie Ihre Kontaktdaten an, damit wir Ihnen den Zugang zu Ihrem Ergebnis zusenden können.',
                type: 'contact',
                required: true
            }
        ];"""

# Read the file
with open('/Users/christoph/Brixon Dropbox/Christoph Sauerborn/Downloads/quickcheck20/qc/public/malta-assessment-v2/index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Find and replace the questions array
# Pattern to match from "const questions = [" to the closing "];"
pattern = r'(// Questions Data\s+const questions = \[)(.*?)(\];)'
replacement = v2_questions_js

content = re.sub(pattern, replacement, content, flags=re.DOTALL)

# Write back
with open('/Users/christoph/Brixon Dropbox/Christoph Sauerborn/Downloads/quickcheck20/qc/public/malta-assessment-v2/index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("✅ Questions updated successfully!")
