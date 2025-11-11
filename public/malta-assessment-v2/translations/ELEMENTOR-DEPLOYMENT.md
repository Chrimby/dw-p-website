# Malta Assessment - Elementor Integration Guide

**Status:** ✅ **READY FOR DEPLOYMENT**
**Version:** 2.0 (Multi-Language, Elementor Direct Embedding)
**Date:** 2025-11-11
**Languages:** 🇩🇪 German, 🇬🇧 English, 🇳🇱 Dutch

---

## Architektur-Übersicht

### Deine Setup:
- ✅ WordPress mit Elementor Page Builder
- ✅ `update.html` wird **direkt** in Elementor HTML-Widget eingebettet (KEIN iframe)
- ✅ Existierende `functions.php` Extension
- ✅ Sprachspezifische WordPress-Seiten (z.B. `/de/malta-check/`, `/en/malta-check/`, `/nl/malta-check/`)

### Wie es funktioniert:
1. **WordPress** erkennt Sprache aus URL (`/de/`, `/en/`, `/nl/`)
2. **functions.php** injiziert JavaScript-Variable `window.qcMaltaLanguage` in `<head>`
3. **Malta Assessment** (`update.html`) liest diese Variable und lädt passende Übersetzungen
4. **Translations** werden dynamisch geladen aus `/wp-content/uploads/malta-assessment-v2/translations/{lang}.json`

---

## 🚀 Deployment Schritte

### Schritt 1: Dateien hochladen

**Upload-Ziel:** `/wp-content/uploads/malta-assessment-v2/`

```bash
# Per SFTP/FTP hochladen:
malta-assessment-v2/
├── update.html          # Aktualisierte Version mit Multi-Language Support
├── translations/
│   ├── de.json          # Deutsche Übersetzungen (15KB)
│   ├── en.json          # Englische Übersetzungen (15KB)
│   └── nl.json          # Niederländische Übersetzungen (15KB)
```

**Tipp:** Nutze FileZilla, Cyberduck oder den WordPress File Manager Plugin.

---

### Schritt 2: Functions.php erweitern

**Datei:** `/wp-content/themes/dein-theme/functions.php` (oder Custom Plugin)

Füge den Code aus `ELEMENTOR-INTEGRATION.php` hinzu:

```php
/**
 * Malta Assessment - Language Detection
 */
function malta_assessment_inject_language_vars() {
    $current_path = $_SERVER['REQUEST_URI'] ?? '';
    $language = 'de'; // Default

    if (strpos($current_path, '/en/') !== false) {
        $language = 'en';
    } elseif (strpos($current_path, '/nl/') !== false) {
        $language = 'nl';
    } elseif (strpos($current_path, '/de/') !== false) {
        $language = 'de';
    }

    $upload_dir = wp_upload_dir();
    $translations_path = $upload_dir['baseurl'] . '/malta-assessment-v2/translations';
    ?>
    <script>
    window.qcMaltaLanguage = '<?php echo esc_js($language); ?>';
    window.qcMaltaTranslationsPath = '<?php echo esc_js($translations_path); ?>';
    </script>
    <?php
}
add_action('wp_head', 'malta_assessment_inject_language_vars');
```

**Wichtig:** Falls du WPML oder Polylang nutzt, siehe `ELEMENTOR-INTEGRATION.php` für alternative Spracherkennung.

---

### Schritt 3: WordPress-Seiten erstellen

Erstelle 3 Seiten in WordPress (eine pro Sprache):

| Sprache | URL-Beispiel | Page Settings |
|---------|--------------|---------------|
| 🇩🇪 Deutsch | `/de/malta-eignungscheck/` | Template: Full Width |
| 🇬🇧 Englisch | `/en/malta-suitability-check/` | Template: Full Width |
| 🇳🇱 Niederländisch | `/nl/malta-geschiktheidscheck/` | Template: Full Width |

**Page Settings:**
- Template: Full Width (kein Sidebar)
- Header/Footer: Optional ausblenden für full-screen Experience

---

### Schritt 4: Elementor-Integration

#### Option A: HTML-Widget (Empfohlen)

1. **Öffne die Seite in Elementor**
2. **Füge HTML-Widget hinzu**
3. **Kopiere den kompletten Inhalt von `update.html`** und füge ihn ein
4. **Speichern & Veröffentlichen**

