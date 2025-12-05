# Tracking & Analytics Dokumentation

Zentrale Dokumentation für das Tracking-Setup von Dr. Werner & Partner und Philipp Sauerborn.

## Dokumente

| Dokument | Beschreibung | Zielgruppe |
|----------|--------------|------------|
| [tracking-konzept-de.md](./tracking-konzept-de.md) | Vollständiges Tracking-Konzept (Deutsch) | Marketing, Tech |
| [tracking-konzept-en.md](./tracking-konzept-en.md) | Complete Tracking Concept (English) | Marketing, Tech |
| [developer-briefing-bigquery.md](./developer-briefing-bigquery.md) | Technisches Briefing für BigQuery Custom Analytics | Developer |
| [server-side-implementation-plan.md](./server-side-implementation-plan.md) | Implementierungsplan Server-Side Tracking | Tech Lead |
| [server-side-migration.md](./server-side-migration.md) | Migrations-Guide von Client zu Server-Side | Tech |

## Architektur-Übersicht

```
┌─────────────────────────────────────────────────────────────┐
│                    TRACKING STACK                           │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Websites                                                   │
│  ├── drwerner.com (GA4 Property 1)                         │
│  └── philippsauerborn.com (GA4 Property 2)                 │
│           │                                                 │
│           ▼                                                 │
│  ┌─────────────────┐                                       │
│  │ GTM Web Container│ (1 pro Website)                      │
│  └────────┬────────┘                                       │
│           │                                                 │
│           ▼                                                 │
│  ┌─────────────────┐                                       │
│  │ Stape Server    │ (Shared Container)                    │
│  │ s.drwerner.com  │                                       │
│  └────────┬────────┘                                       │
│           │                                                 │
│           ├──────────────────┬──────────────────┐          │
│           ▼                  ▼                  ▼          │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐    │
│  │ GA4         │    │ Ad Platforms│    │ BigQuery    │    │
│  │ (2 Props)   │    │ CAPI        │    │ Custom      │    │
│  └─────────────┘    └─────────────┘    └─────────────┘    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

## Komponenten

### 1. GTM Web Container
- Event-Erfassung auf Website
- DataLayer Management
- Consent Mode v2 Integration

### 2. Stape Server Container
- Server-Side Event Processing
- Cookie Keeper (ITP Fix)
- Multi-Destination Routing

### 3. Ad Platform CAPI
- Google Ads Enhanced Conversions
- Meta Conversions API
- LinkedIn Conversions API

### 4. BigQuery Custom Analytics
- Multi-Tenant Event Storage
- User Identity Stitching
- Lead Scoring
- Attribution Modeling

### 5. CRM Integration
- Brevo (Marketing Automation)
- Salesforce (Sales CRM)
- Offline Conversion Upload

## Verwandte Ordner

- [`/tracking`](../../tracking/) - GTM Templates, Stape Config
- [`/tools/quickcheck-malta`](../../tools/quickcheck-malta/) - QuickCheck mit Tracking-Integration
