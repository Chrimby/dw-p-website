# Malta Assessment Installation (Multi-Language)

**Version:** 2.0 | **Sprachen:** 🇩🇪 DE, 🇬🇧 EN, 🇳🇱 NL | **Status:** ✅ Production Ready

## Installation (10 Minuten)

### 1. functions.php öffnen

Öffne deine Theme-Datei:
```
/wp-content/themes/DEIN-THEME/functions.php
```

**Häufige Pfade:**
- Hello Elementor: `/wp-content/themes/hello-elementor/functions.php`
- Astra: `/wp-content/themes/astra/functions.php`
- Child Theme: `/wp-content/themes/DEIN-CHILD-THEME/functions.php`

### 2. Backend-Integration einfügen

**Code:** `functions-php-integration.php`

1. Öffne die Datei `functions-php-integration.php`
2. Kopiere **ALLES** (kompletter Dateiinhalt)
3. Füge es **ans Ende** deiner `functions.php` ein
4. Speichern

**Was macht der Code:**
- Registriert AJAX-Endpunkt `malta_assess_submit`
- Injiziert Nonce in `<head>`: `window.maltaAssessment`
- **Injiziert Spracherkennung**: `window.qcMaltaLanguage` + `window.qcMaltaTranslationsPath`
- Webhook zu n8n: `https://brixon.app.n8n.cloud/webhook/dwp-quickcheck`
- Rate Limiting: 10 Requests/Stunde
- Scoring-Logik (12 Fragen, gewichtet)

**✅ WICHTIG:** Die Spracherkennung ist bereits enthalten! Du brauchst **keine** zusätzliche functions.php Anpassung mehr.

### 3. Dateien hochladen

**Upload nach:** `/wp-content/uploads/malta-assessment-v2/`

```
malta-assessment-v2/
├── translations/
│   ├── de.json (15KB)
│   ├── en.json (15KB)
│   └── nl.json (15KB)
```

**Per SFTP/FTP:**
- Erstelle Ordner `malta-assessment-v2`
- Erstelle Unterordner `translations`
- Lade alle 3 JSON-Dateien hoch

**Testen:** Öffne im Browser:
```
https://deine-domain.com/wp-content/uploads/malta-assessment-v2/translations/de.json
```
Sollte JSON anzeigen (nicht 404).

### 4. WordPress-Seiten erstellen

**Erstelle 3 Seiten:**

| Sprache | URL | Template |
|---------|-----|----------|
| 🇩🇪 Deutsch | `/de/malta-eignungscheck/` | Full Width |
| 🇬🇧 Englisch | `/en/malta-suitability-check/` | Full Width |
| 🇳🇱 Niederländisch | `/nl/malta-geschiktheidscheck/` | Full Width |

**Page Settings:**
- Template: Full Width (kein Sidebar)
- Header/Footer: Optional ausblenden

### 5. Elementor-Integration

**Pro Seite:**

1. **Öffne Seite in Elementor**
2. **Füge HTML Widget hinzu**
3. **Kopiere `public/malta-assessment-v2/update.html` komplett**
4. **Einfügen** (gleicher Code für alle 3 Seiten!)
5. **Speichern & Veröffentlichen**

**Wichtig:** Gleicher HTML-Code für alle Sprachen - die Sprache wird automatisch erkannt!

### 6. Testen

**Jede Sprachseite öffnen:**

1. **Öffne `/de/malta-eignungscheck/`**
2. **F12 → Console Tab**
3. **Prüfe:**
   ```javascript
   console.log(window.qcMaltaLanguage);
   // Muss sein: "de"

   console.log(window.maltaAssessment);
   // Muss sein: {ajaxUrl: "...", nonce: "..."}
   ```

4. **Erwartete Console Logs:**
   ```
   ✅ 🌐 WordPress detected language: de
   ✅ ✅ Translations loaded: de.json from /wp-content/uploads/...
   ✅ ✅ App initialized successfully
   ```

5. **QuickCheck ausfüllen** → Absenden → Sollte Results Screen zeigen

**Wiederhole für:**
- `/en/malta-suitability-check/` → `window.qcMaltaLanguage === "en"`
- `/nl/malta-geschiktheidscheck/` → `window.qcMaltaLanguage === "nl"`

---

## Production Checklist

**Vor Go-Live:**

```php
// In functions-php-integration.php ändern:
define('MALTA_DEBUG_MODE', false);  // Zeile 25
```

**Testen (alle Sprachen):**
- [ ] DE: Lädt korrekt, alle Texte deutsch
- [ ] EN: Lädt korrekt, alle Texte englisch
- [ ] NL: Lädt korrekt, alle Texte niederländisch
- [ ] Formular sendet erfolgreich ab (kein 400/403)
- [ ] Webhook kommt in n8n an
- [ ] Results Screen zeigt korrekte Sprache
- [ ] EN: Q011 Option 1 hat Score 2 (nicht 4) - siehe Console Log

---

## Troubleshooting

### Problem: Seite zeigt immer Deutsch (auch auf /en/ oder /nl/)

**Diagnose:**
```javascript
console.log(window.qcMaltaLanguage); // Was steht hier?
```

**Lösungen:**
- `undefined` → Spracherkennung-Code nicht in functions.php
- Falscher Wert → URL enthält nicht `/en/` oder `/nl/`
- Richtig, aber Übersetzung lädt nicht → JSON-Dateien nicht hochgeladen