**Wiederhole für alle 3 Sprachseiten** (gleicher HTML-Code - Sprache wird automatisch erkannt!)

#### Option B: Code-Snippet vor HTML (Manuell)

Falls automatische Erkennung nicht funktioniert, füge **VOR** dem HTML-Widget ein weiteres HTML-Widget mit:

```html
<script>
window.qcMaltaLanguage = 'en'; // Ändere zu: 'de', 'en', oder 'nl'
</script>
```

**Dann** füge das Malta Assessment HTML ein.

---

### Schritt 5: AJAX Handler anpassen

**Datei:** Deine bestehende AJAX Handler Datei (z.B. `functions.php` oder Custom Plugin)

Ergänze den Handler, um das `language` Feld zu erfassen:

```php
add_action('wp_ajax_nopriv_submit_malta_assessment', 'handle_malta_assessment_submission');
add_action('wp_ajax_submit_malta_assessment', 'handle_malta_assessment_submission');

function handle_malta_assessment_submission() {
    check_ajax_referer('malta_assessment_nonce', 'nonce');

    $data = [
        'geschlecht' => sanitize_text_field($_POST['geschlecht'] ?? ''),
        'vorname' => sanitize_text_field($_POST['vorname'] ?? ''),
        'nachname' => sanitize_text_field($_POST['nachname'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'telefon' => sanitize_text_field($_POST['telefon'] ?? ''),
        'unternehmen' => sanitize_text_field($_POST['unternehmen'] ?? ''),
        'language' => sanitize_text_field($_POST['language'] ?? 'de'), // ← NEU!
        'answers' => json_decode(stripslashes($_POST['answers'] ?? '{}'), true),
        'score' => absint($_POST['score'] ?? 0),
        'percentage' => floatval($_POST['percentage'] ?? 0),
    ];

    // Sende an CRM/Webhook mit language field
    // ... deine bestehende Logik ...

    wp_send_json_success(['language' => $data['language']]);
}
```

**Wichtig:** Das `language` Feld wird automatisch vom Frontend gesendet (via `currentLanguage` Variable).

---

### Schritt 6: Cache-Konfiguration

#### WP Rocket (falls installiert)

**Methode 1: JSON-Dateien von Cache ausschließen**

```php
add_filter('rocket_cache_reject_uri', function($urls) {
    $urls[] = '/wp-content/uploads/malta-assessment-v2/translations/(.*)';
    return $urls;
});
```

**Methode 2: Cache-Headers setzen (besser)**

```php
add_action('send_headers', function() {
    if (strpos($_SERVER['REQUEST_URI'], '/translations/') !== false &&
        strpos($_SERVER['REQUEST_URI'], '.json') !== false) {
        header('Cache-Control: max-age=3600'); // 1 Stunde
    }
});
```

#### Cloudflare (falls genutzt)

- Erstelle Page Rule für: `/wp-content/uploads/malta-assessment-v2/translations/*.json`
- Cache Level: Standard
- Edge Cache TTL: 1 hour

---

## 🧪 Testing

### Lokales Testing (Vor Deployment)

```bash
cd "/Users/christoph/Brixon Dropbox/Christoph Sauerborn/Downloads/quickcheck20/qc-malta-server/public"
python3 -m http.server 8881
```

**Test URLs:**
- 🇩🇪 DE: `http://localhost:8881/malta-assessment-v2/update.html`
- 🇬🇧 EN: `http://localhost:8881/en/malta-assessment-v2/update.html`
- 🇳🇱 NL: `http://localhost:8881/nl/malta-assessment-v2/update.html`

**Console Logs überprüfen:**
```
✅ Should see: "🌐 Detected language: de|en|nl"
✅ Should see: "✅ Translations loaded: de.json|en.json|nl.json from [path]"
✅ Should see: "✅ App initialized successfully"
✅ Should see (EN only): "✅ EN adjustment applied: Q011 Option 1 score = 2"
```

---

### Production Testing (Nach Deployment)

#### 1. Spracherkennung testen

**Öffne jede Sprachseite und checke Console:**

```javascript
// Browser Console öffnen (F12)
console.log(window.qcMaltaLanguage); // Sollte 'de', 'en', oder 'nl' sein
console.log(window.qcMaltaTranslationsPath); // Sollte korrekten Pfad zeigen
```

