# Webhook & Analytics Configuration

**Date:** 2025-10-17
**Webhook Endpoint:** https://brixon.app.n8n.cloud/webhook-test/brixon-b2b-marketing-assessment

---

## Configuration Summary

### ✅ Webhook URL Configured
- **Endpoint:** `https://brixon.app.n8n.cloud/webhook-test/brixon-b2b-marketing-assessment`
- **Location:** Line 1777 in `public/assessment/index.html`
- **Status:** Active and ready to receive data

### ✅ Analytics SDK Initialization: DISABLED
All analytics tracking IDs are intentionally left empty, preventing SDK initialization:

```javascript
tracking: {
    googleTagManagerId: '',      // Empty - GTM will NOT load
    googleAnalyticsId: '',       // Empty - GA will NOT load
    metaPixelId: '',            // Empty - Meta Pixel will NOT load
    linkedinPartnerId: '',      // Empty - LinkedIn will NOT load
    linkedinEventIds: { ... }   // All empty - no LinkedIn conversions
}
```

**How it works:**
- `initializeTracking()` function (Line 3212) has conditional checks for each SDK
- Only loads SDKs if IDs are provided (e.g., `if (config.googleAnalyticsId)`)
- Since all IDs are empty strings, NO external tracking scripts are loaded
- No base codes, no pixels, no third-party tracking

---

## Event Tracking Behavior

### What WILL Happen ✅

1. **DataLayer Events (Harmless)**
   - Events are pushed to `window.dataLayer` array
   - Useful for debugging or future analytics integration
   - Does NOT send data anywhere without GTM/GA

2. **Webhook Data Submission**
   - All assessment data sent to n8n webhook
   - Includes answers, scores, timestamps, assessment ID
   - Events are tracked internally for webhook payload

### What WILL NOT Happen ❌

1. **No Google Analytics Tracking**
   - GA SDK not loaded
   - `gtag()` function not initialized
   - No data sent to Google servers

2. **No Meta Pixel Tracking**
   - Meta Pixel SDK not loaded
   - `fbq()` function not initialized
   - No data sent to Facebook servers

3. **No LinkedIn Insight Tracking**
   - LinkedIn SDK not loaded
   - `lintrk()` function not initialized
   - No data sent to LinkedIn servers

---

## Webhook Data Format

The webhook receives data in the following format:

### Assessment Submission (Complete Results)

```json
{
  "event": "assessment_completed",
  "assessmentId": "a3f8d9c2-1b4e-4f5a-8c7d-2e6b9a1f3d8c",
  "timestamp": "2025-10-17T14:32:15.000Z",
  "answers": {
    "q_101": "4",
    "q_102": "problems",
    "q_103": "1",
    // ... all question answers
  },
  "scores": {
    "reach": 18,
    "relate": 15,
    "respond": 12,
    "retain": 10,
    "total": 55
  },
  "interpretation": "Grundlagen vorhanden, aber noch sehr viel Potenzial."
}
```

### Calendly CTA Click

```json
{
  "event": "calendly_cta",
  "assessmentId": "a3f8d9c2-1b4e-4f5a-8c7d-2e6b9a1f3d8c",
  "answers": { /* all answers */ },
  "scores": { /* all scores */ },
  "destination": "https://calendly.com/sauerborn-brixongroup/...",
  "timestamp": "2025-10-17T14:35:22.000Z"
}
```

---

## Event Flow

### 1. User Starts Assessment
```
User clicks "Start"
  → dispatchTrackingEvent('assessment_started')
  → Event pushed to dataLayer (no external tracking)
  → Webhook does NOT receive this (only on completion)
```

### 2. User Answers Questions
```
User selects option
  → dispatchTrackingEvent('question_answered')
  → Event pushed to dataLayer (no external tracking)
  → Webhook does NOT receive this yet
```

