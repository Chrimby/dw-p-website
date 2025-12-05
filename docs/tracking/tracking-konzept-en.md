# Tracking Concept DrWerner.com

## Comprehensive Server-Side Tracking & Marketing Automation Concept

**Created for:** DrWerner.com (Tax consultancy for company formation & emigration)
**Second Property:** philippsauerborn.com (Thought Leadership Blog)
**Date:** December 2024

---

# Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Current Situation & Goals](#2-current-situation--goals)
3. [Technical Architecture Overview](#3-technical-architecture-overview)
4. [Server-Side Tracking with Stape](#4-server-side-tracking-with-stape)
5. [Consent Management (Usercentrics/Cookiebot)](#5-consent-management-usercentricsookiebot)
6. [Event Tracking Concept](#6-event-tracking-concept)
7. [Ad Platform Integrations](#7-ad-platform-integrations)
8. [Lead Management & Nurturing](#8-lead-management--nurturing)
9. [CRM Strategy (Salesforce + Brevo)](#9-crm-strategy-salesforce--brevo)
10. [Custom Analytics with BigQuery](#10-custom-analytics-with-bigquery)
11. [Cross-Domain Tracking](#11-cross-domain-tracking)
12. [Cookie Keeper & First-Party Data](#12-cookie-keeper--first-party-data)
13. [Data Protection & GDPR](#13-data-protection--gdpr)
14. [Implementation Roadmap](#14-implementation-roadmap)
15. [Technical Checklists](#15-technical-checklists)

---

# 1. Executive Summary

This concept describes a modern, privacy-compliant tracking infrastructure for DrWerner.com and philippsauerborn.com. At its core is **Server-Side Tracking via Stape**, which reduces dependency on browser cookies and enables more precise tracking.

### Core Components:
- **Server-Side GTM** via Stape (s.drwerner.com)
- **Consent Mode v2** with Usercentrics Cookiebot
- **Multi-Platform Tracking**: Google Ads, Meta Ads, LinkedIn Ads
- **Marketing Automation**: Brevo for MQL nurturing
- **CRM**: Salesforce for SQLs and sales pipeline
- **Custom Analytics**: BigQuery for complete customer journey

### Expected Benefits:
1. **Better Data Quality**: Server-side tracking partially bypasses ad blockers
2. **Longer Cookie Lifespan**: Up to 2 years instead of 7 days (Safari ITP)
3. **More Precise Attribution**: Cross-session and cross-device tracking
4. **GDPR Compliance**: Consent-driven data processing
5. **Sales Transparency**: Complete lead journey visibility

---

# 2. Current Situation & Goals

## 2.1 Current Situation

| Aspect | Status |
|--------|--------|
| **Website** | WordPress on drwerner.com |
| **Second Blog** | philippsauerborn.com (Thought Leadership) |
| **CRM** | Salesforce (SQLs/direct inquiries only) |
| **Analytics** | Google Analytics (Client-Side) |
| **Ads** | Google Ads active |
| **Consent** | Usercentrics Cookiebot |
| **Server-Side** | Stape container exists, s.drwerner.com configured |

## 2.2 Challenges

1. **SEO Decline**: Organic traffic dropping, paid strategy becoming more important
2. **Cookie Restrictions**: Safari ITP, Firefox ETP, Chrome Privacy Sandbox
3. **No MQLs**: Currently only direct inquiries (SQLs), no lead nurturing pipeline
4. **Lack of Transparency**: Sales can't see what leads did before inquiring
5. **Cross-Domain Blind Spot**: Activities between both domains not linked

## 2.3 Goals

### Short-term (Phase 1-2)
- Fully implement server-side tracking
- Integrate Meta Ads and LinkedIn Ads
- Lead magnet tracking via Vavolta
- Set up Brevo for email nurturing

### Medium-term (Phase 3-4)
- Map MQL pipeline in Salesforce
- Implement lead scoring
- Build BigQuery customer journey
- Cross-domain tracking

### Long-term
- Complete customer journey transparency
- Automated lead qualification
- Revenue attribution across all channels

---

# 3. Technical Architecture Overview

## 3.1 System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              BROWSER (Client)                                │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐  │
│  │  WordPress  │    │  Vavolta    │    │ Usercentrics│    │  Web GTM    │  │
│  │  Website    │    │ Lead Magnets│    │  Cookiebot  │    │  Container  │  │
│  └──────┬──────┘    └──────┬──────┘    └──────┬──────┘    └──────┬──────┘  │
│         │                  │                  │                  │          │
│         └──────────────────┴──────────────────┴──────────────────┘          │
│                                      │                                       │
│                            Consent-controlled Events                         │
│                                      ▼                                       │
└─────────────────────────────────────────────────────────────────────────────┘
                                       │
                                       │ HTTPS (First-Party)
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         s.drwerner.com (Stape)                              │
│                      SERVER-SIDE GTM CONTAINER                              │
├─────────────────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐  │
│  │  GA4 Client │    │Cookie Keeper│    │  Consent    │    │  Custom     │  │
│  │             │    │  (Stape)    │    │  Handler    │    │  Variables  │  │
│  └─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘  │
│                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │                        SERVER-SIDE TAGS                              │   │
│  ├─────────────┬─────────────┬─────────────┬─────────────┬─────────────┤   │
│  │    GA4      │  Google Ads │  Meta CAPI  │  LinkedIn   │   Brevo     │   │
│  │   Server    │  Enhanced   │             │   CAPI      │   HTTP      │   │
│  │    Tag      │ Conversions │             │             │   Request   │   │
│  └─────────────┴─────────────┴─────────────┴─────────────┴─────────────┘   │
│                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │                      BigQuery HTTP Request Tag                       │   │
│  │              (Custom Analytics / Customer Journey)                   │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────┘
                │              │              │              │              │
                ▼              ▼              ▼              ▼              ▼
         ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐
         │   GA4    │   │  Google  │   │   Meta   │   │ LinkedIn │   │  Brevo   │
         │ Property │   │   Ads    │   │   Ads    │   │   Ads    │   │   API    │
         └──────────┘   └──────────┘   └──────────┘   └──────────┘   └────┬─────┘
                                                                          │
                                                                          ▼
                                                                   ┌──────────┐
                                                                   │Salesforce│
                                                                   │   CRM    │
                                                                   └──────────┘
```

## 3.2 Data Flow Explanation

### What Happens Technically?

1. **Browser → Server (First-Party)**
   - User visits drwerner.com
   - Web GTM captures events (pageviews, clicks, forms)
   - Events are sent to s.drwerner.com (NOT to google-analytics.com)
   - **Benefit**: Appears to the browser as own server → no third-party blocking

2. **Server → Platforms**
   - Stape server receives events
   - Cookie Keeper sets/renews long-lived first-party cookies
   - Server sends data to Google, Meta, LinkedIn APIs
   - **Benefit**: Ad blockers block client-side pixels, not server requests

3. **Server → BigQuery**
   - Every event is additionally streamed to BigQuery
   - Complete customer journey stored
   - Custom analyses and reports possible

---

# 4. Server-Side Tracking with Stape

## 4.1 What is Server-Side Tracking?

### The Problem with Client-Side Tracking

Traditional tracking works like this:
```
Browser → JavaScript Pixel → Directly to Google/Meta/LinkedIn
```

**Problems with this:**
- **Ad Blockers**: Block known tracking domains
- **Safari ITP**: Deletes third-party cookies after 7 days
- **Firefox ETP**: Blocks third-party trackers by default
- **Chrome Privacy Sandbox**: Third-party cookies being phased out in 2024/2025

### The Server-Side Solution

```
Browser → Own Subdomain (s.drwerner.com) → Stape Server → Platforms
```

**Benefits:**
- **First-Party Context**: Browser sees request to own domain
- **Longer Cookies**: Server can set cookies with long lifespans
- **No Ad Blocker Blocking**: Server communication isn't blocked
- **Data Control**: You decide which data goes where

## 4.2 Stape Configuration (already in place)

### Current Infrastructure
- **Container Domain**: s.drwerner.com
- **Stape Zone**: EU (euj.stape.io) - GDPR compliant
- **SSL**: Automatic via Stape

### Required Power-Ups

| Power-Up | Purpose | Status |
|----------|---------|--------|
| **Cookie Keeper** | Extends cookie lifespan to 2 years | Activate |
| **Geo Headers** | Country/region for targeting | Optional |
| **Bot Detection** | Filters bot traffic | Recommended |

## 4.3 GTM Container Structure

### Web Container (Client-Side)

```
GTM Web Container
├── Tags
│   ├── GA4 Configuration (→ Server)
│   ├── Consent Mode Default
│   └── DataLayer Push Tags
├── Triggers
│   ├── All Pages
│   ├── Form Submissions
│   ├── Lead Magnet Downloads
│   ├── QuickCheck Completions
│   └── Newsletter Signups
└── Variables
    ├── Consent State Variables
    ├── User Data Variables
    └── Event Parameters
```

### Server Container (Server-Side)

```
GTM Server Container
├── Clients
│   └── GA4 Client (receives events from Web Container)
├── Tags
│   ├── GA4 Server Tag → Google Analytics
│   ├── Google Ads Conversion Tag → Enhanced Conversions
│   ├── Meta Conversions API → Facebook/Instagram
│   ├── LinkedIn CAPI → LinkedIn
│   ├── Brevo HTTP Request → Email Marketing
│   └── BigQuery HTTP Request → Custom Analytics
├── Triggers
│   ├── All GA4 Events
│   ├── Conversion Events Only
│   └── Consent-Based Triggers
└── Variables
    ├── User Identifiers (hashed)
    ├── Consent Parameters
    └── Event Data Mappings
```

---

# 5. Consent Management (Usercentrics/Cookiebot)

## 5.1 Approach: Default Consent "Granted"

> **Note**: For maximum tracking transparency and data quality, consent defaults to "granted". The cookie banner remains active so Google Consent Mode v2 receives the correct signals.

### Why Cookie Banner is Still Needed?

**Google Consent Mode v2 (mandatory since March 2024)**

Google requires advertisers to transmit consent signals. Without these signals:
- Google Ads remarketing works with limitations
- Conversion tracking is incomplete
- Smart Bidding has less data

| Consent Type | Description | Default Value |
|--------------|-------------|---------------|
| `ad_storage` | Store advertising cookies | **granted** |
| `ad_user_data` | Send user data to Google | **granted** |
| `ad_personalization` | Personalized advertising | **granted** |
| `analytics_storage` | Store analytics cookies | **granted** |

## 5.2 Cookiebot Configuration (Default Granted)

### Cookie Category Mapping

```
Cookiebot Categories → Google Consent Mode v2
─────────────────────────────────────────────
Necessary       → security_storage, functionality_storage (always active)
Preferences     → personalization_storage (default: granted)
Statistics      → analytics_storage (default: granted)
Marketing       → ad_storage, ad_user_data, ad_personalization (default: granted)
```

### WordPress Plugin Settings

1. **Auto-Blocking DISABLED**: All scripts load immediately
2. **IAB TCF 2.2 disabled**: Not needed
3. **GTM Integration**: Cookiebot in GTM with default "granted"
4. **Banner Design**: "Accept" prominent, rejection possible but not prominent

## 5.3 Technical Implementation (Default Granted)

### Consent Flow in GTM

```
1. Page loads
   │
2. GTM Consent Initialization Trigger fires IMMEDIATELY
   │
3. Default Consent State is set:
   │  - analytics_storage: GRANTED ✓
   │  - ad_storage: GRANTED ✓
   │  - ad_user_data: GRANTED ✓
   │  - ad_personalization: GRANTED ✓
   │
4. ALL GTM Tags fire immediately (no waiting for banner click)
   │
5. Cookiebot Banner appears (for users who want to decline)
   │
6. If user clicks "Decline":
   │  → Consent State updated to "denied"
   │  → Future events without user identifier
   │
7. Server-Side Container receives consent parameters (always "granted" on first hit)
```

### GTM Tag: Consent Initialization (Default Granted)

```javascript
// In GTM Web Container - Consent Initialization Trigger
// Tag Type: Google Tag - Consent Initialization

gtag('consent', 'default', {
  'ad_storage': 'granted',
  'ad_user_data': 'granted',
  'ad_personalization': 'granted',
  'analytics_storage': 'granted',
  'functionality_storage': 'granted',
  'personalization_storage': 'granted',
  'security_storage': 'granted',
  'wait_for_update': 500  // Cookiebot has 500ms to override
});
```

### Server-Side Handling

In the Server Container:
- Consent parameters come as part of the GA4 hit
- With default "granted": All user data is sent
- Only with explicit "denied": Anonymized events

**Result:**
- 100% of first-time visitors are fully tracked
- Only users who actively decline are anonymized
- Google receives correct consent signals for compliance

---

# 6. Event Tracking Concept

## 6.1 Event Hierarchy

### Funnel Stages for DrWerner.com

```
AWARENESS (Top of Funnel)
├── page_view                    → Every page
├── scroll_depth                 → 25%, 50%, 75%, 90%
└── video_engagement             → If videos present

INTEREST (Middle of Funnel)
├── content_view                 → Blog article read (>30 sec.)
├── cta_click                    → CTA button clicked
├── service_page_view            → Service pages visited
└── pricing_interest             → Pricing page visited

CONSIDERATION (Middle-Bottom)
├── lead_magnet_view             → Vavolta PDF/Checklist viewed
├── quickcheck_start             → QuickCheck started
├── quickcheck_complete          → QuickCheck completed
└── newsletter_signup            → Newsletter subscription

CONVERSION (Bottom of Funnel)
├── lead_magnet_download         → PDF downloaded (= MQL)
├── contact_form_submit          → Contact form submitted
├── callback_request             → Callback requested
└── consultation_booking         → Consultation booked (= SQL)
```

## 6.2 Event Definitions in Detail

### Lead Magnet Download (MQL Event)

This is the central event for lead generation:

| Parameter | Description | Example Value |
|-----------|-------------|---------------|
| `event_name` | Event identifier | `lead_magnet_download` |
| `lead_magnet_name` | Asset name | `Malta Company Formation Checklist` |
| `lead_magnet_type` | Asset type | `pdf` / `checklist` / `guide` |
| `lead_magnet_topic` | Topic | `company_formation` / `emigration` |
| `user_email` | Email (hashed) | SHA256 Hash |
| `user_name` | Name | `John Smith` |
| `traffic_source` | Source | `google_ads` / `meta_ads` / `organic` |
| `landing_page` | Entry page | `/malta-company-formation` |

### QuickCheck Completion

For interactive questionnaires:

| Parameter | Description | Example Value |
|-----------|-------------|---------------|
| `event_name` | Event identifier | `quickcheck_complete` |
| `quickcheck_name` | Check name | `Emigration Check` |
| `quickcheck_result` | Result category | `malta_suitable` / `cyprus_suitable` |
| `quickcheck_score` | Numerical score | `85` |
| `user_email` | Email (hashed) | SHA256 Hash |
| `recommended_action` | Recommended next step | `book_consultation` |

### Contact Form (SQL Event)

| Parameter | Description | Example Value |
|-----------|-------------|---------------|
| `event_name` | Event identifier | `contact_form_submit` |
| `form_type` | Form type | `consultation_request` / `callback` |
| `service_interest` | Service of interest | `malta_company_formation` |
| `user_email` | Email (hashed) | SHA256 Hash |
| `user_phone` | Phone (hashed) | SHA256 Hash |
| `estimated_value` | Estimated value | Optional |

## 6.3 DataLayer Implementation

### Example: Lead Magnet Download via Vavolta

```javascript
// When Vavolta form is successfully submitted:
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
  'event': 'lead_magnet_download',
  'lead_magnet_name': 'Malta Company Formation Checklist',
  'lead_magnet_type': 'checklist',
  'lead_magnet_topic': 'company_formation',
  'user_data': {
    'email': 'user@example.com',  // Will be hashed server-side
    'first_name': 'John',
    'last_name': 'Smith'
  },
  'traffic_source': '{{Traffic Source Variable}}',
  'landing_page': '{{Page Path}}'
});
```

### Vavolta Integration

**Important Note about Vavolta:**

Vavolta offers GTM integration (from Pro plan, €15/month). The exact technical implementation must be clarified directly with Vavolta because:
1. No public API documentation available
2. Webhooks are included in Team plan (€39/month)
3. Direct CRM integrations (except Attio) not confirmed

**Recommended Approach:**
1. Vavolta Pro plan for GTM integration
2. GTM DataLayer push after successful gating
3. Alternative: Webhook to own endpoint → DataLayer push

---

# 7. Ad Platform Integrations

## 7.1 Google Ads (Enhanced Conversions)

### What are Enhanced Conversions?

Enhanced Conversions improve conversion measurement by sending hashed first-party data (email, phone) to Google. Google matches these with logged-in Google users.

**Benefits:**
- Better attribution even without cookies
- Higher conversion capture (up to 20% more)
- Works even when user declines cookies (with limitations)

### Server-Side Implementation

```
GTM Server Container - Google Ads Tag Configuration:
─────────────────────────────────────────────────────
Conversion ID:        [From Google Ads account]
Conversion Label:     [Per conversion action]

Enhanced Conversions: Enabled
├── Email:           {{Hashed Email}}
├── Phone:           {{Hashed Phone}}
├── First Name:      {{Hashed First Name}}
├── Last Name:       {{Hashed Last Name}}
└── Address:         [Optional]

Consent:
├── Ad Storage:      {{Consent ad_storage}}
└── Ad User Data:    {{Consent ad_user_data}}
```

### Conversion Actions for DrWerner.com

| Conversion | Value | Type | Counting Method |
|------------|-------|------|-----------------|
| Lead Magnet Download | €50 | Secondary | Once |
| QuickCheck Complete | €30 | Secondary | Once |
| Newsletter Signup | €10 | Secondary | Once |
| Contact Request | €200 | Primary | Once |
| Consultation Booking | €500 | Primary | Once |

## 7.2 Meta Conversions API (CAPI)

### What is Meta CAPI?

The Conversions API is Meta's server-side tracking solution. Instead of the browser pixel, events are sent directly from the server to Meta.

**Why CAPI is Important:**
- iOS 14.5+ App Tracking Transparency massively reduces pixel tracking
- CAPI events are not blocked by Apple
- Better data quality for campaign optimization

### Server-Side Implementation

```
GTM Server Container - Meta CAPI Tag:
──────────────────────────────────────
Access Token:       [From Meta Events Manager]
Pixel ID:           [Facebook Pixel ID]

Event Mapping:
├── lead_magnet_download  → Lead
├── quickcheck_complete   → Lead
├── contact_form_submit   → CompleteRegistration
└── newsletter_signup     → Subscribe

User Data (hashed):
├── em (Email):     {{SHA256 Hashed Email}}
├── ph (Phone):     {{SHA256 Hashed Phone}}
├── fn (First Name): {{SHA256 Hashed First Name}}
├── ln (Last Name):  {{SHA256 Hashed Last Name}}
├── client_ip:      {{Client IP}}
├── client_user_agent: {{User Agent}}
└── fbc (Click ID): {{FB Click ID from Cookie}}

Event Matching:
├── event_id:       {{Unique Event ID}}  // For deduplication
└── event_source_url: {{Page URL}}
```

### Deduplication (Important!)

When browser pixel AND CAPI are both active, events must be deduplicated:
1. Same `event_id` for browser and server event
2. Meta recognizes duplicates and counts only once

**Recommendation for DrWerner.com:**
- Disable browser pixel or use only for pageviews
- All conversions via server-side CAPI

## 7.3 LinkedIn Conversions API

### B2B Tracking Specifics

LinkedIn is particularly relevant for a tax consultancy:
- B2B target audience (entrepreneurs, self-employed)
- Higher lead quality than Meta
- Longer sales cycles → attribution more important

### Server-Side Implementation

```
GTM Server Container - LinkedIn CAPI:
──────────────────────────────────────
API Endpoint:       https://api.linkedin.com/rest/conversionEvents
Access Token:       [From LinkedIn Campaign Manager]
Ad Account ID:      [LinkedIn Ad Account]

Conversion Mapping:
├── lead_magnet_download  → LEAD
├── quickcheck_complete   → QUALIFIED_LEAD
├── contact_form_submit   → SUBMIT_LEAD_FORM
└── consultation_booking  → SCHEDULE

User Data:
├── email (SHA256):      {{Hashed Email}}
├── firstName (SHA256):  {{Hashed First Name}}
├── lastName (SHA256):   {{Hashed Last Name}}
├── companyName:         {{Company Name}}  // If captured
└── title:               {{Job Title}}     // If captured
```

### LinkedIn Insight Tag

In addition to CAPI, the LinkedIn Insight Tag should remain active for retargeting:
- Server-side for conversions
- Client-side for audience building

---

# 8. Lead Management & Nurturing

## 8.1 Lead Lifecycle Model

### Definition of Lead Stages for DrWerner.com

```
                    LEAD LIFECYCLE
                         │
    ┌────────────────────┼────────────────────┐
    │                    │                    │
    ▼                    ▼                    ▼
┌─────────┐        ┌─────────┐         ┌─────────┐
│ ANONYMOUS│       │   MQL   │         │   SQL   │
│ Visitor │   →    │Marketing│    →    │  Sales  │
│         │        │Qualified│         │Qualified│
└─────────┘        └─────────┘         └─────────┘
    │                    │                    │
    │                    │                    │
    ▼                    ▼                    ▼
Pageviews          Lead Magnet          Contact
Blog Visits        Downloads            Request
                   QuickCheck           Consultation
                   Newsletter           Booking
```

### Concrete Definitions

| Stage | Trigger | System | Action |
|-------|---------|--------|--------|
| **Anonymous** | Website visit | GA4 | - |
| **Known** | Newsletter Signup | Brevo | Welcome Email |
| **MQL** | Lead Magnet Download OR QuickCheck Complete | Brevo + Salesforce | Nurturing Sequence |
| **SQL** | Contact Request OR Booking | Salesforce | Sales Contact |
| **Opportunity** | Qualified Conversation | Salesforce | Pipeline |
| **Customer** | Client Agreement | Salesforce | Onboarding |

## 8.2 Brevo Nurturing Strategy

### Why Brevo?

1. **GDPR-compliant**: EU servers, German GmbH
2. **Price-Performance**: Cheaper than HubSpot/Marketo
3. **Marketing Automation**: Full-featured workflows
4. **Salesforce Integration**: Native connection possible

### Nurturing Workflows

#### Workflow 1: Lead Magnet Download

```
Trigger: lead_magnet_download Event
    │
    ├── [Day 0] Immediately
    │   └── Email: "Your Download: {Lead Magnet Name}"
    │       └── Content: Download link + value teaser
    │
    ├── [Day 2]
    │   └── Email: "Have you had a chance to look?"
    │       └── Content: Key takeaways + related content
    │
    ├── [Day 5]
    │   └── Condition: Completed QuickCheck?
    │       ├── YES → Skip
    │       └── NO → Email: "Find out which country suits you"
    │           └── CTA: Start QuickCheck
    │
    ├── [Day 8]
    │   └── Email: Case Study / Customer Story
    │       └── Content: Success example from lead's topic
    │
    ├── [Day 12]
    │   └── Condition: Visited website since Day 8?
    │       ├── YES → Email: "We're happy to answer your questions"
    │       │   └── CTA: Free initial consultation
    │       └── NO → Email: Soft reminder with different content
    │
    └── [Day 20]
        └── Lead Scoring Check
            ├── Score >= 50 → Tag: "Sales Ready" + Alert to sales
            └── Score < 50 → Move to long-term nurture
```

#### Workflow 2: QuickCheck Completion

```
Trigger: quickcheck_complete Event
    │
    ├── [Immediately]
    │   └── Email: "Your Result: {QuickCheck Result}"
    │       └── Personalized by result:
    │           ├── Malta suitable → Malta-specific info
    │           ├── Cyprus suitable → Cyprus-specific info
    │           └── Uncertain → Comparison guide
    │
    ├── [Day 3]
    │   └── Email: "Deep Dive: What does {Result} mean for you?"
    │       └── Detailed explanation + FAQ
    │
    ├── [Day 7]
    │   └── Email: "Others with your profile did this..."
    │       └── Case study matching result
    │
    └── [Day 14]
        └── Email: "Let's discuss your situation"
            └── CTA: Book consultation
```

### Lead Scoring in Brevo

| Action | Points |
|--------|--------|
| Newsletter Signup | +10 |
| Blog article read (>2 min) | +5 |
| Service page visited | +10 |
| Pricing page visited | +15 |
| Lead Magnet Download | +25 |
| QuickCheck Complete | +30 |
| Email opened | +3 |
| Email link clicked | +5 |
| Website return (<7 days) | +10 |
| No activity (30 days) | -20 |

**MQL Threshold**: 50 points
**SQL-Ready**: 80 points (+ explicit interest signaled)

---

# 9. CRM Strategy (Salesforce + Brevo)

## 9.1 Recommended Architecture

Based on requirements analysis, I recommend the following split:

```
┌─────────────────────────────────────────────────────────────────┐
│                         BREVO                                    │
│            (Marketing Automation & Nurturing)                    │
├─────────────────────────────────────────────────────────────────┤
│  ✓ All Leads (including anonymous with Cookie ID)               │
│  ✓ Email Marketing & Automation                                  │
│  ✓ Lead Scoring                                                  │
│  ✓ MQL Management                                                │
│  ✓ Website Tracking (Brevo Tracker in addition to GA4)          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ Sync when:
                             │ - Score >= 80
                             │ - Explicit request
                             │ - Booking made
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                       SALESFORCE                                 │
│               (Sales CRM & Pipeline)                             │
├─────────────────────────────────────────────────────────────────┤
│  ✓ SQLs (qualified leads)                                       │
│  ✓ Pipeline Management                                           │
│  ✓ Deal Tracking                                                 │
│  ✓ Client Management                                             │
│  ✓ Revenue Reporting                                             │
└─────────────────────────────────────────────────────────────────┘
```

## 9.2 Salesforce Fields Analysis (Existing vs. New)

### Already Existing Fields (can be reused)

These fields already exist in the Lead object and cover tracking requirements:

| Existing Field | Use for Tracking | Note |
|----------------|------------------|------|
| `gclid__c` | Google Click ID | ✓ Perfect, already exists |
| `Lead_Channel__c` | Traffic Source (google_ads, meta_ads, etc.) | ✓ Use instead of new field |
| `Lead_Quality__c` | Lead Score | ✓ Can be used for Brevo score |
| `LeadSource` | Main source | ✓ Standard field, keep |
| `Source__c` | Detailed source | ✓ For UTM Source |
| `Referrer__c` | Referrer URL | ✓ Perfect for attribution |
| `Service_Type__c` | Service interest | ✓ Already business-relevant |
| `Conversion_URL__c` | Landing Page | ✓ For first-touch attribution |
| `Status` | Lead status | ✓ Extend for MQL/SQL tracking |

### Only These New Fields Needed

Based on analysis, we only need a few additional fields:

| New Field | Type | Description | Priority |
|-----------|------|-------------|----------|
| `MQL_Date__c` | Date | Date of MQL qualification | High |
| `Lead_Magnet_Downloaded__c` | Text | Name of downloaded asset | High |
| `QuickCheck_Result__c` | Picklist | QuickCheck result (malta_suitable, cyprus_suitable, etc.) | High |
| `Brevo_Contact_ID__c` | Text (External ID) | Link to Brevo for sync | High |
| `fbclid__c` | Text | Meta Click ID (analogous to gclid) | Medium |
| `li_fat_id__c` | Text | LinkedIn Click ID | Medium |
| `First_Touch_Campaign__c` | Text | UTM Campaign at first contact | Medium |
| `Last_Website_Visit__c` | DateTime | Last website visit (from Brevo) | Low |

### Extend Status Picklist

The existing `Status` field should contain these values:

```
Status Picklist:
├── New (Default)
├── MQL - Marketing Qualified ← NEW
├── Nurturing ← NEW
├── SQL - Sales Qualified ← NEW (or rename existing value)
├── Contacted
├── Qualified
├── Proposal
├── Won
└── Lost
```

### Field Mapping: Tracking → Salesforce

| Tracking Event | Salesforce Field | Value |
|----------------|------------------|-------|
| Google Click ID | `gclid__c` | From URL parameter |
| Meta Click ID | `fbclid__c` ← NEW | From URL parameter |
| LinkedIn Click ID | `li_fat_id__c` ← NEW | From cookie |
| Traffic Source | `Lead_Channel__c` | google_ads / meta_ads / linkedin_ads / organic |
| UTM Source | `Source__c` | google / facebook / linkedin / etc. |
| Landing Page | `Conversion_URL__c` | Page URL |
| Referrer | `Referrer__c` | Document Referrer |
| Lead Magnet | `Lead_Magnet_Downloaded__c` ← NEW | Asset name |
| QuickCheck Result | `QuickCheck_Result__c` ← NEW | Result category |
| Lead Score | `Lead_Quality__c` | Numerical score from Brevo |
| MQL Date | `MQL_Date__c` ← NEW | Timestamp of qualification |

#### Workflow Rules

1. **MQL → SQL Upgrade**
   - Trigger: Lead_Quality__c >= 80 OR contact request
   - Action: Status to "SQL - Sales Qualified", sales notification

2. **Activity Logging**
   - Log Brevo events as Activities in Salesforce
   - Sales sees: "Max downloaded Malta checklist 2 days ago"

## 9.3 Brevo-Salesforce Sync

### Native Integration

Brevo offers native Salesforce integration:
1. **Contact Sync**: Brevo Contacts ↔ Salesforce Leads/Contacts
2. **Campaign Sync**: Brevo Campaigns → Salesforce Campaigns
3. **Activity Sync**: Email Opens/Clicks → Salesforce Activities

### Sync Rules

```
Brevo → Salesforce:
──────────────────
WHEN: Lead Score >= 80 OR SQL Event (contact request/booking)
WHAT: All contact data + engagement history
WHERE: As new Lead or update existing Lead

Salesforce → Brevo:
──────────────────
WHEN: Lead/Contact created or updated
WHAT: Status updates, owner assignment
WHERE: Update Brevo Contact (for segmentation)
```

### Sales Transparency

Sales sees in Salesforce:
- All downloads by the lead
- QuickCheck result
- Email engagement (which emails opened/clicked)
- Pages visited (via BigQuery/Brevo integration)
- Lead score history
- Traffic source (Google/Meta/LinkedIn/Organic)

---

# 10. Custom Analytics with BigQuery (Consent-Independent)

## 10.1 Why Custom Analytics?

### Core Principle: Own Data Processing = No Consent Required

> **Legal Basis**: BigQuery tracking runs as **own data processing** without transfer to third parties. Since data is processed exclusively internally, no separate consent is required. The BigQuery tag fires on **every request**, regardless of consent status.

**Difference from Ad Platforms:**
- Google Analytics, Meta, LinkedIn = Data goes to third parties → Consent needed
- BigQuery = Own Google Cloud, own processing → No consent needed

### Limitations of GA4

- **Data Storage**: Max. 14 months in free version
- **Sampling**: Data is extrapolated at high traffic
- **Data Sovereignty**: Data sits with Google (as processor)
- **Flexible Analysis**: Complex queries only possible to a limited extent
- **Cross-System**: No direct connection with CRM data
- **Consent-Dependent**: Only limited data without consent

### Benefits of BigQuery (Own Instance)

- **Unlimited Storage**: All events forever
- **No Sampling**: Raw data access
- **SQL Queries**: Full flexibility
- **Integration**: With Salesforce, Brevo, financial data
- **ML-Ready**: BigQuery ML for predictive analytics
- **Consent-Independent**: Every page visit, every event captured
- **Complete Journey**: Even for users who decline consent
- **Multi-Tenant**: One database for all agency clients

## 10.2 Multi-Tenant Architecture (Agency Setup)

### Why Multi-Tenant?

As an agency with multiple client projects, it's inefficient to build separate BigQuery infrastructure for each client. Instead:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    BRIXON ANALYTICS (BigQuery)                               │
│                    One Project, All Clients                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  Dataset: brixon_analytics                                                   │
│  ├── events          (all events from all properties)                       │
│  ├── users           (all users from all properties)                        │
│  ├── sessions        (all sessions from all properties)                     │
│  ├── leads           (identified leads with PII)                            │
│  ├── identity_graph  (user stitching connections)                           │
│  └── properties      (client/project master data)                           │
│                                                                              │
│  Filtering via: property_id                                                  │
│  ├── "drwerner"          → DrWerner.com                                     │
│  ├── "philippsauerborn"  → philippsauerborn.com                             │
│  ├── "client_xyz"        → Another agency client                            │
│  └── ...                                                                     │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Multi-Tenant Benefits

| Aspect | Separate DBs | Multi-Tenant |
|--------|--------------|--------------|
| **Setup Effort** | New for each client | Once, then just add property |
| **Cost** | Multiple base costs | Shared, only by usage |
| **Maintenance** | Separate per DB | Central for all |
| **Cross-Client Insights** | Not possible | Benchmarking possible |
| **Schema Updates** | Individually everywhere | Once for all |
| **Permissions** | Separate projects | Row-level security |

### Property Table (Client Master Data)

```sql
CREATE TABLE `brixon-analytics.analytics.properties` (
  -- Identification
  property_id STRING NOT NULL,          -- "drwerner", "client_xyz"
  property_name STRING NOT NULL,        -- "Dr. Werner & Partner"

  -- Domains
  primary_domain STRING,                -- drwerner.com
  additional_domains ARRAY<STRING>,     -- ["philippsauerborn.com"]
  stape_container_domain STRING,        -- s.drwerner.com

  -- Configuration
  gtm_web_container_id STRING,          -- GTM-XXXXXX
  gtm_server_container_id STRING,       -- GTM-YYYYYY
  ga4_measurement_id STRING,            -- G-XXXXXXX

  -- Business Info
  industry STRING,                      -- "tax_consulting", "saas", "ecommerce"
  business_model STRING,                -- "b2b", "b2c", "b2b2c"

  -- CRM Connections
  salesforce_org_id STRING,
  brevo_account_id STRING,
  hubspot_portal_id STRING,

  -- Status
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  PRIMARY KEY (property_id) NOT ENFORCED
);

-- Example entry
INSERT INTO `brixon-analytics.analytics.properties` VALUES (
  'drwerner',
  'Dr. Werner & Partner Tax Consulting',
  'drwerner.com',
  ['philippsauerborn.com', 'malta-company-formation.com'],
  's.drwerner.com',
  'GTM-XXXXX',
  'GTM-YYYYY',
  'G-ZZZZZ',
  'tax_consulting',
  'b2b',
  'org_salesforce_123',
  'brevo_456',
  NULL,
  TRUE,
  CURRENT_TIMESTAMP(),
  CURRENT_TIMESTAMP()
);
```

## 10.3 Stape User ID as Primary Identifier

### Why Stape User ID?

Stape provides a persistent `X-STAPE-USER-ID` that:
- Is set by the server (not the browser)
- Is ITP/ETP-resistant (HTTP header cookie)
- Persists up to 2 years
- Enables cross-session user identification

### Activation in Stape

```
Stape Dashboard → Container → Power-Ups
────────────────────────────────────────
☑ User ID Header (X-STAPE-USER-ID)
  → Automatically activates persistent User ID
  → Sent with every request in header
  → Available as variable in Server GTM
```

### User ID Hierarchy

```
User Identification Priority:
──────────────────────────────────
1. Email Hash (when user has identified)
   └── Strongest connection, cross-device possible

2. Stape User ID (X-STAPE-USER-ID)
   └── Primary anonymous identifier
   └── Persists across sessions
   └── Server-set, ITP-resistant

3. GA4 Client ID (_ga Cookie)
   └── Fallback when Stape ID not available
   └── Can be deleted by Safari after 7 days

4. Session ID
   └── Only for intra-session analysis
```

## 10.4 Data Model (Multi-Tenant & User Stitching)

### Event Table (with Property ID)

```sql
CREATE TABLE `brixon-analytics.analytics.events` (
  -- ═══════════════════════════════════════════════════
  -- MULTI-TENANT IDENTIFIER (REQUIRED!)
  -- ═══════════════════════════════════════════════════
  property_id STRING NOT NULL,        -- "drwerner", "client_xyz" - Required!

  -- Identification
  event_id STRING NOT NULL,           -- UUID for each event
  event_name STRING NOT NULL,         -- page_view, lead_magnet_download, etc.
  event_timestamp TIMESTAMP NOT NULL, -- Event timestamp

  -- User Identification (Hierarchy)
  stape_user_id STRING,               -- X-STAPE-USER-ID (primary!)
  ga4_client_id STRING,               -- GA4 _ga Cookie
  session_id STRING,                  -- Session ID
  user_email_hash STRING,             -- SHA256 when known

  -- Link to Lead (after identification)
  lead_id STRING,                     -- FK to leads table (after user stitching)

  -- User Properties (only for identified users)
  user_first_name STRING,
  user_last_name STRING,
  user_company STRING,

  -- ═══════════════════════════════════════════════════
  -- COMPLETE AD PLATFORM PARAMETERS
  -- ═══════════════════════════════════════════════════

  -- Traffic Source (General)
  traffic_source STRING,              -- google_ads, meta_ads, linkedin_ads, organic, direct, referral
  traffic_medium STRING,              -- cpc, cpm, organic, referral, email
  traffic_campaign STRING,            -- Campaign name

  -- UTM Parameters (complete)
  utm_source STRING,                  -- google, facebook, linkedin, newsletter
  utm_medium STRING,                  -- cpc, email, social
  utm_campaign STRING,                -- campaign_name
  utm_term STRING,                    -- keyword (for Search)
  utm_content STRING,                 -- ad_variant, cta_text

  -- Google Ads Parameters
  gclid STRING,                       -- Google Click ID
  gclsrc STRING,                      -- Google Click Source
  wbraid STRING,                      -- Web-to-App Attribution
  gbraid STRING,                      -- App-to-Web Attribution
  gad_source STRING,                  -- Google Ads Source
  google_campaign_id STRING,          -- Campaign ID
  google_adgroup_id STRING,           -- Ad Group ID
  google_ad_id STRING,                -- Ad ID
  google_keyword STRING,              -- Search term
  google_matchtype STRING,            -- exact, phrase, broad
  google_network STRING,              -- search, display, youtube
  google_placement STRING,            -- Placement (Display/YouTube)
  google_device STRING,               -- c (computer), m (mobile), t (tablet)
  google_location STRING,             -- Geo-Location ID

  -- Meta Ads Parameters
  fbclid STRING,                      -- Facebook Click ID
  fb_campaign_id STRING,              -- Meta Campaign ID
  fb_adset_id STRING,                 -- Meta Adset ID
  fb_ad_id STRING,                    -- Meta Ad ID
  fb_placement STRING,                -- feed, stories, reels, audience_network
  fb_source STRING,                   -- fb, ig, an, msg

  -- LinkedIn Ads Parameters
  li_fat_id STRING,                   -- LinkedIn First-Party Ad Tracking ID
  linkedin_campaign_id STRING,        -- LinkedIn Campaign ID
  linkedin_creative_id STRING,        -- LinkedIn Creative ID
  linkedin_campaign_group_id STRING,  -- LinkedIn Campaign Group ID

  -- ═══════════════════════════════════════════════════
  -- PAGE & SESSION DATA
  -- ═══════════════════════════════════════════════════

  -- Page Data
  page_url STRING,                    -- Full URL
  page_path STRING,                   -- Path only without domain
  page_title STRING,                  -- Page title
  page_referrer STRING,               -- Previous page
  page_hostname STRING,               -- drwerner.com or philippsauerborn.com

  -- Session Data
  session_number INTEGER,             -- Which visit number for user
  session_start BOOLEAN,              -- First event of session?
  session_engaged BOOLEAN,            -- Engaged session (>10s or conversion)
  page_views_in_session INTEGER,      -- Pageviews so far in session
  time_on_page_seconds INTEGER,       -- Time on previous page

  -- ═══════════════════════════════════════════════════
  -- EVENT-SPECIFIC PARAMETERS
  -- ═══════════════════════════════════════════════════

  -- For Lead Events
  lead_magnet_name STRING,            -- e.g., "Malta Checklist"
  lead_magnet_type STRING,            -- pdf, checklist, guide, video
  lead_magnet_topic STRING,           -- company_formation, emigration
  quickcheck_name STRING,             -- QuickCheck name
  quickcheck_result STRING,           -- Result category
  quickcheck_score INTEGER,           -- Numerical score
  form_name STRING,                   -- Form identifier
  form_destination STRING,            -- Where the request goes

  -- For E-Commerce (if relevant)
  service_interest STRING,            -- Which service is of interest
  estimated_value FLOAT64,            -- Estimated deal value

  -- Event Parameters (JSON for flexibility)
  event_params JSON,                  -- Additional parameters as JSON

  -- ═══════════════════════════════════════════════════
  -- CONSENT STATUS (for documentation)
  -- ═══════════════════════════════════════════════════

  consent_analytics STRING,           -- granted, denied, not_set
  consent_marketing STRING,           -- granted, denied, not_set
  consent_timestamp TIMESTAMP,        -- When consent was given/denied

  -- ═══════════════════════════════════════════════════
  -- GEO & DEVICE
  -- ═══════════════════════════════════════════════════

  -- Geo (from Stape Geo Headers)
  ip_address STRING,                  -- IP (or anonymized)
  country_code STRING,                -- DE, AT, CH, MT, etc.
  country_name STRING,                -- Germany, Austria, etc.
  region STRING,                      -- State/Canton
  city STRING,                        -- City
  postal_code STRING,                 -- Postal code

  -- Device & Browser
  device_category STRING,             -- desktop, mobile, tablet
  device_brand STRING,                -- Apple, Samsung, etc.
  device_model STRING,                -- iPhone 14, Galaxy S23, etc.
  browser_name STRING,                -- Chrome, Safari, Firefox, Edge
  browser_version STRING,             -- Major version
  os_name STRING,                     -- Windows, macOS, iOS, Android
  os_version STRING,                  -- 11, 14.5, etc.
  screen_resolution STRING,           -- 1920x1080
  viewport_size STRING,               -- 1200x800
  user_agent STRING,                  -- Full User Agent

  -- ═══════════════════════════════════════════════════
  -- METADATA
  -- ═══════════════════════════════════════════════════

  ingestion_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  data_source STRING DEFAULT 'stape_gtm',  -- stape_gtm, manual_import, etc.
  is_bot BOOLEAN DEFAULT FALSE,       -- Bot traffic marked
  is_internal BOOLEAN DEFAULT FALSE   -- Internal traffic marked
)
PARTITION BY DATE(event_timestamp)
CLUSTER BY property_id, stape_user_id, event_name;
```

### Lead Table (Identified Users with PII)

> **Important**: This table contains actual personally identifiable information (PII) and is only populated when a user has identified themselves (lead magnet download, contact form, etc.).

```sql
CREATE TABLE `brixon-analytics.analytics.leads` (
  -- ═══════════════════════════════════════════════════
  -- IDENTIFICATION
  -- ═══════════════════════════════════════════════════
  lead_id STRING NOT NULL,              -- UUID, Primary key
  property_id STRING NOT NULL,          -- Multi-Tenant: "drwerner", etc.

  -- Connection to anonymous tracking data
  stape_user_ids ARRAY<STRING>,         -- All known Stape User IDs for this lead
  ga4_client_ids ARRAY<STRING>,         -- All known GA4 Client IDs
  email_hash STRING,                    -- SHA256 for matching without PII access

  -- ═══════════════════════════════════════════════════
  -- PERSONALLY IDENTIFIABLE DATA (PII)
  -- ═══════════════════════════════════════════════════
  email STRING,                         -- Email address
  first_name STRING,
  last_name STRING,
  full_name STRING,                     -- If not captured separately
  phone STRING,
  company STRING,
  job_title STRING,
  website STRING,

  -- Address (if captured)
  street STRING,
  city STRING,
  postal_code STRING,
  country STRING,

  -- ═══════════════════════════════════════════════════
  -- LEAD QUALIFICATION
  -- ═══════════════════════════════════════════════════
  lead_status STRING,                   -- anonymous→known→mql→sql→customer→churned
  lead_score INTEGER DEFAULT 0,         -- Current score
  lead_grade STRING,                    -- A/B/C/D classification

  -- Status Timestamps
  known_at TIMESTAMP,                   -- First time identified
  mql_at TIMESTAMP,                     -- Marketing Qualified
  sql_at TIMESTAMP,                     -- Sales Qualified
  customer_at TIMESTAMP,                -- Became customer
  churned_at TIMESTAMP,                 -- Became inactive/lost

  -- Qualification Triggers
  mql_trigger STRING,                   -- "lead_magnet_download", "quickcheck_complete"
  sql_trigger STRING,                   -- "contact_form", "consultation_booking"

  -- ═══════════════════════════════════════════════════
  -- FIRST TOUCH ATTRIBUTION (copied at identification)
  -- ═══════════════════════════════════════════════════
  first_touch_timestamp TIMESTAMP,
  first_touch_source STRING,            -- google_ads, meta_ads, organic
  first_touch_medium STRING,            -- cpc, organic, referral
  first_touch_campaign STRING,
  first_touch_landing_page STRING,
  first_touch_gclid STRING,
  first_touch_fbclid STRING,
  first_touch_li_fat_id STRING,

  -- ═══════════════════════════════════════════════════
  -- CONVERSION ATTRIBUTION (at Lead Event)
  -- ═══════════════════════════════════════════════════
  conversion_timestamp TIMESTAMP,       -- When did lead become a lead?
  conversion_source STRING,
  conversion_medium STRING,
  conversion_campaign STRING,
  conversion_landing_page STRING,
  conversion_gclid STRING,
  conversion_fbclid STRING,

  -- ═══════════════════════════════════════════════════
  -- ENGAGEMENT DATA
  -- ═══════════════════════════════════════════════════
  lead_magnets_downloaded ARRAY<STRUCT<
    name STRING,
    downloaded_at TIMESTAMP,
    topic STRING
  >>,

  quickcheck_results ARRAY<STRUCT<
    name STRING,
    result STRING,
    score INTEGER,
    completed_at TIMESTAMP
  >>,

  forms_submitted ARRAY<STRUCT<
    form_name STRING,
    submitted_at TIMESTAMP,
    form_data JSON
  >>,

  -- Aggregated Metrics (updated via Scheduled Query)
  total_sessions INTEGER,
  total_pageviews INTEGER,
  total_time_on_site_seconds INTEGER,
  last_seen_at TIMESTAMP,
  days_since_last_visit INTEGER,

  -- Channels & Campaigns (Multi-Touch)
  channels_touched ARRAY<STRING>,       -- ["google_ads", "organic", "email"]
  campaigns_touched ARRAY<STRING>,

  -- ═══════════════════════════════════════════════════
  -- CRM CONNECTIONS
  -- ═══════════════════════════════════════════════════
  salesforce_lead_id STRING,
  salesforce_contact_id STRING,
  salesforce_account_id STRING,
  salesforce_opportunity_id STRING,
  brevo_contact_id STRING,
  hubspot_contact_id STRING,

  -- ═══════════════════════════════════════════════════
  -- BUSINESS VALUE
  -- ═══════════════════════════════════════════════════
  service_interest STRING,              -- Which service is of interest
  estimated_deal_value FLOAT64,
  actual_deal_value FLOAT64,
  customer_lifetime_value FLOAT64,

  -- ═══════════════════════════════════════════════════
  -- METADATA
  -- ═══════════════════════════════════════════════════
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  updated_at TIMESTAMP,
  data_source STRING,                   -- "gtm", "salesforce_import", "brevo_sync"
  is_test_lead BOOLEAN DEFAULT FALSE,
  gdpr_consent_given BOOLEAN,
  gdpr_consent_timestamp TIMESTAMP,
  marketing_consent BOOLEAN,

  PRIMARY KEY (lead_id) NOT ENFORCED
)
CLUSTER BY property_id, lead_status, email_hash;
```

### Identity Graph (User Stitching)

> This table connects anonymous identifiers with identified leads. Enables the complete customer journey to be attributed to a lead.

```sql
CREATE TABLE `brixon-analytics.analytics.identity_graph` (
  -- Connection
  property_id STRING NOT NULL,
  lead_id STRING NOT NULL,              -- FK to leads table
  identifier_type STRING NOT NULL,      -- "stape_user_id", "ga4_client_id", "email_hash"
  identifier_value STRING NOT NULL,     -- The actual identifier value

  -- Context
  first_seen_at TIMESTAMP,              -- When was this identifier first seen
  last_seen_at TIMESTAMP,               -- When last seen
  confidence_score FLOAT64,             -- 0-1, how certain is the assignment
  match_source STRING,                  -- "form_submit", "login", "email_click"

  -- Metadata
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),

  PRIMARY KEY (property_id, lead_id, identifier_type, identifier_value) NOT ENFORCED
)
CLUSTER BY property_id, identifier_type;

-- Index for fast lookup
-- When Stape User ID is known → Find associated Lead
```

### User Stitching Workflow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        USER STITCHING PROCESS                                │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  1. ANONYMOUS VISIT                                                          │
│     ─────────────────                                                        │
│     User visits website → Stape User ID is generated                        │
│     Events are stored in events table                                       │
│     lead_id = NULL (not yet identified)                                     │
│                                                                              │
│  2. IDENTIFICATION (Lead Magnet Download, Contact, etc.)                    │
│     ─────────────────────────────────────────────────────                   │
│     User enters email → lead_magnet_download Event                          │
│     │                                                                        │
│     ├── A) New Email: New Lead is created                                   │
│     │   → INSERT INTO leads (email, stape_user_ids, ...)                    │
│     │   → INSERT INTO identity_graph (stape_user_id → lead_id)              │
│     │                                                                        │
│     └── B) Known Email: Existing Lead                                       │
│         → Stape User ID added to stape_user_ids array                       │
│         → INSERT INTO identity_graph (new stape_user_id → lead_id)          │
│                                                                              │
│  3. RETROACTIVE CONNECTION                                                   │
│     ─────────────────────────                                                │
│     Scheduled Query updates all previous events:                            │
│     UPDATE events SET lead_id = [determined lead_id]                        │
│     WHERE stape_user_id IN (SELECT identifier_value FROM identity_graph)    │
│                                                                              │
│  4. CROSS-DEVICE STITCHING                                                  │
│     ───────────────────────                                                  │
│     User logs in on another device (same email)                             │
│     → New stape_user_id connected to existing Lead                          │
│     → Complete journey visible across all devices                           │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### User Stitching SQL (Scheduled Query)

```sql
-- This query runs every 15 minutes and connects events with leads

-- 1. Find all events with stape_user_id that can be assigned to a lead
MERGE `brixon-analytics.analytics.events` AS e
USING (
  SELECT DISTINCT
    ig.property_id,
    ig.identifier_value AS stape_user_id,
    ig.lead_id
  FROM `brixon-analytics.analytics.identity_graph` ig
  WHERE ig.identifier_type = 'stape_user_id'
) AS matches
ON e.property_id = matches.property_id
   AND e.stape_user_id = matches.stape_user_id
   AND e.lead_id IS NULL  -- Only events without lead assignment
WHEN MATCHED THEN
  UPDATE SET e.lead_id = matches.lead_id;

-- 2. Update lead metrics based on connected events
MERGE `brixon-analytics.analytics.leads` AS l
USING (
  SELECT
    property_id,
    lead_id,
    COUNT(DISTINCT session_id) AS total_sessions,
    COUNT(*) AS total_pageviews,
    MAX(event_timestamp) AS last_seen_at
  FROM `brixon-analytics.analytics.events`
  WHERE lead_id IS NOT NULL
  GROUP BY property_id, lead_id
) AS metrics
ON l.property_id = metrics.property_id AND l.lead_id = metrics.lead_id
WHEN MATCHED THEN
  UPDATE SET
    l.total_sessions = metrics.total_sessions,
    l.total_pageviews = metrics.total_pageviews,
    l.last_seen_at = metrics.last_seen_at,
    l.days_since_last_visit = DATE_DIFF(CURRENT_DATE(), DATE(metrics.last_seen_at), DAY),
    l.updated_at = CURRENT_TIMESTAMP();
```

### Sessions Table (with Property ID)

```sql
CREATE TABLE `brixon-analytics.analytics.sessions` (
  property_id STRING NOT NULL,          -- Multi-Tenant
  session_id STRING NOT NULL,
  stape_user_id STRING NOT NULL,
  lead_id STRING,                       -- FK to leads (after stitching)

  -- Session Timing
  session_start TIMESTAMP,
  session_end TIMESTAMP,
  session_duration_seconds INTEGER,

  -- Engagement
  pageviews INTEGER,
  events_count INTEGER,
  engaged_session BOOLEAN,

  -- Traffic Source (this session)
  source STRING,
  medium STRING,
  campaign STRING,
  gclid STRING,
  fbclid STRING,
  li_fat_id STRING,

  -- Entry & Exit
  landing_page STRING,
  exit_page STRING,

  -- Conversion in Session
  converted BOOLEAN,
  conversion_event STRING,              -- lead_magnet_download, contact_form, etc.

  -- Device/Geo (this session)
  device_category STRING,
  country STRING,
  city STRING,

  -- Metadata
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
)
PARTITION BY DATE(session_start)
CLUSTER BY property_id, stape_user_id;
```

### Users Table (Anonymous User Profiles)

> **Note**: This table contains aggregated data for anonymous users. Once a user is identified, data is transferred to the leads table.

```sql
CREATE TABLE `brixon-analytics.analytics.users` (
  -- Primary Identification
  property_id STRING NOT NULL,
  stape_user_id STRING NOT NULL,        -- Primary key
  ga4_client_ids ARRAY<STRING>,         -- All known GA4 Client IDs
  email_hash STRING,                    -- SHA256 of email (when known)

  -- Lead Connection
  lead_id STRING,                       -- FK to leads (after identification)
  is_identified BOOLEAN DEFAULT FALSE,

  -- First Touch Attribution
  first_touch_date DATE,
  first_touch_timestamp TIMESTAMP,
  first_touch_source STRING,            -- google_ads, meta_ads, organic
  first_touch_medium STRING,
  first_touch_campaign STRING,
  first_touch_landing_page STRING,
  first_touch_gclid STRING,
  first_touch_fbclid STRING,
  first_touch_li_fat_id STRING,

  -- Last Touch Attribution
  last_touch_date DATE,
  last_touch_source STRING,
  last_touch_medium STRING,
  last_touch_campaign STRING,
  last_touch_landing_page STRING,
  last_touch_gclid STRING,
  last_touch_fbclid STRING,
  last_touch_li_fat_id STRING,

  -- Lead Status & Journey
  lead_status STRING,                 -- anonymous, known, mql, sql, customer
  known_date DATE,                    -- When user was identified
  mql_date DATE,                      -- Marketing Qualified Lead date
  mql_trigger STRING,                 -- What triggered MQL
  sql_date DATE,                      -- Sales Qualified Lead date
  sql_trigger STRING,                 -- What triggered SQL
  customer_date DATE,                 -- When user became customer

  -- Engagement Metrics
  total_sessions INTEGER,
  total_pageviews INTEGER,
  total_time_on_site_seconds INTEGER,
  pages_per_session_avg FLOAT64,

  -- Content Engagement
  lead_magnets_downloaded ARRAY<STRING>,
  quickcheck_results ARRAY<STRUCT<name STRING, result STRING, score INTEGER>>,
  forms_submitted ARRAY<STRING>,

  -- Channel Exposure (Multi-Touch)
  channels_touched ARRAY<STRING>,     -- ["google_ads", "organic", "linkedin_ads"]
  campaigns_touched ARRAY<STRING>,    -- All campaigns user had contact with

  -- CRM Connection
  brevo_contact_id STRING,
  salesforce_lead_id STRING,
  salesforce_contact_id STRING,
  salesforce_account_id STRING,

  -- Timestamps
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  last_seen_at TIMESTAMP
)
CLUSTER BY lead_status, first_touch_source;
```

## 10.4 Server-Side → BigQuery Pipeline

### Trigger: ALL Events (Consent-Independent!)

```
GTM Server Container - BigQuery Tag Trigger:
────────────────────────────────────────────
Trigger Name:    All GA4 Events - BigQuery
Trigger Type:    Custom Event
Event Name:      .*  (Regex: All Events)

IMPORTANT: No Consent Check!
→ Tag fires on EVERY event
→ Regardless of consent status
→ Own data processing = No consent needed
```

### GTM Server Tag: BigQuery HTTP Request (Complete)

```
Tag Configuration:
──────────────────
Tag Name:     BigQuery - All Events
Tag Type:     HTTP Request

Endpoint:     https://bigquery.googleapis.com/bigquery/v2/projects/{PROJECT_ID}/datasets/analytics/tables/events/insertAll
Method:       POST

Headers:
  Authorization: Bearer {{BigQuery Service Account Token}}
  Content-Type: application/json

Body Template (JSON):
{
  "rows": [{
    "insertId": "{{Event ID}}",
    "json": {
      "property_id": "{{Property ID}}",

      "event_id": "{{Event ID}}",
      "event_name": "{{Event Name}}",
      "event_timestamp": "{{Timestamp ISO}}",

      "stape_user_id": "{{X-Stape-User-Id}}",
      "ga4_client_id": "{{Client ID}}",
      "session_id": "{{Session ID}}",
      "user_email_hash": "{{Hashed Email}}",

      "traffic_source": "{{Traffic Source}}",
      "traffic_medium": "{{Traffic Medium}}",
      "traffic_campaign": "{{Traffic Campaign}}",

      "utm_source": "{{UTM Source}}",
      "utm_medium": "{{UTM Medium}}",
      "utm_campaign": "{{UTM Campaign}}",
      "utm_term": "{{UTM Term}}",
      "utm_content": "{{UTM Content}}",

      "gclid": "{{GCLID}}",
      "wbraid": "{{WBRAID}}",
      "gbraid": "{{GBRAID}}",
      "google_campaign_id": "{{Google Campaign ID}}",
      "google_adgroup_id": "{{Google Adgroup ID}}",

      "fbclid": "{{FBCLID}}",
      "fb_campaign_id": "{{FB Campaign ID}}",
      "fb_adset_id": "{{FB Adset ID}}",

      "li_fat_id": "{{LinkedIn FAT ID}}",
      "linkedin_campaign_id": "{{LinkedIn Campaign ID}}",

      "page_url": "{{Page URL}}",
      "page_path": "{{Page Path}}",
      "page_title": "{{Page Title}}",
      "page_referrer": "{{Page Referrer}}",
      "page_hostname": "{{Page Hostname}}",

      "lead_magnet_name": "{{Lead Magnet Name}}",
      "quickcheck_result": "{{QuickCheck Result}}",
      "form_name": "{{Form Name}}",

      "consent_analytics": "{{Consent Analytics Status}}",
      "consent_marketing": "{{Consent Marketing Status}}",

      "country_code": "{{Geo Country}}",
      "city": "{{Geo City}}",
      "device_category": "{{Device Category}}",
      "browser_name": "{{Browser Name}}",
      "os_name": "{{OS Name}}",
      "user_agent": "{{User Agent}}",

      "is_bot": {{Is Bot}},
      "is_internal": {{Is Internal Traffic}}
    }
  }]
}

Trigger: All GA4 Events - BigQuery (WITHOUT Consent Condition!)
```

### Variable: X-Stape-User-Id

```
GTM Server Container - Variable:
────────────────────────────────
Variable Name:    X-Stape-User-Id
Variable Type:    Request Header
Header Name:      x-stape-user-id

Fallback:         {{Client ID}}
```

### Variable: Property ID (Multi-Tenant)

```
GTM Server Container - Variable:
────────────────────────────────
Variable Name:    Property ID
Variable Type:    Lookup Table

Input Variable: {{Page Hostname}}

Lookup Table:
┌─────────────────────────────┬──────────────────────┐
│ Hostname                    │ Property ID          │
├─────────────────────────────┼──────────────────────┤
│ drwerner.com                │ drwerner             │
│ www.drwerner.com            │ drwerner             │
│ philippsauerborn.com        │ drwerner             │  ← Same Property
│ www.philippsauerborn.com    │ drwerner             │
│ client-xyz.com              │ client_xyz           │  ← Different client
│ www.client-xyz.com          │ client_xyz           │
└─────────────────────────────┴──────────────────────┘

Default Value (if no match): unknown_property

NOTE: For each new client project, an entry must be
added here. The Property ID value must match the
entry in the BigQuery properties table.
```

## 10.5 What Gets Tracked? (Event Overview)

### Automatic on Every Request

| Event | Description | Trigger |
|-------|-------------|---------|
| `page_view` | Every page load | Automatic |
| `session_start` | New session started | Automatic |
| `first_visit` | First visit | Automatic |
| `user_engagement` | Active time on page | Automatic (>10s) |

### Explicitly Tracked Events

| Event | Description | DataLayer Trigger |
|-------|-------------|-------------------|
| `cta_click` | CTA button clicked | Click on .cta-button |
| `service_page_view` | Service page viewed | /services/* pages |
| `lead_magnet_view` | Lead magnet page | /downloads/* pages |
| `lead_magnet_download` | PDF downloaded | Vavolta Success |
| `quickcheck_start` | QuickCheck started | QuickCheck Form |
| `quickcheck_complete` | QuickCheck completed | QuickCheck Submit |
| `newsletter_signup` | Newsletter subscription | Newsletter Form |
| `contact_form_start` | Contact form started | Form Focus |
| `contact_form_submit` | Contact form submitted | Form Submit |
| `callback_request` | Callback requested | Callback Form |
| `consultation_booking` | Booking made | Calendly/Booking Success |

### NOT Tracked (Performance)

- Scroll events (too many data points)
- Mouse movements
- Hover events
- Micro-interactions

## 10.6 Customer Journey Analysis

### Example Queries

> **Important**: All queries contain `property_id` filter for multi-tenant isolation.

#### Complete Journey of a User (via Stape User ID)

```sql
-- Shows all events for a specific user for DrWerner
SELECT
  e.event_timestamp,
  e.event_name,
  e.page_path,
  e.traffic_source,
  e.traffic_campaign,
  e.lead_magnet_name,
  e.quickcheck_result,
  e.consent_marketing
FROM `brixon-analytics.analytics.events` e
WHERE e.property_id = 'drwerner'                -- Multi-Tenant Filter!
  AND e.stape_user_id = 'xyz123...'
ORDER BY e.event_timestamp;
```

#### Journey of an Identified Lead (via Lead ID)

```sql
-- After User Stitching: Shows ALL events for a lead (including before identification)
SELECT
  e.event_timestamp,
  e.event_name,
  e.page_path,
  e.traffic_source,
  e.traffic_campaign,
  l.first_name,
  l.email,
  l.lead_status
FROM `brixon-analytics.analytics.events` e
JOIN `brixon-analytics.analytics.leads` l
  ON e.property_id = l.property_id AND e.lead_id = l.lead_id
WHERE e.property_id = 'drwerner'
  AND l.email = 'john.smith@example.com'
ORDER BY e.event_timestamp;
```

#### Multi-Touch Attribution Report

```sql
-- All touchpoints before conversion (for DrWerner)
WITH conversions AS (
  SELECT
    property_id,
    stape_user_id,
    MIN(event_timestamp) as conversion_time
  FROM `brixon-analytics.analytics.events`
  WHERE property_id = 'drwerner'                -- Multi-Tenant Filter!
    AND event_name IN ('lead_magnet_download', 'contact_form_submit')
  GROUP BY property_id, stape_user_id
),
touchpoints AS (
  SELECT
    e.stape_user_id,
    e.event_timestamp,
    e.traffic_source,
    e.traffic_campaign,
    e.gclid,
    e.fbclid,
    c.conversion_time
  FROM `brixon-analytics.analytics.events` e
  JOIN conversions c
    ON e.property_id = c.property_id
    AND e.stape_user_id = c.stape_user_id
  WHERE e.property_id = 'drwerner'
    AND e.event_timestamp <= c.conversion_time
    AND e.event_name = 'page_view'
    AND e.traffic_source IS NOT NULL
)
SELECT
  traffic_source,
  COUNT(DISTINCT stape_user_id) as users_touched,
  COUNT(*) as total_touchpoints
FROM touchpoints
GROUP BY traffic_source
ORDER BY users_touched DESC;
```

#### Consent-Independent Analysis

```sql
-- How many users denied consent but still converted?
SELECT
  consent_marketing,
  COUNT(DISTINCT stape_user_id) as unique_users,
  COUNTIF(event_name = 'lead_magnet_download') as lead_downloads,
  COUNTIF(event_name = 'contact_form_submit') as contact_forms
FROM `brixon-analytics.analytics.events`
WHERE property_id = 'drwerner'                  -- Multi-Tenant Filter!
GROUP BY consent_marketing;

-- Result shows: Even "denied" users are fully tracked!
```

#### Lead Quality by Channel

```sql
-- Which channel brings the best leads? (MQL → SQL Conversion Rate)
SELECT
  l.first_touch_source AS channel,
  COUNT(*) AS total_leads,
  COUNTIF(l.lead_status IN ('sql', 'customer')) AS sqls,
  ROUND(COUNTIF(l.lead_status IN ('sql', 'customer')) / COUNT(*) * 100, 1) AS sql_rate_pct,
  COUNTIF(l.lead_status = 'customer') AS customers,
  ROUND(AVG(l.actual_deal_value), 0) AS avg_deal_value
FROM `brixon-analytics.analytics.leads` l
WHERE l.property_id = 'drwerner'                -- Multi-Tenant Filter!
  AND l.lead_status NOT IN ('anonymous')
GROUP BY l.first_touch_source
ORDER BY sqls DESC;
```

#### Time-to-Conversion by Channel

```sql
WITH first_touch AS (
  SELECT
    property_id,
    stape_user_id,
    MIN(event_timestamp) as first_visit,
    ARRAY_AGG(traffic_source ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] as first_source
  FROM `brixon-analytics.analytics.events`
  WHERE property_id = 'drwerner'
    AND event_name = 'page_view'
  GROUP BY property_id, stape_user_id
),
conversions AS (
  SELECT
    property_id,
    stape_user_id,
    MIN(event_timestamp) as conversion_time
  FROM `brixon-analytics.analytics.events`
  WHERE property_id = 'drwerner'
    AND event_name = 'lead_magnet_download'
  GROUP BY property_id, stape_user_id
)
SELECT
  ft.first_source,
  AVG(TIMESTAMP_DIFF(c.conversion_time, ft.first_visit, HOUR)) as avg_hours_to_convert,
  COUNT(*) as conversions
FROM first_touch ft
JOIN conversions c
  ON ft.property_id = c.property_id
  AND ft.stape_user_id = c.stape_user_id
GROUP BY ft.first_source
ORDER BY conversions DESC;
```

#### Cross-Property Benchmark (Agency Overview)

```sql
-- Comparison of all client properties (for agency admins only)
SELECT
  p.property_name,
  p.industry,
  COUNT(DISTINCT e.stape_user_id) AS unique_visitors,
  COUNT(DISTINCT l.lead_id) AS total_leads,
  COUNTIF(l.lead_status = 'mql') AS mqls,
  COUNTIF(l.lead_status = 'sql') AS sqls,
  ROUND(COUNTIF(l.lead_status = 'sql') / NULLIF(COUNTIF(l.lead_status = 'mql'), 0) * 100, 1) AS mql_to_sql_rate
FROM `brixon-analytics.analytics.properties` p
LEFT JOIN `brixon-analytics.analytics.events` e ON p.property_id = e.property_id
LEFT JOIN `brixon-analytics.analytics.leads` l ON p.property_id = l.property_id
WHERE p.is_active = TRUE
  AND e.event_timestamp >= TIMESTAMP_SUB(CURRENT_TIMESTAMP(), INTERVAL 30 DAY)
GROUP BY p.property_name, p.industry
ORDER BY total_leads DESC;
```

---

# 11. Cross-Domain Tracking

## 11.1 Problem Statement

DrWerner.com and philippsauerborn.com are separate domains. Without special configuration:
- User visits philippsauerborn.com → Client ID "A"
- User switches to drwerner.com → New Client ID "B"
- **Result**: One person, two profiles, journey interrupted

## 11.2 Recommended Solution

### Option A: Unified Tracking Infrastructure (Recommended)

```
Both domains use:
├── Same GA4 Property
├── Same GTM Web Container
├── Same Server Container (s.drwerner.com)
└── Cross-Domain Linking activated
```

**Setup:**
1. **GA4 Cross-Domain**: In GA4 Admin → Data Streams → Configure tag settings → Configure your domains
2. **GTM Linker**: Automatic Link Domain Configuration
3. **Server Endpoint**: Both domains send to s.drwerner.com

**For philippsauerborn.com:**
- No separate Stape container needed
- Sends to s.drwerner.com (technically works)
- Cookie domain different, but Client ID passed via URL parameter

### URL Parameter on Domain Switch

When user clicks from philippsauerborn.com → drwerner.com:
```
https://drwerner.com/services?_gl=1*abc123*_ga*MTIzNDU2...
```

The `_gl` parameter contains the encrypted Client ID, which GA4 reads on the target domain.

## 11.3 Unified User ID

Once a user identifies themselves (lead magnet, newsletter), a persistent User ID can be set:

```javascript
// After Lead Magnet Download
gtag('set', 'user_id', 'user_12345');
```

This User ID:
- Is sent to server container
- Stored in BigQuery
- Links all Client IDs to one user profile

---

# 12. Cookie Keeper & First-Party Data

## 12.1 Cookie Lifespan Problem

### Browser Restrictions 2024/2025

| Browser | Third-Party Cookies | First-Party Cookies | ITP/ETP |
|---------|--------------------|--------------------|---------|
| Safari | Blocked | 7 days (JS-set) | ITP active |
| Firefox | Blocked (default) | 7 days (tracking) | ETP active |
| Chrome | 2025 sunset | Unlimited | Privacy Sandbox |
| Edge | Follows Chrome | Unlimited | - |

**Problem**: A user who was here 8 days ago with Safari is counted as new.

## 12.2 Stape Cookie Keeper Solution

### How It Works

```
Without Cookie Keeper:
───────────────────
Browser sets cookie via JavaScript
→ Safari deletes after 7 days
→ User becomes "new"

With Cookie Keeper:
──────────────────
1. Browser sends request to s.drwerner.com
2. Stape server sets cookie via HTTP Response Header
3. HTTP Header Cookies = First-Party, server-set
4. Safari ITP does NOT apply (no JavaScript cookies)
5. Cookie lives up to 2 years
```

### Technical Details

Cookie Keeper sets/renews with every request:
- `_ga` / `_ga_XXXXX` (GA4)
- `_gcl_au` (Google Ads)
- `_fbc` / `_fbp` (Meta)
- Custom Cookies (configurable)

### Activation in Stape

```
Stape Dashboard → Container → Power-Ups → Cookie Keeper
────────────────────────────────────────────────────────
Standard Cookies:
☑ Google Analytics (_ga, _ga_session)
☑ Google Ads (_gcl_au, _gcl_aw)
☑ Meta (_fbc, _fbp)
☑ LinkedIn
☐ TikTok (not relevant)

Custom Cookies:
+ Name: user_id
  Type: Persistent
  TTL: 730 days (2 years)
```

## 12.3 First-Party Data Strategy

### Collecting First-Party Data

With consent, the following data can be collected:

| Data Type | Collection | Use |
|-----------|------------|-----|
| Email | Lead Magnet Form | Identification, Nurturing |
| Name | Lead Magnet Form | Personalization |
| Behavioral Data | Pageviews, Clicks | Segmentation, Scoring |
| Preferences | QuickCheck Answers | Personalized Journey |
| Traffic Source | UTM Parameters | Attribution |

### Ensuring Data Quality

1. **Email Validation**: Only accept valid emails
2. **Deduplication**: User ID management in BigQuery
3. **Consent Tracking**: Document when/how consent was given
4. **Data Hygiene**: Regular cleanup of inactive contacts

---

# 13. Data Protection & Compliance (Transparency First)

> **Note**: GDPR compliance is deferred to Phase 1. Focus is on complete data capture for maximum transparency. Cookie banner remains active for Google Consent Mode.

## 13.1 Current Approach

### Prioritization

| Priority | Aspect | Status |
|----------|--------|--------|
| **1. High** | Complete Tracking | ✓ Implement |
| **1. High** | Consent Mode v2 for Google | ✓ Banner with default "granted" |
| **2. Medium** | PII Hashing | ✓ For ad platforms (Enhanced Conversions) |
| **3. Later** | GDPR Documentation | Deferred |
| **3. Later** | Granular Consent Options | Deferred |

### What Remains Active

1. **Cookie Banner**: Remains for Google Consent Mode v2 (technically required)
2. **Default Consent = Granted**: All tags fire immediately
3. **SHA-256 Hashing**: Email/phone sent hashed to Google/Meta/LinkedIn
4. **DPAs**: Data Processing Agreements with Stape, Brevo, Google Cloud should be in place

## 13.2 Technical Measures (Active)

### PII Hashing

Emails and phone numbers are hashed for Enhanced Conversions:

```javascript
// SHA-256 Hashing (server-side in GTM)
// Required for Google Enhanced Conversions and Meta CAPI

function hashPII(value) {
  return crypto
    .createHash('sha256')
    .update(value.trim().toLowerCase())
    .digest('hex');
}

// Example
hashPII('john.smith@example.com');
// → "a1b2c3d4e5f6..."
```

**Why Hashing Matters:**
- Google and Meta only accept hashed data
- Matching with logged-in users still works
- Raw data stays only in BigQuery (own control)

### Retention Periods

| Data Type | Retention | Justification |
|-----------|-----------|---------------|
| Anonymous Analytics | 26 months | GA4 Standard |
| Cookies (with consent) | 2 years | Cookie Keeper |
| BigQuery Events | 5 years | Business analysis |
| Brevo Contacts | Until withdrawal | Nurturing |
| Salesforce Leads | 10 years | Tax law |

---

# 14. Implementation Roadmap

## Phase 1: Foundation (Weeks 1-3)

### Goals
- Server-side tracking functional
- Consent Mode v2 correctly implemented
- Track basic events

### Tasks

| Task | Responsibility | Status |
|------|----------------|--------|
| Activate Stape Cookie Keeper | Tracking | ⬜ |
| Revise GTM Web Container | Tracking | ⬜ |
| Switch GA4 Configuration to Server | Tracking | ⬜ |
| Check Cookiebot Consent Mode v2 | Tracking | ⬜ |
| Define basic events | Tracking + Marketing | ⬜ |
| DataLayer documentation | Tracking | ⬜ |

### Deliverables
- [ ] Server-Side GA4 Tracking live
- [ ] Consent flow tested
- [ ] Pageviews + Basic Events working

## Phase 2: Lead Tracking (Weeks 4-6)

### Goals
- Vavolta integrated
- Track lead magnet downloads
- Capture newsletter signups

### Tasks

| Task | Responsibility | Status |
|------|----------------|--------|
| Vavolta Account & Setup | Marketing | ⬜ |
| Vavolta ↔ GTM Integration | Tracking | ⬜ |
| Implement lead magnet events | Tracking | ⬜ |
| Newsletter signup tracking | Tracking | ⬜ |
| QuickCheck event tracking | Tracking | ⬜ |
| Contact form events | Tracking | ⬜ |

### Deliverables
- [ ] All lead events visible in GA4
- [ ] Event parameters complete
- [ ] Vavolta working

## Phase 3: Ad Platforms (Weeks 7-9)

### Goals
- Google Ads Enhanced Conversions
- Meta CAPI
- LinkedIn CAPI

### Tasks

| Task | Responsibility | Status |
|------|----------------|--------|
| Google Ads Server Tag Setup | Tracking | ⬜ |
| Configure Enhanced Conversions | Tracking | ⬜ |
| Meta CAPI Access Token | Marketing | ⬜ |
| Meta CAPI Server Tag | Tracking | ⬜ |
| LinkedIn CAPI Setup | Tracking | ⬜ |
| Document conversion mapping | Tracking | ⬜ |
| Testing & Validation | Tracking + Marketing | ⬜ |

### Deliverables
- [ ] Conversions in all ad platforms
- [ ] Deduplication working
- [ ] Attribution correct

## Phase 4: Marketing Automation (Weeks 10-13)

### Goals
- Brevo setup
- Nurturing workflows
- Salesforce extension

### Tasks

| Task | Responsibility | Status |
|------|----------------|--------|
| Brevo Account Setup | Marketing | ⬜ |
| Brevo ↔ GTM Server Integration | Tracking | ⬜ |
| Define lead scoring rules | Marketing | ⬜ |
| Nurturing Workflow 1: Lead Magnet | Marketing | ⬜ |
| Nurturing Workflow 2: QuickCheck | Marketing | ⬜ |
| Extend Salesforce fields | CRM Admin | ⬜ |
| Brevo ↔ Salesforce Sync | CRM Admin | ⬜ |

### Deliverables
- [ ] Brevo receiving events
- [ ] Automated email workflows
- [ ] MQLs visible in Salesforce

## Phase 5: Custom Analytics (Weeks 14-17)

### Goals
- BigQuery setup
- Customer journey tracking
- Reporting dashboard

### Tasks

| Task | Responsibility | Status |
|------|----------------|--------|
| Create BigQuery project | Tech | ⬜ |
| Set up table schema | Tracking | ⬜ |
| Service Account for GTM | Tech | ⬜ |
| BigQuery HTTP Tag | Tracking | ⬜ |
| User stitching logic | Tracking | ⬜ |
| Create basic reports | Tracking | ⬜ |
| Looker Studio Dashboard | Tracking | ⬜ |

### Deliverables
- [ ] Events flowing to BigQuery
- [ ] Customer journey queryable
- [ ] Dashboard for Marketing/Sales

## Phase 6: Cross-Domain & Optimization (Weeks 18-20)

### Goals
- philippsauerborn.com integrated
- Cross-domain tracking
- Fine-tuning

### Tasks

| Task | Responsibility | Status |
|------|----------------|--------|
| philippsauerborn.com GTM Setup | Tracking | ⬜ |
| Cross-Domain Linking | Tracking | ⬜ |
| User ID Matching | Tracking | ⬜ |
| Performance Optimization | Tracking | ⬜ |
| Finalize documentation | Tracking | ⬜ |
| Team Training | Tracking | ⬜ |

### Deliverables
- [ ] Both domains tracked
- [ ] Cross-domain journey working
- [ ] Team can use system

---

# 15. Technical Checklists

## 15.1 GTM Web Container Checklist

- [ ] Consent Initialization Trigger present
- [ ] Cookiebot Tag loads first
- [ ] GA4 Configuration Tag → Server Endpoint
- [ ] DataLayer Variables for all events
- [ ] Consent State Variables configured
- [ ] Debug Mode tested
- [ ] Container published

## 15.2 GTM Server Container Checklist

- [ ] GA4 Client active
- [ ] Cookie Keeper Power-Up activated
- [ ] GA4 Server Tag → Property linked
- [ ] Google Ads Tag → Conversion ID correct
- [ ] Meta CAPI Tag → Access Token valid
- [ ] LinkedIn CAPI Tag → Account ID correct
- [ ] Brevo HTTP Tag → API Key configured
- [ ] BigQuery Tag → Service Account active
- [ ] Consent-based triggers
- [ ] Error logging activated

## 15.3 Consent Checklist

- [ ] Banner appears before tracking
- [ ] All cookie categories correctly assigned
- [ ] Consent Mode v2 signals being sent
- [ ] Server receives consent parameters
- [ ] Tags respect consent
- [ ] Withdrawal works
- [ ] Privacy policy updated

## 15.4 Testing Checklist

- [ ] Anonymous user: Basic tracking with consent only
- [ ] Lead Magnet Download: Event + User Data
- [ ] Contact form: SQL event in Salesforce
- [ ] Cross-Domain: User ID retained
- [ ] Ad platforms: Conversions appear
- [ ] BigQuery: Events streaming
- [ ] Brevo: Contacts being created

---

# Appendix: Glossary

| Term | Explanation |
|------|-------------|
| **Server-Side Tracking** | Tracking data sent from own server (not browser) to platforms |
| **CAPI** | Conversions API - Server-to-server interface for ad platforms |
| **First-Party Cookie** | Cookie set by the visited domain |
| **Third-Party Cookie** | Cookie from a foreign domain (e.g., facebook.com on drwerner.com) |
| **ITP** | Intelligent Tracking Prevention - Apple's cookie blocking in Safari |
| **Consent Mode v2** | Google's framework for transmitting consent signals |
| **MQL** | Marketing Qualified Lead - Lead with engagement signal |
| **SQL** | Sales Qualified Lead - Lead with concrete interest/inquiry |
| **DataLayer** | JavaScript object for structured event data |
| **Enhanced Conversions** | Google Ads feature for better attribution via hashed user data |
| **Cookie Keeper** | Stape feature for extending cookie lifespans |
| **Lead Scoring** | Points-based lead evaluation based on engagement |
| **Attribution** | Assignment of conversions to marketing touchpoints |

---

*This concept was created based on the current state of technology (December 2024) and the specific requirements of DrWerner.com.*
