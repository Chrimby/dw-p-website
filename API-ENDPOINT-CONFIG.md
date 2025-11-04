# API Endpoint Configuration Guide

## Overview

The Malta Assessment uses a server-side API endpoint for secure evaluation. This guide explains how to configure the endpoint URL for different deployment scenarios.

---

## 🎯 Where to Configure

### 1. **HTML File** (Client-Side)

Location: `public/malta-assessment-v2/index.html` (Line ~815)

```javascript
const CONFIG = {
    // Server-side evaluation endpoint
    apiEndpoint: 'https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator',
};
```

**What to change:**
- Replace `https://www.drwerner.com` with your actual domain
- Choose the correct endpoint format based on your deployment method

---

### 2. **PHP File** (Server-Side)

Location: `malta-assessment-evaluator.php` (Line ~35)

```php
// Allowed origins (domains that can call this endpoint)
const ALLOWED_ORIGINS = [
    'https://www.drwerner.com',
    'https://drwerner.com',
];

// Webhook URL (Line ~44)
const WEBHOOK_URL = 'https://brixon.app.n8n.cloud/webhook/malta-eignungscheck';
```

**What to change:**
- Add your domain(s) to `ALLOWED_ORIGINS`
- Update `WEBHOOK_URL` with your webhook endpoint
- Remove localhost entries in production

---

## 📋 Configuration by Deployment Method

### Option 1: WordPress REST API (Recommended)

**HTML Configuration:**
```javascript
const CONFIG = {
    apiEndpoint: 'https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator',
};
```

**PHP Configuration:**
```php
const ALLOWED_ORIGINS = [
    'https://www.drwerner.com',
    'https://drwerner.com', // with and without www
];
```

**WordPress functions.php:**
```php
add_action('rest_api_init', function () {
    register_rest_route('drwerner/v1', '/malta-evaluator', [
        'methods' => 'POST',
        'callback' => function() {
            require_once get_template_directory() . '/malta-assessment-evaluator.php';
            exit;
        },
        'permission_callback' => '__return_true',
    ]);
});
```

**Endpoint URL Format:**
```
https://{your-domain}/wp-json/{namespace}/{endpoint}
```

---

### Option 2: Standalone PHP File

**HTML Configuration:**
```javascript
const CONFIG = {
    apiEndpoint: 'https://www.drwerner.com/api/malta-evaluator.php',
};
```

**PHP Configuration:**
```php
const ALLOWED_ORIGINS = [
    'https://www.drwerner.com',
];
```

**File Location:**
```
/public_html/api/malta-evaluator.php
```

**Endpoint URL Format:**
```
https://{your-domain}/api/malta-evaluator.php
```

---

### Option 3: Custom Subdomain

**HTML Configuration:**
```javascript
const CONFIG = {
    apiEndpoint: 'https://api.drwerner.com/malta-evaluator',
};
```

**PHP Configuration:**
```php
const ALLOWED_ORIGINS = [
    'https://www.drwerner.com',
    'https://drwerner.com',
];
```

---

## 🔒 Security Configuration

### CORS (Cross-Origin Resource Sharing)

**Why it matters:**
- Prevents unauthorized domains from using your API
- Blocks requests from other websites

**How to configure:**
```php
const ALLOWED_ORIGINS = [
    'https://www.drwerner.com',     // Production domain
    'https://drwerner.com',          // Without www
    'http://localhost:8881',         // Local development (REMOVE IN PRODUCTION!)
];
```

**Important:**
- Always use `https://` in production
- Include both `www` and non-`www` versions if needed
- Remove localhost entries before going live

---

### Webhook Configuration

**Why server-side:**
- Webhook URL is hidden from users
- No way to see or manipulate webhook endpoint
- More secure than client-side webhooks

**How to configure:**
```php
const WEBHOOK_URL = 'https://your-webhook-service.com/webhook/endpoint';
const WEBHOOK_ENABLED = true; // Set to false to disable
```

**Supported webhook services:**
- n8n (current)
- Make (formerly Integromat)
- Zapier
- Custom endpoints

---

## 🧪 Testing Your Configuration