**Test:** Öffne direkt:
```
https://deine-domain.com/wp-content/uploads/malta-assessment-v2/translations/en.json
```
Falls 404 → Dateien nicht hochgeladen.

### Problem: "Translation loading failed"

**Console zeigt:**
```
❌ Translation loading failed for language: en
```

**Lösungen:**
1. **JSON-Dateien fehlen:** Prüfe `/wp-content/uploads/malta-assessment-v2/translations/`
2. **Pfad falsch:** Prüfe `window.qcMaltaTranslationsPath` in Console
3. **JSON korrupt:** Validiere auf https://jsonlint.com/

### Problem: "Nonce is missing"

**Lösung:**
```bash
tail -f /wp-content/debug.log
# Sollte zeigen: "[Malta] Integration loaded!"
```

Falls nichts → Code nicht in `functions.php` oder falsches Theme.

### Problem: 400 Bad Request

**Lösung:**
```javascript
console.log(window.maltaAssessment);
```

Wenn `undefined` → Backend-Integration fehlt (Schritt 2).

### Problem: 403 Security check failed

**Lösung:** Hard Refresh
- Mac: `Cmd + Shift + R`
- Windows: `Ctrl + Shift + R`

### Problem: Webhook kommt nicht an

**Debug aktivieren:**
```php
define('MALTA_DEBUG_MODE', true);  // Zeile 25
```

**Log prüfen:**
```bash
tail -f /wp-content/debug.log | grep Malta
```

**Manuell testen:**
```bash
curl -X POST https://brixon.app.n8n.cloud/webhook/dwp-quickcheck \
  -H "Content-Type: application/json" \
  -d '{"test": "manual"}'
```

### Problem: EN Score falsch bei Q011 Option 1

**Erwartung:** EN verwendet Score 2 (nicht 4) für "Not important, have no EU clients"

**Prüfen:**
```javascript
// Console sollte zeigen:
"✅ EN adjustment applied: Q011 Option 1 score = 2"
```

Falls Log fehlt:
- `currentLanguage` ist nicht 'en'
- Hard Refresh: `Cmd + Shift + R`

### Problem: Weißer Bildschirm nach Upload

**Lösung:**
1. Via FTP alte `functions.php` wiederherstellen
2. Code nochmal kopieren (Syntax-Fehler)
3. Kein `?>` am Dateiende

---

## Konfiguration

### Debug-Modus
```php
define('MALTA_DEBUG_MODE', true);  // Zeile 25 in functions-php-integration.php
```
Logs in: `/wp-content/debug.log`

### Rate Limiting
```php
define('MALTA_RATE_LIMIT_MAX', 10);      // Zeile 29 (max Requests)
define('MALTA_RATE_LIMIT_WINDOW', 3600); // Zeile 33 (pro Stunde)
```

### Webhook ändern/deaktivieren
```php
define('MALTA_WEBHOOK_URL', 'https://deine-webhook-url.com');  // Zeile 17
define('MALTA_WEBHOOK_ENABLED', false);  // Zeile 21 (zum Deaktivieren)
```

---

## Multi-Language Features

### Automatische Spracherkennung
System erkennt Sprache aus URL:
- `/de/` → Deutsch (de.json)
- `/en/` → Englisch (en.json)
- `/nl/` → Niederländisch (nl.json)
- Kein Präfix → Deutsch (Fallback)

### Länder-spezifische Anpassungen

**🇬🇧 Englisch:**
- Q011 Option 1 ("Not important, have no EU clients") = Score 2 statt 4
- Senkt Gesamtscore für Non-EU Fokus

**🇳🇱 Niederländisch:**
- Q003 zeigt Exit Tax Warnung: "Let op: Bij emigratie uit Nederland kan de vertrekbelasting van toepassing zijn."

### Übersetzungen aktualisieren

1. **JSON-Datei bearbeiten** (z.B. `en.json`)
2. **Per FTP hochladen** (überschreibt alte)
3. **Cache leeren:**
   - WP Rocket: Dashboard → Cache leeren
   - Browser: `Cmd + Shift + R`

### Neue Sprache hinzufügen (z.B. Französisch)

1. **Kopiere `de.json` → `fr.json`**
2. **Übersetze alle Strings** (professioneller Übersetzer empfohlen)
3. **Erweitere Spracherkennung:**
   ```php
   // In malta_assessment_inject_language_vars() hinzufügen:
   if (strpos($current_path, '/fr/') !== false) {
       $language = 'fr';
   }
   ```
4. **Erstelle WordPress-Seite:** `/fr/verification-eligibilite-malte/`
5. **Teste vollständig**

---

## Cache-Optimierung (Optional)

### WP Rocket

```php
add_action('send_headers', function() {
    if (strpos($_SERVER['REQUEST_URI'], '/translations/') !== false &&
        strpos($_SERVER['REQUEST_URI'], '.json') !== false) {
        header('Cache-Control: max-age=3600'); // 1 Stunde
    }
});
```

### Cloudflare

Page Rule für: `/wp-content/uploads/malta-assessment-v2/translations/*.json`
- Cache Level: Standard
- Edge Cache TTL: 1 hour

---

**Installationszeit:** 10-15 Minuten (mit allen 3 Sprachen)
**Schwierigkeit:** Mittel
