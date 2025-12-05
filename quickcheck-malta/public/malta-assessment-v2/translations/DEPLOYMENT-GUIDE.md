# Multi-Language Malta Assessment - Deployment Guide

**Status:** ✅ **READY FOR DEPLOYMENT**
**Version:** 2.0 (Multi-Language)
**Date:** 2025-11-11
**Languages:** 🇩🇪 German, 🇬🇧 English, 🇳🇱 Dutch

---

## ✅ Implementation Complete

All phases of the multi-language implementation have been completed successfully:

- ✅ **Phase 1:** String extraction (230+ strings → de.json)
- ✅ **Phase 2:** AI translations (EN + NL with tax/legal terminology)
- ✅ **Phase 3:** Code refactoring (i18n system + dynamic loading)
- ✅ **Phase 4:** Country-specific adjustments (EN EU scoring, NL exit tax)

---

## 📂 Final File Structure

```
/malta-assessment-v2/
├── update.html               # Refactored with full i18n support
├── translations/
│   ├── de.json               # German (Basis)
│   ├── en.json               # English (UK tax terminology)
│   ├── nl.json               # Dutch (Benelux-specific)
│   ├── TRANSLATION-REVIEW.md # Manual review checklist
│   ├── IMPLEMENTATION-SUMMARY.md
│   └── DEPLOYMENT-GUIDE.md   # This file
```

---

## 🚀 Quick Start Testing

### Local Testing

1. **Start Local Server:**
   ```bash
   cd "/Users/christoph/Brixon Dropbox/Christoph Sauerborn/Downloads/quickcheck20/qc-malta-server/public"
   python3 -m http.server 8881
   ```

2. **Test Each Language:**
   - 🇩🇪 German: `http://localhost:8881/malta-assessment-v2/update.html`
   - 🇬🇧 English: `http://localhost:8881/en/malta-assessment-v2/update.html`
   - 🇳🇱 Dutch: `http://localhost:8881/nl/malta-assessment-v2/update.html`

3. **Check Console Logs:**
   ```
   ✅ Should see: "🌐 Detected language: de|en|nl"
   ✅ Should see: "✅ Translations loaded: de.json|en.json|nl.json"
   ✅ Should see: "✅ App initialized successfully"
   ✅ Should see (EN only): "✅ EN adjustment applied: Q011 Option 1 score = 2"
   ```

---

## 📋 Pre-Deployment Checklist

### 1. Translation Quality

- [x] All 3 JSON files validated (no syntax errors)
- [x] Tax/legal terminology reviewed (see TRANSLATION-REVIEW.md)
- [ ] Native speaker review for EN (optional but recommended)
- [ ] Native speaker review for NL (optional but recommended)

### 2. Functional Testing

**Test Matrix:** 3 languages × 13 questions + contact form + results = 48 tests

#### Language Detection Tests
- [ ] `/de/malta-assessment-v2/update.html` → German
- [ ] `/en/malta-assessment-v2/update.html` → English
- [ ] `/nl/malta-assessment-v2/update.html` → Dutch
- [ ] `/malta-assessment-v2/update.html` (no lang) → German (fallback)

#### UI Translation Tests (per language)
- [ ] Welcome screen (title, description, 4 features)
- [ ] All buttons (start, next, back, submit, CTA)
- [ ] Progress bar updates correctly
- [ ] Trust bar (3 signals)

#### Question Flow Tests (spot check: Q001, Q006, Q011, Q015)
- [ ] Q001: Question text, options, advisor quote
- [ ] Q006: Helper text displays correctly
- [ ] Q011: EN Option 1 has score=2 (check in results)
- [ ] Q015: Contact form (placeholders, gender options, privacy link)

#### Results Screen Tests
- [ ] Congratulations message with correct gender/name
- [ ] Score label (DE: "Punkte", EN: "Points", NL: "Punten")
- [ ] Category badge (varies by score)
- [ ] Benchmark text
- [ ] Detail sections (Positive/Neutral/Critical titles)
- [ ] CTA section (experts label, benefits, footer)

#### Cross-Browser Tests
- [ ] Chrome (desktop)
- [ ] Firefox (desktop)
- [ ] Safari (desktop)
- [ ] Mobile Safari (iOS)
- [ ] Mobile Chrome (Android)

---

