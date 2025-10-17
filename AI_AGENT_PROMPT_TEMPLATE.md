# B2B Marketing Assessment - Notion DB Integration

**Zweck:** Dieses Dokument erklärt, wie der AI Agent die Assessment-Daten aus dem Webhook-Payload extrahiert und für die Notion-Datenbank aufbereitet.

**Zielgruppe:** AI Agent (für n8n Automation)

---

## Deine Aufgabe

Du erhältst Assessment-Daten via Webhook und bereitest sie für die Notion-Datenbank auf. Die Daten sollen so strukturiert werden, dass wir später:

1. **Kundenantworten nachvollziehen können** - Was hat der Kunde bei jeder Frage geantwortet?
2. **Scores analysieren können** - Wie hat der Kunde in jeder Phase abgeschnitten?
3. **Follow-up priorisieren können** - Welche Empfehlungen haben wir ausgesprochen?

---

## Webhook Payload Struktur

### Übersicht

```json
{
  "event": "assessment_completed",
  "assessmentId": "uuid-string",
  "timestamp": "2025-10-17T14:32:15.000Z",
  "language": "de",
  "answersDetailed": [ /* Array mit allen Frage-Antwort-Paaren */ ],
  "scores": { /* Scores pro Phase */ },
  "interpretations": { /* Interpretationen pro Phase */ },
  "recommendations": [ /* Priorisierte Empfehlungen */ ]
}
```

---

## 1. Assessment Metadata

Diese Felder identifizieren das Assessment:

| Feld | Typ | Beschreibung | Notion Feld |
|------|-----|--------------|-------------|
| `assessmentId` | String (UUID) | Eindeutige ID des Assessments | **Assessment ID** (Text) |
| `timestamp` | String (ISO 8601) | Zeitpunkt der Fertigstellung | **Completed At** (Date) |
| `language` | String (`de` / `en`) | Sprache des Assessments | **Language** (Select) |

---

## 2. Detailed Answers (answersDetailed)

Ein Array mit allen Frage-Antwort-Paaren. Jedes Element hat folgende Struktur:

### Struktur

```json
{
  "questionId": "q_101",
  "section": "reach",
  "sectionTitle": "Phase 1: Reach - Werden Sie überhaupt gefunden?",
  "questionText": "Wie aktiv sind Sie oder Ihre Führungskräfte persönlich auf LinkedIn?",
  "questionHint": "Wichtig: Wir meinen persönliche Profile...",
  "questionType": "single",
  "answerValue": "4",
  "answerText": "Sehr aktiv mit strategischem Content",
  "score": 4
}
```

### Felder Erklärung

| Feld | Typ | Beschreibung |
|------|-----|--------------|
| `questionId` | String | Interne ID der Frage (z.B. `q_101`) |
| `section` | String | Phase: `reach`, `relate`, `respond`, `retain` |
| `sectionTitle` | String | Titel der Phase (mit Sprache) |
| `questionText` | String | **Voller Frage-Text** (das ist wichtig!) |
| `questionHint` | String | Zusätzlicher Hinweis (kann leer sein) |
| `questionType` | String | `single`, `multi`, `text`, `textarea`, `email` |
| `answerValue` | String | Rohwert (Code oder Text) |
| `answerText` | String | **Lesbarer Antwort-Text** (das ist wichtig!) |
| `score` | Number/null | Punkte für diese Antwort (kann null sein) |

### Notion DB Schema für Answers

Erstelle eine **separate Notion-Datenbank** für Antworten (1:n Relation zum Assessment):

| Notion Property | Type | Mapping |
|----------------|------|---------|
| **Assessment** | Relation | → Assessment ID |
| **Question ID** | Text | `questionId` |
| **Section** | Select | `section` (Reach/Relate/Respond/Retain) |
| **Question** | Text | `questionText` |
| **Answer** | Text | `answerText` |
| **Score** | Number | `score` |
| **Type** | Select | `questionType` |