### 1. Test Endpoint Accessibility

Open browser console (F12) and run:

```javascript
fetch('https://www.drwerner.com/wp-json/drwerner/v1/malta-evaluator', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    answers: {"q001": "4"}
  })
})
.then(r => r.json())
.then(console.log)
.catch(console.error)
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "percentage": 85,
    "category": "excellent",
    ...
  }
}
```

---

### 2. Test CORS Configuration

**Symptoms of CORS issues:**
```
Access to fetch at '...' from origin '...' has been blocked by CORS policy
```

**Solution:**
1. Check `ALLOWED_ORIGINS` in PHP file
2. Ensure domain matches exactly (including `https://`)
3. Check for typos in domain name

---

### 3. Test Webhook Delivery

**Check if webhook is working:**
1. Enable debug mode in PHP: `const DEBUG_MODE = true;`
2. Fill out questionnaire
3. Check PHP error logs for webhook status
4. Check your webhook service for received data

**Webhook payload structure:**
```json
{
  "timestamp": "2025-11-04T20:00:00Z",
  "contact": {
    "geschlecht": "Herr",
    "vorname": "Max",
    "nachname": "Mustermann",
    "email": "max@example.com"
  },
  "answers": { ... },
  "score": {
    "percentage": 85,
    "category": "excellent"
  },
  "interpretation": "...",
  "detailedResults": [ ... ]
}
```

---

## 🚨 Common Issues

### Issue 1: "Failed to fetch"

**Cause:** Wrong API endpoint URL

**Solution:**
1. Verify endpoint URL in HTML CONFIG
2. Test endpoint directly in browser
3. Check if PHP file is uploaded

---

### Issue 2: CORS Error

**Cause:** Domain not in ALLOWED_ORIGINS

**Solution:**
```php
// Add your domain here
const ALLOWED_ORIGINS = [
    'https://www.drwerner.com', // ← Your domain
];
```

---

### Issue 3: 404 Not Found (WordPress)

**Cause:** REST API route not registered

**Solution:**
1. Check if code is in `functions.php`
2. Verify namespace and endpoint name match
3. Try flushing WordPress permalinks (Settings → Permalinks → Save)

---

### Issue 4: Webhook Not Receiving Data

**Cause:** Webhook disabled or wrong URL

**Solution:**
```php
const WEBHOOK_URL = 'https://your-correct-url.com/webhook'; // ← Check this
const WEBHOOK_ENABLED = true; // ← Must be true
```

---

## 📝 Configuration Checklist

Before going live, verify:

- [ ] **HTML:** API endpoint URL is correct
- [ ] **HTML:** Domain uses `https://` (not `http://`)
- [ ] **PHP:** ALLOWED_ORIGINS contains your production domain(s)
- [ ] **PHP:** Localhost entries removed from ALLOWED_ORIGINS
- [ ] **PHP:** WEBHOOK_URL is correct
- [ ] **PHP:** WEBHOOK_ENABLED is true
- [ ] **PHP:** DEBUG_MODE is false in production
- [ ] **WordPress:** REST API route registered (if using WordPress)
- [ ] **Server:** PHP file uploaded to correct location
- [ ] **Server:** File permissions are 644
- [ ] **Testing:** Endpoint responds to POST requests
- [ ] **Testing:** CORS headers present
- [ ] **Testing:** Webhook receives data

---

## 🆘 Need Help?

### Quick Diagnostics

Run this in browser console:
```javascript
// Test 1: Check if endpoint is reachable
fetch('YOUR_API_ENDPOINT_HERE')
  .then(r => r.text())
  .then(console.log)
  .catch(console.error);

// Expected: {"success":false,"error":"Only POST requests are allowed"}
```

### Support Resources

- **QUICKSTART.md** - Quick setup guide
- **README.md** - Complete documentation
- **DEPLOYMENT-CHECKLIST.md** - Step-by-step deployment

---

**Version:** 2.3
**Last Updated:** 2025-11-04
**Related Files:**
- `malta-assessment-evaluator.php` - Server-side script
- `public/malta-assessment-v2/index.html` - Client-side HTML