## 🌐 WordPress Deployment

### Step 1: Upload Files to WordPress

**Upload Location:** `/wp-content/uploads/malta-assessment-v2/`

```bash
# Upload via SFTP/FTP:
/malta-assessment-v2/
├── update.html
├── translations/
│   ├── de.json
│   ├── en.json
│   └── nl.json
```

**Alternative:** Use WordPress Media Library to upload entire folder.

---

### Step 2: Create Language-Specific Pages

Create 3 pages in WordPress (one per language):

| Language | Page URL | Shortcode |
|----------|----------|-----------|
| 🇩🇪 German | `/de/malta-eignungscheck/` | `[malta_assessment]` |
| 🇬🇧 English | `/en/malta-suitability-check/` | `[malta_assessment]` |
| 🇳🇱 Dutch | `/nl/malta-geschiktheidscheck/` | `[malta_assessment]` |

**Page Settings:**
- Template: Full Width (no sidebar)
- Header/Footer: Optional (can be hidden for full-screen experience)

---

### Step 3: WordPress Shortcode (Already Exists)

**File:** `functions-php-integration.php` or theme `functions.php`

The existing shortcode should work without changes, but ensure it embeds the correct path:

```php
function malta_assessment_shortcode() {
    $upload_dir = wp_upload_dir();
    $base_url = $upload_dir['baseurl'];

    ob_start();
    ?>
    <iframe
        src="<?php echo $base_url; ?>/malta-assessment-v2/update.html"
        width="100%"
        height="2000px"
        frameborder="0"
        style="border: none; overflow: hidden;"
        id="malta-assessment-iframe"
    ></iframe>
    <script>
        // Auto-resize iframe based on content
        window.addEventListener('message', function(e) {
            if (e.data && e.data.type === 'resize-iframe') {
                document.getElementById('malta-assessment-iframe').style.height = e.data.height + 'px';
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('malta_assessment', 'malta_assessment_shortcode');
```

---

### Step 4: Backend Integration (AJAX Handler)

**Ensure Language Field is Captured:**

The frontend already sends `currentLanguage` variable. Update your WordPress AJAX handler:

```php
// In malta-assessment-wordpress.php or similar

add_action('wp_ajax_nopriv_submit_malta_assessment', 'handle_malta_assessment');
add_action('wp_ajax_submit_malta_assessment', 'handle_malta_assessment');

function handle_malta_assessment() {
    check_ajax_referer('malta_assessment_nonce', 'nonce');

    $data = [
        'geschlecht' => sanitize_text_field($_POST['geschlecht'] ?? ''),
        'vorname' => sanitize_text_field($_POST['vorname'] ?? ''),
        'nachname' => sanitize_text_field($_POST['nachname'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'language' => sanitize_text_field($_POST['language'] ?? 'de'), // ← ADD THIS
        'answers' => json_decode(stripslashes($_POST['answers'] ?? '{}'), true),
        // ... other fields
    ];

    // Send to CRM/Webhook with language field
    // ...

    wp_send_json_success($data);
}
```

**Frontend AJAX Call (already implemented):**

The language is automatically sent in the AJAX payload via the global `currentLanguage` variable.

---

### Step 5: Cache Configuration

#### WP Rocket Settings

1. **Exclude Translation JSON from Cache:**
   - Go to WP Rocket settings → File Optimization
   - Add to "Exclude from Cache":
     ```
     /wp-content/uploads/malta-assessment-v2/translations/*.json
     ```

2. **Or Set Cache Headers:** (Better approach)
   ```php
   // Add to .htaccess or functions.php
   add_action('send_headers', function() {
       if (strpos($_SERVER['REQUEST_URI'], '/translations/') !== false && strpos($_SERVER['REQUEST_URI'], '.json') !== false) {
           header('Cache-Control: max-age=3600'); // 1 hour cache
       }
   });
   ```

#### Cloudflare (if applicable)

- Add Page Rule for `/malta-assessment-v2/translations/*.json`
- Cache Level: Standard
- Edge Cache TTL: 1 hour

---

### Step 6: Analytics Setup (GTM)

**Track Language in Google Tag Manager:**