**Wichtig:** Für jedes Element im `answersDetailed` Array wird ein neuer Eintrag in der Answers-DB erstellt.

---

## 3. Scores

Punktzahlen pro Phase und gesamt:

```json
{
  "reach": 18,
  "relate": 15,
  "respond": 12,
  "retain": 10,
  "total": 55
}
```

### Notion DB Schema für Scores

Diese Felder kommen direkt in die **Haupt-Assessment-DB**:

| Notion Property | Type | Mapping |
|----------------|------|---------|
| **Score Reach** | Number | `scores.reach` |
| **Score Relate** | Number | `scores.relate` |
| **Score Respond** | Number | `scores.respond` |
| **Score Retain** | Number | `scores.retain` |
| **Score Total** | Number | `scores.total` |

---

## 4. Interpretations

Interpretationen für jede Phase + Overall:

```json
{
  "reach": {
    "percentage": 51,
    "title": "Sichtbar mit Potenzial",
    "description": "Sie sind aktiv und werden wahrgenommen..."
  },
  "relate": { /* ... */ },
  "respond": { /* ... */ },
  "retain": { /* ... */ },
  "overall": {
    "percentage": 45,
    "title": "Solide Basis im Aufbau",
    "description": "Einige Hebel sind gesetzt..."
  }
}
```

### Notion DB Schema für Interpretations

Auch in der **Haupt-Assessment-DB**:

| Notion Property | Type | Mapping |
|----------------|------|---------|
| **Overall Percentage** | Number | `interpretations.overall.percentage` |
| **Overall Title** | Text | `interpretations.overall.title` |
| **Overall Description** | Text | `interpretations.overall.description` |
| **Reach Percentage** | Number | `interpretations.reach.percentage` |
| **Reach Title** | Text | `interpretations.reach.title` |
| **Relate Percentage** | Number | `interpretations.relate.percentage` |
| **Relate Title** | Text | `interpretations.relate.title` |
| **Respond Percentage** | Number | `interpretations.respond.percentage` |
| **Respond Title** | Text | `interpretations.respond.title` |
| **Retain Percentage** | Number | `interpretations.retain.percentage` |
| **Retain Title** | Text | `interpretations.retain.title` |

---

## 5. Recommendations

Array mit priorisierten Empfehlungen (max. 4 Einträge):

```json
[
  {
    "phase": "respond",
    "phaseBadge": "Phase 3",
    "phaseTitle": "Respond",
    "title": "Sales-Alignment herstellen",
    "text": "Definieren Sie gemeinsame KPIs, Response-SLAs...",
    "meta": "Aktueller Score: 44%",
    "priority": 1
  },
  {
    "phase": "retain",
    "phaseBadge": "Phase 4",
    "phaseTitle": "Retain",
    "title": "Bestandskunden aktivieren",
    "text": "Starten Sie systematische Kundenprogramme...",
    "meta": "Aktueller Score: 37%",
    "priority": 2
  }
]
```

### Notion DB Schema für Recommendations

Erstelle eine **separate Notion-Datenbank** für Empfehlungen (1:n Relation zum Assessment):

| Notion Property | Type | Mapping |
|----------------|------|---------|
| **Assessment** | Relation | → Assessment ID |
| **Priority** | Number | `priority` |
| **Phase** | Select | `phase` (reach/relate/respond/retain/overall) |
| **Title** | Text | `title` |
| **Description** | Text | `text` |
| **Meta** | Text | `meta` |

---

## Beispiel: Notion DB Struktur

### Datenbank 1: Assessments (Haupt-DB)

| Assessment ID | Completed At | Language | Score Total | Overall Title | ... |
|--------------|--------------|----------|-------------|---------------|-----|
| abc-123 | 2025-10-17 | de | 55 | Solide Basis im Aufbau | ... |

### Datenbank 2: Answers (1:n zu Assessment)