#### 2. Translation Loading testen

**Nach Page Load in Console:**
```
✅ "🌐 WordPress detected language: en"
✅ "✅ Translations loaded: en.json from /wp-content/uploads/malta-assessment-v2/translations"
```

#### 3. Functional Tests

**Test Checklist (pro Sprache):**
- [ ] Welcome Screen zeigt korrekte Sprache
- [ ] "Jetzt starten" Button hat korrekten Text
- [ ] Q001: Frage + Optionen in korrekter Sprache
- [ ] Q006: Helper Text angezeigt
- [ ] Fortschrittsbalken Text korrekt (z.B. "Frage 5 von 14")
- [ ] Q015: Contact Form (alle Felder, Gender-Optionen, Privacy-Link)
- [ ] Results Screen: Congratulations Message, Score Label, CTA Buttons

#### 4. EN-Specific Test: Q011 Scoring

**Wichtig für Englisch:**
1. Wähle bei Q011 Option 1: "Not important, have no EU clients"
2. Vervollständige Assessment
3. **Verify:** Score sollte **NIEDRIGER** sein als bei DE/NL mit gleichen Antworten
4. **Check Console:** `✅ EN adjustment applied: Q011 Option 1 score = 2`

#### 5. NL-Specific Test: Exit Tax Warning

**Wichtig für Niederländisch:**
1. Navigiere zu Q003 (Umzug nach Malta)
2. **Verify:** Helper Text zeigt: *"Let op: Bij emigratie uit Nederland kan de vertrekbelasting van toepassing zijn."*

---

## 🐛 Troubleshooting

### Problem: Seite zeigt immer Deutsch, auch auf /en/ oder /nl/

**Diagnose:**
```javascript
// Browser Console (F12)
console.log(window.qcMaltaLanguage); // Zeigt was?
```

**Lösungen:**
1. **Falls `undefined`:**
   - functions.php Code nicht korrekt eingefügt
   - Code ist in Child Theme, aber Parent Theme wird verwendet
   - Check: WordPress Admin → Design → Theme Editor → functions.php

2. **Falls falsche Sprache:**
   - URL-Erkennung funktioniert nicht
   - Prüfe: Page URL enthält wirklich `/en/` oder `/nl/`?

3. **Falls richtig, aber Translations laden nicht:**
   - JSON-Dateien nicht hochgeladen oder falscher Pfad
   - Test direkt: `https://yoursite.com/wp-content/uploads/malta-assessment-v2/translations/en.json`

---

### Problem: Console zeigt "Translation loading failed"

**Diagnose:**
```javascript
// Check exact error message
console.error(); // Zeigt Fehlermeldung
```

**Lösungen:**
1. **404 Not Found:**
   - JSON-Dateien nicht hochgeladen
   - Pfad falsch konfiguriert
   - Check: `window.qcMaltaTranslationsPath` zeigt korrekten Pfad?

2. **CORS Error:**
   - Selten bei WordPress, aber falls ja:
   ```php
   add_action('send_headers', function() {
       header('Access-Control-Allow-Origin: *');
   });
   ```

3. **JSON Syntax Error:**
   - JSON-Datei korrupt
   - Validate auf: https://jsonlint.com/

---

### Problem: EN Scoring identisch mit DE/NL bei Q011 Option 1

**Diagnose:**
```javascript
// Check if adjustment was applied
// Should see in console:
"✅ EN adjustment applied: Q011 Option 1 score = 2"
```

**Lösungen:**
1. Falls Log nicht erscheint:
   - `currentLanguage` ist nicht 'en'
   - Check: `console.log(currentLanguage)`

2. Falls Log erscheint, aber Score falsch:
   - Clear browser cache + hard reload (Cmd+Shift+R / Ctrl+Shift+R)

---

### Problem: Contact Form Gender Optionen falsch

**Symptom:** NL Form zeigt "Frau/Herr" statt "Mevr./Dhr."

**Lösung:**
1. Check: `nl.json` hat korrekte Gender-Optionen?
   ```json
   "geschlecht_options": {
     "frau": "Mevr.",
     "herr": "Dhr."
   }
   ```

2. Clear cache + hard reload

---

## 🔄 Updates & Wartung

### Übersetzungen aktualisieren

**Scenario:** CTA Button Text ändern