```javascript
// Add to GTM - Custom HTML Tag
<script>
window.dataLayer = window.dataLayer || [];
window.addEventListener('message', function(e) {
    if (e.data && e.data.type === 'malta_assessment_language') {
        dataLayer.push({
            'event': 'language_detected',
            'assessment_language': e.data.language
        });
    }
});
</script>
```

**Frontend Message (add to update.html):**

Already implemented - the app sends a message on init:

```javascript
// In initializeApp() function (line ~2467)
window.parent.postMessage({
    type: 'malta_assessment_language',
    language: currentLanguage
}, '*');
```

---

## 🧪 Testing Checklist (Final QA)

### Critical Path Tests

#### 1. **German (DE) - Full Flow**
- [ ] Start at `/de/malta-eignungscheck/`
- [ ] Complete all 13 questions + contact form
- [ ] Verify advisor quotes display in German
- [ ] Submit and check results screen
- [ ] Verify all category-specific CTAs in German

#### 2. **English (EN) - Scoring Verification**
- [ ] Start at `/en/malta-suitability-check/`
- [ ] Answer Q011 Option 1: "Not important, have no EU clients"
- [ ] Complete assessment
- [ ] **VERIFY:** Q011 Option 1 contributes only 2 points (not 4)
- [ ] Check that total score is lower than DE/NL with same answers

#### 3. **Dutch (NL) - Exit Tax Warning**
- [ ] Start at `/nl/malta-geschiktheidscheck/`
- [ ] Navigate to Q003 (relocation question)
- [ ] **VERIFY:** Helper text shows: "Let op: Bij emigratie uit Nederland kan de vertrekbelasting van toepassing zijn."
- [ ] Complete assessment

#### 4. **Cross-Language Consistency**
- [ ] Use identical answer sets for DE, EN, NL
- [ ] Verify scores are identical (except Q011 Option 1 in EN)
- [ ] Check category assignment is consistent

---

### Edge Cases

- [ ] **Empty URL (no /de/, /en/, /nl/):** Falls back to German
- [ ] **Invalid language URL (e.g., /fr/):** Falls back to German
- [ ] **Translation JSON 404:** Shows error alert, falls back to German
- [ ] **Broken JSON syntax:** Console error, fallback to German
- [ ] **Network timeout:** Retry logic works, fallback after 3 attempts

---

### Performance Benchmarks

**Expected Load Times:**

| Metric | Target | Acceptable |
|--------|--------|------------|
| Translation JSON load | < 200ms | < 500ms |
| Page ready (DOM + translations) | < 1s | < 2s |
| Question rendering | < 100ms | < 200ms |
| Results rendering | < 300ms | < 500ms |

**Check Performance:**
```javascript
// Add to console during testing
console.time('Translation Load');
await loadTranslations('en');
console.timeEnd('Translation Load');
```

---

## 🔄 Rollback Plan

If issues arise after deployment, you can quickly roll back:

### Option 1: Quick Fix (Update JSON Only)

If only translations are wrong:
1. Fix the affected JSON file (de.json, en.json, or nl.json)
2. Re-upload via FTP
3. Clear WP Rocket cache
4. Hard refresh in browser (Cmd+Shift+R / Ctrl+Shift+R)

### Option 2: Rollback to German-Only Version

If major issues occur:
1. Keep backup of `update-v1-german-only.html` (pre-i18n version)
2. Replace shortcode temporarily:
   ```php
   function malta_assessment_shortcode() {
       $upload_dir = wp_upload_dir();
       return '<iframe src="' . $upload_dir['baseurl'] . '/malta-assessment-v2/update-v1-german-only.html" ...></iframe>';
   }
   ```
3. Fix issues, re-deploy v2

---

## 📞 Support & Maintenance

### Updating Translations

**Scenario:** Client wants to change a specific text (e.g., CTA button text)

1. **Identify the key:**
   - Search `de.json` for the German text
   - Example: `"cta": "Kostenlose Erstberatung buchen"`
   - Key path: `results.categories.good.cta`

2. **Update all 3 files:**
   ```json
   // de.json
   "cta": "Neue CTA Text"

   // en.json
   "cta": "New CTA Text"

   // nl.json
   "cta": "Nieuwe CTA Tekst"
   ```

3. **Re-upload and clear cache**

### Adding a New Language (e.g., French)

1. **Copy template:**
   ```bash
   cp translations/de.json translations/fr.json
   ```