| Assessment | Question | Answer | Score |
|-----------|----------|--------|-------|
| abc-123 | Wie aktiv sind Sie auf LinkedIn? | Sehr aktiv mit strategischem Content | 4 |
| abc-123 | Worüber schreiben Sie hauptsächlich? | Über konkrete Probleme unserer Zielgruppe | 5 |
| ... | ... | ... | ... |

### Datenbank 3: Recommendations (1:n zu Assessment)

| Assessment | Priority | Phase | Title | Description |
|-----------|----------|-------|-------|-------------|
| abc-123 | 1 | respond | Sales-Alignment herstellen | Definieren Sie gemeinsame KPIs... |
| abc-123 | 2 | retain | Bestandskunden aktivieren | Starten Sie systematische... |

---

## AI Agent Workflow (n8n)

### Schritt 1: Webhook empfangen
- Parse JSON payload
- Validiere `event === 'assessment_completed'`

### Schritt 2: Assessment-Eintrag erstellen
- Erstelle neuen Eintrag in **Assessments DB**
- Felder: `assessmentId`, `timestamp`, `language`, alle Scores, alle Interpretations

### Schritt 3: Answers speichern
- Iteriere über `answersDetailed` Array
- Für jedes Element: Erstelle Eintrag in **Answers DB**
- Verknüpfe mit Assessment via Relation

### Schritt 4: Recommendations speichern
- Iteriere über `recommendations` Array
- Für jedes Element: Erstelle Eintrag in **Recommendations DB**
- Verknüpfe mit Assessment via Relation

### Schritt 5: Notification (optional)
- Sende Slack/E-Mail-Benachrichtigung
- Info: Neues Assessment mit Overall Score + Top-Empfehlung

---

## Wichtige Hinweise

### 1. Frage-Antwort-Paare sind lesbar
**Keine Codes!** Die Felder `questionText` und `answerText` enthalten den vollen Text:
- ✅ `"questionText": "Wie aktiv sind Sie auf LinkedIn?"`
- ✅ `"answerText": "Sehr aktiv mit strategischem Content"`
- ❌ NICHT: `"q_101": "4"`

### 2. Freitext-Antworten
Bei `questionType: "text"`, `"textarea"` oder `"email"`:
- `answerValue` === `answerText` (kein Mapping nötig)
- `score` ist `null` (keine Punkte für Freitext)

### 3. Multi-Select-Fragen
Bei `questionType: "multi"`:
- `answerValue` ist ein Array: `["option1", "option2"]`
- `answerText` ist ein komma-separierter String: `"Option 1, Option 2"`

### 4. Fehlende Antworten
Nicht beantwortete Fragen sind NICHT im `answersDetailed` Array enthalten.

---

## Testing

### Test-Payload generieren
Öffne das Assessment lokal und fülle es aus:
```bash
cd "public/assessment"
python3 -m http.server 8000
```
Öffne: http://localhost:8000

### n8n Webhook testen
```bash
curl -X POST https://brixon.app.n8n.cloud/webhook-test/brixon-b2b-marketing-assessment \
  -H "Content-Type: application/json" \
  -d @test-payload.json
```

---

## Zusammenfassung

**Der AI Agent muss:**
1. ✅ Webhook-Payload validieren
2. ✅ Assessment-Eintrag in Notion erstellen
3. ✅ Alle Frage-Antwort-Paare als separate Einträge speichern (mit **lesbarem Text**)
4. ✅ Alle Empfehlungen als separate Einträge speichern
5. ✅ Relations korrekt setzen (Answers → Assessment, Recommendations → Assessment)

**Der AI Agent muss NICHT:**
- ❌ Dem Kunden Ergebnisse erklären (das macht die Assessment-Webseite)
- ❌ Interpretation oder Analyse durchführen (ist schon im Payload)
- ❌ E-Mails an den Kunden senden (separate Automation)

---

**Version:** 2.0 (Notion DB Focus)
**Letzte Änderung:** 2025-10-17
**Status:** ✅ Production Ready
