# Developer Briefing: Custom Analytics System

## Technisches Entwicklerbriefing für BigQuery Analytics Pipeline

**Projekt:** DrWerner.com Custom Analytics
**Erstellungsdatum:** Dezember 2024
**Version:** 1.0
**Referenz:** Tracking-Konzept-DrWerner-DE.md (Abschnitt 10)

---

# Inhaltsverzeichnis

1. [Projektübersicht](#1-projektübersicht)
2. [Architektur-Übersicht](#2-architektur-übersicht)
3. [Datenbank-Schema](#3-datenbank-schema)
4. [API-Endpunkte](#4-api-endpunkte)
5. [Cloud Functions](#5-cloud-functions)
6. [Scheduled Queries](#6-scheduled-queries)
7. [Integrationen](#7-integrationen)
8. [Looker Studio Connector](#8-looker-studio-connector)
9. [Deployment & DevOps](#9-deployment--devops)
10. [Deliverables & Timeline](#10-deliverables--timeline)

---

# 1. Projektübersicht

## 1.1 Ziel

Entwicklung eines consent-unabhängigen Custom Analytics Systems basierend auf BigQuery, das:
- Vollständige Customer Journey erfasst (unabhängig vom Consent-Status)
- User Stitching über Devices und Sessions hinweg ermöglicht
- Lead Scoring automatisiert berechnet
- Attribution über alle Marketing-Kanäle bietet
- Multi-Tenant-fähig für Agentur-Betrieb ist

## 1.2 Kernanforderungen

| Anforderung | Beschreibung |
|-------------|--------------|
| **Consent-Unabhängigkeit** | Daten werden als eigene Verarbeitung ohne Drittanbieterweitergabe gespeichert |
| **Multi-Tenant** | Ein BigQuery-Projekt für alle Kunden, Filterung via `property_id` |
| **User Stitching** | Verknüpfung anonymer User mit identifizierten Leads |
| **Lead Scoring** | Automatisierte Score-Berechnung basierend auf Engagement |
| **Attribution** | First-Touch, Last-Touch und Multi-Touch Attribution |

## 1.3 Tech Stack

```
┌─────────────────────────────────────────────────────────────┐
│                     TECH STACK                               │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Daten-Erfassung:                                            │
│  ├── Stape Server Container (GTM Server-Side)               │
│  ├── BigQuery HTTP Request Tag                              │
│  └── Webhooks (Brevo, Salesforce)                           │
│                                                              │
│  Datenbank:                                                  │
│  └── Google BigQuery (GCP Projekt: brixon-analytics)        │
│                                                              │
│  Verarbeitung:                                               │
│  ├── BigQuery Scheduled Queries                             │
│  ├── Cloud Functions (Node.js/Python)                       │
│  └── Cloud Pub/Sub (Event-Streaming)                        │
│                                                              │
│  APIs:                                                       │
│  ├── Cloud Run (REST API Endpoints)                         │
│  └── Cloud Functions (Webhook Handler)                      │
│                                                              │
│  Reporting:                                                  │
│  ├── Looker Studio (Dashboards)                             │
│  └── BigQuery Views (Aggregierte Daten)                     │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

# 2. Architektur-Übersicht

## 2.1 Datenfluss-Diagramm

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          DATENFLUSS ARCHITEKTUR                              │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│                           ┌─────────────────────┐                            │
│                           │   Website Traffic   │                            │
│                           │  (drwerner.com)     │                            │
│                           │  (philippsauerborn) │                            │
│                           └──────────┬──────────┘                            │
│                                      │                                       │
│                                      ▼                                       │
│                           ┌─────────────────────┐                            │
│                           │  Stape Server GTM   │                            │
│                           │  s.drwerner.com     │                            │
│                           └──────────┬──────────┘                            │
│                                      │                                       │
│              ┌───────────────────────┼───────────────────────┐               │
│              │                       │                       │               │
│              ▼                       ▼                       ▼               │
│    ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐        │
│    │   GA4 Server    │    │  Ad Platforms   │    │  BigQuery HTTP  │        │
│    │     Tag         │    │  (Ads, Meta,    │    │     Tag         │        │
│    │                 │    │   LinkedIn)     │    │                 │        │
│    └────────┬────────┘    └────────┬────────┘    └────────┬────────┘        │
│             │                      │                      │                  │
│             │                      │                      ▼                  │
│             │                      │           ┌─────────────────────┐       │
│             │                      │           │    BigQuery         │       │
│             │                      │           │    events Table     │       │
│             │                      │           │    (Raw Events)     │       │
│             │                      │           └──────────┬──────────┘       │
│             │                      │                      │                  │
│             │                      │                      ▼                  │
│             │                      │           ┌─────────────────────┐       │
│             │                      │           │  Scheduled Queries  │       │
│             │                      │           │  (15min Intervall)  │       │
│             │                      │           └──────────┬──────────┘       │
│             │                      │                      │                  │
│             │                      │         ┌────────────┼────────────┐     │
│             │                      │         │            │            │     │
│             │                      │         ▼            ▼            ▼     │
│             │                      │    ┌────────┐  ┌────────┐  ┌────────┐  │
│             │                      │    │sessions│  │ users  │  │identity│  │
│             │                      │    │ Table  │  │ Table  │  │ _graph │  │
│             │                      │    └────────┘  └────────┘  └────────┘  │
│             │                      │                                         │
└─────────────┼──────────────────────┼─────────────────────────────────────────┘
              │                      │
              │                      │
      ┌───────┴───────┐      ┌───────┴───────┐
      │               │      │               │
      ▼               ▼      ▼               ▼
┌──────────┐   ┌──────────┐  ┌──────────┐   ┌──────────┐
│   GA4    │   │  Google  │  │   Meta   │   │ LinkedIn │
│ Property │   │   Ads    │  │   Ads    │   │   Ads    │
└──────────┘   └──────────┘  └──────────┘   └──────────┘
```

## 2.2 Webhook Integration

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        WEBHOOK INTEGRATION                                   │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   ┌─────────────┐         ┌─────────────┐         ┌─────────────┐           │
│   │   Brevo     │         │ Salesforce  │         │   Vavolta   │           │
│   │   CRM       │         │    CRM      │         │ Lead Magnets│           │
│   └──────┬──────┘         └──────┬──────┘         └──────┬──────┘           │
│          │                       │                       │                   │
│          │ Webhook               │ Webhook               │ Webhook           │
│          ▼                       ▼                       ▼                   │
│   ┌─────────────────────────────────────────────────────────────────┐       │
│   │              Cloud Functions (Webhook Handler)                   │       │
│   │                                                                  │       │
│   │   /api/webhooks/brevo      → Brevo Contact Events               │       │
│   │   /api/webhooks/salesforce → Lead Status Changes                │       │
│   │   /api/webhooks/vavolta    → Lead Magnet Downloads              │       │
│   │                                                                  │       │
│   └──────────────────────────────┬──────────────────────────────────┘       │
│                                  │                                           │
│                                  ▼                                           │
│                    ┌─────────────────────────┐                               │
│                    │       BigQuery          │                               │
│                    │   leads / events        │                               │
│                    └─────────────────────────┘                               │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

# 3. Datenbank-Schema

## 3.1 BigQuery Projekt-Struktur

```
brixon-analytics (GCP Projekt)
└── analytics (Dataset)
    ├── events           (Partitioned by DATE, Clustered by property_id, stape_user_id)
    ├── leads            (Clustered by property_id, lead_status)
    ├── sessions         (Partitioned by DATE, Clustered by property_id)
    ├── users            (Clustered by lead_status, first_touch_source)
    ├── identity_graph   (Clustered by property_id, identifier_type)
    ├── properties       (Multi-Tenant Konfiguration)
    │
    └── Views:
        ├── vw_lead_funnel
        ├── vw_channel_attribution
        ├── vw_user_journey
        ├── vw_campaign_performance
        ├── vw_unified_user_journey
        └── vw_cross_brand_conversions
```

## 3.2 Tabellen-Übersicht

### events (Haupttabelle)

> Detailliertes Schema siehe Tracking-Konzept Abschnitt 10.4

**Wichtige Felder:**
- `property_id` (STRING, NOT NULL) - Multi-Tenant Identifier
- `event_id` (STRING, NOT NULL) - UUID
- `stape_user_id` (STRING) - Primärer User Identifier
- `lead_id` (STRING) - FK zu leads (nach User-Stitching)
- `event_timestamp` (TIMESTAMP)
- `event_name` (STRING)

**Partitionierung & Clustering:**
```sql
PARTITION BY DATE(event_timestamp)
CLUSTER BY property_id, stape_user_id, event_name
```

### leads (Identifizierte User)

**Wichtige Felder:**
- `lead_id` (STRING, PRIMARY KEY)
- `property_id` (STRING, NOT NULL)
- `email` (STRING) - PII
- `lead_status` (STRING) - anonymous→known→mql→sql→customer
- `lead_score` (INTEGER)
- `stape_user_ids` (ARRAY<STRING>) - Alle verknüpften Anonymous IDs
- Attribution-Felder (first_touch_*, conversion_*)

### identity_graph (User Stitching)

**Zweck:** Verknüpft anonyme Identifier mit identifizierten Leads.

```sql
CREATE TABLE identity_graph (
  property_id STRING NOT NULL,
  master_uid STRING NOT NULL,           -- Kanonische User ID
  identifier STRING NOT NULL,            -- Der verknüpfte Identifier
  identifier_type STRING NOT NULL,       -- "anonymous_id", "email", "stape_user_id"
  first_seen_at TIMESTAMP,
  last_seen_at TIMESTAMP,
  confidence_score FLOAT64,              -- 0-1
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
)
CLUSTER BY property_id, identifier_type;
```

### sessions (Aggregierte Session-Daten)

**Felder:**
- Session Timing (start, end, duration)
- Traffic Source der Session
- Conversion-Info
- Entry/Exit Pages

### users (Anonyme User-Profile)

**Zweck:** Aggregierte Daten für anonyme User vor Identifikation.

---

# 4. API-Endpunkte

## 4.1 Übersicht

| Endpoint | Methode | Beschreibung |
|----------|---------|--------------|
| `/api/leads/score` | POST | Lead Score berechnen |
| `/api/users/stitch` | POST | User IDs verknüpfen |
| `/api/attribution/report` | GET | Attribution Report |
| `/api/webhooks/brevo` | POST | Brevo Webhook Handler |
| `/api/webhooks/salesforce` | POST | Salesforce Webhook Handler |
| `/api/webhooks/stape` | POST | Stape Offline Conversion |

## 4.2 Lead Score API

### POST /api/leads/score

**Request:**
```json
{
  "property_id": "drwerner",
  "lead_id": "uuid-lead-123",
  "recalculate": true
}
```

**Response:**
```json
{
  "lead_id": "uuid-lead-123",
  "previous_score": 35,
  "new_score": 52,
  "score_breakdown": {
    "page_views": 10,
    "lead_magnets": 15,
    "quickcheck": 20,
    "email_engagement": 5,
    "recency_bonus": 2
  },
  "grade": "B",
  "mql_threshold_reached": true,
  "recommended_action": "sync_to_salesforce"
}
```

### Lead Scoring Regeln

```javascript
// Scoring Konfiguration
const SCORING_RULES = {
  // Page Views (max 20 Punkte)
  page_view: {
    base: 1,
    service_page: 3,
    case_study: 2,
    max_total: 20
  },

  // Lead Magnet Downloads (max 25 Punkte)
  lead_magnet_download: {
    base: 10,
    topic_match: 5,  // Bonus wenn Topic zu ICP passt
    max_total: 25
  },

  // QuickCheck (max 25 Punkte)
  quickcheck_complete: {
    base: 15,
    high_score_result: 10,  // Bonus bei gutem Ergebnis
    max_total: 25
  },

  // E-Mail Engagement (max 15 Punkte)
  email_open: 1,
  email_click: 3,

  // Kontaktformular (max 30 Punkte)
  contact_form_submit: 30,

  // Recency Decay
  recency: {
    within_7_days: 1.0,      // Voller Score
    within_30_days: 0.8,     // 80%
    within_90_days: 0.5,     // 50%
    older: 0.2               // 20%
  },

  // Thresholds
  mql_threshold: 50,
  sql_threshold: 80
};
```

## 4.3 User Stitching API

### POST /api/users/stitch

**Request:**
```json
{
  "property_id": "drwerner",
  "identifiers": [
    {
      "type": "stape_user_id",
      "value": "uid_abc123"
    },
    {
      "type": "email",
      "value": "max@example.com"
    }
  ],
  "source_event": "lead_magnet_download"
}
```

**Response:**
```json
{
  "status": "merged",
  "master_uid": "uid_master_456",
  "merged_identifiers": ["uid_abc123", "uid_xyz789"],
  "lead_id": "lead_uuid_123",
  "is_new_lead": false,
  "previous_anonymous_events": 47
}
```

### Stitching Logik (Pseudocode)

```javascript
async function stitchUser(propertyId, identifiers, sourceEvent) {
  // 1. Prüfe ob E-Mail bereits bekannt
  const email = identifiers.find(i => i.type === 'email')?.value;

  if (email) {
    const existingLead = await findLeadByEmail(propertyId, email);

    if (existingLead) {
      // Merge: Füge neue Identifier zum bestehenden Lead
      for (const identifier of identifiers) {
        await insertIdentityMapping(existingLead.master_uid, identifier);
      }

      // Update Lead mit neuen stape_user_ids
      await updateLeadIdentifiers(existingLead.lead_id, identifiers);

      return {
        status: 'merged',
        master_uid: existingLead.master_uid,
        lead_id: existingLead.lead_id,
        is_new_lead: false
      };
    }
  }

  // 2. Prüfe ob stape_user_id bereits verknüpft ist
  const stapeId = identifiers.find(i => i.type === 'stape_user_id')?.value;

  if (stapeId) {
    const existingMapping = await findMappingByIdentifier(propertyId, stapeId);

    if (existingMapping) {
      // Füge weitere Identifier zu bestehendem Master hinzu
      for (const identifier of identifiers) {
        await insertIdentityMapping(existingMapping.master_uid, identifier);
      }

      return {
        status: 'extended',
        master_uid: existingMapping.master_uid,
        lead_id: existingMapping.lead_id
      };
    }
  }

  // 3. Neuer User: Erstelle Lead und Mappings
  const newMasterUid = generateUUID();
  const newLeadId = generateUUID();

  await createLead(propertyId, newLeadId, email, identifiers);

  for (const identifier of identifiers) {
    await insertIdentityMapping(newMasterUid, identifier);
  }

  return {
    status: 'created',
    master_uid: newMasterUid,
    lead_id: newLeadId,
    is_new_lead: true
  };
}
```

## 4.4 Attribution Report API

### GET /api/attribution/report

**Query Parameters:**
- `property_id` (required)
- `start_date` (YYYY-MM-DD)
- `end_date` (YYYY-MM-DD)
- `model` (first_touch | last_touch | linear | time_decay)
- `conversion_event` (lead_magnet_download | contact_form_submit | ...)

**Response:**
```json
{
  "property_id": "drwerner",
  "date_range": {
    "start": "2024-01-01",
    "end": "2024-12-31"
  },
  "attribution_model": "first_touch",
  "conversion_event": "contact_form_submit",
  "results": [
    {
      "channel": "google_ads",
      "conversions": 45,
      "conversion_value": 4500,
      "percentage": 38.5
    },
    {
      "channel": "organic",
      "conversions": 32,
      "conversion_value": 3200,
      "percentage": 27.4
    },
    {
      "channel": "linkedin_ads",
      "conversions": 20,
      "conversion_value": 2000,
      "percentage": 17.1
    }
  ],
  "total_conversions": 117,
  "total_value": 11700
}
```

---

# 5. Cloud Functions

## 5.1 Webhook Handler: Brevo

**Trigger:** Brevo sendet Webhook bei Kontakt-Events

**Datei:** `functions/webhooks/brevo.js`

```javascript
const { BigQuery } = require('@google-cloud/bigquery');

exports.handleBrevoWebhook = async (req, res) => {
  const bigquery = new BigQuery();

  try {
    const event = req.body;

    // Validiere Webhook Signature
    if (!validateBrevoSignature(req)) {
      return res.status(401).send('Invalid signature');
    }

    const eventType = event.event;
    const contactEmail = event.email;
    const timestamp = new Date(event.date).toISOString();

    // Property ID aus Brevo Attribut oder Default
    const propertyId = event.attributes?.property_id || 'drwerner';

    switch (eventType) {
      case 'contact_created':
        await handleContactCreated(bigquery, propertyId, event);
        break;

      case 'contact_updated':
        await handleContactUpdated(bigquery, propertyId, event);
        break;

      case 'email_opened':
      case 'email_clicked':
        await handleEmailEngagement(bigquery, propertyId, event, eventType);
        break;

      case 'unsubscribed':
        await handleUnsubscribe(bigquery, propertyId, event);
        break;

      default:
        console.log(`Unhandled Brevo event: ${eventType}`);
    }

    res.status(200).send('OK');

  } catch (error) {
    console.error('Brevo webhook error:', error);
    res.status(500).send('Error processing webhook');
  }
};

async function handleContactCreated(bigquery, propertyId, event) {
  const email = event.email;
  const emailHash = hashSHA256(email.toLowerCase());

  // Prüfe ob Lead bereits existiert
  const [existingLead] = await bigquery.query({
    query: `
      SELECT lead_id FROM \`brixon-analytics.analytics.leads\`
      WHERE property_id = @propertyId AND email_hash = @emailHash
    `,
    params: { propertyId, emailHash }
  });

  if (existingLead.length === 0) {
    // Neuer Lead
    const leadId = generateUUID();

    await bigquery.query({
      query: `
        INSERT INTO \`brixon-analytics.analytics.leads\`
        (lead_id, property_id, email, email_hash, first_name, last_name,
         lead_status, known_at, brevo_contact_id, created_at)
        VALUES
        (@leadId, @propertyId, @email, @emailHash, @firstName, @lastName,
         'known', CURRENT_TIMESTAMP(), @brevoId, CURRENT_TIMESTAMP())
      `,
      params: {
        leadId,
        propertyId,
        email: event.email,
        emailHash,
        firstName: event.attributes?.FIRSTNAME || null,
        lastName: event.attributes?.LASTNAME || null,
        brevoId: event.id.toString()
      }
    });

    // User Stitching triggern falls stape_user_id bekannt
    if (event.attributes?.stape_user_id) {
      await triggerUserStitching(propertyId, leadId, event.attributes.stape_user_id, email);
    }
  } else {
    // Update bestehenden Lead
    await bigquery.query({
      query: `
        UPDATE \`brixon-analytics.analytics.leads\`
        SET brevo_contact_id = @brevoId, updated_at = CURRENT_TIMESTAMP()
        WHERE lead_id = @leadId
      `,
      params: {
        brevoId: event.id.toString(),
        leadId: existingLead[0].lead_id
      }
    });
  }
}

async function handleEmailEngagement(bigquery, propertyId, event, eventType) {
  // Logge E-Mail Engagement als Event
  await bigquery.query({
    query: `
      INSERT INTO \`brixon-analytics.analytics.events\`
      (property_id, event_id, event_name, event_timestamp, user_email_hash,
       event_params, data_source)
      VALUES
      (@propertyId, @eventId, @eventName, @timestamp, @emailHash,
       @eventParams, 'brevo_webhook')
    `,
    params: {
      propertyId,
      eventId: generateUUID(),
      eventName: eventType === 'email_opened' ? 'email_open' : 'email_click',
      timestamp: new Date(event.date),
      emailHash: hashSHA256(event.email.toLowerCase()),
      eventParams: JSON.stringify({
        campaign_name: event.campaign?.name,
        email_subject: event.subject,
        link_clicked: event.link || null
      })
    }
  });

  // Lead Score aktualisieren
  await triggerLeadScoreRecalculation(propertyId, event.email);
}
```

## 5.2 Webhook Handler: Salesforce

**Trigger:** Salesforce Flow sendet Webhook bei Lead Status-Änderung

**Datei:** `functions/webhooks/salesforce.js`

```javascript
exports.handleSalesforceWebhook = async (req, res) => {
  const bigquery = new BigQuery();

  try {
    const payload = req.body;

    // Validiere Salesforce Webhook
    if (!validateSalesforceAuth(req)) {
      return res.status(401).send('Unauthorized');
    }

    const {
      lead_id: sfLeadId,
      status: newStatus,
      email,
      gclid,
      fbclid,
      li_fat_id,
      conversion_value
    } = payload;

    const propertyId = payload.property_id || 'drwerner';

    // Finde BigQuery Lead anhand Salesforce ID oder E-Mail
    const [lead] = await bigquery.query({
      query: `
        SELECT lead_id, lead_status FROM \`brixon-analytics.analytics.leads\`
        WHERE property_id = @propertyId
          AND (salesforce_lead_id = @sfLeadId OR email = @email)
      `,
      params: { propertyId, sfLeadId, email }
    });

    if (lead.length === 0) {
      console.error(`Lead not found: ${sfLeadId} / ${email}`);
      return res.status(404).send('Lead not found');
    }

    const bigqueryLeadId = lead[0].lead_id;
    const previousStatus = lead[0].lead_status;

    // Status-Mapping Salesforce → BigQuery
    const statusMapping = {
      'New': 'known',
      'MQL': 'mql',
      'SQL': 'sql',
      'Qualified': 'sql',
      'Unqualified': 'disqualified',
      'Converted': 'customer'
    };

    const mappedStatus = statusMapping[newStatus] || newStatus.toLowerCase();

    // Update Lead Status
    await bigquery.query({
      query: `
        UPDATE \`brixon-analytics.analytics.leads\`
        SET
          lead_status = @newStatus,
          salesforce_lead_id = @sfLeadId,
          ${mappedStatus === 'sql' ? 'sql_at = CURRENT_TIMESTAMP(),' : ''}
          ${mappedStatus === 'customer' ? 'customer_at = CURRENT_TIMESTAMP(),' : ''}
          updated_at = CURRENT_TIMESTAMP()
        WHERE lead_id = @leadId
      `,
      params: {
        newStatus: mappedStatus,
        sfLeadId,
        leadId: bigqueryLeadId
      }
    });

    // Bei SQL oder höher: Offline Conversion triggern
    if (['sql', 'customer'].includes(mappedStatus) && previousStatus !== mappedStatus) {
      await triggerOfflineConversion(propertyId, {
        leadId: bigqueryLeadId,
        status: mappedStatus,
        gclid,
        fbclid,
        li_fat_id,
        value: conversion_value || (mappedStatus === 'sql' ? 100 : 500)
      });
    }

    // Bei Unqualified: Negative Conversion
    if (mappedStatus === 'disqualified' && previousStatus !== 'disqualified') {
      await triggerNegativeConversion(propertyId, {
        leadId: bigqueryLeadId,
        gclid,
        fbclid
      });
    }

    res.status(200).send('OK');

  } catch (error) {
    console.error('Salesforce webhook error:', error);
    res.status(500).send('Error');
  }
};

async function triggerOfflineConversion(propertyId, data) {
  // Sende an Stape Server für Offline Conversion Upload
  const stapeEndpoint = `https://s.drwerner.com/api/offline-conversion`;

  await fetch(stapeEndpoint, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      property_id: propertyId,
      conversion_action: data.status === 'sql' ? 'qualified_lead' : 'customer_acquired',
      gclid: data.gclid,
      fbclid: data.fbclid,
      li_fat_id: data.li_fat_id,
      conversion_value: data.value,
      conversion_timestamp: new Date().toISOString()
    })
  });
}

async function triggerNegativeConversion(propertyId, data) {
  // Sende negative Conversion an Google Ads
  const stapeEndpoint = `https://s.drwerner.com/api/offline-conversion`;

  await fetch(stapeEndpoint, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      property_id: propertyId,
      conversion_action: 'unqualified_lead',
      gclid: data.gclid,
      conversion_value: -1,
      conversion_timestamp: new Date().toISOString()
    })
  });
}
```

## 5.3 Lead Score Calculator

**Trigger:** Pub/Sub Topic `lead-score-recalculation`

**Datei:** `functions/scoring/calculate-score.js`

```javascript
const { BigQuery } = require('@google-cloud/bigquery');

const SCORING_CONFIG = {
  // Events und ihre Basiswerte
  events: {
    page_view: { base: 1, max: 20 },
    service_page_view: { base: 3, max: 15 },
    lead_magnet_download: { base: 15, max: 25 },
    quickcheck_complete: { base: 20, max: 25 },
    newsletter_signup: { base: 5, max: 5 },
    contact_form_start: { base: 5, max: 5 },
    email_open: { base: 1, max: 10 },
    email_click: { base: 3, max: 15 }
  },

  // Bonuspunkte
  bonuses: {
    multiple_sessions: { threshold: 3, points: 5 },
    high_engagement: { threshold: 300, points: 10 },  // Sekunden
    multi_channel: { threshold: 2, points: 5 }
  },

  // Recency Decay (Tage seit letztem Besuch)
  recency_decay: {
    0: 1.0,    // Heute
    7: 0.9,
    14: 0.8,
    30: 0.6,
    60: 0.4,
    90: 0.2
  },

  // Grade Thresholds
  grades: {
    A: 80,
    B: 60,
    C: 40,
    D: 20
  },

  mql_threshold: 50,
  sql_threshold: 80
};

exports.calculateLeadScore = async (message, context) => {
  const bigquery = new BigQuery();

  const data = JSON.parse(Buffer.from(message.data, 'base64').toString());
  const { property_id: propertyId, lead_id: leadId } = data;

  try {
    // 1. Hole alle Events des Leads
    const [events] = await bigquery.query({
      query: `
        SELECT
          event_name,
          event_timestamp,
          page_path,
          traffic_source
        FROM \`brixon-analytics.analytics.events\`
        WHERE property_id = @propertyId
          AND lead_id = @leadId
        ORDER BY event_timestamp
      `,
      params: { propertyId, leadId }
    });

    // 2. Hole Lead-Metadaten
    const [leadData] = await bigquery.query({
      query: `
        SELECT
          lead_status,
          lead_score AS previous_score,
          total_sessions,
          total_time_on_site_seconds,
          channels_touched,
          last_seen_at
        FROM \`brixon-analytics.analytics.leads\`
        WHERE lead_id = @leadId
      `,
      params: { leadId }
    });

    if (leadData.length === 0) {
      console.error(`Lead not found: ${leadId}`);
      return;
    }

    const lead = leadData[0];

    // 3. Berechne Score
    const scoreBreakdown = {};
    let totalScore = 0;

    // Event-basierter Score
    const eventCounts = {};
    for (const event of events) {
      const eventName = event.event_name;
      eventCounts[eventName] = (eventCounts[eventName] || 0) + 1;
    }

    for (const [eventName, config] of Object.entries(SCORING_CONFIG.events)) {
      const count = eventCounts[eventName] || 0;
      const eventScore = Math.min(count * config.base, config.max);
      scoreBreakdown[eventName] = eventScore;
      totalScore += eventScore;
    }

    // Bonuspunkte
    if (lead.total_sessions >= SCORING_CONFIG.bonuses.multiple_sessions.threshold) {
      scoreBreakdown.multiple_sessions_bonus = SCORING_CONFIG.bonuses.multiple_sessions.points;
      totalScore += SCORING_CONFIG.bonuses.multiple_sessions.points;
    }

    if (lead.total_time_on_site_seconds >= SCORING_CONFIG.bonuses.high_engagement.threshold) {
      scoreBreakdown.high_engagement_bonus = SCORING_CONFIG.bonuses.high_engagement.points;
      totalScore += SCORING_CONFIG.bonuses.high_engagement.points;
    }

    if (lead.channels_touched && lead.channels_touched.length >= SCORING_CONFIG.bonuses.multi_channel.threshold) {
      scoreBreakdown.multi_channel_bonus = SCORING_CONFIG.bonuses.multi_channel.points;
      totalScore += SCORING_CONFIG.bonuses.multi_channel.points;
    }

    // Recency Decay
    const daysSinceLastVisit = lead.last_seen_at
      ? Math.floor((Date.now() - new Date(lead.last_seen_at).getTime()) / (1000 * 60 * 60 * 24))
      : 999;

    let decayFactor = 0.1;
    for (const [days, factor] of Object.entries(SCORING_CONFIG.recency_decay).sort((a, b) => b[0] - a[0])) {
      if (daysSinceLastVisit <= parseInt(days)) {
        decayFactor = factor;
      }
    }

    totalScore = Math.round(totalScore * decayFactor);
    scoreBreakdown.recency_decay = decayFactor;

    // 4. Bestimme Grade
    let grade = 'F';
    for (const [g, threshold] of Object.entries(SCORING_CONFIG.grades).sort((a, b) => b[1] - a[1])) {
      if (totalScore >= threshold) {
        grade = g;
        break;
      }
    }

    // 5. Bestimme Status-Änderung
    const previousScore = lead.previous_score || 0;
    const currentStatus = lead.lead_status;
    let newStatus = currentStatus;
    let statusChanged = false;

    if (totalScore >= SCORING_CONFIG.sql_threshold && currentStatus !== 'sql' && currentStatus !== 'customer') {
      // Prüfe ob Kontaktformular ausgefüllt wurde
      if (eventCounts.contact_form_submit > 0) {
        newStatus = 'sql';
        statusChanged = true;
      }
    } else if (totalScore >= SCORING_CONFIG.mql_threshold && currentStatus === 'known') {
      newStatus = 'mql';
      statusChanged = true;
    }

    // 6. Update Lead
    await bigquery.query({
      query: `
        UPDATE \`brixon-analytics.analytics.leads\`
        SET
          lead_score = @score,
          lead_grade = @grade,
          ${statusChanged ? `lead_status = @newStatus,` : ''}
          ${statusChanged && newStatus === 'mql' ? `mql_at = CURRENT_TIMESTAMP(), mql_trigger = 'score_threshold',` : ''}
          updated_at = CURRENT_TIMESTAMP()
        WHERE lead_id = @leadId
      `,
      params: {
        score: totalScore,
        grade,
        newStatus,
        leadId
      }
    });

    // 7. Bei MQL: Brevo Attribut updaten
    if (statusChanged && newStatus === 'mql') {
      await updateBrevoMqlStatus(propertyId, leadId);
    }

    // 8. Bei SQL: Salesforce Sync triggern
    if (statusChanged && newStatus === 'sql') {
      await triggerSalesforceSync(propertyId, leadId);
    }

    console.log(`Lead ${leadId}: Score ${previousScore} → ${totalScore}, Grade ${grade}, Status ${currentStatus} → ${newStatus}`);

  } catch (error) {
    console.error(`Error calculating score for ${leadId}:`, error);
    throw error;
  }
};
```

---

# 6. Scheduled Queries

## 6.1 User Stitching Query (alle 15 Minuten)

```sql
-- Scheduled Query: user_stitching
-- Frequenz: Alle 15 Minuten

-- 1. Events mit Leads verknüpfen
MERGE `brixon-analytics.analytics.events` AS e
USING (
  SELECT DISTINCT
    ig.property_id,
    ig.identifier AS stape_user_id,
    ig.master_uid,
    l.lead_id
  FROM `brixon-analytics.analytics.identity_graph` ig
  JOIN `brixon-analytics.analytics.leads` l
    ON ig.property_id = l.property_id
    AND ig.master_uid IN UNNEST(l.stape_user_ids)
  WHERE ig.identifier_type = 'anonymous_id'
) AS matches
ON e.property_id = matches.property_id
   AND e.stape_user_id = matches.stape_user_id
   AND e.lead_id IS NULL
WHEN MATCHED THEN
  UPDATE SET e.lead_id = matches.lead_id;

-- 2. Lead-Metriken aktualisieren
MERGE `brixon-analytics.analytics.leads` AS l
USING (
  SELECT
    property_id,
    lead_id,
    COUNT(DISTINCT session_id) AS total_sessions,
    COUNT(*) AS total_pageviews,
    SUM(IFNULL(time_on_page_seconds, 0)) AS total_time_on_site_seconds,
    MAX(event_timestamp) AS last_seen_at,
    ARRAY_AGG(DISTINCT traffic_source IGNORE NULLS) AS channels_touched,
    ARRAY_AGG(DISTINCT traffic_campaign IGNORE NULLS) AS campaigns_touched
  FROM `brixon-analytics.analytics.events`
  WHERE lead_id IS NOT NULL
  GROUP BY property_id, lead_id
) AS metrics
ON l.property_id = metrics.property_id AND l.lead_id = metrics.lead_id
WHEN MATCHED THEN
  UPDATE SET
    l.total_sessions = metrics.total_sessions,
    l.total_pageviews = metrics.total_pageviews,
    l.total_time_on_site_seconds = metrics.total_time_on_site_seconds,
    l.last_seen_at = metrics.last_seen_at,
    l.days_since_last_visit = DATE_DIFF(CURRENT_DATE(), DATE(metrics.last_seen_at), DAY),
    l.channels_touched = metrics.channels_touched,
    l.campaigns_touched = metrics.campaigns_touched,
    l.updated_at = CURRENT_TIMESTAMP();
```

## 6.2 Session Aggregation (alle 30 Minuten)

```sql
-- Scheduled Query: session_aggregation
-- Frequenz: Alle 30 Minuten

-- Aggregiere Events zu Sessions
MERGE `brixon-analytics.analytics.sessions` AS s
USING (
  SELECT
    property_id,
    session_id,
    stape_user_id,
    lead_id,
    MIN(event_timestamp) AS session_start,
    MAX(event_timestamp) AS session_end,
    TIMESTAMP_DIFF(MAX(event_timestamp), MIN(event_timestamp), SECOND) AS session_duration_seconds,
    COUNT(*) AS events_count,
    COUNTIF(event_name = 'page_view') AS pageviews,

    -- Erste Traffic Source der Session
    ARRAY_AGG(traffic_source IGNORE NULLS ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS source,
    ARRAY_AGG(traffic_medium IGNORE NULLS ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS medium,
    ARRAY_AGG(traffic_campaign IGNORE NULLS ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS campaign,
    ARRAY_AGG(gclid IGNORE NULLS ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS gclid,
    ARRAY_AGG(fbclid IGNORE NULLS ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS fbclid,
    ARRAY_AGG(li_fat_id IGNORE NULLS ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS li_fat_id,

    -- Landing & Exit Page
    ARRAY_AGG(page_path ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS landing_page,
    ARRAY_REVERSE(ARRAY_AGG(page_path ORDER BY event_timestamp))[OFFSET(0)] AS exit_page,

    -- Conversion
    MAX(CASE WHEN event_name IN ('lead_magnet_download', 'contact_form_submit', 'quickcheck_complete') THEN 1 ELSE 0 END) = 1 AS converted,
    ARRAY_AGG(CASE WHEN event_name IN ('lead_magnet_download', 'contact_form_submit', 'quickcheck_complete') THEN event_name END IGNORE NULLS LIMIT 1)[SAFE_OFFSET(0)] AS conversion_event,

    -- Device & Geo (erste Werte)
    ARRAY_AGG(device_category IGNORE NULLS LIMIT 1)[SAFE_OFFSET(0)] AS device_category,
    ARRAY_AGG(country_code IGNORE NULLS LIMIT 1)[SAFE_OFFSET(0)] AS country,
    ARRAY_AGG(city IGNORE NULLS LIMIT 1)[SAFE_OFFSET(0)] AS city

  FROM `brixon-analytics.analytics.events`
  WHERE event_timestamp >= TIMESTAMP_SUB(CURRENT_TIMESTAMP(), INTERVAL 2 HOUR)
  GROUP BY property_id, session_id, stape_user_id, lead_id
) AS new_sessions
ON s.property_id = new_sessions.property_id AND s.session_id = new_sessions.session_id
WHEN MATCHED THEN
  UPDATE SET
    s.session_end = new_sessions.session_end,
    s.session_duration_seconds = new_sessions.session_duration_seconds,
    s.events_count = new_sessions.events_count,
    s.pageviews = new_sessions.pageviews,
    s.converted = new_sessions.converted,
    s.conversion_event = new_sessions.conversion_event,
    s.lead_id = COALESCE(new_sessions.lead_id, s.lead_id)
WHEN NOT MATCHED THEN
  INSERT ROW;
```

## 6.3 Lead Score Batch Update (täglich)

```sql
-- Scheduled Query: daily_lead_score_update
-- Frequenz: Täglich um 02:00 UTC

-- Triggere Score-Recalculation für alle aktiven Leads
-- via Pub/Sub Message
EXPORT DATA
OPTIONS (
  format = 'JSON',
  uri = 'gs://brixon-analytics-pubsub-staging/lead-scores/*.json',
  overwrite = true
)
AS
SELECT
  property_id,
  lead_id,
  'recalculate' AS action
FROM `brixon-analytics.analytics.leads`
WHERE lead_status NOT IN ('customer', 'disqualified', 'churned')
  AND last_seen_at >= TIMESTAMP_SUB(CURRENT_TIMESTAMP(), INTERVAL 90 DAY);

-- Cloud Function liest diese Datei und sendet Pub/Sub Messages
```

## 6.4 First Touch Attribution Update (stündlich)

```sql
-- Scheduled Query: first_touch_attribution
-- Frequenz: Stündlich

-- Update First Touch für neu verknüpfte Leads
UPDATE `brixon-analytics.analytics.leads` AS l
SET
  first_touch_timestamp = ft.first_timestamp,
  first_touch_source = ft.first_source,
  first_touch_medium = ft.first_medium,
  first_touch_campaign = ft.first_campaign,
  first_touch_landing_page = ft.first_landing_page,
  first_touch_gclid = ft.first_gclid,
  first_touch_fbclid = ft.first_fbclid,
  first_touch_li_fat_id = ft.first_li_fat_id,
  updated_at = CURRENT_TIMESTAMP()
FROM (
  SELECT
    property_id,
    lead_id,
    ARRAY_AGG(event_timestamp ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS first_timestamp,
    ARRAY_AGG(traffic_source ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS first_source,
    ARRAY_AGG(traffic_medium ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS first_medium,
    ARRAY_AGG(traffic_campaign ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS first_campaign,
    ARRAY_AGG(page_path ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS first_landing_page,
    ARRAY_AGG(gclid ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS first_gclid,
    ARRAY_AGG(fbclid ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS first_fbclid,
    ARRAY_AGG(li_fat_id ORDER BY event_timestamp LIMIT 1)[OFFSET(0)] AS first_li_fat_id
  FROM `brixon-analytics.analytics.events`
  WHERE lead_id IS NOT NULL
  GROUP BY property_id, lead_id
) AS ft
WHERE l.property_id = ft.property_id
  AND l.lead_id = ft.lead_id
  AND l.first_touch_timestamp IS NULL;
```

---

# 7. Integrationen

## 7.1 Brevo Integration

### Bidirektionaler Sync

```
┌─────────────────────────────────────────────────────────────┐
│                    BREVO INTEGRATION                         │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  BigQuery → Brevo (Outbound)                                │
│  ───────────────────────────                                │
│  Trigger: Lead wird erstellt oder identifiziert             │
│  Action:  Brevo Kontakt anlegen/updaten via API             │
│  Daten:   E-Mail, Name, Lead Score, Attribution             │
│                                                              │
│  Brevo → BigQuery (Inbound)                                 │
│  ─────────────────────────                                  │
│  Trigger: Webhook Events                                     │
│  Events:  contact_created, email_opened, email_clicked,     │
│           unsubscribed, hard_bounced                         │
│  Action:  Event in BigQuery speichern, Score aktualisieren  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Brevo API Client

```javascript
// lib/brevo-client.js

const SibApiV3Sdk = require('sib-api-v3-sdk');

class BrevoClient {
  constructor() {
    const defaultClient = SibApiV3Sdk.ApiClient.instance;
    const apiKey = defaultClient.authentications['api-key'];
    apiKey.apiKey = process.env.BREVO_API_KEY;

    this.contactsApi = new SibApiV3Sdk.ContactsApi();
  }

  async createOrUpdateContact(lead) {
    const contact = new SibApiV3Sdk.CreateContact();

    contact.email = lead.email;
    contact.attributes = {
      FIRSTNAME: lead.first_name,
      LASTNAME: lead.last_name,
      COMPANY: lead.company,
      LEAD_STATUS: lead.lead_status,
      LEAD_SCORE: lead.lead_score,
      LEAD_GRADE: lead.lead_grade,
      PROPERTY_ID: lead.property_id,
      STAPE_USER_ID: lead.stape_user_ids?.[0],
      FIRST_TOUCH_SOURCE: lead.first_touch_source,
      FIRST_TOUCH_CAMPAIGN: lead.first_touch_campaign,
      MQL_DATE: lead.mql_at,
      IS_MQL: lead.lead_status === 'mql' || lead.lead_status === 'sql',
      IS_SQL: lead.lead_status === 'sql'
    };

    // Listen basierend auf Property
    const listMapping = {
      'drwerner': [1, 2],  // Haupt-Liste + Nurturing
      'philippsauerborn': [3]
    };
    contact.listIds = listMapping[lead.property_id] || [1];

    try {
      await this.contactsApi.createContact(contact);
    } catch (error) {
      if (error.status === 400 && error.body?.message?.includes('already exist')) {
        // Update existing contact
        const updateContact = new SibApiV3Sdk.UpdateContact();
        updateContact.attributes = contact.attributes;
        await this.contactsApi.updateContact(lead.email, updateContact);
      } else {
        throw error;
      }
    }
  }

  async updateMqlStatus(email, isMql) {
    const updateContact = new SibApiV3Sdk.UpdateContact();
    updateContact.attributes = {
      IS_MQL: isMql,
      MQL_DATE: isMql ? new Date().toISOString() : null
    };

    // Verschiebe in MQL-Workflow
    if (isMql) {
      updateContact.listIds = [2];  // MQL Nurturing Liste
    }

    await this.contactsApi.updateContact(email, updateContact);
  }
}

module.exports = BrevoClient;
```

## 7.2 Salesforce Integration

### Sync-Regeln

```
┌─────────────────────────────────────────────────────────────┐
│                  SALESFORCE SYNC RULES                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Wann wird ein Salesforce Lead erstellt?                    │
│  ────────────────────────────────────────                   │
│  1. Lead Score >= 80 (SQL Threshold)                        │
│  2. Kontaktformular ausgefüllt                              │
│  3. Direktanfrage via E-Mail/Telefon (manuell)              │
│                                                              │
│  Feld-Mapping BigQuery → Salesforce                         │
│  ───────────────────────────────────                        │
│  email               → Email                                 │
│  first_name          → FirstName                            │
│  last_name           → LastName                             │
│  company             → Company                               │
│  phone               → Phone                                 │
│  lead_score          → Lead_Score__c                        │
│  lead_grade          → Lead_Grade__c                        │
│  first_touch_source  → First_Touch_Source__c                │
│  first_touch_campaign→ First_Touch_Campaign__c              │
│  gclid               → gclid__c                             │
│  fbclid              → fbclid__c                            │
│  li_fat_id           → li_fat_id__c                         │
│  service_interest    → Service_Interest__c                  │
│                                                              │
│  Salesforce → BigQuery (Webhook)                            │
│  ─────────────────────────────────                          │
│  Lead.Status changes → Update lead_status in BigQuery       │
│  Lead.IsConverted    → Set customer_at                      │
│  Lead.Unqualified    → Trigger negative conversion          │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Salesforce API Client

```javascript
// lib/salesforce-client.js

const jsforce = require('jsforce');

class SalesforceClient {
  constructor() {
    this.conn = new jsforce.Connection({
      loginUrl: process.env.SALESFORCE_LOGIN_URL,
      version: '58.0'
    });
  }

  async connect() {
    await this.conn.login(
      process.env.SALESFORCE_USERNAME,
      process.env.SALESFORCE_PASSWORD + process.env.SALESFORCE_SECURITY_TOKEN
    );
  }

  async createLead(lead) {
    await this.connect();

    const sfLead = {
      Email: lead.email,
      FirstName: lead.first_name,
      LastName: lead.last_name || 'Unknown',
      Company: lead.company || 'Unknown',
      Phone: lead.phone,

      // Custom Fields
      Lead_Score__c: lead.lead_score,
      Lead_Grade__c: lead.lead_grade,
      Lead_Lifecycle_Stage__c: 'SQL',
      MQL_Date__c: lead.mql_at,
      SQL_Date__c: new Date().toISOString(),
      MQL_Source__c: lead.mql_trigger,

      // Attribution
      First_Touch_Source__c: lead.first_touch_source,
      First_Touch_Campaign__c: lead.first_touch_campaign,
      gclid__c: lead.first_touch_gclid,
      fbclid__c: lead.first_touch_fbclid,
      li_fat_id__c: lead.first_touch_li_fat_id,

      // Service Interest
      Service_Interest__c: lead.service_interest,

      // External IDs
      BigQuery_Lead_ID__c: lead.lead_id,
      Brevo_Contact_ID__c: lead.brevo_contact_id,

      // Source
      LeadSource: this.mapLeadSource(lead.first_touch_source)
    };

    const result = await this.conn.sobject('Lead').create(sfLead);

    return result.id;
  }

  async updateLead(sfLeadId, updates) {
    await this.connect();

    await this.conn.sobject('Lead').update({
      Id: sfLeadId,
      ...updates
    });
  }

  mapLeadSource(source) {
    const mapping = {
      'google_ads': 'Google Ads',
      'meta_ads': 'Meta Ads',
      'linkedin_ads': 'LinkedIn Ads',
      'organic': 'Organic Search',
      'direct': 'Direct',
      'referral': 'Referral',
      'email': 'Email Marketing'
    };
    return mapping[source] || 'Other';
  }
}

module.exports = SalesforceClient;
```

## 7.3 Stape Offline Conversion Endpoint

### Cloud Function für Stape

```javascript
// functions/stape/offline-conversion.js

exports.handleOfflineConversion = async (req, res) => {
  // CORS
  res.set('Access-Control-Allow-Origin', 'https://s.drwerner.com');

  if (req.method === 'OPTIONS') {
    res.set('Access-Control-Allow-Methods', 'POST');
    res.set('Access-Control-Allow-Headers', 'Content-Type');
    return res.status(204).send('');
  }

  try {
    const {
      property_id,
      conversion_action,
      gclid,
      gbraid,
      wbraid,
      fbclid,
      li_fat_id,
      conversion_value,
      conversion_timestamp,
      user_email,
      user_phone
    } = req.body;

    const responses = {};

    // Google Ads Offline Conversion
    if (gclid || gbraid || wbraid) {
      responses.google_ads = await sendGoogleAdsConversion({
        conversion_action,
        gclid,
        gbraid,
        wbraid,
        conversion_value,
        conversion_timestamp,
        user_email,
        user_phone
      });
    }

    // Meta CAPI
    if (fbclid || user_email) {
      responses.meta = await sendMetaConversion({
        event_name: conversion_action,
        fbclid,
        conversion_value,
        conversion_timestamp,
        user_email,
        user_phone
      });
    }

    // LinkedIn CAPI
    if (li_fat_id || user_email) {
      responses.linkedin = await sendLinkedInConversion({
        conversion_action,
        li_fat_id,
        conversion_value,
        conversion_timestamp,
        user_email
      });
    }

    res.status(200).json({
      success: true,
      responses
    });

  } catch (error) {
    console.error('Offline conversion error:', error);
    res.status(500).json({
      success: false,
      error: error.message
    });
  }
};

async function sendGoogleAdsConversion(data) {
  const { GoogleAdsApi } = require('google-ads-api');

  const client = new GoogleAdsApi({
    client_id: process.env.GOOGLE_ADS_CLIENT_ID,
    client_secret: process.env.GOOGLE_ADS_CLIENT_SECRET,
    developer_token: process.env.GOOGLE_ADS_DEVELOPER_TOKEN
  });

  const customer = client.Customer({
    customer_id: process.env.GOOGLE_ADS_CUSTOMER_ID,
    refresh_token: process.env.GOOGLE_ADS_REFRESH_TOKEN
  });

  const conversion = {
    conversion_action: `customers/${process.env.GOOGLE_ADS_CUSTOMER_ID}/conversionActions/${getConversionActionId(data.conversion_action)}`,
    conversion_date_time: formatGoogleDateTime(data.conversion_timestamp),
    conversion_value: data.conversion_value,
    currency_code: 'EUR'
  };

  // Identifier hinzufügen
  if (data.gclid) {
    conversion.gclid = data.gclid;
  } else if (data.gbraid) {
    conversion.gbraid = data.gbraid;
  } else if (data.wbraid) {
    conversion.wbraid = data.wbraid;
  }

  // Enhanced Conversions
  if (data.user_email || data.user_phone) {
    conversion.user_identifiers = [];

    if (data.user_email) {
      conversion.user_identifiers.push({
        hashed_email: hashSHA256(data.user_email.toLowerCase().trim())
      });
    }

    if (data.user_phone) {
      conversion.user_identifiers.push({
        hashed_phone_number: hashSHA256(normalizePhone(data.user_phone))
      });
    }
  }

  await customer.conversionUploads.uploadClickConversions({
    customer_id: process.env.GOOGLE_ADS_CUSTOMER_ID,
    conversions: [conversion],
    partial_failure: true
  });

  return { status: 'sent' };
}

function getConversionActionId(action) {
  const mapping = {
    'lead_magnet_download': '123456789',
    'quickcheck_complete': '123456790',
    'qualified_lead': '123456791',
    'customer_acquired': '123456792',
    'unqualified_lead': '123456793'
  };
  return mapping[action] || mapping['qualified_lead'];
}
```

---

# 8. Looker Studio Connector

## 8.1 BigQuery Views für Reporting

### View: Lead Funnel

```sql
CREATE OR REPLACE VIEW `brixon-analytics.analytics.vw_lead_funnel` AS
SELECT
  property_id,
  DATE(created_at) AS date,

  -- Funnel Stages
  COUNTIF(lead_status IN ('known', 'mql', 'sql', 'customer')) AS total_leads,
  COUNTIF(lead_status IN ('mql', 'sql', 'customer')) AS mqls,
  COUNTIF(lead_status IN ('sql', 'customer')) AS sqls,
  COUNTIF(lead_status = 'customer') AS customers,

  -- Conversion Rates
  SAFE_DIVIDE(COUNTIF(lead_status IN ('mql', 'sql', 'customer')), COUNTIF(lead_status IN ('known', 'mql', 'sql', 'customer'))) AS known_to_mql_rate,
  SAFE_DIVIDE(COUNTIF(lead_status IN ('sql', 'customer')), COUNTIF(lead_status IN ('mql', 'sql', 'customer'))) AS mql_to_sql_rate,
  SAFE_DIVIDE(COUNTIF(lead_status = 'customer'), COUNTIF(lead_status IN ('sql', 'customer'))) AS sql_to_customer_rate,

  -- By Source
  first_touch_source,
  first_touch_campaign

FROM `brixon-analytics.analytics.leads`
WHERE lead_status NOT IN ('disqualified')
GROUP BY property_id, DATE(created_at), first_touch_source, first_touch_campaign;
```

### View: Channel Attribution

```sql
CREATE OR REPLACE VIEW `brixon-analytics.analytics.vw_channel_attribution` AS
WITH first_touch AS (
  SELECT
    property_id,
    DATE(known_at) AS conversion_date,
    first_touch_source AS channel,
    first_touch_campaign AS campaign,
    'first_touch' AS attribution_model,
    1 AS conversions,
    estimated_deal_value AS value
  FROM `brixon-analytics.analytics.leads`
  WHERE lead_status IN ('mql', 'sql', 'customer')
),

last_touch AS (
  SELECT
    l.property_id,
    DATE(l.known_at) AS conversion_date,
    s.source AS channel,
    s.campaign AS campaign,
    'last_touch' AS attribution_model,
    1 AS conversions,
    l.estimated_deal_value AS value
  FROM `brixon-analytics.analytics.leads` l
  JOIN `brixon-analytics.analytics.sessions` s
    ON l.lead_id = s.lead_id
    AND s.converted = TRUE
  WHERE l.lead_status IN ('mql', 'sql', 'customer')
)

SELECT * FROM first_touch
UNION ALL
SELECT * FROM last_touch;
```

### View: Campaign Performance

```sql
CREATE OR REPLACE VIEW `brixon-analytics.analytics.vw_campaign_performance` AS
SELECT
  e.property_id,
  DATE(e.event_timestamp) AS date,
  e.traffic_source AS channel,
  e.traffic_campaign AS campaign,

  -- Traffic
  COUNT(DISTINCT e.session_id) AS sessions,
  COUNT(DISTINCT e.stape_user_id) AS users,
  COUNT(*) AS events,

  -- Engagement
  AVG(s.session_duration_seconds) AS avg_session_duration,
  AVG(s.pageviews) AS avg_pageviews,

  -- Conversions
  COUNTIF(e.event_name = 'lead_magnet_download') AS lead_magnet_downloads,
  COUNTIF(e.event_name = 'quickcheck_complete') AS quickcheck_completions,
  COUNTIF(e.event_name = 'contact_form_submit') AS contact_submissions,

  -- Leads Generated
  COUNT(DISTINCT CASE WHEN e.event_name IN ('lead_magnet_download', 'quickcheck_complete', 'contact_form_submit') THEN e.lead_id END) AS leads_generated

FROM `brixon-analytics.analytics.events` e
LEFT JOIN `brixon-analytics.analytics.sessions` s
  ON e.property_id = s.property_id AND e.session_id = s.session_id
WHERE e.traffic_campaign IS NOT NULL
GROUP BY e.property_id, DATE(e.event_timestamp), e.traffic_source, e.traffic_campaign;
```

### View: Cross-Brand Conversions

```sql
CREATE OR REPLACE VIEW `brixon-analytics.analytics.vw_cross_brand_conversions` AS
WITH user_journeys AS (
  SELECT
    ig.master_uid,
    e.property_id,
    e.event_name,
    e.event_timestamp,
    ROW_NUMBER() OVER (PARTITION BY ig.master_uid ORDER BY e.event_timestamp) AS event_order
  FROM `brixon-analytics.analytics.events` e
  JOIN `brixon-analytics.analytics.identity_graph` ig
    ON e.stape_user_id = ig.identifier
    AND ig.identifier_type = 'anonymous_id'
  WHERE e.event_name IN ('lead_magnet_download', 'quickcheck_complete', 'contact_form_submit')
)

SELECT
  master_uid,
  COUNT(DISTINCT property_id) AS brands_touched,
  ARRAY_AGG(STRUCT(property_id, event_name, event_timestamp) ORDER BY event_timestamp) AS journey,
  MIN(event_timestamp) AS first_conversion,
  MAX(event_timestamp) AS last_conversion,
  COUNTIF(property_id = 'drwerner') AS drwerner_conversions,
  COUNTIF(property_id = 'philippsauerborn') AS philipp_conversions
FROM user_journeys
GROUP BY master_uid
HAVING COUNT(DISTINCT property_id) > 1;
```

## 8.2 Dashboard Struktur

### Executive Dashboard

| Metrik | Datenquelle | Visualisierung |
|--------|-------------|----------------|
| Leads gesamt | vw_lead_funnel | Scorecard |
| MQL Conversion Rate | vw_lead_funnel | Gauge |
| SQL Conversion Rate | vw_lead_funnel | Gauge |
| Lead Funnel | vw_lead_funnel | Funnel Chart |
| Leads nach Kanal | vw_channel_attribution | Pie Chart |
| Trend über Zeit | vw_lead_funnel | Time Series |

### Marketing Deep Dive

| Metrik | Datenquelle | Visualisierung |
|--------|-------------|----------------|
| Campaign Performance | vw_campaign_performance | Table |
| Channel Attribution | vw_channel_attribution | Stacked Bar |
| Conversion Paths | vw_cross_brand_conversions | Sankey |
| Lead Score Distribution | leads | Histogram |
| Top Lead Magnets | events | Bar Chart |

---

# 9. Deployment & DevOps

## 9.1 GCP Ressourcen

```yaml
# terraform/main.tf (vereinfacht)

# BigQuery Dataset
resource "google_bigquery_dataset" "analytics" {
  dataset_id = "analytics"
  project    = "brixon-analytics"
  location   = "EU"

  access {
    role          = "OWNER"
    user_by_email = "analytics-sa@brixon-analytics.iam.gserviceaccount.com"
  }
}

# Cloud Functions
resource "google_cloudfunctions_function" "brevo_webhook" {
  name        = "brevo-webhook-handler"
  runtime     = "nodejs18"
  entry_point = "handleBrevoWebhook"

  trigger_http = true

  environment_variables = {
    BREVO_WEBHOOK_SECRET = var.brevo_webhook_secret
  }
}

resource "google_cloudfunctions_function" "salesforce_webhook" {
  name        = "salesforce-webhook-handler"
  runtime     = "nodejs18"
  entry_point = "handleSalesforceWebhook"

  trigger_http = true
}

resource "google_cloudfunctions_function" "lead_scorer" {
  name        = "lead-score-calculator"
  runtime     = "nodejs18"
  entry_point = "calculateLeadScore"

  event_trigger {
    event_type = "google.pubsub.topic.publish"
    resource   = google_pubsub_topic.lead_scoring.name
  }
}

# Pub/Sub Topic
resource "google_pubsub_topic" "lead_scoring" {
  name = "lead-score-recalculation"
}

# Scheduled Queries
resource "google_bigquery_data_transfer_config" "user_stitching" {
  display_name = "User Stitching Query"
  data_source_id = "scheduled_query"
  schedule = "every 15 minutes"

  params = {
    query = file("${path.module}/queries/user_stitching.sql")
  }
}
```

## 9.2 CI/CD Pipeline

```yaml
# .github/workflows/deploy.yml

name: Deploy Analytics Pipeline

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'

      - name: Install dependencies
        run: npm ci

      - name: Run tests
        run: npm test

      - name: Lint
        run: npm run lint

  deploy-functions:
    needs: test
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Authenticate to GCP
        uses: google-github-actions/auth@v1
        with:
          credentials_json: ${{ secrets.GCP_SA_KEY }}

      - name: Deploy Cloud Functions
        run: |
          gcloud functions deploy brevo-webhook-handler \
            --runtime nodejs18 \
            --trigger-http \
            --allow-unauthenticated \
            --entry-point handleBrevoWebhook \
            --source ./functions/webhooks

          gcloud functions deploy lead-score-calculator \
            --runtime nodejs18 \
            --trigger-topic lead-score-recalculation \
            --entry-point calculateLeadScore \
            --source ./functions/scoring

  deploy-scheduled-queries:
    needs: test
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Authenticate to GCP
        uses: google-github-actions/auth@v1
        with:
          credentials_json: ${{ secrets.GCP_SA_KEY }}

      - name: Deploy Scheduled Queries
        run: |
          bq query --use_legacy_sql=false < ./queries/create_views.sql
```

## 9.3 Monitoring & Alerting

```yaml
# monitoring/alerts.yaml

alerts:
  - name: "High Error Rate - Brevo Webhook"
    condition: >
      error_count > 10 within 5 minutes
    notification:
      slack_channel: "#analytics-alerts"

  - name: "User Stitching Query Failed"
    condition: >
      scheduled_query_status = 'FAILED'
    notification:
      slack_channel: "#analytics-alerts"
      email: "tech@brixon.io"

  - name: "Lead Score Calculation Backlog"
    condition: >
      pubsub_unacked_messages > 1000
    notification:
      slack_channel: "#analytics-alerts"

  - name: "BigQuery Streaming Insert Errors"
    condition: >
      streaming_insert_errors > 0
    notification:
      slack_channel: "#analytics-alerts"
```

---

# 10. Deliverables & Timeline

## 10.1 Deliverables Checkliste

### Phase 1: BigQuery Setup
- [ ] BigQuery Projekt `brixon-analytics` erstellen
- [ ] Dataset `analytics` mit EU-Location
- [ ] Service Account mit nötigen Permissions
- [ ] Alle 6 Tabellen erstellen (events, leads, sessions, users, identity_graph, properties)
- [ ] Partitionierung und Clustering konfigurieren

### Phase 2: Cloud Functions
- [ ] Brevo Webhook Handler
- [ ] Salesforce Webhook Handler
- [ ] Lead Score Calculator
- [ ] Offline Conversion Handler
- [ ] User Stitching API

### Phase 3: Scheduled Queries
- [ ] User Stitching Query (15min)
- [ ] Session Aggregation Query (30min)
- [ ] Lead Score Batch Update (täglich)
- [ ] First Touch Attribution Update (stündlich)

### Phase 4: Views & Reporting
- [ ] vw_lead_funnel
- [ ] vw_channel_attribution
- [ ] vw_campaign_performance
- [ ] vw_unified_user_journey
- [ ] vw_cross_brand_conversions

### Phase 5: Looker Studio
- [ ] Executive Dashboard
- [ ] Marketing Deep Dive Dashboard
- [ ] Sales Pipeline Dashboard
- [ ] BigQuery Connector konfigurieren

### Phase 6: Integration Testing
- [ ] E2E Test: Website → BigQuery
- [ ] E2E Test: Brevo Webhook → BigQuery
- [ ] E2E Test: Salesforce Webhook → Offline Conversion
- [ ] Lead Score Calculation Validierung
- [ ] User Stitching Validierung

## 10.2 Technische Dokumentation

| Dokument | Beschreibung |
|----------|--------------|
| API Reference | OpenAPI Spec für alle Endpoints |
| Schema Documentation | BigQuery Tabellen-Schema |
| Integration Guide | Brevo/Salesforce Setup |
| Runbook | Häufige Operationen & Troubleshooting |

## 10.3 Kontakte

| Rolle | Verantwortung |
|-------|---------------|
| **Tracking Lead** | GTM Setup, Stape Konfiguration |
| **Backend Developer** | Cloud Functions, API Endpoints |
| **Data Engineer** | BigQuery Schema, Scheduled Queries |
| **BI Analyst** | Looker Studio Dashboards |

---

# Anhang A: Environment Variables

```bash
# .env.example

# GCP
GOOGLE_CLOUD_PROJECT=brixon-analytics
GOOGLE_APPLICATION_CREDENTIALS=/path/to/service-account.json

# BigQuery
BIGQUERY_DATASET=analytics

# Brevo
BREVO_API_KEY=xkeysib-xxx
BREVO_WEBHOOK_SECRET=xxx

# Salesforce
SALESFORCE_LOGIN_URL=https://login.salesforce.com
SALESFORCE_USERNAME=integration@company.com
SALESFORCE_PASSWORD=xxx
SALESFORCE_SECURITY_TOKEN=xxx

# Google Ads
GOOGLE_ADS_CLIENT_ID=xxx.apps.googleusercontent.com
GOOGLE_ADS_CLIENT_SECRET=xxx
GOOGLE_ADS_DEVELOPER_TOKEN=xxx
GOOGLE_ADS_CUSTOMER_ID=1234567890
GOOGLE_ADS_REFRESH_TOKEN=xxx

# Meta CAPI
META_PIXEL_ID=xxx
META_ACCESS_TOKEN=xxx

# LinkedIn CAPI
LINKEDIN_PARTNER_ID=xxx
LINKEDIN_ACCESS_TOKEN=xxx
```

---

# Anhang B: SQL Snippets

## Nützliche Queries

### Finde alle Events eines Users
```sql
SELECT * FROM `brixon-analytics.analytics.events`
WHERE property_id = 'drwerner'
  AND stape_user_id = 'uid_abc123'
ORDER BY event_timestamp;
```

### Lead Journey rekonstruieren
```sql
WITH journey AS (
  SELECT
    e.*,
    l.email,
    l.lead_status
  FROM `brixon-analytics.analytics.events` e
  JOIN `brixon-analytics.analytics.leads` l
    ON e.lead_id = l.lead_id
  WHERE l.email = 'max@example.com'
)
SELECT
  event_timestamp,
  event_name,
  page_path,
  traffic_source,
  traffic_campaign
FROM journey
ORDER BY event_timestamp;
```

### Attribution Debug
```sql
SELECT
  l.lead_id,
  l.email,
  l.first_touch_source,
  l.first_touch_campaign,
  l.first_touch_gclid,
  l.conversion_source,
  l.conversion_campaign,
  l.conversion_gclid
FROM `brixon-analytics.analytics.leads` l
WHERE l.property_id = 'drwerner'
  AND l.lead_status IN ('mql', 'sql', 'customer')
ORDER BY l.known_at DESC
LIMIT 100;
```

---

**Ende des Developer Briefings**

*Dokument erstellt: Dezember 2024*
*Version: 1.0*