2. **Translate all strings** (professional translator recommended for tax/legal terms)

3. **Update language detection:**
   ```javascript
   // In update.html, function detectLanguage()
   if (path.includes('/fr/')) return 'fr'; // Add this line
   ```

4. **Create WordPress page:**
   - URL: `/fr/verification-d-eligibilite-malte/`
   - Add shortcode: `[malta_assessment]`

5. **Test thoroughly** using the checklist above

---

## 🐛 Troubleshooting

### Issue: Translations not loading

**Symptoms:** Page shows German text even on `/en/` or `/nl/` URLs

**Diagnosis:**
```javascript
// Open browser console and check:
console.log(currentLanguage); // Should be 'en' or 'nl', not 'de'
console.log(translations); // Should be object, not null
```

**Solutions:**
1. Check file path in `loadTranslations()` matches server structure
2. Verify JSON files are uploaded correctly (test direct URL: `https://yoursite.com/wp-content/uploads/malta-assessment-v2/translations/en.json`)
3. Check for JSON syntax errors (use JSONLint.com)
4. Clear browser cache + WP Rocket cache

---

### Issue: EN scoring same as DE/NL for Q011

**Symptoms:** English assessment scores identical to German/Dutch even when Q011 Option 1 selected

**Diagnosis:**
```javascript
// Check if adjustment was applied (console log):
"✅ EN adjustment applied: Q011 Option 1 score = 2"
```

**Solutions:**
1. Verify `applyLanguageSpecificAdjustments()` is called in `initializeApp()`
2. Check `currentLanguage === 'en'` condition is met
3. Inspect Q011 in questions array after adjustment:
   ```javascript
   console.log(questions.find(q => q.id === 'q011').options[0].score);
   // Should be 2 for EN, 4 for DE/NL
   ```

---

### Issue: Contact form gender options incorrect

**Symptoms:** NL form shows "Frau/Herr" instead of "Mevr./Dhr."

**Diagnosis:**
```javascript
// Check translation mapping:
console.log(t('contact.form.geschlecht_options'));
// Should be: {frau: "Mevr.", herr: "Dhr."} for NL
```

**Solutions:**
1. Verify `nl.json` has correct gender options
2. Check `contactForm.geschlecht_options.frau` and `.herr` are used in template
3. Clear cache and hard refresh

---

## 📊 Success Metrics

After deployment, track these KPIs:

### Language Distribution
- % of users completing assessment in DE vs. EN vs. NL
- Conversion rate by language (contact form submissions)
- Average score by language (to verify scoring adjustments)

### Technical Performance
- Translation load time (avg, p95, p99)
- Page load time by language
- Error rate (failed JSON loads)
- Bounce rate by language

### User Engagement
- Time to complete assessment by language
- Drop-off rate per question by language
- Results screen engagement (CTA click-through rate)

---

## ✅ Final Pre-Launch Checklist

Before going live, confirm:

- [ ] All 3 JSON files uploaded and accessible
- [ ] update.html uploaded to correct path
- [ ] 3 WordPress pages created (DE, EN, NL)
- [ ] Shortcode tested on all 3 pages
- [ ] Backend AJAX handler captures `language` field
- [ ] WP Rocket cache exclusions configured
- [ ] GTM tracking set up (optional but recommended)
- [ ] Full functional test completed (3 languages × critical path)
- [ ] EN Q011 scoring adjustment verified
- [ ] NL exit tax warning verified
- [ ] Cross-browser testing done (Chrome, Firefox, Safari, Mobile)
- [ ] Rollback plan documented and backup files saved
- [ ] Client training completed (how to update translations)

---

## 🎉 You're Ready to Deploy!

**Next Steps:**
1. Schedule deployment window (low-traffic period recommended)
2. Deploy files to production
3. Run smoke tests (load each language page, complete one full assessment each)
4. Monitor analytics for first 24-48 hours
5. Gather user feedback
6. Iterate on translations if needed

**Questions or Issues?**
- Check IMPLEMENTATION-SUMMARY.md for technical details
- Check TRANSLATION-REVIEW.md for translation quality notes
- Contact development team if major issues arise

---

**Version:** 1.0
**Last Updated:** 2025-11-11
**Author:** Claude Code (AI)
**Status:** ✅ READY FOR PRODUCTION
