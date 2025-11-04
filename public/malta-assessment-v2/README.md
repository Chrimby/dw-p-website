# Malta Eignungscheck - Standalone Assessment Tool

## Übersicht
Dieses Assessment-Tool ist eine Portierung des ursprünglichen Malta QuickCheck Systems von Dr. Werner & Partner. Es basiert auf dem Brixon Assessment Framework und berechnet einen Eignungsgrad von 0-100% für Malta als Unternehmensstandort.

## Features
- ✅ 15 Fragen (12 bewertete Fragen + 3 optionale Fragen)
- ✅ Automatische Score-Berechnung (0-100%)
- ✅ Drei Eignungskategorien:
  - **Niedrig** (0-29%): Eher ungeeignet
  - **Mittel** (30-59%): Bedingt geeignet
  - **Hoch** (60-100%): Sehr geeignet
- ✅ Detaillierte Auswertung mit positiven, neutralen und kritischen Faktoren
- ✅ Webhook-Integration für CRM/Marketing Automation
- ✅ Responsive Design für alle Geräte
- ✅ Branding: Schwarz/Weiß mit #f7e74f Akzent

## Fragenstruktur

### Bewertete Fragen (mit Scoring)
1. **Beschäftigungsverhältnis** (Angestellt/Einzelunternehmer/Unternehmer)
2. **Rechtsform** (Einzelunternehmen/Personengesellschaft/Kapitalgesellschaft/Ohne)
3. **Physische Kundentermine** (Ja/Nein/Keine Termine)
4. **Auslandsleben** (Ja/Nein/Weiß nicht)
5. **Malta spezifisch** (Ja/Nein/Weiß nicht)
6. **Perspektivischer Umsatz** (4 Kategorien)
7. **Perspektivischer Gewinn** (4 Kategorien)
8. **Standortbindung** (Wohnsitzland/Eher Wohnsitzland/Eher international/Sehr international)
9. **Unternehmensfortschritt** (0-10%/10-30%/>90% teilweise/>90% komplett)
10. **Physische Aufstellung** (Hohe/Eher hohe/Eher geringe/Keine Physis)
11. **Interesse an Malta** (Ja/Ja für Unternehmung/Naja/Nein)

### Nicht-bewertete Fragen
- **Branche** (Textfeld, nur für Analyse)
- **Situationsbeschreibung** (Textarea, optional)
- **Sonstige Anmerkungen** (Textarea, optional)

### Kontaktformular (Pflicht)
- Anrede (Herr/Frau)
- Vorname
- Nachname
- E-Mail
- Datenschutz-Checkbox

## Scoring-System

### Scoring-Logik
Jede bewertete Frage hat eine maximale Punktzahl von 10 Punkten. Die Antworten werden unterschiedlich gewichtet:

```javascript
// Beispiel Frage 1: Beschäftigungsverhältnis
Angestellter: 5 Punkte (50%)
Einzelunternehmer: 7 Punkte (70%)
Unternehmer: 10 Punkte (100%)

// Beispiel Frage 3: Kundentermine
Ja (physisch vor Ort): 2 Punkte (20%)
Nein: 10 Punkte (100%)
Keine Termine: 8 Punkte (80%)
```

### Kategorisierung der Ergebnisse

**Detail-Kategorien** (für einzelne Antworten):
- **Positiv**: Score >= 8 (Grün)
- **Neutral**: Score 4-7 (Gelb)
- **Kritisch**: Score <= 3 (Rot)

**Gesamt-Kategorien**:
- **Niedrig** (0-29%): Malta eher ungeeignet
- **Mittel** (30-59%): Malta bedingt geeignet
- **Hoch** (60-100%): Malta sehr geeignet

## WordPress Integration

### Methode 1: Als separate Seite einbinden

1. **Datei hochladen:**
   ```bash
   # Ordner erstellen
   mkdir -p /wp-content/uploads/malta-assessment/

   # Datei hochladen
   cp index.html /wp-content/uploads/malta-assessment/
   ```

2. **WordPress Seite erstellen:**
   - Gehen Sie zu WordPress → Seiten → Neu hinzufügen
   - Titel: "Malta Eignungscheck"
   - Template: "Vollbreite" (falls verfügbar)

3. **HTML Block einfügen:**
   ```html
   <iframe
       src="/wp-content/uploads/malta-assessment/index.html"
       style="width: 100%; min-height: 100vh; border: none;"
       title="Malta Eignungscheck">
   </iframe>
   ```

### Methode 2: Direkt in WordPress einbetten

1. **Custom HTML Block** in WordPress erstellen
2. **Kompletten Inhalt** der `index.html` Datei kopieren
3. **In den Block einfügen**

### Methode 3: Shortcode (Empfohlen)

Fügen Sie in `functions.php` ein:

```php
function malta_assessment_shortcode() {
    ob_start();
    include(ABSPATH . 'wp-content/uploads/malta-assessment/index.html');
    return ob_get_clean();
}
add_shortcode('malta_assessment', 'malta_assessment_shortcode');
```