1. **Identifiziere Key:**
   ```bash
   grep -r "Kostenlose Erstberatung" translations/
   # → results.categories.good.cta
   ```

2. **Update alle 3 Dateien:**
   ```json
   // de.json
   "cta": "Neuer CTA Text"

   // en.json
   "cta": "New CTA Text"

   // nl.json
   "cta": "Nieuwe CTA Tekst"
   ```

3. **Upload via FTP** (überschreibe alte Dateien)

4. **Clear Cache:**
   - WP Rocket: Dashboard → Clear Cache
   - Browser: Hard Reload (Cmd+Shift+R / Ctrl+Shift+R)

---

### Neue Sprache hinzufügen (z.B. Französisch)

1. **Copy Template:**
   ```bash
   cp de.json fr.json
   ```

2. **Übersetzen** (professioneller Übersetzer empfohlen für Tax/Legal Terms)

3. **Functions.php erweitern:**
   ```php
   if (strpos($current_path, '/fr/') !== false) {
       $language = 'fr';
   }
   ```

4. **WordPress Page erstellen:**
   - URL: `/fr/verification-eligibilite-malte/`
   - Elementor: Gleicher HTML-Code wie DE/EN/NL

5. **Testen** mit vollständigem Checklist

---

## ✅ Final Pre-Launch Checklist

Vor dem Go-Live:

### Dateien
- [ ] `update.html` hochgeladen nach `/wp-content/uploads/malta-assessment-v2/`
- [ ] `de.json`, `en.json`, `nl.json` hochgeladen nach `/translations/` subfolder
- [ ] Alle JSON-Dateien direkt erreichbar (Test: URL im Browser öffnen)

### WordPress
- [ ] Functions.php Code eingefügt (Theme oder Custom Plugin)
- [ ] 3 WordPress-Seiten erstellt (DE, EN, NL)
- [ ] Jede Seite hat Malta Assessment HTML in Elementor eingebettet
- [ ] AJAX Handler aktualisiert (erfasst `language` field)

### Cache
- [ ] WP Rocket Exclusions konfiguriert (falls installiert)
- [ ] Cloudflare Page Rules erstellt (falls genutzt)

### Testing
- [ ] Jede Sprachseite geladen → Console Logs checken
- [ ] Full Assessment in jeder Sprache durchgeführt
- [ ] EN Q011 Option 1 Score-Anpassung verifiziert
- [ ] NL Q003 Exit Tax Warning verifiziert
- [ ] Contact Form Submission getestet (alle Sprachen)

### Browser Testing
- [ ] Chrome (Desktop)
- [ ] Firefox (Desktop)
- [ ] Safari (Desktop)
- [ ] Mobile Safari (iOS) - **Critical!**
- [ ] Mobile Chrome (Android)

---

## 📊 Monitoring (Post-Launch)

### Analytics Setup (Optional, aber empfohlen)

**Google Tag Manager:**

```javascript
// Add to GTM - Custom HTML Tag
<script>
window.dataLayer = window.dataLayer || [];
if (window.qcMaltaLanguage) {
    dataLayer.push({
        'event': 'malta_assessment_language',
        'assessment_language': window.qcMaltaLanguage
    });
}
</script>
```

**KPIs zu tracken:**
- % User pro Sprache (DE vs. EN vs. NL)
- Conversion Rate pro Sprache (Contact Form Submissions)
- Average Score pro Sprache
- Drop-off Rate pro Question pro Sprache

---

## 🎉 Du bist fertig!

**Next Steps:**
1. ✅ Schedule Deployment (low-traffic window recommended)
2. ✅ Deploy files to production
3. ✅ Run smoke tests (load each language, complete one assessment)
4. ✅ Monitor analytics first 24-48h
5. ✅ Gather user feedback
6. ✅ Iterate on translations if needed

**Fragen oder Probleme?**
- Check `IMPLEMENTATION-SUMMARY.md` für technische Details
- Check `TRANSLATION-REVIEW.md` für Übersetzungsqualität
- Check `ELEMENTOR-INTEGRATION.php` für vollständigen Code

---

**Version:** 1.0 (Elementor Direct Embedding)
**Last Updated:** 2025-11-11
**Architecture:** WordPress + Elementor (Direct Embedding, NO iframe)
**Status:** ✅ READY FOR PRODUCTION
