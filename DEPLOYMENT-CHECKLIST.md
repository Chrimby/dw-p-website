# 🚀 Malta Assessment - Server-Side Deployment Checklist

## Overview

Diese Checkliste führt dich Schritt für Schritt durch das Deployment der server-seitigen Auswertungslogik für das Malta Assessment Questionnaire.

**Warum server-seitig?**
- ✅ Scoring-Logik ist unsichtbar für User
- ✅ Keine Manipulation möglich
- ✅ Geschäftslogik bleibt geheim
- ✅ Professioneller und sicherer

---

## 📋 Pre-Deployment Checklist

### 1. Files Ready
- [ ] `malta-assessment-evaluator.php` - Haupt-Evaluator Script
- [ ] `README.md` - Vollständige Dokumentation
- [ ] `client-integration-example.js` - JavaScript Code für HTML Update
- [ ] `test-evaluator.php` - Test Script (optional)

### 2. Access Ready
- [ ] WordPress Admin Zugang (für Option 1)
- [ ] FTP/SFTP Zugang zum Server (für alle Optionen)
- [ ] Domain/URL für CORS Configuration bekannt

### 3. Requirements Check
- [ ] Server hat PHP 7.4 oder höher
- [ ] HTTPS ist aktiviert (erforderlich für CORS)
- [ ] Sessions sind aktiviert (für Rate Limiting)

---

## 🎯 Deployment Options (Wähle EINE)

### Option A: WordPress REST API ⭐ EMPFOHLEN

**Vorteile:**
- Am sichersten
- Nutzt WordPress Standard-Mechanismen
- Automatisches Error Handling
- Clean URLs

**Schritte:**

1. **Upload PHP File**
   ```
   Ziel: /wp-content/themes/[your-theme]/malta-assessment-evaluator.php
   ```
   - [ ] Via FTP/SFTP uploaden
   - [ ] File Permissions auf 644 setzen

2. **Configure Allowed Origins**
   - [ ] Öffne `malta-assessment-evaluator.php` (Zeile 35)
   - [ ] Trage deine Domain(s) ein:
   ```php
   const ALLOWED_ORIGINS = [
       'https://www.drwerner.com',
       'https://drwerner.com',
   ];
   ```
   - [ ] Remove localhost entries (nur für Production!)

3. **Add REST API Endpoint**
   - [ ] Öffne `wp-content/themes/[your-theme]/functions.php`
   - [ ] Füge folgenden Code hinzu:

   ```php
   /**
    * Malta Assessment Evaluator REST API
    */
   add_action('rest_api_init', function () {
       register_rest_route('drwerner/v1', '/malta-evaluator', [
           'methods' => 'POST',
           'callback' => 'malta_assessment_evaluate',
           'permission_callback' => '__return_true',
       ]);
   });

   function malta_assessment_evaluate(WP_REST_Request $request) {
       require_once get_template_directory() . '/malta-assessment-evaluator.php';
       exit;
   }
   ```

4. **Verify Endpoint**
   - [ ] Öffne Browser Console (F12)
   - [ ] Gehe zu: `https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator`
   - [ ] Sollte zeigen: `{"success":false,"error":"Only POST requests are allowed"}`
   - [ ] ✅ Wenn du diese Meldung siehst, funktioniert der Endpoint!

5. **Your Endpoint URL:**
   ```
   https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator
   ```

---

### Option B: Standalone PHP File

**Vorteile:**
- Einfacher
- Keine WordPress-Änderungen nötig
- Schnell deploybar

**Schritte:**

1. **Create API Directory**
   ```
   Ziel: /public_html/api/
   ```
   - [ ] Erstelle Ordner `api` im Document Root

2. **Upload PHP File**
   ```
   Ziel: /public_html/api/malta-evaluator.php
   ```
   - [ ] Via FTP/SFTP uploaden als `malta-evaluator.php`
   - [ ] File Permissions auf 644 setzen

3. **Configure Allowed Origins**
   - [ ] Öffne `malta-evaluator.php` (Zeile 35)
   - [ ] Trage deine Domain(s) ein (siehe Option A)

4. **Verify Endpoint**
   - [ ] Öffne: `https://www.drwerner.com/api/malta-evaluator.php`
   - [ ] Sollte zeigen: `{"success":false,"error":"Only POST requests are allowed"}`
   - [ ] ✅ Wenn du diese Meldung siehst, funktioniert es!