Verwendung: `[malta_assessment]` auf jeder Seite

## Webhook Konfiguration

### 1. Webhook URL setzen

Öffnen Sie `index.html` und suchen Sie nach:

```javascript
const CONFIG = {
    webhookUrl: 'https://brixon.app.n8n.cloud/webhook/malta-eignungscheck',
};
```

Ersetzen Sie die URL mit Ihrer eigenen Webhook-URL.

### 2. Webhook Payload Format

Der Webhook sendet folgende Daten als JSON:

```json
{
  "timestamp": "2025-11-03T10:30:00.000Z",
  "contact": {
    "geschlecht": "Herr",
    "vorname": "Max",
    "nachname": "Mustermann",
    "email": "max@example.com",
    "terms": true
  },
  "answers": {
    "q001": "3",
    "q002": "3",
    "q003": "2",
    "q004": "IT-Consulting",
    "q005": "1",
    "q006": "1",
    "q007": "4",
    "q008": "3",
    "q009": "4",
    "q010": "4",
    "q011": "4",
    "q012": "1",
    "q013": "Ich suche nach Steueroptimierung...",
    "q014": "Weitere Anmerkungen..."
  },
  "score": {
    "percentage": 85,
    "totalScore": 93,
    "maxScore": 110,
    "category": "high"
  },
  "interpretation": "Malta eignet sich für Sie! ...",
  "detailedResults": [
    {
      "questionId": "q001",
      "questionText": "Wie sind Sie aktuell beschäftigt?",
      "answer": "Unternehmer",
      "answerDescription": "Inhaber eines Unternehmens...",
      "score": 10,
      "category": "positive"
    }
    // ... weitere Ergebnisse
  ]
}
```

### 3. Empfohlene Webhook-Services

**Make.com (Integromat)**
```
https://hook.eu1.make.com/xxxxxxxxxxxxx
```

**Zapier**
```
https://hooks.zapier.com/hooks/catch/xxxxx/xxxxx/
```

**n8n (Self-hosted)**
```
https://your-n8n-instance.com/webhook/malta-eignungscheck
```

### 4. Webhook Testing