### 3. User Completes Assessment
```
Last question answered
  → showResults() function called
  → Webhook receives FULL payload with:
     - All answers
     - All scores
     - Timestamp
     - Assessment ID
```

### 4. User Clicks Calendly CTA
```
User clicks "Termin vereinbaren"
  → sendBookingSnapshot() function called
  → Webhook receives snapshot with:
     - Event: 'calendly_cta'
     - All assessment data
     - Destination URL
```

---

## Code Locations

### Configuration
- **Line 1776-1791:** CONFIG object with webhook URL and tracking IDs

### Tracking Functions
- **Line 3212-3280:** `initializeTracking()` - SDK initialization (all disabled)
- **Line 3282-3330:** `dispatchTrackingEvent()` - Event dispatching (dataLayer only)

### Webhook Submission
- **Line 3170-3192:** `sendBookingSnapshot()` - Calendly CTA tracking
- **Line 4310-4360:** Results submission with full payload

---

## Testing the Webhook

### Test Assessment Completion

1. Open assessment: https://brixongroup.com/de/b2b-marketing-assessment
2. Complete entire assessment
3. Check n8n webhook logs for payload

**Expected Webhook Call:**
- Method: POST
- Content-Type: application/json
- Body: Complete assessment data (answers + scores)

### Test Calendly CTA

1. Complete assessment to see results
2. Click "Termin vereinbaren" button
3. Check n8n webhook logs for second payload

**Expected Webhook Call:**
- Method: POST
- Body: Assessment snapshot + destination URL

---

## Privacy & GDPR Compliance

### No Third-Party Tracking ✅
- No Google Analytics cookies
- No Meta Pixel tracking
- No LinkedIn Insight tag
- No external analytics SDKs loaded

### Data Collection
- Data only sent to YOUR n8n webhook
- You control data storage and processing
- No data shared with third parties (Google, Meta, LinkedIn)

### Cookie Consent
Since no third-party tracking is implemented:
- No analytics cookies are set
- Only essential functional cookies (localStorage for assessment state)
- Cookie consent banner NOT required for analytics (but good practice for other reasons)

---

## Troubleshooting

### Webhook Not Receiving Data

1. **Check URL is correct:**
   ```javascript
   // Line 1777
   webhookUrl: 'https://brixon.app.n8n.cloud/webhook-test/brixon-b2b-marketing-assessment'
   ```

2. **Check browser console for errors:**
   - Open DevTools (F12)
   - Complete assessment
   - Look for fetch errors or CORS issues

3. **Verify n8n webhook is active:**
   - Check n8n workflow is running
   - Test endpoint with curl:
   ```bash
   curl -X POST https://brixon.app.n8n.cloud/webhook-test/brixon-b2b-marketing-assessment \
     -H "Content-Type: application/json" \
     -d '{"test": true}'
   ```

### Events Not Firing

- Events ARE firing (to dataLayer)
- No external analytics to verify against
- Check webhook logs for actual data submission

---

## Future: Enabling Analytics

If you want to enable analytics later:

1. **Add Tracking IDs:**
   ```javascript
   tracking: {
       googleAnalyticsId: 'G-XXXXXXXXXX',  // Your GA4 ID
       metaPixelId: '1234567890',           // Your Meta Pixel ID
       // etc.
   }
   ```

2. **Deploy Updated File**

3. **Verify SDKs Load:**
   - Check browser console for gtag(), fbq() functions
   - Check network tab for analytics scripts

---

## Summary

✅ **Webhook URL:** Configured and ready
✅ **Analytics SDKs:** All disabled (empty IDs)
✅ **Event Tracking:** Internal only (dataLayer pushes)
✅ **Data Flow:** Assessment → n8n webhook only
✅ **Privacy:** No third-party tracking

**Next Action:** Deploy updated `index.html` and test webhook endpoint with a complete assessment run.

---

**Status:** ✅ Configuration Complete
**Risk:** Low (webhook URL change only)
**Testing Required:** Complete one full assessment to verify webhook receives data