5. **Your Endpoint URL:**
   ```
   https://www.drwerner.com/api/malta-evaluator.php
   ```

---

### Option C: WordPress Plugin

**Vorteile:**
- Portable
- Einfach zu deaktivieren
- Keine Theme-Änderungen

**Schritte:**
- [ ] Siehe README.md "Option 3: WordPress Plugin" für Details
- [ ] Empfohlen nur wenn du mehrere Sites hast

---

## 🔧 Client-Side Integration

### 1. Backup Original HTML
```bash
# Erstelle Backup bevor du änderst!
cp public/malta-assessment-v2-dwp/index.html public/malta-assessment-v2-dwp/index.html.backup
```
- [ ] Backup erstellt

### 2. Open HTML File
- [ ] Öffne: `public/malta-assessment-v2-dwp/index.html` in Editor

### 3. Update Configuration
Finde die `<script>` Section und füge hinzu (Zeile ~1438):

```javascript
// API Configuration
const API_ENDPOINT = 'https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator';
// oder für Standalone:
// const API_ENDPOINT = 'https://www.drwerner.com/api/malta-evaluator.php';
```
- [ ] API_ENDPOINT hinzugefügt mit deiner URL

### 4. Replace calculateScore() Function
- [ ] Finde die Funktion `calculateScore()` (Zeile ~1982)
- [ ] Ersetze KOMPLETT mit Code aus `client-integration-example.js`
- [ ] Verwende den `async function calculateScore()` Code

### 5. Update calculateAndShowResults() Function
- [ ] Finde die Funktion `calculateAndShowResults()` (Zeile ~2038)
- [ ] Ersetze KOMPLETT mit Code aus `client-integration-example.js`
- [ ] Verwende den `async function calculateAndShowResults()` Code

### 6. Optional: Add Error Handling
- [ ] Füge `getErrorMessage()` function hinzu (siehe `client-integration-example.js`)
- [ ] Füge Retry Logic hinzu (optional, siehe example)

---

## ✅ Testing

### Test 1: Endpoint Erreichbarkeit
```javascript
// Paste in Browser Console (F12) auf deiner Website
fetch('https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    answers: {
      "q001": "4",
      "q002": "4",
      "q003": "4",
      "q004": "3",
      "q005": "4"
    }
  })
})
.then(r => r.json())
.then(console.log)
.catch(console.error)
```

**Expected Output:**
```json
{
  "success": true,
  "data": {
    "percentage": 85,
    "category": "excellent",
    "categoryLabel": "Malta ist hervorragend geeignet",
    ...
  }
}
```

- [ ] Test durchgeführt
- [ ] Response erhalten
- [ ] `success: true` im Response

### Test 2: CORS Check
- [ ] Öffne Malta Assessment auf deiner Website
- [ ] Öffne Browser Console (F12)
- [ ] Keine CORS Errors sichtbar
- [ ] Wenn CORS Error: Prüfe ALLOWED_ORIGINS in PHP

### Test 3: Full User Journey
- [ ] Öffne Malta Assessment
- [ ] Beantworte alle Fragen
- [ ] Klicke "Ergebnis anzeigen"
- [ ] Loading Spinner erscheint
- [ ] Ergebnis wird korrekt angezeigt
- [ ] Keine JavaScript Errors in Console

### Test 4: Rate Limiting
- [ ] Sende 10 Requests innerhalb 1 Minute
- [ ] 11. Request sollte Error 429 zeigen
- [ ] Error Message ist user-friendly

### Test 5: Different Score Ranges
Test verschiedene Antwort-Kombinationen:

- [ ] **High Score (85%+)** - Nur 4er Antworten
  - Erwartet: "excellent" Category

- [ ] **Good Score (60-75%)** - Mix aus 3-4
  - Erwartet: "good" Category

- [ ] **Moderate Score (40-55%)** - Mix aus 2-3
  - Erwartet: "moderate" Category

- [ ] **Low Score (20-35%)** - Viele 1-2 Antworten
  - Erwartet: "fair" Category

- [ ] **Very Low Score (<20%)** - Nur niedrige Antworten
  - Erwartet: "explore" Category

---

## 🔒 Security Checklist