Zum Testen können Sie nutzen:
- [webhook.site](https://webhook.site) - Zeigt alle eingehenden Requests
- [requestbin.com](https://requestbin.com) - Ähnlich wie webhook.site

## Anpassungen

### Farben ändern

```css
:root {
    --color-black: #0a0a0a;      /* Hauptfarbe */
    --color-white: #ffffff;      /* Hintergrund */
    --color-accent: #f7e74f;     /* Akzentfarbe (Gelb) */

    /* Kategorie-Farben */
    --color-success: #22c55e;    /* Grün für "Positiv" */
    --color-warning: #eab308;    /* Gelb für "Neutral" */
    --color-danger: #ef4444;     /* Rot für "Kritisch" */
}
```

### Texte anpassen

#### Willkommenstext
Suchen Sie nach `.welcome-title` und `.welcome-text`

#### Interpretationen
Suchen Sie in der `calculateAndShowResults()` Funktion nach:
```javascript
if (percentage < 30) {
    interpretation = 'Malta eignet sich eher nicht für Sie...';
}
```

#### CTA Texte
Suchen Sie nach `.cta-section`

### Fragen hinzufügen/entfernen

1. Öffnen Sie `index.html`
2. Suchen Sie nach `const questions = [`
3. Fügen Sie neue Fragen im gleichen Format hinzu:

```javascript
{
    id: 'q016',
    text: 'Ihre neue Frage?',
    helper: 'Optionaler Hilfetext',
    type: 'single_choice',
    required: true,
    options: [
        { value: '1', label: 'Option 1', description: '', score: 5 },
        { value: '2', label: 'Option 2', description: '', score: 10 }
    ]
}
```

**Verfügbare Fragetypen:**
- `single_choice` - Multiple Choice mit einer Antwort
- `text` - Einzeiliges Textfeld
- `textarea` - Mehrzeiliges Textfeld
- `contact` - Kontaktformular (nur einmal verwenden!)

### Scoring-Grenzen anpassen

Suchen Sie nach der `calculateAndShowResults()` Funktion:

```javascript
if (percentage < 30) {
    category = 'low';
    categoryLabel = 'Eher ungeeignet';
} else if (percentage < 60) {
    category = 'medium';
    categoryLabel = 'Bedingt geeignet';
} else {
    category = 'high';
    categoryLabel = 'Sehr geeignet';
}
```

## Unterschiede zum Original

### Was wurde übernommen:
- ✅ Alle 14 Originalfragen
- ✅ Scoring-System (0-100%)
- ✅ Drei Kategorien (Niedrig/Mittel/Hoch)
- ✅ Kontaktformular mit Datenschutz
- ✅ Detaillierte Auswertung

### Was wurde modernisiert:
- ✅ Modernes, responsives Design
- ✅ Bessere UX mit Animationen
- ✅ Accessibility-Optimierungen
- ✅ Mobile-First Ansatz
- ✅ Webhook statt direkter DB-Speicherung
- ✅ Standalone HTML (keine PHP Dependencies)

### Was wurde entfernt:
- ❌ Direkte MySQL Datenbankanbindung (ersetzt durch Webhook)
- ❌ PHP Shortcodes (können optional wieder hinzugefügt werden)
- ❌ Admin-Access Parameter (nicht mehr nötig)
- ❌ 7-Tage Ablauf-Links (kann über Webhook-Backend gelöst werden)

## Vergleich: Alt vs. Neu

### Alte Lösung (functions.php)
```php
// MySQL Direkt-Verbindung
$conn = mysqli_connect($host, $user, $pass, $db);
$sql = "Select score from QC Where keyword = '$keyword'";
$result = mysqli_fetch_assoc($stmt);

// Ergebnis in WordPress Shortcodes
add_shortcode('QC-Score', 'quickcheck_score');
```

### Neue Lösung (Webhook)
```javascript
// Sende Daten an Webhook
await fetch(CONFIG.webhookUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
});

// Backend verarbeitet Daten asynchron
// - Speichert in DB/CRM
// - Sendet E-Mails
// - Triggert Automations
```

## Migration von Alt zu Neu

Falls Sie vom alten System migrieren möchten:

### 1. Datenbank Export
```sql
SELECT
    keyword,
    vorname,
    nachname,
    email,
    score,
    timestamp,
    beschreibung
FROM QC
WHERE timestamp > DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

### 2. Webhook Backend Setup
Erstellen Sie einen Webhook-Endpoint, der:
- Daten empfängt
- In Datenbank speichert
- E-Mails versendet (Ergebnis-Link)
- CRM/Marketing Tools updated

### 3. E-Mail Template
Das alte System hat E-Mails mit Links versendet:
```
https://drwerner.com/malta-ergebnis/?id=XXXXXXXXXXXXXXXXXXXXXXXXXX
```

Die neue Lösung kann ähnlich funktionieren:
- Webhook empfängt Daten
- Generiert eindeutigen Keyword (26 Zeichen)
- Speichert in DB
- Sendet E-Mail mit Link
- Ergebnisseite lädt Daten per AJAX

## Performance

**Lighthouse Scores (Target):**
- Performance: 95+
- Accessibility: 100
- Best Practices: 100
- SEO: 100

**Bundle Size:**
- HTML: ~45KB (inkl. CSS & JS)
- Keine externen Dependencies
- Nur Google Fonts (optional)

**Load Time:**
- First Contentful Paint: <1s
- Time to Interactive: <1.5s

## Browser Support

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile Safari (iOS 14+)
- ✅ Chrome Mobile

## Accessibility (WCAG 2.1)

- ✅ Keyboard Navigation
- ✅ Screen Reader Support
- ✅ Focus Indicators
- ✅ Touch Target Size (min 44x44px)
- ✅ Color Contrast (AA Standard)
- ✅ Reduced Motion Support

## Security

**Best Practices implementiert:**
- ✅ Keine direkten SQL Queries
- ✅ Content Security Policy kompatibel
- ✅ XSS Prevention durch DOM-basierte Updates
- ✅ HTTPS-only für Webhook
- ✅ Input Validation auf Client-Seite
- ⚠️ Server-seitige Validation muss im Webhook implementiert werden

## Troubleshooting

### Problem: Webhook wird nicht gesendet

**Lösung:**
1. Browser Console öffnen (F12)
2. Nach Fehler-Meldungen suchen
3. Webhook-URL überprüfen
4. CORS-Einstellungen des Webhooks prüfen

### Problem: Styling sieht anders aus in WordPress

**Lösung:**
```css
/* Fügen Sie einen Namespace hinzu */
.malta-assessment * {
    all: revert;
}
```

### Problem: Fortschrittsbalken funktioniert nicht

**Lösung:**
- JavaScript-Fehler in Console prüfen
- Stellen Sie sicher, dass kein Theme-JS interferiert

### Problem: Mobile Layout bricht

**Lösung:**
```css
/* Viewport Meta Tag prüfen */
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

## Deployment Checklist

- [ ] Webhook-URL konfiguriert
- [ ] Datenschutz-Link angepasst
- [ ] Texte & Branding angepasst
- [ ] Auf Desktop getestet
- [ ] Auf Mobile getestet
- [ ] Webhook-Empfang getestet
- [ ] E-Mail-Versand getestet
- [ ] Analytics konfiguriert (optional)
- [ ] WordPress Integration getestet

## Support & Weiterentwicklung

**Für weitere Anpassungen:**
- Code ist klar kommentiert und modular
- Alle Funktionen sind dokumentiert
- Einfach zu erweitern

**Professionelle Hilfe:**
- Kontaktieren Sie einen Web-Entwickler
- Oder die Brixon Group für Support

---

**Version:** 1.0
**Entwickelt:** November 2025
**Basierend auf:** Brixon Assessment Framework
**Original System:** Dr. Werner & Partner Malta QuickCheck
**Lizenz:** Proprietär