### Pre-Production
- [ ] `DEBUG_MODE = false` in PHP file gesetzt
- [ ] Localhost entries aus ALLOWED_ORIGINS entfernt
- [ ] File Permissions korrekt (644 für PHP files)
- [ ] HTTPS aktiviert (kein HTTP!)
- [ ] PHP Version >= 7.4 verifiziert

### Post-Production
- [ ] Rate Limiting funktioniert (Test durchgeführt)
- [ ] CORS blockt unerlaubte Domains
- [ ] Error Messages sind user-friendly (keine Stack Traces)
- [ ] Server Logs checken nach Errors (erste 24h)

---

## 🐛 Troubleshooting

### Problem: CORS Error
```
Access to fetch has been blocked by CORS policy
```

**Lösung:**
1. Prüfe ALLOWED_ORIGINS in PHP (Zeile 35)
2. Stelle sicher Domain EXAKT matched (mit https://)
3. Prüfe Schreibweise (www. vs ohne www.)

---

### Problem: 500 Internal Server Error

**Lösung:**
1. Aktiviere Debug Mode: `const DEBUG_MODE = true;`
2. Prüfe PHP Error Logs:
   - WordPress: `/wp-content/debug.log`
   - Server: `/var/log/php/error.log` oder per cPanel
3. Häufige Ursachen:
   - PHP Syntax Error (fehlendes Semikolon)
   - PHP Version zu alt (<7.4)
   - Session Probleme (Session-Ordner nicht beschreibbar)

---

### Problem: Rate Limit zu streng

**Lösung:**
Erhöhe Limits in PHP (Zeile 43-44):
```php
const RATE_LIMIT_MAX_REQUESTS = 50; // Statt 10
const RATE_LIMIT_TIME_WINDOW = 3600; // 1 Stunde
```

---

### Problem: Firewall blockt Requests

**Symptom:** Request erreicht Server nicht

**Lösung:**
1. Prüfe Firewall Logs (Cloudflare, Sucuri, etc.)
2. Whitelist die IP-Range deiner User
3. Disable "Block JSON POSTs" Rule falls vorhanden
4. Kontaktiere Hosting Support

---

## 📊 Monitoring (Erste Woche)

### Daily Checks
- [ ] Tag 1: Server Error Logs checken
- [ ] Tag 2: Rate Limit Violations checken
- [ ] Tag 3: Response Times messen (<200ms?)
- [ ] Tag 7: User Feedback sammeln

### Key Metrics
- **Response Time:** Sollte <200ms sein
- **Error Rate:** Sollte <1% sein
- **Rate Limit Hits:** Sollte selten sein (außer bei Bots)

---

## 🎉 Post-Deployment

### Cleanup
- [ ] Entferne Backup HTML wenn alles funktioniert
- [ ] Entferne Test-Scripts vom Server
- [ ] Entferne Debug-Outputs aus Console Logs

### Documentation
- [ ] Notiere Endpoint URL für Team
- [ ] Dokumentiere für nächsten Developer
- [ ] Speichere dieses Checklist als Reference

### Celebration
- [ ] ✅ Server-Side Evaluation ist live!
- [ ] 🎊 Scoring Logic ist jetzt geheim
- [ ] 🔒 User können nicht mehr manipulieren
- [ ] 🚀 Professionelles Setup erreicht

---

## 📞 Support

**Bei Problemen:**
1. Prüfe diese Checkliste nochmal durch
2. Siehe README.md "Troubleshooting" Section
3. Aktiviere DEBUG_MODE für detaillierte Errors
4. Check Server PHP Error Logs

**Quick Commands:**
```bash
# Check PHP Version
php -v

# Check PHP Error Log (WordPress)
tail -f /path/to/wp-content/debug.log

# Test Endpoint with curl
curl -X POST https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator \
  -H "Content-Type: application/json" \
  -d '{"answers":{"q001":"4"}}'
```

---

## 📝 Deployment Log

**Deployment Date:** _________________

**Deployed By:** _________________

**Deployment Option:** ☐ A (REST API)  ☐ B (Standalone)  ☐ C (Plugin)

**Endpoint URL:** _________________________________

**Tests Passed:**
- [ ] Endpoint Erreichbarkeit
- [ ] CORS Check
- [ ] Full User Journey
- [ ] Rate Limiting
- [ ] Score Calculations

**Notes:**
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________

**Sign-off:** _________________ (Name & Date)

---

**Version:** 2.0
**Last Updated:** 2025-11-04
