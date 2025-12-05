# Tracking-Konzept DrWerner.com

## Ausführliches Server-Side Tracking & Marketing Automation Konzept

**Erstellt für:** DrWerner.com (Steuerkanzlei für Firmengründung & Auswanderung)
**Zweite Property:** philippsauerborn.com (Thought Leadership Blog)
**Datum:** Dezember 2024

---

# Inhaltsverzeichnis

1. [Executive Summary](#1-executive-summary)
2. [Ausgangssituation & Ziele](#2-ausgangssituation--ziele)
3. [Technische Architektur Übersicht](#3-technische-architektur-übersicht)
4. [Server-Side Tracking mit Stape](#4-server-side-tracking-mit-stape)
5. [Consent Management (Usercentrics/Cookiebot)](#5-consent-management-usercentricsookiebot)
6. [Event-Tracking Konzept](#6-event-tracking-konzept)
7. [Ad-Platform Integrationen](#7-ad-platform-integrationen)
8. [Lead Management & Nurturing](#8-lead-management--nurturing)
9. [CRM-Strategie (Salesforce + Brevo)](#9-crm-strategie-salesforce--brevo)
10. [Custom Analytics mit BigQuery](#10-custom-analytics-mit-bigquery)
11. [Cross-Domain Tracking](#11-cross-domain-tracking)
12. [Cookie Keeper & First-Party Data](#12-cookie-keeper--first-party-data)
13. [Datenschutz & DSGVO](#13-datenschutz--dsgvo)
14. [Phasenplan zur Umsetzung](#14-phasenplan-zur-umsetzung)
15. [Technische Checklisten](#15-technische-checklisten)

---

# 1. Executive Summary

Dieses Konzept beschreibt eine moderne, datenschutzkonforme Tracking-Infrastruktur für DrWerner.com und philippsauerborn.com. Der Kern ist **Server-Side Tracking über Stape**, das die Abhängigkeit von Browser-Cookies reduziert und präziseres Tracking ermöglicht.

### Kernkomponenten:
- **Server-Side GTM** über Stape (s.drwerner.com)
- **Consent Mode v2** mit Usercentrics Cookiebot
- **Multi-Platform Tracking**: Google Ads, Meta Ads, LinkedIn Ads
- **Marketing Automation**: Brevo für MQL-Nurturing
- **CRM**: Salesforce für SQLs und Sales-Pipeline
- **Custom Analytics**: BigQuery für vollständige Customer Journey

### Erwartete Vorteile:
1. **Bessere Datenqualität**: Server-Side Tracking umgeht Ad-Blocker teilweise
2. **Längere Cookie-Lebensdauer**: Bis zu 2 Jahre statt 7 Tage (Safari ITP)
3. **Präzisere Attribution**: Cross-Session und Cross-Device Tracking
4. **DSGVO-Konformität**: Consent-gesteuerte Datenverarbeitung
5. **Vertriebstransparenz**: Vollständige Lead-Journey sichtbar

---

# 2. Ausgangssituation & Ziele

## 2.1 Aktuelle Situation

| Aspekt | Status |
|--------|--------|
| **Website** | WordPress auf drwerner.com |
| **Zweiter Blog** | philippsauerborn.com (Thought Leadership) |
| **CRM** | Salesforce (nur SQLs/Direktanfragen) |
| **Analytics** | Google Analytics (Client-Side) |
| **Ads** | Google Ads aktiv |
| **Consent** | Usercentrics Cookiebot |
| **Server-Side** | Stape-Container vorhanden, s.drwerner.com konfiguriert |

## 2.2 Herausforderungen

1. **SEO-Rückgang**: Organischer Traffic sinkt, Paid-Strategie wird wichtiger
2. **Cookie-Einschränkungen**: Safari ITP, Firefox ETP, Chrome Privacy Sandbox
3. **Keine MQLs**: Bisher nur Direktanfragen (SQLs), keine Lead-Nurturing-Pipeline
4. **Fehlende Transparenz**: Vertrieb sieht nicht, was Leads vor der Anfrage gemacht haben
5. **Cross-Domain-Blindspot**: Aktivitäten zwischen beiden Domains nicht verknüpft

## 2.3 Ziele

### Kurzfristig (Phase 1-2)
- Server-Side Tracking vollständig implementieren
- Meta Ads und LinkedIn Ads integrieren
- Lead Magnet Tracking über Vavolta
- Brevo für E-Mail-Nurturing einrichten

### Mittelfristig (Phase 3-4)
- MQL-Pipeline in Salesforce abbilden
- Lead Scoring implementieren
- BigQuery Customer Journey aufbauen
- Cross-Domain Tracking

### Langfristig
- Vollständige Customer Journey Transparenz
- Automatisierte Lead-Qualifizierung
- Revenue Attribution über alle Kanäle

---

# 3. Technische Architektur Übersicht

## 3.1 Systemarchitektur-Diagramm

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
│                            Consent-gesteuerte Events                         │
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

## 3.2 Datenfluss-Erklärung

### Was passiert technisch?

1. **Browser → Server (First-Party)**
   - Nutzer besucht drwerner.com
   - Web GTM erfasst Events (Pageviews, Klicks, Formulare)
   - Events werden an s.drwerner.com gesendet (NICHT an google-analytics.com)
   - **Vorteil**: Sieht für Browser wie eigener Server aus → keine Third-Party-Blockierung

2. **Server → Plattformen**
   - Stape-Server empfängt Events
   - Cookie Keeper setzt/erneuert langlebige First-Party Cookies
   - Server sendet Daten an Google, Meta, LinkedIn APIs
   - **Vorteil**: Ad-Blocker blockieren Client-Side Pixel, nicht Server-Requests

3. **Server → BigQuery**
   - Jeder Event wird zusätzlich an BigQuery gestreamt
   - Vollständige Customer Journey gespeichert
   - Eigene Analysen und Reports möglich

---

# 4. Server-Side Tracking mit Stape

## 4.1 Was ist Server-Side Tracking?

### Das Problem mit Client-Side Tracking

Traditionelles Tracking funktioniert so:
```
Browser → JavaScript Pixel → Direkt an Google/Meta/LinkedIn
```

**Probleme dabei:**
- **Ad-Blocker**: Blockieren bekannte Tracking-Domains
- **Safari ITP**: Löscht Third-Party Cookies nach 7 Tagen
- **Firefox ETP**: Blockiert Third-Party Tracker standardmäßig
- **Chrome Privacy Sandbox**: Third-Party Cookies werden 2024/2025 eingestellt

### Die Server-Side Lösung

```
Browser → Eigene Subdomain (s.drwerner.com) → Stape Server → Plattformen
```

**Vorteile:**
- **First-Party Context**: Browser sieht Anfrage an eigene Domain
- **Längere Cookies**: Server kann Cookies mit langer Laufzeit setzen
- **Keine Ad-Blocker-Blockierung**: Server-Kommunikation wird nicht blockiert
- **Datenkontrolle**: Ihr entscheidet, welche Daten wohin gehen

## 4.2 Stape-Konfiguration (bereits vorhanden)

### Aktuelle Infrastruktur
- **Container-Domain**: s.drwerner.com
- **Stape-Zone**: EU (euj.stape.io) - DSGVO-konform
- **SSL**: Automatisch über Stape

### Benötigte Power-Ups

| Power-Up | Zweck | Status |
|----------|-------|--------|
| **Cookie Keeper** | Verlängert Cookie-Lebensdauer auf 2 Jahre | Aktivieren |
| **Geo Headers** | Land/Region für Targeting | Optional |
| **Bot Detection** | Filtert Bot-Traffic | Empfohlen |

## 4.3 GTM Container-Struktur (Ausführlich)

Der Google Tag Manager ist das zentrale Nervensystem des gesamten Tracking-Setups. Er besteht aus zwei Containern, die zusammenarbeiten: Der **Web Container** läuft im Browser des Besuchers und erfasst alle Interaktionen. Der **Server Container** läuft auf dem Stape-Server und verarbeitet diese Daten, bevor sie an die verschiedenen Plattformen weitergeleitet werden.

### Warum zwei Container?

Die Aufteilung in Web und Server Container hat einen entscheidenden Vorteil: Der Browser sieht nur Anfragen an die eigene Domain (s.drwerner.com), nicht an Google, Meta oder LinkedIn. Das umgeht Ad-Blocker und Browser-Einschränkungen wie Safari ITP. Außerdem behält DrWerner die volle Kontrolle darüber, welche Daten wohin fließen.

---

### Web Container (Client-Side) - Detaillierte Erklärung

Der Web Container ist das "Ohr" des Systems. Er lauscht auf alles, was im Browser passiert, und sendet strukturierte Informationen an den Server.

#### Tags im Web Container

| Tag | Zweck | Warum wichtig? |
|-----|-------|----------------|
| **GA4 Configuration** | Initialisiert GA4 und sendet alle Events an den Server-Endpoint (s.drwerner.com statt an Google direkt) | Ohne diesen Tag würden alle Events direkt an Google gehen und könnten von Ad-Blockern blockiert werden. Mit Server-Endpoint sieht der Browser nur eine First-Party-Anfrage. |
| **Consent Mode Default** | Setzt den initialen Consent-Status auf "granted" für alle Kategorien | Google verlangt seit März 2024 Consent-Signale. Ohne diesen Tag würde Google Ads Remarketing und Smart Bidding nicht funktionieren. Default "granted" bedeutet: Tracking startet sofort, Cookiebot kann später überschreiben. |
| **DataLayer Push Tags** | Strukturieren Event-Daten wenn bestimmte Aktionen passieren (Formular abgesendet, Button geklickt) | Der DataLayer ist die "Sprache" zwischen Website und GTM. Ohne strukturierte Daten wüsste GTM nicht, was ein Lead Magnet Download ist oder welcher QuickCheck abgeschlossen wurde. |
| **Stape User ID Tag** | Liest die vom Server gesetzte User ID und macht sie für alle Tags verfügbar | Ermöglicht Cross-Session Tracking. Ohne diese ID wäre jeder Besuch nach 7 Tagen (Safari) ein "neuer" User. |
| **Click ID Persistence Tag** | Speichert GCLID/FBCLID im localStorage und DataLayer | Click IDs aus der URL gehen verloren, sobald der User auf eine andere Seite navigiert. Dieser Tag sorgt dafür, dass die Attribution auch bei späteren Conversions funktioniert. |

#### Trigger im Web Container

Trigger definieren **WANN** ein Tag feuern soll. Jeder Trigger ist wie ein Wächter, der auf bestimmte Ereignisse wartet.

| Trigger | Wann feuert er? | Business-Relevanz |
|---------|-----------------|-------------------|
| **Consent Initialization** | Sofort beim Laden der Seite, noch vor allem anderen | Muss als allererstes feuern, damit Google Consent Mode aktiv ist, bevor irgendein Tag lädt. |
| **All Pages** | Bei jedem Seitenaufruf | Basis für Pageview-Tracking, Session-Erkennung, Traffic-Source-Erfassung |
| **Elementor Form Submit (Thank You Page)** | Wenn User auf einer Thank-You-Page landet | Zuverlässiger als DOM-Visibility-Trigger. Erfasst Kontaktanfragen (SQL) und Newsletter-Signups. |
| **Lead Magnet Request (Elementor Form)** | Wenn Thank-You-Page nach Lead-Magnet-Formular erscheint | Erfasst MQLs. Jeder Lead Magnet Request ist ein qualifizierter Lead, der in die Nurturing-Pipeline kommt. Zustellung erfolgt via automatisiertem Vavolta-Link. |
| **QuickCheck Complete** | Wenn User QuickCheck abschließt | Zeigt hohes Interesse. Ergebnis (Malta/Zypern geeignet) hilft bei Personalisierung und Vertriebspriorisierung. |
| **CTA Click** | Wenn wichtige Buttons geklickt werden | Misst Engagement. Zeigt, welche CTAs funktionieren und wo User im Funnel abspringen. |
| **Service Page View** | Wenn User Leistungsseiten besucht | Intent-Signal. User die Leistungsseiten besuchen, sind weiter im Funnel als reine Blog-Leser. |
| **Scroll Depth 50%** | Wenn User mindestens 50% einer Seite scrollt | Qualitätssignal für Content. Hilft zu verstehen, ob Seiten wirklich gelesen werden. |

#### Variables im Web Container

Variables sind die "Datenträger". Sie extrahieren und speichern Informationen, die Tags und Trigger benötigen.

| Variable | Was speichert sie? | Wozu wird sie gebraucht? |
|----------|-------------------|--------------------------|
| **Consent State - Analytics** | granted/denied für analytics_storage | Bestimmt, ob GA4 User-Daten senden darf |
| **Consent State - Marketing** | granted/denied für ad_storage, ad_user_data, ad_personalization | Bestimmt, ob Werbe-Cookies gesetzt werden dürfen |
| **GCLID Persistent** | Google Click ID (aus URL oder localStorage) | Wird an Server gesendet für Google Ads Attribution |
| **FBCLID Persistent** | Meta Click ID (aus URL oder localStorage) | Wird an Server gesendet für Meta CAPI Attribution |
| **UTM Parameters** | utm_source, utm_medium, utm_campaign, utm_term, utm_content | Traffic-Attribution für alle Kanäle |
| **User Email (wenn bekannt)** | E-Mail-Adresse aus Formular | Für Enhanced Conversions (gehashed an Server) |
| **Lead Magnet Name** | Name des heruntergeladenen Assets | Für Segmentierung und Nurturing-Trigger |
| **QuickCheck Result** | Ergebnis des QuickChecks | Für personalisierte Follow-up und Salesforce |
| **Page Path** | Aktueller URL-Pfad | Für Trigger-Bedingungen und Event-Parameter |
| **Referrer** | Woher kam der User? | First-Touch Attribution |

---

### Server Container (Server-Side) - Detaillierte Erklärung

Der Server Container ist das "Gehirn" des Systems. Er empfängt alle Events vom Web Container, reichert sie an, und verteilt sie an die richtigen Empfänger.

#### Clients im Server Container

Clients sind die "Eingangstore" des Server Containers. Sie empfangen und interpretieren eingehende Requests.

| Client | Funktion | Warum wichtig? |
|--------|----------|----------------|
| **GA4 Client** | Empfängt alle GA4-Events vom Web Container | Der Standard-Client, der die meisten Events verarbeitet. Versteht das GA4-Protokoll und extrahiert alle Event-Daten. |
| **Stape Cookie Keeper Client** | Setzt/erneuert First-Party Cookies via HTTP Response | Der Schlüssel zur Safari ITP Umgehung. Ohne diesen Client würden Cookies nach 7 Tagen gelöscht. |

#### Tags im Server Container

Jeder Tag ist eine "Ausgangstür" zu einer anderen Plattform. Der Server Container entscheidet, welche Daten wohin gehen.

| Tag | Ziel-Plattform | Welche Events? | Warum Server-Side? |
|-----|----------------|----------------|-------------------|
| **GA4 Server Tag** | Google Analytics | Alle Events | Server-Side GA4 ist zuverlässiger, wird nicht von Ad-Blockern blockiert, und ermöglicht längere Cookie-Lebensdauer |
| **Google Ads Enhanced Conversions** | Google Ads | Nur Conversions (lead_magnet_download, contact_form_submit, consultation_booking) | Enhanced Conversions benötigen gehashte User-Daten. Server-Side Hashing ist sicherer und zuverlässiger. Verbessert Attribution um bis zu 20%. |
| **Meta CAPI** | Facebook/Instagram Ads | Nur Conversions | Meta CAPI umgeht iOS 14.5 App Tracking Transparency. Ohne CAPI wären 30-50% der iOS-User nicht trackbar. |
| **LinkedIn CAPI** | LinkedIn Ads | Nur Conversions | Besonders wichtig für B2B. LinkedIn-User sind oft genau die Zielgruppe von DrWerner (Unternehmer, Selbstständige). |
| **Brevo HTTP Request** | Brevo (E-Mail Marketing) | Lead-Events (lead_magnet_download, newsletter_signup, quickcheck_complete) | Ermöglicht automatische Nurturing-Workflows. Ein Lead Magnet Download triggert sofort die richtige E-Mail-Sequenz. |
| **BigQuery HTTP Request** | BigQuery (Eigene Analytics) | ALLE Events | Consent-unabhängig! Eigene Datenverarbeitung ohne Weitergabe an Dritte. Volle Customer Journey auch für User die Consent ablehnen. |
| **Microsoft Clarity Tag** | Microsoft Clarity | Alle Pageviews | Heatmaps und Session Recordings für UX-Optimierung. Kostenlos und DSGVO-konform. |

#### Trigger im Server Container

Server-Side Trigger entscheiden, welche Tags bei welchen Events feuern.

| Trigger | Wann feuert er? | Welche Tags feuern? |
|---------|-----------------|---------------------|
| **All GA4 Events** | Bei jedem eingehenden GA4-Event | GA4 Server Tag, BigQuery Tag |
| **Conversion Events Only** | Nur bei: lead_magnet_download, contact_form_submit, newsletter_signup, quickcheck_complete, consultation_booking | Google Ads, Meta CAPI, LinkedIn CAPI |
| **Lead Events Only** | Bei: lead_magnet_download, newsletter_signup, quickcheck_complete | Brevo HTTP Request |
| **SQL Events Only** | Bei: contact_form_submit, consultation_booking | Salesforce (via Brevo oder direkt) |
| **Consent Granted - Marketing** | Nur wenn consent_marketing = "granted" | Google Ads, Meta CAPI, LinkedIn CAPI (für User die explizit zugestimmt haben) |
| **Always Fire (No Consent Check)** | Bei JEDEM Event, unabhängig vom Consent | BigQuery Tag (eigene Datenverarbeitung) |

#### Variables im Server Container

Server-Side Variables verarbeiten und transformieren Daten vor dem Versand.

| Variable | Funktion | Verwendung |
|----------|----------|------------|
| **Hashed Email** | SHA-256 Hash der E-Mail-Adresse | Google Enhanced Conversions, Meta CAPI, LinkedIn CAPI - Plattformen akzeptieren nur gehashte PII |
| **Hashed Phone** | SHA-256 Hash der Telefonnummer | Google Enhanced Conversions - verbessert Matching-Rate |
| **GCLID** | Google Click ID aus Request | Google Ads Attribution - verknüpft Conversion mit Klick |
| **FBCLID** | Meta Click ID aus Request | Meta CAPI Attribution - verknüpft Conversion mit Klick |
| **LinkedIn FAT ID** | LinkedIn First-Party Ad Tracking ID | LinkedIn CAPI Attribution |
| **Property ID** | Lookup: Hostname → Property ID (z.B. drwerner.com → "drwerner") | Multi-Tenant BigQuery - trennt Daten verschiedener Kunden |
| **Stape User ID** | Persistente User ID aus HTTP Header | Cross-Session User-Identifikation, Safari ITP resistant |
| **Event ID** | Unique ID für jedes Event | Deduplizierung wenn sowohl Browser-Pixel als auch Server-Side feuern |

---

### Zusammenspiel der Container (Datenfluss-Beispiel)

**Szenario: User lädt Malta-Checkliste herunter**

```
1. USER AKTION
   User füllt Elementor-Formular auf drwerner.com aus, gibt E-Mail ein, klickt "Absenden"
   → Weiterleitung auf Thank-You-Page (/danke-download/?asset=malta-checkliste)
                                    │
                                    ▼
2. WEB CONTAINER (Browser)
   ├── Thank-You-Page Trigger erkennt erfolgreiche Formular-Absendung
   ├── DataLayer Push Tag schreibt Event-Daten:
   │   {
   │     event: 'lead_magnet_download',
   │     lead_magnet_name: 'Malta Checkliste',
   │     user_email: 'max@example.com',
   │     gclid: 'abc123...',  ← aus localStorage (vorher persistiert)
   │     utm_source: 'google',
   │     utm_campaign: 'malta_firmengründung'
   │   }
   └── GA4 Configuration Tag sendet Event an s.drwerner.com

   PARALLEL: Automation sendet E-Mail mit einzigartigem Vavolta-Link an User
   → Vavolta trackt später Engagement (PDF-Views, Downloads) separat
                                    │
                                    ▼
3. SERVER CONTAINER (Stape)
   ├── GA4 Client empfängt Event
   ├── Cookie Keeper erneuert User-Cookies (2 Jahre Laufzeit)
   ├── Variables verarbeiten Daten:
   │   ├── Email hashen → SHA256
   │   ├── GCLID extrahieren
   │   ├── Property ID lookup → "drwerner"
   │   └── Stape User ID auslesen
   │
   ├── Tags feuern parallel:
   │   ├── GA4 Server Tag → Google Analytics (Event erscheint in GA4)
   │   ├── Google Ads Tag → Enhanced Conversion mit gehashter Email + GCLID
   │   ├── Meta CAPI → Lead Event mit gehashter Email
   │   ├── Brevo HTTP → Neuer Kontakt + Lead Magnet Download Event
   │   └── BigQuery → Vollständiger Event-Record mit allen Parametern
                                    │
                                    ▼
4. DOWNSTREAM SYSTEME
   ├── GA4: Event sichtbar, Lead wird gezählt
   ├── Google Ads: Conversion getrackt, Smart Bidding lernt
   ├── Meta Ads: Lead Conversion getrackt, Lookalike Audience wächst
   ├── Brevo: Nurturing-Workflow startet (Tag 0: Download-Email)
   ├── BigQuery: Event in Customer Journey gespeichert
   └── Salesforce: (via Brevo Sync wenn Score >= 80)
```

---

### Architektur-Diagramm (Container-Struktur)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           GTM WEB CONTAINER                                  │
│                     (läuft im Browser des Users)                            │
├─────────────────────────────────────────────────────────────────────────────┤
│  TAGS                          │  TRIGGERS                                   │
│  ─────                         │  ────────                                   │
│  • GA4 Configuration           │  • Consent Initialization (first!)          │
│  • Consent Mode Default        │  • All Pages                                │
│  • DataLayer Push Tags         │  • Elementor Form → Thank You Page          │
│  • Click ID Persistence        │  • Vavolta Success                          │
│  • Stape User ID Reader        │  • QuickCheck Complete                      │
│                                │  • CTA Click                                │
│                                │  • Service Page View                        │
├────────────────────────────────┴────────────────────────────────────────────┤
│  VARIABLES                                                                   │
│  ─────────                                                                   │
│  • Consent States (analytics, marketing)                                     │
│  • GCLID/FBCLID Persistent (aus localStorage)                               │
│  • UTM Parameters                                                            │
│  • User Email, Lead Magnet Name, QuickCheck Result                          │
│  • Page Path, Referrer, Page Title                                          │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    │ HTTPS Request an s.drwerner.com
                                    │ (First-Party, nicht blockierbar)
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                          GTM SERVER CONTAINER                                │
│                      (läuft auf Stape EU Server)                            │
├─────────────────────────────────────────────────────────────────────────────┤
│  CLIENTS                                                                     │
│  ───────                                                                     │
│  • GA4 Client (empfängt GA4-Protokoll Events)                               │
│  • Stape Cookie Keeper (setzt HTTP-Header Cookies)                          │
├─────────────────────────────────────────────────────────────────────────────┤
│  TAGS                          │  TRIGGERS                                   │
│  ─────                         │  ────────                                   │
│  • GA4 Server Tag              │  • All GA4 Events                           │
│  • Google Ads Enhanced Conv.   │  • Conversion Events Only                   │
│  • Meta CAPI                   │  • Lead Events Only                         │
│  • LinkedIn CAPI               │  • SQL Events Only                          │
│  • Brevo HTTP Request          │  • Always Fire (BigQuery)                   │
│  • BigQuery HTTP Request       │  • Consent Granted - Marketing              │
│  • Microsoft Clarity           │                                             │
├────────────────────────────────┴────────────────────────────────────────────┤
│  VARIABLES                                                                   │
│  ─────────                                                                   │
│  • Hashed Email/Phone (SHA-256)                                             │
│  • GCLID, FBCLID, LinkedIn FAT ID                                           │
│  • Property ID (Lookup Table)                                                │
│  • Stape User ID (X-STAPE-USER-ID Header)                                   │
│  • Event ID (für Deduplizierung)                                            │
│  • All UTM Parameters                                                        │
└─────────────────────────────────────────────────────────────────────────────┘
                │           │           │           │           │
                ▼           ▼           ▼           ▼           ▼
           ┌────────┐  ┌────────┐  ┌────────┐  ┌────────┐  ┌────────┐
           │  GA4   │  │ Google │  │  Meta  │  │LinkedIn│  │ Brevo  │
           │        │  │  Ads   │  │  CAPI  │  │  CAPI  │  │        │
           └────────┘  └────────┘  └────────┘  └────────┘  └────────┘
                                        │
                                        ▼
                                   ┌────────┐
                                   │BigQuery│ ← Consent-unabhängig!
                                   │        │   Alle Events, alle User
                                   └────────┘
```

---

# 5. Consent Management (Usercentrics/Cookiebot)

## 5.1 Ansatz: Default Consent "Granted"

> **Hinweis**: Für maximale Tracking-Transparenz und Datenqualität wird Consent standardmäßig auf "granted" gesetzt. Der Cookie-Banner bleibt aktiv, damit Google Consent Mode v2 die korrekten Signale erhält.

### Warum Cookie-Banner trotzdem nötig?

**Google Consent Mode v2 (Pflicht seit März 2024)**

Google verlangt für Werbetreibende die Übermittlung von Consent-Signalen. Ohne diese Signale:
- Google Ads Remarketing funktioniert eingeschränkt
- Conversion-Tracking ist unvollständig
- Smart Bidding hat weniger Daten

| Consent-Typ | Beschreibung | Default-Wert |
|-------------|--------------|--------------|
| `ad_storage` | Werbe-Cookies speichern | **granted** |
| `ad_user_data` | Nutzerdaten an Google senden | **granted** |
| `ad_personalization` | Personalisierte Werbung | **granted** |
| `analytics_storage` | Analytics-Cookies speichern | **granted** |

## 5.2 Cookiebot-Konfiguration (Default Granted)

### Cookie-Kategorien Mapping

```
Cookiebot Kategorien → Google Consent Mode v2
─────────────────────────────────────────────
Notwendig       → security_storage, functionality_storage (immer aktiv)
Präferenzen     → personalization_storage (default: granted)
Statistik       → analytics_storage (default: granted)
Marketing       → ad_storage, ad_user_data, ad_personalization (default: granted)
```

### WordPress Plugin Einstellungen

1. **Auto-Blocking DEAKTIVIEREN**: Alle Skripte laden sofort
2. **IAB TCF 2.2 deaktivieren**: Nicht nötig
3. **GTM Integration**: Cookiebot im GTM mit Default "granted"
4. **Banner-Design**: "Akzeptieren" prominent, Ablehnung möglich aber nicht prominent

## 5.3 Technische Implementation (Default Granted)

### Consent-Flow im GTM

```
1. Seite lädt
   │
2. GTM Consent Initialization Trigger feuert SOFORT
   │
3. Default Consent State wird gesetzt:
   │  - analytics_storage: GRANTED ✓
   │  - ad_storage: GRANTED ✓
   │  - ad_user_data: GRANTED ✓
   │  - ad_personalization: GRANTED ✓
   │
4. ALLE GTM Tags feuern sofort (kein Warten auf Banner-Klick)
   │
5. Cookiebot Banner erscheint (für User die ablehnen wollen)
   │
6. Falls User "Ablehnen" klickt:
   │  → Consent State wird auf "denied" aktualisiert
   │  → Zukünftige Events ohne User-Identifier
   │
7. Server-Side Container erhält Consent-Parameter (immer "granted" beim ersten Hit)
```

### GTM Tag: Consent Initialization (Default Granted)

```javascript
// Im GTM Web Container - Consent Initialization Trigger
// Tag-Typ: Google Tag - Consent Initialization

gtag('consent', 'default', {
  'ad_storage': 'granted',
  'ad_user_data': 'granted',
  'ad_personalization': 'granted',
  'analytics_storage': 'granted',
  'functionality_storage': 'granted',
  'personalization_storage': 'granted',
  'security_storage': 'granted',
  'wait_for_update': 500  // Cookiebot hat 500ms um zu überschreiben
});
```

### Server-Side Handling

Im Server Container:
- Consent-Parameter kommen als Teil des GA4-Hits
- Bei Default "granted": Alle User-Daten werden gesendet
- Nur bei explizitem "denied": Anonymisierte Events

**Ergebnis:**
- 100% der Erstbesucher werden vollständig getrackt
- Nur User die aktiv ablehnen, werden anonymisiert
- Google erhält korrekte Consent-Signale für Compliance

## 5.4 Cookie-Banner Alternativen Evaluation

### Warum diese Evaluation?

Der Cookie-Banner ist kein Nice-to-have – er ist seit März 2024 **technisch erforderlich** für Google Ads Conversion Tracking (Consent Mode v2). Die Frage ist nicht ob, sondern welche Lösung am besten passt.

### Vergleich der Optionen

| Kriterium | Cookiebot | Usercentrics | Borlabs Cookie | Complianz |
|-----------|-----------|--------------|----------------|-----------|
| **Setup-Komplexität** | ⭐⭐ Einfach | ⭐⭐⭐ Mittel | ⭐⭐ Einfach | ⭐⭐ Einfach |
| **Consent Mode v2** | ✓ Nativ | ✓ Nativ | ✓ Mit Config | ✓ Mit Config |
| **GTM Integration** | ✓ Sehr gut | ✓ Sehr gut | ✓ Gut | ⚠ Eingeschränkt |
| **WordPress Plugin** | ✓ Ja | ✓ Ja | ✓ Ja (WP-only) | ✓ Ja (WP-only) |
| **Auto-Blocking** | ✓ Ja | ✓ Ja | ✓ Ja | ✓ Ja |
| **Preis/Monat** | €9-15 | €9-25 | €39/Jahr | €45/Jahr |
| **DSGVO-Scan** | ✓ Automatisch | ✓ Automatisch | ⚠ Manuell | ⚠ Manuell |
| **Server-Side Support** | ✓ Gut | ✓ Sehr gut | ⚠ Begrenzt | ⚠ Begrenzt |

### Empfehlung für DrWerner.com: Cookiebot

**Warum Cookiebot die beste Wahl ist:**

1. **Bereits im Einsatz**: Keine Migration nötig, Team kennt das Tool
2. **Einfachstes Setup**: WordPress Plugin + GTM Tag, fertig
3. **Automatischer Cookie-Scan**: Erkennt neue Cookies automatisch
4. **Native Consent Mode v2 Integration**: Kein Custom-Code nötig
5. **Faire Preisgestaltung**: €9/Monat für die benötigte Funktionalität
6. **Server-Side kompatibel**: Consent-Parameter werden korrekt an Stape weitergeleitet

### Alternativen – wann sinnvoll?

**Usercentrics wählen, wenn:**
- Mehrere Domains/Brands zentral verwaltet werden sollen
- Enterprise-Features (A/B-Testing, Analytics) gewünscht
- Bereits Usercentrics-Erfahrung im Team

**Borlabs Cookie wählen, wenn:**
- Nur WordPress, keine anderen Plattformen
- Einmalzahlung statt Abo bevorzugt
- Volle Kontrolle über Cookie-Kategorien gewünscht

**Complianz wählen, wenn:**
- Minimales Budget (kostenlose Version verfügbar)
- Einfache WordPress-Site ohne komplexes Tracking

### Setup-Checkliste Cookiebot (Einfaches Setup)

```
COOKIEBOT SCHNELL-SETUP
───────────────────────

1. COOKIEBOT ACCOUNT
   □ Account erstellen auf cookiebot.com
   □ Domain hinzufügen: drwerner.com
   □ Ersten Cookie-Scan abwarten (~24h)

2. WORDPRESS PLUGIN
   □ Plugin "Cookiebot" installieren
   □ Cookiebot ID eintragen
   □ Auto-Blocking: DEAKTIVIEREN (wir nutzen GTM)
   □ IAB TCF: DEAKTIVIEREN

3. GTM WEB CONTAINER
   □ Cookiebot Tag aus Template Gallery hinzufügen
   □ Trigger: Consent Initialization - All Pages
   □ Cookiebot ID als Variable anlegen

4. CONSENT MODE CONFIG
   □ Default Consent Tag erstellen (siehe 5.3)
   □ wait_for_update: 500ms
   □ Alle Werte auf "granted" setzen

5. TESTEN
   □ GTM Preview Mode aktivieren
   □ Banner erscheint
   □ Consent-Signale in GA4 DebugView prüfen
   □ Server-Container empfängt Consent-Parameter

ZEITAUFWAND: ~2 Stunden für Basis-Setup
```

### Banner-Design Empfehlung

Für maximale Akzeptanz bei Default "granted":

```
┌─────────────────────────────────────────────────────────────┐
│  🍪 Cookie-Einstellungen                                    │
│                                                             │
│  Wir nutzen Cookies, um Ihnen die bestmögliche             │
│  Erfahrung auf unserer Website zu bieten.                  │
│                                                             │
│  [██████ Alle akzeptieren ██████]  ← Prominent, farbig     │
│                                                             │
│  [Einstellungen]  [Nur notwendige]  ← Klein, dezent        │
│                                                             │
│  Mehr in unserer Datenschutzerklärung                      │
└─────────────────────────────────────────────────────────────┘
```

**Wichtig**: "Alle akzeptieren" muss der optisch dominante Button sein. "Nur notwendige" darf existieren (DSGVO), aber nicht gleich prominent.

## 5.5 Microsoft Clarity Integration

### Was ist Microsoft Clarity?

Microsoft Clarity ist ein **kostenloses** Behavior Analytics Tool, das ergänzend zu GA4 eingesetzt werden kann. Es bietet:

- **Session Recordings**: Echte Nutzer-Sessions als Video ansehen
- **Heatmaps**: Klick-, Scroll- und Bewegungs-Heatmaps
- **Rage Clicks**: Erkennung von frustrierten Klicks
- **Dead Clicks**: Klicks auf nicht-interaktive Elemente
- **Quick Backs**: Nutzer, die sofort zurücknavigieren

### Warum Clarity zusätzlich zu GA4?

| Feature | GA4 | Microsoft Clarity |
|---------|-----|-------------------|
| **Session Recordings** | ✗ Nicht verfügbar | ✓ Unbegrenzt kostenlos |
| **Heatmaps** | ✗ Nicht verfügbar | ✓ Klick, Scroll, Attention |
| **Funnel-Analyse** | ✓ Ja | ✗ Eingeschränkt |
| **Attribution** | ✓ Ja | ✗ Nein |
| **UX-Probleme finden** | ⚠ Indirekt | ✓ Direkt sichtbar |
| **Kosten** | Kostenlos | Kostenlos |
| **DSGVO** | ⚠ US-Server (Google) | ⚠ US-Server (Microsoft) |

**Empfehlung**: Clarity für qualitative UX-Insights, GA4 für quantitative Analyse.

### Consent Mode Integration

Microsoft Clarity unterstützt **Google Consent Mode v2 nativ** seit 2024. Das bedeutet:

1. Clarity respektiert automatisch den Consent-Status aus dem Cookie-Banner
2. Bei `analytics_storage: denied` werden keine Session Recordings erstellt
3. Bei `analytics_storage: granted` (Default) volle Funktionalität

### GTM Implementation (Consent-Ready)

#### Option A: Über GTM Web Container (Empfohlen)

```
GTM Web Container - Microsoft Clarity Tag
─────────────────────────────────────────
Tag-Typ:           Custom HTML ODER Community Template "Microsoft Clarity"
Clarity Project ID: [Aus Clarity Dashboard]

Trigger:           Consent Initialization - All Pages
                   (Feuert bei granted UND denied, Clarity handhabt Consent intern)

Consent Settings:
├── Built-in Consent Checks: Aktiviert
├── analytics_storage: Required
└── ad_storage: Not required
```

**Community Template verwenden:**
1. GTM → Tags → Neu → Tag-Typ suchen → "Microsoft Clarity"
2. Community Template von "nickultra" oder offizielle Version wählen
3. Nur Project ID eintragen
4. Consent Mode wird automatisch respektiert

#### Option B: Clarity Tracking Code (Fallback)

Falls kein GTM verfügbar, kann Clarity auch direkt eingebunden werden. Der Code prüft dann den Cookiebot-Status:

```javascript
// Nur laden wenn analytics_storage = granted
if (typeof Cookiebot !== 'undefined' && Cookiebot.consent.statistics) {
  (function(c,l,a,r,i,t,y){
    c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
  })(window, document, "clarity", "script", "CLARITY_PROJECT_ID");
}
```

**Hinweis**: Bei Default "granted" Ansatz wird Clarity immer geladen, da Consent initial auf granted steht.

### Clarity + Cookiebot Zusammenspiel

```
Consent-Flow mit Clarity:
─────────────────────────
1. Seite lädt
   │
2. Cookiebot setzt Default Consent (granted)
   │
3. GTM Clarity Tag feuert
   │
4. Clarity prüft Consent Mode Status
   ├── analytics_storage: granted → Volle Aufzeichnung
   └── analytics_storage: denied → Nur aggregierte Daten (keine Recordings)
   │
5. Falls User später "Ablehnen" klickt:
   └── Clarity stoppt Aufzeichnung für diese Session
```

### Setup-Checklist Microsoft Clarity

```
CLARITY SETUP
─────────────
1. CLARITY ACCOUNT
   □ Account erstellen auf clarity.microsoft.com
   □ Neues Projekt "DrWerner.com" anlegen
   □ Project ID notieren (Format: xxxxx)
   □ Domain verifizieren

2. GTM INTEGRATION
   □ Community Template "Microsoft Clarity" installieren
   □ Tag mit Project ID konfigurieren
   □ Consent Settings aktivieren (analytics_storage required)
   □ Trigger: Consent Initialization - All Pages

3. IP-AUSSCHLÜSSE (Optional aber empfohlen)
   □ Eigene IPs ausschließen (Clarity Dashboard → Settings → IP Blocking)
   □ Agentur-IPs ausschließen

4. PRIVACY SETTINGS
   □ Masking-Level prüfen (Standard: "Balanced")
   □ Sensitive Felder automatisch maskiert (Passwort, Kreditkarte)
   □ Ggf. CSS-Selektoren für zusätzliche Maskierung

5. TESTING
   □ Eigene Session aufzeichnen lassen
   □ Prüfen ob Recording erscheint (kann 2h dauern)
   □ Consent-Ablehnung testen → Recording sollte stoppen
```

### Datenschutz-Hinweise

| Aspekt | Clarity-Verhalten |
|--------|-------------------|
| **IP-Adressen** | Automatisch anonymisiert |
| **Passwörter** | Automatisch maskiert |
| **Formularfelder** | "Balanced" maskiert sensible Felder |
| **Keystroke-Logging** | Deaktiviert per Default |
| **PII in URLs** | Sollte manuell konfiguriert werden |
| **Datenstandort** | Microsoft Azure (EU oder US wählbar) |

**Wichtig für DSGVO**:
- In der Datenschutzerklärung erwähnen
- Microsoft als Auftragsverarbeiter führen
- Bei Datenstandort-Auswahl "EU" bevorzugen (falls verfügbar)

### Wann Clarity nutzen?

| Use Case | Clarity verwenden? |
|----------|-------------------|
| Landing Page nicht konvertiert | ✓ Session Recordings analysieren |
| Formular wird abgebrochen | ✓ Wo genau brechen User ab? |
| Hohe Bounce Rate auf Seite | ✓ Heatmap: Was sehen User (nicht)? |
| CTA wird nicht geklickt | ✓ Aufmerksamkeits-Heatmap prüfen |
| Mobile UX-Probleme | ✓ Mobile Sessions separat filtern |
| Kampagnen-Attribution | ✗ GA4 verwenden |
| Traffic-Quellen analysieren | ✗ GA4 verwenden |
| Conversion-Tracking | ✗ GA4/Server-Side verwenden |

### Clarity in der Gesamt-Architektur

```
┌─────────────────────────────────────────────────────────────────┐
│                        ANALYTICS STACK                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  QUANTITATIVE ANALYSE          QUALITATIVE ANALYSE              │
│  ─────────────────────         ────────────────────             │
│                                                                 │
│  ┌─────────────────────┐       ┌─────────────────────┐         │
│  │      GA4            │       │   Microsoft Clarity │         │
│  │  (Server-Side)      │       │     (Client-Side)   │         │
│  ├─────────────────────┤       ├─────────────────────┤         │
│  │ • Traffic-Quellen   │       │ • Session Recordings│         │
│  │ • Conversions       │       │ • Heatmaps          │         │
│  │ • User Journeys     │       │ • Rage Clicks       │         │
│  │ • Attribution       │       │ • Dead Clicks       │         │
│  │ • Audiences         │       │ • Scroll Depth      │         │
│  └─────────────────────┘       └─────────────────────┘         │
│           │                              │                      │
│           └──────────┬───────────────────┘                      │
│                      │                                          │
│                      ▼                                          │
│           ┌─────────────────────┐                               │
│           │     BigQuery        │                               │
│           │  (Eigene Analysen)  │                               │
│           └─────────────────────┘                               │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘

Empfohlene Nutzung:
• GA4 täglich für KPIs und Trends
• Clarity wöchentlich für UX-Reviews
• BigQuery für tiefe Analysen und Reporting
```

---

# 6. Event-Tracking Konzept

## 6.1 Warum vollständige Customer Journey Transparenz?

Für eine effektive Marketing-Optimierung reicht es nicht, nur Conversions zu messen. Der Wert liegt in der **vollständigen Sichtbarkeit** der gesamten Customer Journey – von der ersten Werbeanzeige bis zum unterschriebenen Mandat. Nur so können Fragen beantwortet werden wie:

- Welche Blog-Artikel lesen Leads, die später Mandanten werden?
- Wie viele Touchpoints braucht ein typischer Mandant vor der Anfrage?
- Welche Seiten führen zu Absprüngen statt zu Conversions?
- Lohnt sich der LinkedIn-Traffic oder sind das nur "Lurker"?

### Das Event-Modell: Vollständige Journey-Transparenz

Das folgende Event-Modell erfasst **jeden relevanten Schritt** der Customer Journey. Die Events sind so konzipiert, dass sie später in BigQuery zu einer vollständigen User-Historie zusammengesetzt werden können.

## 6.2 Event-Hierarchie nach Funnel-Stufe

### Übersicht: Alle Events im Funnel

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              CUSTOMER JOURNEY EVENTS                             │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                  │
│  AWARENESS (Erster Kontakt)                                                      │
│  ─────────────────────────                                                       │
│  ├── first_visit              → Allererster Besuch (je User)                    │
│  ├── page_view                → Jeder Seitenaufruf                              │
│  ├── session_start            → Neue Session beginnt                            │
│  ├── traffic_source_captured  → Quelle des Besuchs erfasst (inkl. Click IDs)   │
│  └── blog_view                → Blog-Artikel aufgerufen                         │
│                                                                                  │
│  ENGAGEMENT (Aktives Interesse)                                                  │
│  ──────────────────────────────                                                  │
│  ├── content_engagement       → Blog gelesen (>60 Sek. auf Seite)               │
│  ├── scroll_milestone         → 50% oder 90% der Seite gescrollt               │
│  ├── internal_link_click      → Klick auf internen Link (zeigt Navigation)     │
│  ├── outbound_link_click      → Klick auf externen Link (z.B. Referenzen)      │
│  ├── video_start              → Video gestartet                                 │
│  ├── video_progress           → Video 25%/50%/75% gesehen                       │
│  ├── video_complete           → Video vollständig gesehen                       │
│  └── file_download            → Nicht-gated Datei heruntergeladen              │
│                                                                                  │
│  INTEREST (Gezieltes Interesse)                                                  │
│  ──────────────────────────────                                                  │
│  ├── service_page_view        → Leistungsseite besucht (Malta, Zypern, etc.)   │
│  ├── team_page_view           → Team/Über-uns Seite besucht                    │
│  ├── case_study_view          → Fallstudie/Referenz angesehen                  │
│  ├── faq_interaction          → FAQ aufgeklappt/gelesen                        │
│  ├── cta_click                → Call-to-Action Button geklickt                 │
│  ├── cta_visible              → CTA in Viewport (für A/B Tests)                │
│  └── pricing_interest         → Preisseite oder Honorar-Sektion besucht        │
│                                                                                  │
│  CONSIDERATION (Aktive Erwägung)                                                 │
│  ───────────────────────────────                                                 │
│  ├── lead_magnet_view         → Vavolta Gating-Seite aufgerufen                │
│  ├── quickcheck_start         → QuickCheck begonnen                            │
│  ├── quickcheck_step          → QuickCheck Zwischenschritt (pro Frage)         │
│  ├── quickcheck_complete      → QuickCheck abgeschlossen (= MQL)               │
│  ├── newsletter_form_view     → Newsletter-Formular gesehen                    │
│  ├── newsletter_signup        → Newsletter-Anmeldung (= Known Lead)            │
│  ├── return_visit             → Wiederkehrender Besuch (<7 Tage)               │
│  └── high_intent_page         → "Jetzt anfragen" oder "Termin" Seite besucht   │
│                                                                                  │
│  CONVERSION (Leads & Anfragen)                                                   │
│  ─────────────────────────────                                                   │
│  ├── lead_magnet_download     → PDF heruntergeladen nach Gating (= MQL)        │
│  ├── contact_form_view        → Kontaktformular in Viewport                    │
│  ├── contact_form_start       → Erstes Feld im Formular ausgefüllt             │
│  ├── contact_form_submit      → Kontaktformular abgesendet (= SQL)             │
│  ├── callback_request         → Rückruf-Formular abgesendet (= SQL)            │
│  ├── consultation_booking     → Beratungstermin gebucht via Calendly (= SQL)   │
│  └── phone_click              → Klick auf Telefonnummer (mobil)                │
│                                                                                  │
│  POST-CONVERSION (Nach der Anfrage)                                              │
│  ───────────────────────────────────                                             │
│  ├── thank_you_page_view      → Danke-Seite nach Formular gesehen              │
│  ├── email_link_click         → Link aus Nurturing-E-Mail geklickt             │
│  ├── returning_lead           → Identifizierter Lead kehrt zurück              │
│  └── referral_click           → Lead klickt auf Empfehlungs-Link               │
│                                                                                  │
└─────────────────────────────────────────────────────────────────────────────────┘
```

## 6.3 Detaillierte Event-Definitionen

### Awareness-Events (Erster Kontakt)

Diese Events erfassen, wie Besucher zum ersten Mal auf die Website kommen und welche Quellen sie bringen.

| Event | Beschreibung | Wann wird es ausgelöst? | Marketing-Nutzen |
|-------|--------------|-------------------------|------------------|
| `first_visit` | Allererster Besuch eines Users | Automatisch bei erstem Seitenaufruf (basierend auf Stape User ID) | Identifiziert neue potenzielle Kunden; Basis für First-Touch-Attribution |
| `page_view` | Jeder einzelne Seitenaufruf | Bei jedem Seitenwechsel | Grundlage für alle Analysen; zeigt Navigation und Interessen |
| `session_start` | Neue Browsing-Session beginnt | Nach 30 Min. Inaktivität oder neuer Tag | Zeigt Engagement-Frequenz; wie oft kommen User zurück? |
| `traffic_source_captured` | Erfasst Quelle inkl. Click IDs | Bei jedem page_view mit neuen UTM/Click-Parametern | Attribution: Welche Kampagne hat den User gebracht? |
| `blog_view` | Blog-Artikel wurde aufgerufen | Bei Aufruf einer /blog/* URL | Content-Performance: Welche Themen interessieren? |

### Engagement-Events (Aktives Interesse)

Diese Events zeigen, ob Besucher wirklich mit dem Content interagieren oder nur "durchklicken".

| Event | Beschreibung | Wann wird es ausgelöst? | Marketing-Nutzen |
|-------|--------------|-------------------------|------------------|
| `content_engagement` | User hat Content wirklich gelesen | Nach >60 Sekunden auf einer Seite | Unterscheidet echtes Lesen von Bounce; Content-Qualität messen |
| `scroll_milestone` | User hat signifikant gescrollt | Bei 50% und 90% Scroll-Tiefe | Zeigt ob Content bis zum Ende gelesen wird |
| `internal_link_click` | Klick auf internen Link | Bei Klick auf Link zu anderer Seite derselben Domain | Navigationsverhalten verstehen; interne Verlinkung optimieren |
| `outbound_link_click` | Klick auf externen Link | Bei Klick auf Link zu fremder Domain | Zeigt Interesse an Referenzen, Partnern, externen Ressourcen |
| `video_start` | Video-Wiedergabe gestartet | Bei Play-Button-Klick oder Autoplay | Video-Content-Performance messen |
| `video_progress` | Video-Fortschritt | Bei 25%, 50%, 75% der Videolänge | Engagement-Tiefe bei Videos; wo springen User ab? |
| `video_complete` | Video vollständig gesehen | Bei 90%+ der Videolänge | Hochwertige Engagement-Metrik für Video-Content |
| `file_download` | Nicht-gated Download | Bei Klick auf Download-Link (PDF ohne Gating) | Interesse an bestimmten Themen ohne Lead-Erfassung |

### Interest-Events (Gezieltes Interesse an Services)

Diese Events zeigen, dass ein Besucher sich konkret für die Dienstleistungen interessiert.

| Event | Beschreibung | Wann wird es ausgelöst? | Marketing-Nutzen |
|-------|--------------|-------------------------|------------------|
| `service_page_view` | Besuch einer Leistungsseite | Bei Aufruf von /leistungen/*, /malta/*, /zypern/* etc. | Konkretisiert Interesse; welcher Service wird gesucht? |
| `team_page_view` | Team-/Über-uns-Seite besucht | Bei Aufruf von /team, /ueber-uns, /dr-werner | Trust-Signal: User will wissen, mit wem er arbeiten würde |
| `case_study_view` | Fallstudie/Referenz angesehen | Bei Aufruf von /referenzen/*, /fallstudien/* | Hohes Kaufsignal: User sucht Bestätigung/Social Proof |
| `faq_interaction` | FAQ aufgeklappt | Bei Klick auf FAQ-Accordion | Zeigt konkrete Fragen/Bedenken; FAQ-Optimierung |
| `cta_click` | CTA-Button geklickt | Bei Klick auf .cta-button Elemente | Direktes Interesse-Signal; CTA-Performance messen |
| `cta_visible` | CTA im sichtbaren Bereich | Wenn CTA in Viewport scrollt | A/B-Testing: Wird CTA überhaupt gesehen? |
| `pricing_interest` | Preisinteresse | Bei Aufruf der Honorar-Seite oder Scroll zu Preis-Sektion | Starkes Kaufsignal: User evaluiert Kosten |

### Consideration-Events (Aktive Erwägung)

Diese Events zeigen, dass der Besucher kurz vor einer Conversion steht.

| Event | Beschreibung | Wann wird es ausgelöst? | Marketing-Nutzen |
|-------|--------------|-------------------------|------------------|
| `lead_magnet_view` | Vavolta-Seite aufgerufen | Bei Aufruf einer Vavolta-Gating-Seite | Top-of-Funnel Lead-Interest; wie viele sehen das Angebot? |
| `quickcheck_start` | QuickCheck begonnen | Bei Interaktion mit erstem QuickCheck-Schritt | Aktives Engagement mit Qualification-Tool |
| `quickcheck_step` | QuickCheck Zwischenschritt | Bei jedem Step-Wechsel im QuickCheck | Wo brechen User ab? Welche Fragen sind problematisch? |
| `quickcheck_complete` | QuickCheck abgeschlossen | Bei Anzeige des Ergebnisses | **MQL-Event**: User hat Zeit investiert und Ergebnis erhalten |
| `newsletter_form_view` | Newsletter-Formular gesehen | Wenn Newsletter-Formular in Viewport | Wie oft wird das Formular überhaupt gesehen? |
| `newsletter_signup` | Newsletter-Anmeldung | Bei erfolgreicher Newsletter-Anmeldung | **Known Lead**: User gibt E-Mail für Content |
| `return_visit` | Wiederkehrender Besuch | Wenn bekannter User innerhalb von 7 Tagen zurückkehrt | Zeigt nachhaltiges Interesse; Nurturing wirkt |
| `high_intent_page` | High-Intent-Seite besucht | Bei Aufruf von /kontakt, /termin-buchen, /anfrage | Starkes Kaufsignal: User sucht aktiv Kontaktmöglichkeit |

### Conversion-Events (Leads & Direktanfragen)

Diese Events markieren echte Conversions – MQLs und SQLs.

| Event | Beschreibung | Wann wird es ausgelöst? | Marketing-Nutzen |
|-------|--------------|-------------------------|------------------|
| `lead_magnet_download` | Lead Magnet heruntergeladen | Nach erfolgreichem Gating (Thank You Page) | **MQL**: User hat Kontaktdaten für Content gegeben |
| `contact_form_view` | Kontaktformular gesehen | Wenn Formular in Viewport scrollt | Wie viele sehen das Formular vs. füllen es aus? |
| `contact_form_start` | Formular-Eingabe begonnen | Bei Focus auf erstes Formularfeld | Formular-Abandonment messen; wo gehen User verloren? |
| `contact_form_submit` | Kontaktformular abgesendet | Bei erfolgreicher Formular-Absendung (Thank You Page) | **SQL**: Direkte Anfrage für Beratung |
| `callback_request` | Rückruf angefordert | Bei erfolgreicher Rückruf-Anfrage | **SQL**: User möchte kontaktiert werden |
| `consultation_booking` | Beratungstermin gebucht | Bei erfolgreichem Calendly-Booking | **SQL (Höchste Qualität)**: User hat Termin verbindlich gebucht |
| `phone_click` | Telefonnummer geklickt | Bei Klick auf tel: Link (mobil) | Mobiles Kontakt-Intent; oft hochwertige Leads |

### Post-Conversion Events (Nach der Anfrage)

Diese Events tracken das Verhalten nach einer Conversion – wichtig für Nurturing und Attribution.

| Event | Beschreibung | Wann wird es ausgelöst? | Marketing-Nutzen |
|-------|--------------|-------------------------|------------------|
| `thank_you_page_view` | Danke-Seite gesehen | Bei Aufruf einer /danke* oder /thank-you* Seite | Bestätigt erfolgreiche Conversion; Basis für Ad-Tracking |
| `email_link_click` | Link aus E-Mail geklickt | Bei Klick mit utm_medium=email Parameter | Nurturing-Effektivität: Werden E-Mails gelesen und geklickt? |
| `returning_lead` | Identifizierter Lead kehrt zurück | Wenn User mit bekannter E-Mail/ID die Seite wieder besucht | Lead-Engagement nach MQL; Sales-Readiness |
| `referral_click` | Empfehlungs-Link geklickt | Bei Klick auf Referral/Empfehlungs-Links | Empfehlungs-Marketing messen |

## 6.2 Event-Definitionen im Detail

### Lead Magnet Download (MQL-Event)

Dies ist das zentrale Event für die Lead-Generierung:

| Parameter | Beschreibung | Beispielwert |
|-----------|--------------|--------------|
| `event_name` | Event-Bezeichnung | `lead_magnet_download` |
| `lead_magnet_name` | Name des Assets | `Checkliste Firmengründung Malta` |
| `lead_magnet_type` | Asset-Typ | `pdf` / `checklist` / `guide` |
| `lead_magnet_topic` | Thematik | `firmengründung` / `auswanderung` |
| `user_email` | E-Mail (gehashed) | SHA256 Hash |
| `user_name` | Name | `Max Mustermann` |
| `traffic_source` | Quelle | `google_ads` / `meta_ads` / `organic` |
| `landing_page` | Einstiegsseite | `/malta-firmengründung` |

### QuickCheck Completion

Für die interaktiven Questionnaires:

| Parameter | Beschreibung | Beispielwert |
|-----------|--------------|--------------|
| `event_name` | Event-Bezeichnung | `quickcheck_complete` |
| `quickcheck_name` | Name des Checks | `Auswanderungs-Check` |
| `quickcheck_result` | Ergebnis-Kategorie | `malta_geeignet` / `zypern_geeignet` |
| `quickcheck_score` | Numerischer Score | `85` |
| `user_email` | E-Mail (gehashed) | SHA256 Hash |
| `recommended_action` | Empfohlener nächster Schritt | `beratung_buchen` |

### Kontaktformular (SQL-Event)

| Parameter | Beschreibung | Beispielwert |
|-----------|--------------|--------------|
| `event_name` | Event-Bezeichnung | `contact_form_submit` |
| `form_type` | Formular-Typ | `beratungsanfrage` / `rueckruf` |
| `service_interest` | Interessierter Service | `firmengründung_malta` |
| `user_email` | E-Mail (gehashed) | SHA256 Hash |
| `user_phone` | Telefon (gehashed) | SHA256 Hash |
| `estimated_value` | Geschätzter Wert | Optional |

## 6.3 Formular-Tracking mit Thank You Pages (Elementor)

> **Warum Thank You Pages statt DOM-Events?**
> Erfahrungsgemäß funktioniert das Tracking von Formular-Absendungen über DOM-Events (Element Visibility, Form Submit Trigger) in WordPress/Elementor nicht zuverlässig. Die stabilste Methode ist das Tracking über **Thank You Pages** – dedizierte Danke-Seiten, die nach erfolgreicher Formular-Absendung angezeigt werden.

### Das Prinzip: Thank You Page als Conversion-Trigger

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    FORMULAR-TRACKING ÜBER THANK YOU PAGES                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  SCHRITT 1: User füllt Formular aus                                         │
│  ┌─────────────────────────────────┐                                        │
│  │  Kontaktformular                │                                        │
│  │  ├── Name: Max Mustermann       │                                        │
│  │  ├── E-Mail: max@example.com    │                                        │
│  │  ├── Telefon: +49 170 1234567   │                                        │
│  │  └── Interesse: Firmengründung  │                                        │
│  │                                 │                                        │
│  │  [Absenden]                     │                                        │
│  └─────────────────────────────────┘                                        │
│                    │                                                         │
│                    ▼                                                         │
│  SCHRITT 2: Elementor leitet auf Thank You Page weiter                      │
│  ┌─────────────────────────────────┐                                        │
│  │  /danke-kontakt/                │                                        │
│  │  oder                           │                                        │
│  │  /danke-beratung/               │                                        │
│  │  oder                           │                                        │
│  │  /danke-rueckruf/               │                                        │
│  └─────────────────────────────────┘                                        │
│                    │                                                         │
│                    ▼                                                         │
│  SCHRITT 3: GTM Trigger feuert auf Thank You Page                           │
│  ┌─────────────────────────────────┐                                        │
│  │  Trigger: Page Path enthält     │                                        │
│  │  "/danke" ODER "/thank-you"     │                                        │
│  │                                 │                                        │
│  │  → contact_form_submit Event    │                                        │
│  │  → Daten aus URL-Parametern     │                                        │
│  │    oder DataLayer extrahieren   │                                        │
│  └─────────────────────────────────┘                                        │
│                    │                                                         │
│                    ▼                                                         │
│  SCHRITT 4: Event geht an Server-Side GTM                                   │
│  ┌─────────────────────────────────┐                                        │
│  │  → GA4 Server Tag               │                                        │
│  │  → Google Ads Conversion        │                                        │
│  │  → Meta CAPI                    │                                        │
│  │  → LinkedIn CAPI                │                                        │
│  │  → BigQuery                     │                                        │
│  │  → Brevo (Salesforce später)    │                                        │
│  └─────────────────────────────────┘                                        │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Thank You Pages für DrWerner.com

Folgende Danke-Seiten sollten angelegt werden:

| Formular-Typ | Thank You Page URL | GTM Event | Lead-Typ |
|--------------|-------------------|-----------|----------|
| Kontaktformular (Allgemein) | `/danke-kontakt/` | `contact_form_submit` | SQL |
| Beratungsanfrage | `/danke-beratung/` | `consultation_request` | SQL |
| Rückruf-Anfrage | `/danke-rueckruf/` | `callback_request` | SQL |
| Newsletter | `/danke-newsletter/` | `newsletter_signup` | MQL |
| Lead Magnet Download | `/danke-download/` | `lead_magnet_download` | MQL |
| QuickCheck Abschluss | `/danke-quickcheck/` | `quickcheck_complete` | MQL |

### Elementor Formular-Einstellung

In jedem Elementor-Formular muss die Weiterleitung auf die entsprechende Thank You Page konfiguriert werden:

```
Elementor Form Widget → Actions After Submit:
─────────────────────────────────────────────
1. E-Mail senden (an Kanzlei)
2. Redirect
   └── Redirect To: /danke-kontakt/?form=kontakt&email={{email}}&name={{name}}
```

**Wichtig:** Die URL-Parameter ermöglichen es, Formular-Daten auf der Thank You Page für das Tracking verfügbar zu machen.

### GTM Trigger-Konfiguration für SQL-Events

#### Trigger 1: Allgemeine Kontaktanfrage (SQL)

```
GTM Web Container - Trigger:
─────────────────────────────
Trigger Name:    Thank You - Kontaktformular
Trigger Type:    Page View - DOM Ready

Bedingung:
  Page Path    enthält    /danke-kontakt

Dieser Trigger feuert das Event:  contact_form_submit
```

#### Trigger 2: Beratungsanfrage (SQL - Höchster Wert)

```
GTM Web Container - Trigger:
─────────────────────────────
Trigger Name:    Thank You - Beratungsanfrage
Trigger Type:    Page View - DOM Ready

Bedingung:
  Page Path    enthält    /danke-beratung

Dieser Trigger feuert das Event:  consultation_request
```

#### Trigger 3: Rückruf-Anfrage (SQL)

```
GTM Web Container - Trigger:
─────────────────────────────
Trigger Name:    Thank You - Rückruf
Trigger Type:    Page View - DOM Ready

Bedingung:
  Page Path    enthält    /danke-rueckruf

Dieser Trigger feuert das Event:  callback_request
```

### GTM Tag für SQL-Events (Kontaktanfragen)

```
GTM Web Container - GA4 Event Tag:
───────────────────────────────────
Tag Name:         GA4 - contact_form_submit
Tag Type:         Google Analytics: GA4 Event
Measurement ID:   [Server-Side Endpoint]

Event Name:       contact_form_submit

Event Parameters:
├── form_type:          {{URL Parameter - form}}
├── user_email:         {{URL Parameter - email}}
├── user_name:          {{URL Parameter - name}}
├── user_phone:         {{URL Parameter - phone}}
├── service_interest:   {{URL Parameter - service}}
├── page_referrer:      {{Referrer}}
├── landing_page:       {{First Page Path}}
├── traffic_source:     {{Traffic Source}}
├── gclid:              {{Stored GCLID}}
├── fbclid:             {{Stored FBCLID}}
└── utm_campaign:       {{Stored UTM Campaign}}

User Properties:
├── user_email_hash:    {{SHA256 Email}}
└── user_phone_hash:    {{SHA256 Phone}}

Trigger:  Thank You - Kontaktformular
```

### Conversion-Werte für Ad-Plattformen

Diese Werte werden für die Optimierung der Werbekampagnen genutzt:

| Event | Google Ads Wert | Meta Ads Wert | LinkedIn Ads Wert | Begründung |
|-------|----------------|---------------|-------------------|------------|
| `newsletter_signup` | €10 | €10 | €10 | Niedrigste Qualifikation |
| `lead_magnet_download` | €50 | €50 | €50 | MQL - Interesse gezeigt |
| `quickcheck_complete` | €30 | €30 | €30 | MQL - Aktives Engagement |
| `contact_form_submit` | €200 | €200 | €200 | SQL - Direkte Anfrage |
| `callback_request` | €250 | €250 | €250 | SQL - Telefon-Interesse |
| `consultation_request` | €500 | €500 | €500 | SQL - Höchste Qualität |

## 6.4 Vavolta: PDF-Delivery & Engagement-Tracking

### Was ist Vavolta?

Vavolta ist eine **PDF-Hosting- und Delivery-Plattform** mit integriertem Engagement-Tracking. Wichtig: Vavolta ist KEIN Formular-Tool auf der Website, sondern hostet die Lead Magnets und trackt, was User nach dem Download damit machen.

### Architektur: Lead Capture vs. PDF-Delivery

```
LEAD CAPTURE (auf drwerner.com)          PDF-DELIVERY (extern via Vavolta)
─────────────────────────────────        ─────────────────────────────────

1. User füllt Elementor-Formular         4. User öffnet E-Mail
   auf drwerner.com aus                     mit einzigartigem Vavolta-Link
         │                                        │
         ▼                                        ▼
2. Thank-You-Page erscheint              5. User klickt Link und landet
   → GTM trackt lead_magnet_download        auf drwerner.vavolta.com/xyz123
         │                                        │
         ▼                                        ▼
3. Automation sendet E-Mail              6. Vavolta trackt:
   mit personalisiertem                     • Seite aufgerufen
   Vavolta-Link                             • PDF angesehen
                                            • PDF heruntergeladen
                                            • Verweildauer
```

**Wichtige Unterscheidung:**
- **Lead-Erfassung** (wer ist der Lead?) → Passiert auf drwerner.com via Elementor
- **Engagement-Tracking** (was macht der Lead mit dem Content?) → Passiert in Vavolta

### 1:1 Mapping: E-Mail ↔ Vavolta-Link

Jeder Lead erhält einen **einzigartigen Vavolta-Link**, der nur für diese E-Mail-Adresse generiert wird:

```
max.mustermann@example.com  →  drwerner.vavolta.com/abc123
anna.schmidt@firma.de       →  drwerner.vavolta.com/def456
thomas.mueller@gmbh.com     →  drwerner.vavolta.com/ghi789
```

**Vorteil**: Vavolta weiß genau, welcher Lead welches Engagement zeigt.

### Vavolta GTM-Integration (Optional)

Vavolta bietet eine eigene GTM-Integration innerhalb der Vavolta-Seiten (drwerner.vavolta.com). Diese Events können für erweiterte Analysen genutzt werden:

```
Verfügbare Vavolta-Events:
──────────────────────────
• leadmagnet_view        → User hat Vavolta-Seite aufgerufen
• page_change            → User hat Seite im PDF gewechselt
• download               → User hat PDF heruntergeladen
• email_verified         → E-Mail-Adresse bestätigt
• duration               → Verweildauer-Tracking
```

**Hinweis**: Diese Events werden auf drwerner.vavolta.com getrackt, NICHT auf drwerner.com.

### Empfehlung für DrWerner.com

**Fokus auf Website-Formulare**, weil:
1. Lead-Erfassung passiert auf drwerner.com (Elementor-Formulare)
2. Das `lead_magnet_download` Event wird durch Thank-You-Page ausgelöst
3. Vavolta-Engagement ist optional für tiefere Analyse (wer liest wirklich?)
4. Keine zusätzliche Tracking-Komplexität auf der Hauptseite nötig

**Optional (Phase 2+)**: Vavolta-GTM für "Gläserner User" nutzen:
- Wer hat das PDF nur angefordert vs. wirklich gelesen?
- Welche Seiten wurden am längsten betrachtet?
- Korrelation zwischen Lesetiefe und SQL-Conversion

## 6.5 DataLayer Erklärung (Ohne Code)

### Was ist der DataLayer?

Der **DataLayer** ist wie ein Notizzettel, auf den die Website Informationen schreibt, die der Google Tag Manager dann lesen kann.

**Einfaches Beispiel:**
- User füllt Kontaktformular aus
- Website schreibt auf den Notizzettel: "Event: Kontaktformular, Name: Max, E-Mail: max@example.com"
- GTM liest den Notizzettel und sagt: "Ah, ein Kontaktformular wurde ausgefüllt! Das melde ich an Google Analytics, Google Ads und Meta."

### Wann wird der DataLayer beschrieben?

| Aktion auf der Website | Was wird auf den DataLayer geschrieben? |
|----------------------|----------------------------------------|
| Seite wird geladen | page_view mit Seiten-URL, Titel, etc. |
| Thank You Page erscheint | Formular-Event mit allen Feldern |
| Scroll zu 50% | scroll_milestone mit Prozent-Wert |
| Video wird gestartet | video_start mit Video-Name |
| CTA-Button geklickt | cta_click mit Button-Text |

### Wer beschreibt den DataLayer?

1. **Automatisch durch GTM**: Basis-Events wie page_view, scroll, clicks
2. **Durch Thank You Pages**: Formular-Conversions über URL-Parameter
3. **Durch Plugins**: Calendly, Booking-Tools, etc. (wenn GTM-Integration vorhanden)
4. **Durch Custom Code**: Für spezielle Anforderungen (möglichst vermeiden)

### Warum Thank You Pages besser sind als DataLayer-Code

| Aspekt | DataLayer-Code | Thank You Pages |
|--------|----------------|-----------------|
| **Zuverlässigkeit** | Kann durch Updates brechen | Sehr stabil |
| **Wartung** | Entwickler nötig | Marketing kann selbst pflegen |
| **Debugging** | Komplex | Einfach (Seite aufrufen = Test) |
| **Elementor-Kompatibilität** | Problematisch | 100% kompatibel |
| **Empfehlung** | Nur wenn nötig | **Standard-Methode** |

---

# 7. Ad-Platform Integrationen

## 7.1 Google Ads (Enhanced Conversions)

### Was sind Enhanced Conversions?

Enhanced Conversions verbessern die Conversion-Messung, indem gehashte First-Party-Daten (E-Mail, Telefon) an Google gesendet werden. Google matched diese mit eingeloggten Google-Nutzern.

**Vorteile:**
- Bessere Attribution auch ohne Cookies
- Höhere Conversion-Erfassung (bis zu 20% mehr)
- Funktioniert auch wenn User Cookies ablehnt (mit Einschränkungen)

### Server-Side Implementation

```
GTM Server Container - Google Ads Tag Konfiguration:
─────────────────────────────────────────────────────
Conversion ID:        [Aus Google Ads Konto]
Conversion Label:     [Pro Conversion-Aktion]

Enhanced Conversions: Aktiviert
├── Email:           {{Hashed Email}}
├── Phone:           {{Hashed Phone}}
├── First Name:      {{Hashed First Name}}
├── Last Name:       {{Hashed Last Name}}
└── Address:         [Optional]

Consent:
├── Ad Storage:      {{Consent ad_storage}}
└── Ad User Data:    {{Consent ad_user_data}}
```

### Conversion-Aktionen für DrWerner.com

| Conversion | Wert | Typ | Zähl-Methode |
|------------|------|-----|--------------|
| Lead Magnet Download | €50 | Sekundär | Einmal |
| QuickCheck Complete | €30 | Sekundär | Einmal |
| Newsletter Signup | €10 | Sekundär | Einmal |
| Kontaktanfrage | €200 | Primär | Einmal |
| Beratungstermin | €500 | Primär | Einmal |

## 7.2 Meta Conversions API (CAPI)

### Was ist Meta CAPI?

Die Conversions API ist Metas Server-Side Tracking Lösung. Statt über das Browser-Pixel werden Events direkt vom Server an Meta gesendet.

**Warum CAPI wichtig ist:**
- iOS 14.5+ App Tracking Transparency reduziert Pixel-Tracking massiv
- CAPI-Events werden von Apple nicht blockiert
- Bessere Datenqualität für Kampagnen-Optimierung

### Server-Side Implementation

```
GTM Server Container - Meta CAPI Tag:
──────────────────────────────────────
Access Token:       [Aus Meta Events Manager]
Pixel ID:           [Facebook Pixel ID]

Event Mapping:
├── lead_magnet_download  → Lead
├── quickcheck_complete   → Lead
├── contact_form_submit   → CompleteRegistration
└── newsletter_signup     → Subscribe

User Data (gehashed):
├── em (Email):     {{SHA256 Hashed Email}}
├── ph (Phone):     {{SHA256 Hashed Phone}}
├── fn (First Name): {{SHA256 Hashed First Name}}
├── ln (Last Name):  {{SHA256 Hashed Last Name}}
├── client_ip:      {{Client IP}}
├── client_user_agent: {{User Agent}}
└── fbc (Click ID): {{FB Click ID aus Cookie}}

Event Matching:
├── event_id:       {{Unique Event ID}}  // Für Deduplizierung
└── event_source_url: {{Page URL}}
```

### Deduplizierung (Wichtig!)

Wenn Browser-Pixel UND CAPI aktiv sind, müssen Events dedupliziert werden:
1. Gleiche `event_id` für Browser und Server Event
2. Meta erkennt Duplikate und zählt nur einmal

**Empfehlung für DrWerner.com:**
- Browser-Pixel deaktivieren oder nur für Pageviews
- Alle Conversions über Server-Side CAPI

## 7.3 LinkedIn Conversions API

### Besonderheiten B2B-Tracking

LinkedIn ist für eine Steuerkanzlei besonders relevant:
- B2B-Zielgruppe (Unternehmer, Selbstständige)
- Höhere Lead-Qualität als Meta
- Längere Sales-Cycles → Attribution wichtiger

### Server-Side Implementation

```
GTM Server Container - LinkedIn CAPI:
──────────────────────────────────────
API Endpoint:       https://api.linkedin.com/rest/conversionEvents
Access Token:       [Aus LinkedIn Campaign Manager]
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
├── companyName:         {{Company Name}}  // Wenn erfasst
└── title:               {{Job Title}}     // Wenn erfasst
```

### LinkedIn Insight Tag

Zusätzlich zum CAPI sollte der LinkedIn Insight Tag für Retargeting aktiv bleiben:
- Server-Side für Conversions
- Client-Side für Audience Building

---

# 8. Lead Management & Nurturing

## 8.1 Lead-Lifecycle Modell

### Definition der Lead-Stufen für DrWerner.com

```
                    LEAD-LIFECYCLE
                         │
    ┌────────────────────┼────────────────────┐
    │                    │                    │
    ▼                    ▼                    ▼
┌─────────┐        ┌─────────┐         ┌─────────┐
│  ANONYM │        │   MQL   │         │   SQL   │
│ Visitor │   →    │Marketing│    →    │  Sales  │
│         │        │Qualified│         │Qualified│
└─────────┘        └─────────┘         └─────────┘
    │                    │                    │
    │                    │                    │
    ▼                    ▼                    ▼
Pageviews          Lead Magnet          Kontakt-
Blog-Besuche       Downloads            Anfrage
                   QuickCheck           Beratungs-
                   Newsletter           termin
```

### Konkrete Definitionen

| Stufe | Trigger | System | Aktion |
|-------|---------|--------|--------|
| **Anonymous** | Websitebesuch | GA4 | - |
| **Known** | Newsletter Signup | Brevo | Welcome Mail |
| **MQL** | Lead Magnet Download ODER QuickCheck Complete | Brevo + Salesforce | Nurturing-Sequenz |
| **SQL** | Kontaktanfrage ODER Termin | Salesforce | Sales-Kontakt |
| **Opportunity** | Qualifiziertes Gespräch | Salesforce | Pipeline |
| **Customer** | Mandatsvertrag | Salesforce | Onboarding |

## 8.2 Brevo Nurturing-Strategie

### Warum Brevo?

1. **DSGVO-konform**: EU-Server, deutsche GmbH
2. **Preis-Leistung**: Günstiger als HubSpot/Marketo
3. **Marketing Automation**: Vollwertige Workflows
4. **Salesforce-Integration**: Native Verbindung möglich

### Nurturing-Workflows

#### Workflow 1: Lead Magnet Download

```
Trigger: lead_magnet_download Event
    │
    ├── [Tag 0] Sofort
    │   └── E-Mail: "Dein Download: {Lead Magnet Name}"
    │       └── Inhalt: Download-Link + Mehrwert-Teaser
    │
    ├── [Tag 2]
    │   └── E-Mail: "Hast du schon reingeschaut?"
    │       └── Inhalt: Key Takeaways + verwandter Content
    │
    ├── [Tag 5]
    │   └── Bedingung: Hat QuickCheck gemacht?
    │       ├── JA → Skip
    │       └── NEIN → E-Mail: "Finde heraus, welches Land zu dir passt"
    │           └── CTA: QuickCheck starten
    │
    ├── [Tag 8]
    │   └── E-Mail: Case Study / Kundengeschichte
    │       └── Inhalt: Erfolgsbeispiel aus dem Lead-Thema
    │
    ├── [Tag 12]
    │   └── Bedingung: Hat Website besucht seit Tag 8?
    │       ├── JA → E-Mail: "Deine Fragen beantworten wir gerne"
    │       │   └── CTA: Kostenloses Erstgespräch
    │       └── NEIN → E-Mail: Soft Reminder mit anderem Content
    │
    └── [Tag 20]
        └── Lead Scoring Check
            ├── Score >= 50 → Tag: "Sales Ready" + Alert an Vertrieb
            └── Score < 50 → In Long-Term Nurture verschieben
```

#### Workflow 2: QuickCheck Completion

```
Trigger: quickcheck_complete Event
    │
    ├── [Sofort]
    │   └── E-Mail: "Dein Ergebnis: {QuickCheck Result}"
    │       └── Personalisiert nach Ergebnis:
    │           ├── Malta geeignet → Malta-spezifische Infos
    │           ├── Zypern geeignet → Zypern-spezifische Infos
    │           └── Unsicher → Vergleichs-Guide
    │
    ├── [Tag 3]
    │   └── E-Mail: "Vertiefung: Was bedeutet {Result} für dich?"
    │       └── Detaillierte Erklärung + FAQ
    │
    ├── [Tag 7]
    │   └── E-Mail: "Andere mit deinem Profil haben das gemacht..."
    │       └── Case Study passend zum Ergebnis
    │
    └── [Tag 14]
        └── E-Mail: "Lass uns deine Situation besprechen"
            └── CTA: Beratungsgespräch buchen
```

### Lead Scoring in Brevo

| Aktion | Punkte |
|--------|--------|
| Newsletter Signup | +10 |
| Blog-Artikel gelesen (>2 Min) | +5 |
| Service-Seite besucht | +10 |
| Preisseite besucht | +15 |
| Lead Magnet Download | +25 |
| QuickCheck Complete | +30 |
| E-Mail geöffnet | +3 |
| E-Mail Link geklickt | +5 |
| Website-Return (<7 Tage) | +10 |
| Keine Aktivität (30 Tage) | -20 |

**MQL-Schwelle**: 50 Punkte
**SQL-Ready**: 80 Punkte (+ explizites Interesse signalisiert)

---

# 9. CRM-Strategie (Salesforce + Brevo)

## 9.1 Empfohlene Architektur

Nach Analyse der Anforderungen empfehle ich folgende Aufteilung:

```
┌─────────────────────────────────────────────────────────────────┐
│                         BREVO                                    │
│            (Marketing Automation & Nurturing)                    │
├─────────────────────────────────────────────────────────────────┤
│  ✓ Alle Leads (auch anonyme mit Cookie-ID)                      │
│  ✓ E-Mail-Marketing & Automation                                 │
│  ✓ Lead Scoring                                                  │
│  ✓ MQL-Management                                                │
│  ✓ Website Tracking (Brevo Tracker zusätzlich zu GA4)           │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ Sync bei:
                             │ - Score >= 80
                             │ - Explizite Anfrage
                             │ - Termin gebucht
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                       SALESFORCE                                 │
│               (Sales CRM & Pipeline)                             │
├─────────────────────────────────────────────────────────────────┤
│  ✓ SQLs (qualifizierte Leads)                                   │
│  ✓ Pipeline Management                                           │
│  ✓ Deal Tracking                                                 │
│  ✓ Mandanten-Management                                          │
│  ✓ Umsatz-Reporting                                              │
└─────────────────────────────────────────────────────────────────┘
```

## 9.2 Salesforce-Felder Analyse (Bestehende vs. Neue)

### Bereits vorhandene Felder (können wiederverwendet werden)

Diese Felder existieren bereits im Lead-Objekt und decken Tracking-Anforderungen ab:

| Bestehendes Feld | Verwendung für Tracking | Bemerkung |
|------------------|------------------------|-----------|
| `gclid__c` | Google Click ID | ✓ Perfekt, bereits vorhanden |
| `Lead_Channel__c` | Traffic Source (google_ads, meta_ads, etc.) | ✓ Nutzen statt neues Feld |
| `Lead_Quality__c` | Lead Score | ✓ Kann für Brevo-Score verwendet werden |
| `LeadSource` | Haupt-Quelle | ✓ Standard-Feld, behalten |
| `Source__c` | Detaillierte Quelle | ✓ Für UTM Source |
| `Referrer__c` | Referrer URL | ✓ Perfekt für Attribution |
| `Service_Type__c` | Service-Interesse | ✓ Bereits business-relevant |
| `Conversion_URL__c` | Landing Page | ✓ Für First-Touch Attribution |
| `Status` | Lead-Status | ✓ Für MQL/SQL-Tracking erweitern |

### Nur diese neuen Felder anlegen

Basierend auf der Analyse brauchen wir nur noch wenige zusätzliche Felder:

| Neues Feld | Typ | Beschreibung | Priorität |
|------------|-----|--------------|-----------|
| `MQL_Date__c` | Date | Datum der MQL-Qualifizierung | Hoch |
| `Lead_Magnet_Downloaded__c` | Text | Name des heruntergeladenen Assets | Hoch |
| `QuickCheck_Result__c` | Picklist | Ergebnis des QuickChecks (malta_geeignet, zypern_geeignet, etc.) | Hoch |
| `Brevo_Contact_ID__c` | Text (External ID) | Verknüpfung zu Brevo für Sync | Hoch |
| `fbclid__c` | Text | Meta Click ID (analog zu gclid) | Mittel |
| `li_fat_id__c` | Text | LinkedIn Click ID | Mittel |
| `First_Touch_Campaign__c` | Text | UTM Campaign beim Erstkontakt | Mittel |
| `Last_Website_Visit__c` | DateTime | Letzter Website-Besuch (aus Brevo) | Niedrig |

### Status-Picklist erweitern

Das bestehende `Status`-Feld sollte diese Werte enthalten:

```
Status Picklist:
├── New (Standard)
├── MQL - Marketing Qualified ← NEU
├── Nurturing ← NEU
├── SQL - Sales Qualified ← NEU (oder bestehender Wert umbenennen)
├── Contacted
├── Qualified
├── Proposal
├── Won
└── Lost
```

### Feld-Mapping: Tracking → Salesforce

| Tracking-Event | Salesforce-Feld | Wert |
|----------------|-----------------|------|
| Google Click ID | `gclid__c` | Aus URL-Parameter |
| Meta Click ID | `fbclid__c` ← NEU | Aus URL-Parameter |
| LinkedIn Click ID | `li_fat_id__c` ← NEU | Aus Cookie |
| Traffic Source | `Lead_Channel__c` | google_ads / meta_ads / linkedin_ads / organic |
| UTM Source | `Source__c` | google / facebook / linkedin / etc. |
| Landing Page | `Conversion_URL__c` | Page URL |
| Referrer | `Referrer__c` | Document Referrer |
| Lead Magnet | `Lead_Magnet_Downloaded__c` ← NEU | Asset-Name |
| QuickCheck Result | `QuickCheck_Result__c` ← NEU | Ergebnis-Kategorie |
| Lead Score | `Lead_Quality__c` | Numerischer Score aus Brevo |
| MQL-Datum | `MQL_Date__c` ← NEU | Timestamp der Qualifizierung |

#### Workflow-Regeln

1. **MQL → SQL Upgrade**
   - Trigger: Lead_Quality__c >= 80 ODER Kontaktanfrage
   - Aktion: Status auf "SQL - Sales Qualified", Sales-Benachrichtigung

2. **Activity Logging**
   - Brevo-Events als Activities in Salesforce loggen
   - Vertrieb sieht: "Max hat vor 2 Tagen Malta-Checkliste heruntergeladen"

## 9.3 Brevo-Salesforce Sync

### Native Integration

Brevo bietet native Salesforce-Integration:
1. **Contact Sync**: Brevo Contacts ↔ Salesforce Leads/Contacts
2. **Campaign Sync**: Brevo Kampagnen → Salesforce Campaigns
3. **Activity Sync**: E-Mail-Opens/Clicks → Salesforce Activities

### Sync-Regeln

```
Brevo → Salesforce:
──────────────────
WANN: Lead Score >= 80 ODER SQL-Event (Kontaktanfrage/Termin)
WAS:  Alle Kontaktdaten + Engagement-Historie
WO:   Als neuer Lead oder Update existierender Lead

Salesforce → Brevo:
──────────────────
WANN: Lead/Contact erstellt oder aktualisiert
WAS:  Status-Updates, Owner-Zuweisung
WO:   Brevo Contact aktualisieren (für Segmentierung)
```

### Vertriebstransparenz

Der Vertrieb sieht in Salesforce:
- Alle Downloads des Leads
- QuickCheck-Ergebnis
- E-Mail-Engagement (welche E-Mails geöffnet/geklickt)
- Besuchte Seiten (via BigQuery/Brevo Integration)
- Lead Score Historie
- Traffic Source (Google/Meta/LinkedIn/Organic)

## 9.4 MQL/SQL Lifecycle Management (Erweitert)

### Lead-Lifecycle-Architektur

Die Lead-Qualifizierung erfolgt **auf dem Standard Lead-Objekt** mit zusätzlichen Lifecycle-Feldern (kein Custom Object erforderlich).

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      LEAD LIFECYCLE FLOW                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  [Anonymer Besucher]                                                         │
│         │                                                                    │
│         ▼                                                                    │
│  [Lead Magnet / QuickCheck / Newsletter]                                     │
│         │                                                                    │
│         ▼                                                                    │
│  ┌──────────────────────────────────────┐                                   │
│  │          BREVO KONTAKT               │                                   │
│  │  • Lifecycle: "MQL"                  │                                   │
│  │  • Lead Score startet                │                                   │
│  │  • Nurturing beginnt                 │                                   │
│  └──────────────────┬───────────────────┘                                   │
│                     │                                                        │
│         ┌───────────┴───────────┐                                           │
│         │                       │                                           │
│         ▼                       ▼                                           │
│  [Score >= 50]           [Kontaktformular]                                  │
│         │                       │                                           │
│         └───────────┬───────────┘                                           │
│                     │                                                        │
│                     ▼                                                        │
│  ┌──────────────────────────────────────┐                                   │
│  │        SALESFORCE LEAD               │                                   │
│  │  • Lifecycle: "SQL"                  │                                   │
│  │  • MQL_Date__c + SQL_Date__c         │                                   │
│  │  • Brevo is_sql = true               │                                   │
│  │  • Nurturing gestoppt                │                                   │
│  └──────────────────┬───────────────────┘                                   │
│                     │                                                        │
│         ┌───────────┴───────────┐                                           │
│         │                       │                                           │
│         ▼                       ▼                                           │
│  [Qualified]            [Unqualified]                                       │
│      │                       │                                              │
│      ▼                       ▼                                              │
│  Opportunity         Feedback an                                            │
│                      Ad-Plattformen                                         │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Erweiterte Salesforce-Felder für MQL/SQL

Diese Felder ergänzen die bereits definierten Felder:

| Feld (API Name) | Typ | Werte | Beschreibung |
|-----------------|-----|-------|--------------|
| `Lead_Lifecycle_Stage__c` | Picklist | New, MQL, SQL, Nurturing, Unqualified, Disqualified | Aktueller Lifecycle-Status |
| `SQL_Date__c` | DateTime | - | Zeitpunkt der SQL-Qualifikation |
| `MQL_Source__c` | Picklist | Lead_Magnet, QuickCheck, Newsletter, Webinar | Was hat MQL ausgelöst |
| `MQL_Content__c` | Text | - | Welcher spezifische Lead Magnet / QuickCheck |
| `Days_as_MQL__c` | Formula | `TODAY() - MQL_Date__c` | Automatisch berechnet |
| `Conversion_Sent_Google__c` | Checkbox | - | Offline Conversion an Google gesendet? |
| `Conversion_Sent_Meta__c` | Checkbox | - | CAPI Conversion an Meta gesendet? |
| `Conversion_Sent_LinkedIn__c` | Checkbox | - | CAPI Conversion an LinkedIn gesendet? |
| `Unqualified_Reason__c` | Picklist | Budget, Timeline, Fit, Competitor, No_Response | Grund für Disqualifikation |

### MQL-to-Direct-SQL Szenario

**Anwendungsfall**: Ein User hat einen Lead Magnet heruntergeladen (ist MQL in Brevo), füllt später direkt das Kontaktformular aus.

```javascript
// Logik im GTM Server Container / n8n Workflow
IF (Brevo-Kontakt existiert mit gleicher E-Mail) {
    // Bestehender MQL wird zu SQL
    salesforce.upsertLead({
        email: user_email,
        Lead_Lifecycle_Stage__c: "SQL",
        MQL_Source__c: brevo.getAttribute('mql_source'),  // ursprünglicher Lead Magnet
        MQL_Date__c: brevo.getAttribute('mql_date'),       // behalten
        SQL_Date__c: NOW(),
        Brevo_Contact_ID__c: brevo.contact_id
    });

    // Brevo aktualisieren
    brevo.updateContact(user_email, {
        is_sql: true,
        sql_date: NOW()
    });

    // Nurturing-Workflow in Brevo stoppen
    brevo.removeFromWorkflow(user_email, 'mql_nurturing');

} ELSE {
    // Neuer SQL ohne MQL-Phase
    salesforce.createLead({
        email: user_email,
        Lead_Lifecycle_Stage__c: "SQL",
        MQL_Source__c: "Direct_Contact",
        SQL_Date__c: NOW()
    });

    brevo.createContact(user_email, {
        is_sql: true,
        sql_date: NOW()
    });
}
```

## 9.5 "Unqualified" Feedback Loop

### Warum Feedback wichtig ist

Wenn Sales einen Lead als "Unqualified" markiert, sollen die Ad-Plattformen diese Information erhalten:
- **Google Ads**: Für Smart Bidding (negative Signale verbessern Algorithmus)
- **Meta**: Für Audience Exclusion und Lookalike-Optimierung
- **LinkedIn**: Für Exclusion Lists

### Prozess-Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    UNQUALIFIED FEEDBACK LOOP                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  1. Sales markiert Lead als "Unqualified" in Salesforce                     │
│     └── Setzt Lead_Lifecycle_Stage__c = "Unqualified"                       │
│     └── Wählt Unqualified_Reason__c                                         │
│                                                                              │
│  2. Salesforce Flow triggert                                                 │
│     └── Prüft: Stage = "Unqualified" UND Conversion_Sent_* = false          │
│     └── Sendet Webhook an Stape                                             │
│                                                                              │
│  3. Stape Server Container empfängt Webhook                                  │
│     └── Endpoint: https://s.drwerner.com/webhook/unqualified                │
│                                                                              │
│  4. Offline Conversion Tags feuern                                           │
│     ├── Google Ads: Conversion "unqualified_lead" mit gclid                 │
│     ├── Meta CAPI: Event "lead_unqualified"                                 │
│     └── (LinkedIn: Kein natives Support, nur Exclusion)                     │
│                                                                              │
│  5. Salesforce Update                                                        │
│     └── Conversion_Sent_Google__c = true                                    │
│     └── Conversion_Sent_Meta__c = true                                      │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Stape Webhook Endpoint Konfiguration

```javascript
// GTM Server Container: Webhook Client
// Endpoint: /webhook/unqualified

// Request Body von Salesforce:
{
  "lead_id": "00Q...",
  "email": "max@example.com",
  "gclid": "EAIaIQob...",
  "fbclid": "IwAR3x...",
  "li_fat_id": "abc123",
  "unqualified_reason": "Budget",
  "lifecycle_stage": "Unqualified"
}

// Server-Side Tag: Google Ads Offline Conversion
{
  conversion_action_id: "AW-123456/unqualified",
  gclid: {{Webhook.gclid}},
  conversion_time: {{timestamp}},
  conversion_value: -1,  // Negativer Wert als Signal
  currency_code: "EUR"
}

// Server-Side Tag: Meta CAPI
{
  event_name: "lead_unqualified",
  user_data: {
    em: sha256({{Webhook.email}}),
    fbc: {{Webhook.fbclid}}
  },
  custom_data: {
    reason: {{Webhook.unqualified_reason}},
    value: -1
  }
}
```

### Salesforce Flow Konfiguration

```
Flow: "Send Unqualified Feedback"
────────────────────────────────
Trigger: Record Update auf Lead
Bedingungen:
  - Lead_Lifecycle_Stage__c = "Unqualified"
  - Conversion_Sent_Google__c = false (OR Conversion_Sent_Meta__c = false)
  - gclid__c != NULL (OR fbclid__c != NULL)

Aktion: HTTP Callout
  URL: https://s.drwerner.com/webhook/unqualified
  Method: POST
  Headers:
    Content-Type: application/json
    X-API-Key: {{Stape_Webhook_Key}}
  Body:
    {
      "lead_id": "{!Lead.Id}",
      "email": "{!Lead.Email}",
      "gclid": "{!Lead.gclid__c}",
      "fbclid": "{!Lead.fbclid__c}",
      "li_fat_id": "{!Lead.li_fat_id__c}",
      "unqualified_reason": "{!Lead.Unqualified_Reason__c}"
    }
```

## 9.6 Offline Conversion Upload via Stape

### Aktuelle vs. Neue Architektur

**Aktuell (limitiert):**
- Direkte Google Ads Connection für Offline Conversions
- Nur Google Ads unterstützt
- Manuelle/periodische Uploads

**Neu mit Stape (empfohlen):**
- Echtzeit-Webhook von Salesforce
- Multi-Platform: Google Ads + Meta + LinkedIn
- Automatisiert bei jeder Status-Änderung

### Architektur-Diagramm

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                 OFFLINE CONVERSION VIA STAPE                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  SALESFORCE                          STAPE SERVER                            │
│  ──────────                          ────────────                            │
│                                                                              │
│  Lead-Status ändert sich             Webhook empfängt Daten                  │
│       │                                    │                                 │
│       │ Flow triggert                      │                                 │
│       │                                    ▼                                 │
│       └──────────────────────────►  /webhook/conversion                     │
│                                           │                                  │
│                                           ├──► Google Ads Offline Conv.     │
│                                           │    - gclid Matching              │
│                                           │    - Enhanced Conversions        │
│                                           │                                  │
│                                           ├──► Meta CAPI                     │
│                                           │    - fbclid Matching             │
│                                           │    - Custom Events               │
│                                           │                                  │
│                                           └──► LinkedIn CAPI                 │
│                                                - li_fat_id Matching          │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Conversion Actions & Wertigkeiten

| Conversion Action | Google Ads | Meta CAPI | LinkedIn | Wert (EUR) | Trigger |
|-------------------|------------|-----------|----------|------------|---------|
| `lead_magnet_download` | ✓ | ✓ | ✓ | 5 | Event im Browser |
| `quickcheck_complete` | ✓ | ✓ | ✓ | 25 | Event im Browser |
| `newsletter_signup` | ✓ | ✓ | ✓ | 2 | Event im Browser |
| `contact_request` (SQL) | ✓ | ✓ | ✓ | 100 | Event im Browser |
| `qualified_lead` | ✓ | ✓ | ✓ | 500 | Salesforce Webhook |
| `opportunity_created` | ✓ | ✓ | ✓ | 1000 | Salesforce Webhook |
| `deal_won` | ✓ | ✓ | ✓ | Actual Deal Value | Salesforce Webhook |
| `unqualified_lead` | ✓ | (Event) | - | -1 | Salesforce Webhook |

### Salesforce Flow für Qualified/Won Conversions

```
Flow: "Send Qualified Conversion"
─────────────────────────────────
Trigger: Record Update auf Opportunity
Bedingungen:
  - StageName = "Closed Won"
  - Conversion_Sent_Google__c = false

Aktion: HTTP Callout
  URL: https://s.drwerner.com/webhook/conversion
  Body:
    {
      "conversion_type": "deal_won",
      "lead_id": "{!Opportunity.Lead__c.Id}",
      "gclid": "{!Opportunity.Lead__c.gclid__c}",
      "fbclid": "{!Opportunity.Lead__c.fbclid__c}",
      "conversion_value": "{!Opportunity.Amount}",
      "currency": "EUR"
    }
```

---

# 10. Custom Analytics mit BigQuery (Consent-Unabhängig)

## 10.1 Warum eigene Analytics?

### Kernprinzip: Eigene Datenverarbeitung = Kein Consent erforderlich

> **Rechtliche Basis**: BigQuery-Tracking läuft als **eigene Datenverarbeitung** ohne Weitergabe an Dritte. Da die Daten ausschließlich intern verarbeitet werden, ist kein separater Consent erforderlich. Das BigQuery-Tag feuert bei **jedem Request**, unabhängig vom Consent-Status.

**Unterschied zu Ad-Plattformen:**
- Google Analytics, Meta, LinkedIn = Daten gehen an Dritte → Consent nötig
- BigQuery = Eigene Google Cloud, eigene Verarbeitung → Kein Consent nötig

### Limitationen von GA4

- **Datenspeicherung**: Max. 14 Monate in kostenloser Version
- **Sampling**: Bei hohem Traffic werden Daten hochgerechnet
- **Datenhoheit**: Daten liegen bei Google (als Auftragsverarbeiter)
- **Flexible Analyse**: Komplexe Queries nur begrenzt möglich
- **Cross-System**: Keine direkte Verknüpfung mit CRM-Daten
- **Consent-Abhängig**: Ohne Consent nur eingeschränkte Daten

### Vorteile BigQuery (Eigene Instanz)

- **Unbegrenzte Speicherung**: Alle Events für immer
- **Kein Sampling**: Rohdaten-Zugriff
- **SQL-Queries**: Volle Flexibilität
- **Verknüpfung**: Mit Salesforce, Brevo, Finanzdaten
- **ML-Ready**: BigQuery ML für Predictive Analytics
- **Consent-Unabhängig**: Jeder Seitenbesuch, jedes Event wird erfasst
- **Vollständige Journey**: Auch für User die Consent ablehnen
- **Multi-Tenant**: Eine Datenbank für alle Agentur-Kunden

## 10.2 Multi-Tenant Architektur (Agentur-Setup)

### Warum Multi-Tenant?

Als Agentur mit mehreren Kunden-Projekten ist es ineffizient, für jeden Kunden eine separate BigQuery-Infrastruktur aufzubauen. Stattdessen:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    BRIXON ANALYTICS (BigQuery)                               │
│                    Ein Projekt, alle Kunden                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  Dataset: brixon_analytics                                                   │
│  ├── events          (alle Events aller Properties)                         │
│  ├── users           (alle User aller Properties)                           │
│  ├── sessions        (alle Sessions aller Properties)                       │
│  ├── leads           (identifizierte Leads mit PII)                         │
│  ├── identity_graph  (User-Stitching Verknüpfungen)                         │
│  └── properties      (Kunden/Projekte Stammdaten)                           │
│                                                                              │
│  Filterung über: property_id                                                 │
│  ├── "drwerner"          → DrWerner.com                                     │
│  ├── "philippsauerborn"  → philippsauerborn.com                             │
│  ├── "kunde_xyz"         → Weiterer Agentur-Kunde                           │
│  └── ...                                                                     │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Vorteile Multi-Tenant

| Aspekt | Separate DBs | Multi-Tenant |
|--------|--------------|--------------|
| **Setup-Aufwand** | Pro Kunde neu | Einmal, dann nur Property anlegen |
| **Kosten** | Mehrfache Grundkosten | Geteilt, nur nach Nutzung |
| **Wartung** | Pro DB separat | Zentral für alle |
| **Cross-Client Insights** | Nicht möglich | Benchmarking möglich |
| **Schema-Updates** | Überall einzeln | Einmal für alle |
| **Berechtigungen** | Separate Projekte | Row-Level Security |

### Property-Tabelle (Kunden-Stammdaten)

```sql
CREATE TABLE `brixon-analytics.analytics.properties` (
  -- Identifikation
  property_id STRING NOT NULL,          -- "drwerner", "kunde_xyz"
  property_name STRING NOT NULL,        -- "Dr. Werner & Partner"

  -- Domains
  primary_domain STRING,                -- drwerner.com
  additional_domains ARRAY<STRING>,     -- ["philippsauerborn.com"]
  stape_container_domain STRING,        -- s.drwerner.com

  -- Konfiguration
  gtm_web_container_id STRING,          -- GTM-XXXXXX
  gtm_server_container_id STRING,       -- GTM-YYYYYY
  ga4_measurement_id STRING,            -- G-XXXXXXX

  -- Business Info
  industry STRING,                      -- "steuerberatung", "saas", "ecommerce"
  business_model STRING,                -- "b2b", "b2c", "b2b2c"

  -- CRM Verknüpfungen
  salesforce_org_id STRING,
  brevo_account_id STRING,
  hubspot_portal_id STRING,

  -- Status
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  PRIMARY KEY (property_id) NOT ENFORCED
);

-- Beispiel-Eintrag
INSERT INTO `brixon-analytics.analytics.properties` VALUES (
  'drwerner',
  'Dr. Werner & Partner Steuerberatung',
  'drwerner.com',
  ['philippsauerborn.com', 'malta-firmengründung.de'],
  's.drwerner.com',
  'GTM-XXXXX',
  'GTM-YYYYY',
  'G-ZZZZZ',
  'steuerberatung',
  'b2b',
  'org_salesforce_123',
  'brevo_456',
  NULL,
  TRUE,
  CURRENT_TIMESTAMP(),
  CURRENT_TIMESTAMP()
);
```

## 10.3 Stape User ID als primärer Identifier

### Warum Stape User ID?

Stape bietet eine persistente `X-STAPE-USER-ID` die:
- Vom Server gesetzt wird (nicht vom Browser)
- ITP/ETP-resistent ist (HTTP-Header Cookie)
- Bis zu 2 Jahre persistiert
- Cross-Session User-Identifikation ermöglicht

### Aktivierung in Stape

```
Stape Dashboard → Container → Power-Ups
────────────────────────────────────────
☑ User ID Header (X-STAPE-USER-ID)
  → Aktiviert automatisch persistente User ID
  → Wird bei jedem Request im Header mitgesendet
  → Verfügbar als Variable im Server GTM
```

### User ID Hierarchie

```
Priorität der User-Identifikation:
──────────────────────────────────
1. E-Mail Hash (wenn User sich identifiziert hat)
   └── Stärkste Verknüpfung, cross-device möglich

2. Stape User ID (X-STAPE-USER-ID)
   └── Primärer Anonymous-Identifier
   └── Persistiert über Sessions hinweg
   └── Server-gesetzt, ITP-resistent

3. GA4 Client ID (_ga Cookie)
   └── Fallback wenn Stape ID nicht verfügbar
   └── Kann von Safari nach 7 Tagen gelöscht werden

4. Session ID
   └── Nur für Intra-Session Analyse
```

## 10.4 Datenmodell (Multi-Tenant & User-Stitching)

### Event-Tabelle (mit Property ID)

```sql
CREATE TABLE `brixon-analytics.analytics.events` (
  -- ═══════════════════════════════════════════════════
  -- MULTI-TENANT IDENTIFIER (PFLICHT!)
  -- ═══════════════════════════════════════════════════
  property_id STRING NOT NULL,        -- "drwerner", "kunde_xyz" - Pflichtfeld!

  -- Identifikation
  event_id STRING NOT NULL,           -- UUID für jedes Event
  event_name STRING NOT NULL,         -- page_view, lead_magnet_download, etc.
  event_timestamp TIMESTAMP NOT NULL, -- Zeitpunkt des Events

  -- User Identifikation (Hierarchie)
  stape_user_id STRING,               -- X-STAPE-USER-ID (primär!)
  ga4_client_id STRING,               -- GA4 _ga Cookie
  session_id STRING,                  -- Session ID
  user_email_hash STRING,             -- SHA256 wenn bekannt

  -- Verknüpfung zu Lead (nach Identifikation)
  lead_id STRING,                     -- FK zu leads Tabelle (nach User-Stitching)

  -- User Properties (nur bei identifizierten Usern)
  user_first_name STRING,
  user_last_name STRING,
  user_company STRING,

  -- ═══════════════════════════════════════════════════
  -- VOLLSTÄNDIGE AD-PLATFORM PARAMETER
  -- ═══════════════════════════════════════════════════

  -- Traffic Source (Allgemein)
  traffic_source STRING,              -- google_ads, meta_ads, linkedin_ads, organic, direct, referral
  traffic_medium STRING,              -- cpc, cpm, organic, referral, email
  traffic_campaign STRING,            -- Kampagnenname

  -- UTM Parameter (vollständig)
  utm_source STRING,                  -- google, facebook, linkedin, newsletter
  utm_medium STRING,                  -- cpc, email, social
  utm_campaign STRING,                -- kampagnenname
  utm_term STRING,                    -- keyword (bei Search)
  utm_content STRING,                 -- ad-variante, cta-text

  -- Google Ads Parameter
  gclid STRING,                       -- Google Click ID
  gclsrc STRING,                      -- Google Click Source
  wbraid STRING,                      -- Web-to-App Attribution
  gbraid STRING,                      -- App-to-Web Attribution
  gad_source STRING,                  -- Google Ads Source
  google_campaign_id STRING,          -- Kampagnen-ID
  google_adgroup_id STRING,           -- Anzeigengruppen-ID
  google_ad_id STRING,                -- Anzeigen-ID
  google_keyword STRING,              -- Suchbegriff
  google_matchtype STRING,            -- exact, phrase, broad
  google_network STRING,              -- search, display, youtube
  google_placement STRING,            -- Placement (Display/YouTube)
  google_device STRING,               -- c (computer), m (mobile), t (tablet)
  google_location STRING,             -- Geo-Location ID

  -- Meta Ads Parameter
  fbclid STRING,                      -- Facebook Click ID
  fb_campaign_id STRING,              -- Meta Kampagnen-ID
  fb_adset_id STRING,                 -- Meta Adset ID
  fb_ad_id STRING,                    -- Meta Ad ID
  fb_placement STRING,                -- feed, stories, reels, audience_network
  fb_source STRING,                   -- fb, ig, an, msg

  -- LinkedIn Ads Parameter
  li_fat_id STRING,                   -- LinkedIn First-Party Ad Tracking ID
  linkedin_campaign_id STRING,        -- LinkedIn Kampagnen-ID
  linkedin_creative_id STRING,        -- LinkedIn Creative ID
  linkedin_campaign_group_id STRING,  -- LinkedIn Kampagnengruppen-ID

  -- ═══════════════════════════════════════════════════
  -- PAGE & SESSION DATA
  -- ═══════════════════════════════════════════════════

  -- Page Data
  page_url STRING,                    -- Vollständige URL
  page_path STRING,                   -- Nur Pfad ohne Domain
  page_title STRING,                  -- Seitentitel
  page_referrer STRING,               -- Vorherige Seite
  page_hostname STRING,               -- drwerner.com oder philippsauerborn.com

  -- Session Data
  session_number INTEGER,             -- Wievielter Besuch des Users
  session_start BOOLEAN,              -- Erstes Event der Session?
  session_engaged BOOLEAN,            -- Engagement-Session (>10s oder Conversion)
  page_views_in_session INTEGER,      -- Bisherige Pageviews in Session
  time_on_page_seconds INTEGER,       -- Zeit auf vorheriger Seite

  -- ═══════════════════════════════════════════════════
  -- EVENT-SPEZIFISCHE PARAMETER
  -- ═══════════════════════════════════════════════════

  -- Für Lead Events
  lead_magnet_name STRING,            -- z.B. "Malta Checkliste"
  lead_magnet_type STRING,            -- pdf, checklist, guide, video
  lead_magnet_topic STRING,           -- firmengründung, auswanderung
  quickcheck_name STRING,             -- Name des QuickChecks
  quickcheck_result STRING,           -- Ergebnis-Kategorie
  quickcheck_score INTEGER,           -- Numerischer Score
  form_name STRING,                   -- Formular-Identifier
  form_destination STRING,            -- Wohin geht die Anfrage

  -- Für E-Commerce (falls relevant)
  service_interest STRING,            -- Welcher Service interessiert
  estimated_value FLOAT64,            -- Geschätzter Deal-Wert

  -- Event Parameters (JSON für Flexibilität)
  event_params JSON,                  -- Zusätzliche Parameter als JSON

  -- ═══════════════════════════════════════════════════
  -- CONSENT STATUS (zur Dokumentation)
  -- ═══════════════════════════════════════════════════

  consent_analytics STRING,           -- granted, denied, not_set
  consent_marketing STRING,           -- granted, denied, not_set
  consent_timestamp TIMESTAMP,        -- Wann wurde Consent gegeben/verweigert

  -- ═══════════════════════════════════════════════════
  -- GEO & DEVICE
  -- ═══════════════════════════════════════════════════

  -- Geo (aus Stape Geo Headers)
  ip_address STRING,                  -- IP (oder anonymisiert)
  country_code STRING,                -- DE, AT, CH, MT, etc.
  country_name STRING,                -- Deutschland, Österreich, etc.
  region STRING,                      -- Bundesland/Kanton
  city STRING,                        -- Stadt
  postal_code STRING,                 -- PLZ

  -- Device & Browser
  device_category STRING,             -- desktop, mobile, tablet
  device_brand STRING,                -- Apple, Samsung, etc.
  device_model STRING,                -- iPhone 14, Galaxy S23, etc.
  browser_name STRING,                -- Chrome, Safari, Firefox, Edge
  browser_version STRING,             -- Hauptversion
  os_name STRING,                     -- Windows, macOS, iOS, Android
  os_version STRING,                  -- 11, 14.5, etc.
  screen_resolution STRING,           -- 1920x1080
  viewport_size STRING,               -- 1200x800
  user_agent STRING,                  -- Vollständiger User Agent

  -- ═══════════════════════════════════════════════════
  -- METADATA
  -- ═══════════════════════════════════════════════════

  ingestion_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  data_source STRING DEFAULT 'stape_gtm',  -- stape_gtm, manual_import, etc.
  is_bot BOOLEAN DEFAULT FALSE,       -- Bot-Traffic markiert
  is_internal BOOLEAN DEFAULT FALSE   -- Interne Zugriffe markiert
)
PARTITION BY DATE(event_timestamp)
CLUSTER BY property_id, stape_user_id, event_name;
```

### Lead-Tabelle (Identifizierte Nutzer mit PII)

> **Wichtig**: Diese Tabelle enthält echte personenbezogene Daten (PII) und wird nur befüllt, wenn ein User sich identifiziert hat (Lead Magnet Download, Kontaktformular, etc.).

```sql
CREATE TABLE `brixon-analytics.analytics.leads` (
  -- ═══════════════════════════════════════════════════
  -- IDENTIFIKATION
  -- ═══════════════════════════════════════════════════
  lead_id STRING NOT NULL,              -- UUID, Primärschlüssel
  property_id STRING NOT NULL,          -- Multi-Tenant: "drwerner", etc.

  -- Verknüpfung zu anonymen Tracking-Daten
  stape_user_ids ARRAY<STRING>,         -- Alle bekannten Stape User IDs dieses Leads
  ga4_client_ids ARRAY<STRING>,         -- Alle bekannten GA4 Client IDs
  email_hash STRING,                    -- SHA256 für Matching ohne PII-Zugriff

  -- ═══════════════════════════════════════════════════
  -- PERSONENBEZOGENE DATEN (PII)
  -- ═══════════════════════════════════════════════════
  email STRING,                         -- E-Mail-Adresse
  first_name STRING,
  last_name STRING,
  full_name STRING,                     -- Falls nicht getrennt erfasst
  phone STRING,
  company STRING,
  job_title STRING,
  website STRING,

  -- Adresse (falls erfasst)
  street STRING,
  city STRING,
  postal_code STRING,
  country STRING,

  -- ═══════════════════════════════════════════════════
  -- LEAD-QUALIFIZIERUNG
  -- ═══════════════════════════════════════════════════
  lead_status STRING,                   -- anonymous→known→mql→sql→customer→churned
  lead_score INTEGER DEFAULT 0,         -- Aktueller Score
  lead_grade STRING,                    -- A/B/C/D Klassifizierung

  -- Status-Timestamps
  known_at TIMESTAMP,                   -- Erstes Mal identifiziert
  mql_at TIMESTAMP,                     -- Marketing Qualified
  sql_at TIMESTAMP,                     -- Sales Qualified
  customer_at TIMESTAMP,                -- Wurde Kunde
  churned_at TIMESTAMP,                 -- Wurde inaktiv/verloren

  -- Qualifizierungs-Trigger
  mql_trigger STRING,                   -- "lead_magnet_download", "quickcheck_complete"
  sql_trigger STRING,                   -- "contact_form", "consultation_booking"

  -- ═══════════════════════════════════════════════════
  -- FIRST TOUCH ATTRIBUTION (bei Identifikation kopiert)
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
  -- CONVERSION ATTRIBUTION (bei Lead-Event)
  -- ═══════════════════════════════════════════════════
  conversion_timestamp TIMESTAMP,       -- Wann wurde Lead zum Lead?
  conversion_source STRING,
  conversion_medium STRING,
  conversion_campaign STRING,
  conversion_landing_page STRING,
  conversion_gclid STRING,
  conversion_fbclid STRING,

  -- ═══════════════════════════════════════════════════
  -- ENGAGEMENT-DATEN
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

  -- Aggregierte Metriken (via Scheduled Query aktualisiert)
  total_sessions INTEGER,
  total_pageviews INTEGER,
  total_time_on_site_seconds INTEGER,
  last_seen_at TIMESTAMP,
  days_since_last_visit INTEGER,

  -- Kanäle & Kampagnen (Multi-Touch)
  channels_touched ARRAY<STRING>,       -- ["google_ads", "organic", "email"]
  campaigns_touched ARRAY<STRING>,

  -- ═══════════════════════════════════════════════════
  -- CRM-VERKNÜPFUNGEN
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
  service_interest STRING,              -- Welcher Service interessiert
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

### Identity Graph (User-Stitching)

> Diese Tabelle verknüpft anonyme Identifier mit identifizierten Leads. Ermöglicht die Zuordnung der vollständigen Customer Journey zu einem Lead.

```sql
CREATE TABLE `brixon-analytics.analytics.identity_graph` (
  -- Verknüpfung
  property_id STRING NOT NULL,
  lead_id STRING NOT NULL,              -- FK zu leads Tabelle
  identifier_type STRING NOT NULL,      -- "stape_user_id", "ga4_client_id", "email_hash"
  identifier_value STRING NOT NULL,     -- Der tatsächliche Identifier-Wert

  -- Kontext
  first_seen_at TIMESTAMP,              -- Wann wurde dieser Identifier erstmals gesehen
  last_seen_at TIMESTAMP,               -- Wann zuletzt
  confidence_score FLOAT64,             -- 0-1, wie sicher ist die Zuordnung
  match_source STRING,                  -- "form_submit", "login", "email_click"

  -- Metadata
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),

  PRIMARY KEY (property_id, lead_id, identifier_type, identifier_value) NOT ENFORCED
)
CLUSTER BY property_id, identifier_type;

-- Index für schnelles Lookup
-- Wenn Stape User ID bekannt → Finde zugehörigen Lead
```

### User-Stitching Workflow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        USER-STITCHING PROZESS                                │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  1. ANONYMER BESUCH                                                          │
│     ─────────────────                                                        │
│     User besucht Website → Stape User ID wird generiert                     │
│     Events werden in events-Tabelle gespeichert                             │
│     lead_id = NULL (noch nicht identifiziert)                               │
│                                                                              │
│  2. IDENTIFIKATION (Lead Magnet Download, Kontakt, etc.)                    │
│     ─────────────────────────────────────────────────────                   │
│     User gibt E-Mail ein → lead_magnet_download Event                       │
│     │                                                                        │
│     ├── A) Neue E-Mail: Neuer Lead wird erstellt                            │
│     │   → INSERT INTO leads (email, stape_user_ids, ...)                    │
│     │   → INSERT INTO identity_graph (stape_user_id → lead_id)              │
│     │                                                                        │
│     └── B) Bekannte E-Mail: Existierender Lead                              │
│         → Stape User ID wird zu stape_user_ids Array hinzugefügt            │
│         → INSERT INTO identity_graph (neuer stape_user_id → lead_id)        │
│                                                                              │
│  3. RÜCKWIRKENDE VERKNÜPFUNG                                                │
│     ─────────────────────────                                                │
│     Scheduled Query aktualisiert alle bisherigen Events:                    │
│     UPDATE events SET lead_id = [ermittelter lead_id]                       │
│     WHERE stape_user_id IN (SELECT identifier_value FROM identity_graph)    │
│                                                                              │
│  4. CROSS-DEVICE STITCHING                                                  │
│     ───────────────────────                                                  │
│     User loggt sich auf anderem Gerät ein (gleiche E-Mail)                  │
│     → Neuer stape_user_id wird mit bestehendem Lead verknüpft               │
│     → Vollständige Journey über alle Geräte sichtbar                        │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### User-Stitching SQL (Scheduled Query)

```sql
-- Diese Query läuft alle 15 Minuten und verknüpft Events mit Leads

-- 1. Finde alle Events mit stape_user_id die einem Lead zugeordnet werden können
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
   AND e.lead_id IS NULL  -- Nur Events ohne Lead-Zuordnung
WHEN MATCHED THEN
  UPDATE SET e.lead_id = matches.lead_id;

-- 2. Update Lead-Metriken basierend auf verknüpften Events
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

### Sessions-Tabelle (mit Property ID)

```sql
CREATE TABLE `brixon-analytics.analytics.sessions` (
  property_id STRING NOT NULL,          -- Multi-Tenant
  session_id STRING NOT NULL,
  stape_user_id STRING NOT NULL,
  lead_id STRING,                       -- FK zu leads (nach Stitching)

  -- Session Timing
  session_start TIMESTAMP,
  session_end TIMESTAMP,
  session_duration_seconds INTEGER,

  -- Engagement
  pageviews INTEGER,
  events_count INTEGER,
  engaged_session BOOLEAN,

  -- Traffic Source (dieser Session)
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

  -- Device/Geo (dieser Session)
  device_category STRING,
  country STRING,
  city STRING,

  -- Metadata
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
)
PARTITION BY DATE(session_start)
CLUSTER BY property_id, stape_user_id;
```

### Users-Tabelle (Anonyme User-Profile)

> **Hinweis**: Diese Tabelle enthält aggregierte Daten für anonyme User. Sobald ein User identifiziert wird, werden die Daten in die leads-Tabelle übertragen.

```sql
CREATE TABLE `brixon-analytics.analytics.users` (
  -- Primäre Identifikation
  property_id STRING NOT NULL,
  stape_user_id STRING NOT NULL,        -- Primärschlüssel
  ga4_client_ids ARRAY<STRING>,         -- Alle bekannten GA4 Client IDs
  email_hash STRING,                    -- SHA256 der E-Mail (wenn bekannt)

  -- Lead-Verknüpfung
  lead_id STRING,                       -- FK zu leads (nach Identifikation)
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
  known_date DATE,                    -- Wann wurde User identifiziert
  mql_date DATE,                      -- Marketing Qualified Lead Datum
  mql_trigger STRING,                 -- Was hat MQL ausgelöst
  sql_date DATE,                      -- Sales Qualified Lead Datum
  sql_trigger STRING,                 -- Was hat SQL ausgelöst
  customer_date DATE,                 -- Wann wurde User Kunde

  -- Engagement Metriken
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
  campaigns_touched ARRAY<STRING>,    -- Alle Kampagnen mit denen User Kontakt hatte

  -- CRM Verknüpfung
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

### Trigger: ALLE Events (Consent-Unabhängig!)

```
GTM Server Container - BigQuery Tag Trigger:
────────────────────────────────────────────
Trigger Name:    All GA4 Events - BigQuery
Trigger Type:    Custom Event
Event Name:      .*  (Regex: Alle Events)

WICHTIG: Kein Consent-Check!
→ Tag feuert bei JEDEM Event
→ Unabhängig vom Consent-Status
→ Eigene Datenverarbeitung = Kein Consent nötig
```

### GTM Server Tag: BigQuery HTTP Request (Vollständig)

```
Tag Konfiguration:
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

Trigger: All GA4 Events - BigQuery (OHNE Consent-Bedingung!)
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

Eingabe-Variable: {{Page Hostname}}

Lookup-Tabelle:
┌─────────────────────────────┬──────────────────────┐
│ Hostname                    │ Property ID          │
├─────────────────────────────┼──────────────────────┤
│ drwerner.com                │ drwerner             │
│ www.drwerner.com            │ drwerner             │
│ philippsauerborn.com        │ drwerner             │  ← Gleiche Property
│ www.philippsauerborn.com    │ drwerner             │
│ kunde-xyz.de                │ kunde_xyz            │  ← Anderer Kunde
│ www.kunde-xyz.de            │ kunde_xyz            │
└─────────────────────────────┴──────────────────────┘

Default-Wert (wenn kein Match): unknown_property

HINWEIS: Bei jedem neuen Kunden-Projekt muss hier ein
Eintrag hinzugefügt werden. Der Property ID Wert muss
mit dem Eintrag in der BigQuery properties-Tabelle
übereinstimmen.
```

## 10.5 Was wird getrackt? (Event-Übersicht)

### Automatisch bei jedem Request

| Event | Beschreibung | Trigger |
|-------|--------------|---------|
| `page_view` | Jeder Seitenaufruf | Automatisch |
| `session_start` | Neue Session begonnen | Automatisch |
| `first_visit` | Erstbesuch | Automatisch |
| `user_engagement` | Aktive Zeit auf Seite | Automatisch (>10s) |

### Explizit getrackte Events

| Event | Beschreibung | DataLayer Trigger |
|-------|--------------|-------------------|
| `cta_click` | CTA-Button geklickt | Click auf .cta-button |
| `service_page_view` | Leistungsseite angesehen | /leistungen/* Seiten |
| `lead_magnet_view` | Lead Magnet Seite | /downloads/* Seiten |
| `lead_magnet_download` | PDF heruntergeladen | Vavolta Success |
| `quickcheck_start` | QuickCheck begonnen | QuickCheck Formular |
| `quickcheck_complete` | QuickCheck abgeschlossen | QuickCheck Submit |
| `newsletter_signup` | Newsletter-Anmeldung | Newsletter Form |
| `contact_form_start` | Kontaktformular begonnen | Form Focus |
| `contact_form_submit` | Kontaktformular gesendet | Form Submit |
| `callback_request` | Rückruf angefordert | Callback Form |
| `consultation_booking` | Termin gebucht | Calendly/Booking Success |

### NICHT getrackt (Performance)

- Scroll-Events (zu viele Datenpunkte)
- Mouse-Movements
- Hover-Events
- Micro-Interactions

## 10.6 Customer Journey Analyse

### Beispiel-Queries

> **Wichtig**: Alle Queries enthalten `property_id` Filter für Multi-Tenant Isolation.

#### Vollständige Journey eines Users (via Stape User ID)

```sql
-- Zeigt alle Events eines bestimmten Users für DrWerner
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

#### Journey eines identifizierten Leads (via Lead ID)

```sql
-- Nach User-Stitching: Zeigt ALLE Events eines Leads (auch vor Identifikation)
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
  AND l.email = 'max.mustermann@example.com'
ORDER BY e.event_timestamp;
```

#### Multi-Touch Attribution Report

```sql
-- Alle Touchpoints vor Conversion (für DrWerner)
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

#### Consent-Unabhängige Analyse

```sql
-- Wie viele User haben Consent verweigert, aber trotzdem konvertiert?
SELECT
  consent_marketing,
  COUNT(DISTINCT stape_user_id) as unique_users,
  COUNTIF(event_name = 'lead_magnet_download') as lead_downloads,
  COUNTIF(event_name = 'contact_form_submit') as contact_forms
FROM `brixon-analytics.analytics.events`
WHERE property_id = 'drwerner'                  -- Multi-Tenant Filter!
GROUP BY consent_marketing;

-- Ergebnis zeigt: Auch "denied" User werden vollständig getrackt!
```

#### Lead-Qualität nach Kanal

```sql
-- Welcher Kanal bringt die besten Leads? (MQL → SQL Conversion Rate)
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

#### Time-to-Conversion nach Kanal

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

#### Cross-Property Benchmark (Agentur-Überblick)

```sql
-- Vergleich aller Kunden-Properties (nur für Agentur-Admins)
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

## 11.1 Problemstellung

DrWerner.com und philippsauerborn.com sind separate Domains. Ohne spezielle Konfiguration:
- User besucht philippsauerborn.com → Client ID "A"
- User wechselt zu drwerner.com → Neue Client ID "B"
- **Resultat**: Eine Person, zwei Profile, Journey unterbrochen

## 11.2 Multi-Brand Architektur (Separate GA4 Properties)

### Grundprinzip: Separation auf Property-Ebene, Vereinigung auf Daten-Ebene

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        MULTI-BRAND TRACKING ARCHITEKTUR                      │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   drwerner.com                           philippsauerborn.com               │
│   ┌────────────────────────┐             ┌────────────────────────┐         │
│   │ GA4 Property           │             │ GA4 Property           │         │
│   │ "DrWerner"             │             │ "Philipp Sauerborn"    │         │
│   │ G-DRWERNER001          │             │ G-PHILIPP001           │         │
│   └───────────┬────────────┘             └───────────┬────────────┘         │
│               │                                       │                      │
│   ┌───────────┴────────────┐             ┌───────────┴────────────┐         │
│   │ GTM Web Container      │             │ GTM Web Container      │         │
│   │ GTM-DRWERNER           │             │ GTM-PHILIPP            │         │
│   │ (Brand-spezifisch)     │             │ (Brand-spezifisch)     │         │
│   └───────────┬────────────┘             └───────────┬────────────┘         │
│               │                                       │                      │
│               └──────────────────┬────────────────────┘                      │
│                                  ↓                                           │
│                    ┌─────────────────────────────┐                           │
│                    │ SHARED: Stape Server        │                           │
│                    │ Container                   │                           │
│                    │ s.drwerner.com              │                           │
│                    │                             │                           │
│                    │ - Routing nach property_id  │                           │
│                    │ - Unified User ID           │                           │
│                    │ - Click ID Management       │                           │
│                    └──────────────┬──────────────┘                           │
│                                   ↓                                          │
│                    ┌─────────────────────────────┐                           │
│                    │ BigQuery                    │                           │
│                    │ (Unified Data Layer)        │                           │
│                    │                             │                           │
│                    │ - Alle Events mit           │                           │
│                    │   property_id Feld          │                           │
│                    │ - Cross-Brand User          │                           │
│                    │   Stitching                 │                           │
│                    │ - Unified Attribution       │                           │
│                    └─────────────────────────────┘                           │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Vorteile dieser Architektur

| Aspekt | Vorteil |
|--------|---------|
| **Brand-spezifische Reports** | Jede Brand hat eigene GA4-Metriken, Audiences, Explorations |
| **Flexibilität** | Weitere Brands können einfach hinzugefügt werden |
| **Unified Attribution** | BigQuery führt alle Daten zusammen für Cross-Brand-Analyse |
| **Saubere Daten** | Keine Vermischung von Brand-Metriken in GA4 |
| **Compliance** | Separate Consent-Behandlung pro Brand möglich |

### GTM Web Container Konfiguration

**Container 1: GTM-DRWERNER (drwerner.com)**
```javascript
// GA4 Configuration Variable
{
  "measurementId": "G-DRWERNER001",
  "property_id": "drwerner",  // Custom dimension für BigQuery
  "send_to_server": true,
  "server_container_url": "https://s.drwerner.com"
}
```

**Container 2: GTM-PHILIPP (philippsauerborn.com)**
```javascript
// GA4 Configuration Variable
{
  "measurementId": "G-PHILIPP001",
  "property_id": "philippsauerborn",  // Custom dimension für BigQuery
  "send_to_server": true,
  "server_container_url": "https://s.drwerner.com"  // Shared!
}
```

### Server Container: Multi-Property Routing

```javascript
// Stape Server Tag: GA4 Routing
const propertyId = eventData.property_id;
const measurementId = propertyId === 'drwerner'
  ? 'G-DRWERNER001'
  : 'G-PHILIPP001';

// An korrektes GA4 Property senden
sendToGA4({
  measurement_id: measurementId,
  ...eventData
});

// IMMER an BigQuery mit property_id
sendToBigQuery({
  property_id: propertyId,
  ...eventData
});
```

## 11.3 Cross-Brand User Journey Tracking

### Herausforderung
- User besucht drwerner.com → erhält `brixon_uid = "uid_abc123"`
- User besucht später philippsauerborn.com → erhält `brixon_uid = "uid_xyz789"`
- **Problem**: Zwei verschiedene User IDs für dieselbe Person

### Lösung: Server-Side User ID Stitching

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        USER ID STITCHING FLOW                                │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  PHASE 1: ANONYME INTERAKTION                                                │
│  ─────────────────────────────────                                           │
│                                                                              │
│  drwerner.com                    philippsauerborn.com                        │
│  brixon_uid = "uid_abc123"       brixon_uid = "uid_xyz789"                  │
│  (Cookie gesetzt)                (separater Cookie)                          │
│                                                                              │
│  → In BigQuery: Separate User IDs, keine Verbindung                         │
│                                                                              │
│  PHASE 2: IDENTIFIKATION (z.B. Lead Magnet auf drwerner.com)                │
│  ─────────────────────────────────────────────────────────────              │
│                                                                              │
│  User gibt E-Mail an: max@example.com                                        │
│  Server prüft: Ist diese E-Mail bereits bekannt?                             │
│                                                                              │
│  FALL A: Neue E-Mail                                                         │
│  → Master-UID erstellen: "uid_master_001"                                    │
│  → Mapping speichern: uid_abc123 → uid_master_001                           │
│                                                                              │
│  FALL B: E-Mail existiert bereits (von philippsauerborn.com)                │
│  → Existierende Master-UID finden: "uid_master_001"                          │
│  → BEIDE Anonymous IDs verknüpfen:                                           │
│    uid_abc123 → uid_master_001                                               │
│    uid_xyz789 → uid_master_001                                               │
│                                                                              │
│  PHASE 3: IDENTITY GRAPH IN BIGQUERY                                         │
│  ─────────────────────────────────────                                       │
│                                                                              │
│  identity_graph Tabelle:                                                     │
│  ┌──────────────────┬────────────────────┬─────────────────┐                │
│  │ master_uid       │ identifier         │ identifier_type │                │
│  ├──────────────────┼────────────────────┼─────────────────┤                │
│  │ uid_master_001   │ uid_abc123         │ anonymous_id    │                │
│  │ uid_master_001   │ uid_xyz789         │ anonymous_id    │                │
│  │ uid_master_001   │ max@example.com    │ email           │                │
│  │ uid_master_001   │ +49171234567       │ phone           │                │
│  └──────────────────┴────────────────────┴─────────────────┘                │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Server-Side Implementation (Stape)

```javascript
// Bei jeder Identifikation (Lead Magnet, Kontaktformular, Newsletter)
const email = eventData.user_email;
const currentUid = getCookie('brixon_uid') || generateUID();
const propertyId = eventData.property_id;

if (email) {
  // 1. Prüfe ob E-Mail bereits bekannt
  const existingMapping = await bigQueryLookup(
    `SELECT master_uid FROM identity_graph
     WHERE identifier = '${email}' AND identifier_type = 'email'`
  );

  if (existingMapping) {
    // E-Mail existiert → Merge mit existierendem Master
    const masterUid = existingMapping.master_uid;

    // Aktuellen Anonymous-UID mit Master verknüpfen
    await bigQueryInsert('identity_graph', {
      master_uid: masterUid,
      identifier: currentUid,
      identifier_type: 'anonymous_id',
      property_id: propertyId,
      created_at: new Date()
    });

    // Cookie auf Master-UID setzen
    setCookie('brixon_uid', masterUid, { domain: '.drwerner.com', maxAge: 63072000 });

  } else {
    // Neue E-Mail → Neuen Master erstellen
    const newMasterUid = 'uid_master_' + generateUUID();

    // Anonymous-UID verknüpfen
    await bigQueryInsert('identity_graph', {
      master_uid: newMasterUid,
      identifier: currentUid,
      identifier_type: 'anonymous_id',
      property_id: propertyId,
      created_at: new Date()
    });

    // E-Mail verknüpfen
    await bigQueryInsert('identity_graph', {
      master_uid: newMasterUid,
      identifier: email,
      identifier_type: 'email',
      property_id: propertyId,
      created_at: new Date()
    });

    setCookie('brixon_uid', newMasterUid, { domain: '.drwerner.com', maxAge: 63072000 });
  }
}
```

### Cross-Domain Link Tracking (Optional)

Wenn explizite Links zwischen den Brands existieren:

```javascript
// GTM Web Tag: Outbound Link Decoration
document.querySelectorAll('a[href*="philippsauerborn.com"]').forEach(link => {
  link.addEventListener('click', function(e) {
    const currentUid = getCookie('brixon_uid');
    if (currentUid) {
      const url = new URL(this.href);
      url.searchParams.set('_bxuid', currentUid);
      this.href = url.toString();
    }
  });
});
```

```javascript
// Stape Server: Eingehenden _bxuid Parameter verarbeiten
const incomingBxuid = getUrlParameter('_bxuid');
const existingUid = getCookie('brixon_uid');

if (incomingBxuid && incomingBxuid !== existingUid) {
  // Beide UIDs im identity_graph verknüpfen
  await linkAnonymousIds(incomingBxuid, existingUid);
}
```

## 11.4 BigQuery: Cross-Brand Reporting Views

### View: Unified User Journey

```sql
-- View: vw_unified_user_journey
-- Kombiniert Events von allen Properties mit aufgelösten User IDs

CREATE OR REPLACE VIEW `project.dataset.vw_unified_user_journey` AS
WITH resolved_users AS (
  SELECT
    e.*,
    COALESCE(ig.master_uid, e.user_id) AS unified_user_id
  FROM `project.dataset.events` e
  LEFT JOIN `project.dataset.identity_graph` ig
    ON e.user_id = ig.identifier
    AND ig.identifier_type = 'anonymous_id'
)
SELECT
  unified_user_id,
  property_id,
  event_name,
  event_timestamp,
  page_location,
  traffic_source,
  -- Cross-Brand Journey
  LAG(property_id) OVER (
    PARTITION BY unified_user_id
    ORDER BY event_timestamp
  ) AS previous_property,
  -- Time since last interaction
  TIMESTAMP_DIFF(
    event_timestamp,
    LAG(event_timestamp) OVER (PARTITION BY unified_user_id ORDER BY event_timestamp),
    MINUTE
  ) AS minutes_since_last_event
FROM resolved_users
ORDER BY unified_user_id, event_timestamp;
```

### View: Cross-Brand Conversion Paths

```sql
-- View: vw_cross_brand_conversions
-- Zeigt Conversion Paths die beide Brands berühren

CREATE OR REPLACE VIEW `project.dataset.vw_cross_brand_conversions` AS
WITH user_touchpoints AS (
  SELECT
    unified_user_id,
    property_id,
    event_name,
    event_timestamp,
    traffic_source
  FROM `project.dataset.vw_unified_user_journey`
),
conversions AS (
  SELECT
    unified_user_id,
    event_timestamp AS conversion_time,
    event_name AS conversion_type
  FROM user_touchpoints
  WHERE event_name IN ('contact_form_submit', 'lead_magnet_download', 'quickcheck_complete')
)
SELECT
  c.unified_user_id,
  c.conversion_type,
  c.conversion_time,
  -- Properties besucht vor Conversion
  ARRAY_AGG(DISTINCT t.property_id) AS properties_in_journey,
  -- Ist es eine Cross-Brand Journey?
  CASE
    WHEN COUNT(DISTINCT t.property_id) > 1 THEN TRUE
    ELSE FALSE
  END AS is_cross_brand_journey,
  -- Touchpoints vor Conversion
  COUNT(*) AS touchpoints_before_conversion
FROM conversions c
JOIN user_touchpoints t
  ON c.unified_user_id = t.unified_user_id
  AND t.event_timestamp < c.conversion_time
GROUP BY c.unified_user_id, c.conversion_type, c.conversion_time;
```

---

# 12. Cookie Keeper & First-Party Data

## 12.1 Cookie-Lebensdauer Problem

### Browser-Einschränkungen 2024/2025

| Browser | Third-Party Cookies | First-Party Cookies | ITP/ETP |
|---------|--------------------|--------------------|---------|
| Safari | Blockiert | 7 Tage (JS-gesetzt) | ITP aktiv |
| Firefox | Blockiert (Standard) | 7 Tage (Tracking) | ETP aktiv |
| Chrome | 2025 eingestellt | Unbegrenzt | Privacy Sandbox |
| Edge | Folgt Chrome | Unbegrenzt | - |

**Problem**: Ein User, der vor 8 Tagen mit Safari da war, wird als neuer User gezählt.

## 12.2 Stape Cookie Keeper Lösung

### Funktionsweise

```
Ohne Cookie Keeper:
───────────────────
Browser setzt Cookie via JavaScript
→ Safari löscht nach 7 Tagen
→ User wird "neu"

Mit Cookie Keeper:
──────────────────
1. Browser sendet Request an s.drwerner.com
2. Stape-Server setzt Cookie via HTTP Response Header
3. HTTP-Header-Cookies = First-Party, Server-gesetzt
4. Safari ITP greift NICHT (keine JavaScript-Cookies)
5. Cookie lebt bis zu 2 Jahre
```

### Technische Details

Cookie Keeper setzt/erneuert bei jedem Request:
- `_ga` / `_ga_XXXXX` (GA4)
- `_gcl_au` (Google Ads)
- `_fbc` / `_fbp` (Meta)
- Custom Cookies (konfigurierbar)

### Aktivierung in Stape

```
Stape Dashboard → Container → Power-Ups → Cookie Keeper
────────────────────────────────────────────────────────
Standard Cookies:
☑ Google Analytics (_ga, _ga_session)
☑ Google Ads (_gcl_au, _gcl_aw)
☑ Meta (_fbc, _fbp)
☑ LinkedIn
☐ TikTok (nicht relevant)

Custom Cookies:
+ Name: user_id
  Typ: Persistent
  TTL: 730 Tage (2 Jahre)
```

## 12.3 First-Party Data Strategie

### Sammeln von First-Party Daten

Mit Consent können folgende Daten gesammelt werden:

| Datentyp | Erfassung | Verwendung |
|----------|-----------|------------|
| E-Mail | Lead Magnet Form | Identifikation, Nurturing |
| Name | Lead Magnet Form | Personalisierung |
| Verhaltensdaten | Pageviews, Clicks | Segmentierung, Scoring |
| Präferenzen | QuickCheck Antworten | Personalisierte Journey |
| Traffic Source | UTM Parameter | Attribution |

### Datenqualität sicherstellen

1. **E-Mail Validierung**: Nur gültige E-Mails akzeptieren
2. **Deduplizierung**: User-ID Management in BigQuery
3. **Consent-Tracking**: Dokumentieren wann/wie Consent gegeben wurde
4. **Daten-Hygiene**: Regelmäßige Bereinigung inaktiver Kontakte

## 12.4 Click ID Persistierung (GCLID, FBCLID, li_fat_id)

### Das Problem: Click IDs gehen verloren

Wenn ein User über eine Werbeanzeige auf die Website kommt, hängt die Click ID als URL-Parameter dran:

```
https://drwerner.com/malta-firmengründung?gclid=EAIaIQob...
https://drwerner.com/malta-firmengründung?fbclid=IwAR3x...
https://drwerner.com/malta-firmengründung?li_fat_id=abc123...
```

**Was passiert ohne Persistierung:**
1. User klickt Google Ads Anzeige → landet auf Seite mit `?gclid=...`
2. User navigiert zu anderer Seite → GCLID ist weg (nicht mehr in URL)
3. User füllt 3 Tage später Kontaktformular aus → Keine Attribution möglich!

**Das bedeutet:** Google, Meta und LinkedIn können die Conversion nicht der Anzeige zuordnen → Kampagnen-Optimierung leidet.

### Die Lösung: Mehrschichtige Persistierung

Click IDs müssen an drei Stellen gespeichert werden:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    CLICK ID PERSISTIERUNG                                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  1. ERSTE ERFASSUNG (bei Seitenaufruf mit Click ID)                         │
│     ─────────────────────────────────────────────────                       │
│     URL: drwerner.com/seite?gclid=ABC123&fbclid=XYZ789                      │
│                        │                                                     │
│                        ▼                                                     │
│     GTM Web Container liest URL-Parameter aus                               │
│                        │                                                     │
│                        ▼                                                     │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │                    SPEICHERUNG (3 Wege)                              │   │
│  ├───────────────────┬───────────────────┬─────────────────────────────┤   │
│  │   1. COOKIES      │   2. DATALAYER    │   3. LOCAL STORAGE          │   │
│  │   (via Cookie     │   (für GTM Tags)  │   (Backup)                  │   │
│  │    Keeper)        │                   │                             │   │
│  │                   │                   │                             │   │
│  │   _gcl_aw=ABC123  │   gclid: ABC123   │   drw_gclid=ABC123          │   │
│  │   _fbc=XYZ789     │   fbclid: XYZ789  │   drw_fbclid=XYZ789         │   │
│  │   _li_fat=...     │   li_fat_id: ...  │   drw_li_fat=...            │   │
│  │                   │                   │                             │   │
│  │   Lebensdauer:    │   Lebensdauer:    │   Lebensdauer:              │   │
│  │   2 Jahre (Stape) │   Session         │   Unbegrenzt                │   │
│  └───────────────────┴───────────────────┴─────────────────────────────┘   │
│                                                                              │
│  2. BEI JEDEM SEITENAUFRUF                                                  │
│     ──────────────────────                                                   │
│     GTM prüft: Gibt es Click IDs in URL, Cookie oder LocalStorage?          │
│     → Schreibt vorhandene Werte in DataLayer                                │
│     → Server Container erhält Click IDs bei jedem Event                     │
│                                                                              │
│  3. BEI CONVERSION (Formular, Kontaktanfrage)                               │
│     ─────────────────────────────────────────                               │
│     Click IDs werden mitgesendet an:                                        │
│     ├── Google Ads (Enhanced Conversions mit GCLID)                         │
│     ├── Meta CAPI (mit fbclid/fbc)                                          │
│     ├── LinkedIn CAPI (mit li_fat_id)                                       │
│     ├── BigQuery (vollständige Attribution)                                 │
│     └── Salesforce (Lead-Datensatz)                                         │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Welche Click IDs gibt es?

| Plattform | Parameter | Cookie-Name | Beschreibung |
|-----------|-----------|-------------|--------------|
| **Google Ads** | `gclid` | `_gcl_aw`, `_gcl_au` | Google Click Identifier - wichtigste ID für Google Ads Attribution |
| **Google Ads** | `wbraid` | - | Web-to-App Attribution (iOS) |
| **Google Ads** | `gbraid` | - | App-to-Web Attribution |
| **Meta Ads** | `fbclid` | `_fbc` | Facebook Click Identifier |
| **Meta Ads** | - | `_fbp` | Facebook Browser ID (automatisch) |
| **LinkedIn Ads** | `li_fat_id` | `li_fat_id` | LinkedIn First-Party Ad Tracking |

### Cookie Keeper Konfiguration für Click IDs

Stape Cookie Keeper muss für alle Click ID Cookies aktiviert sein:

```
Stape Dashboard → Container → Power-Ups → Cookie Keeper
────────────────────────────────────────────────────────

Standard Cookies (ALLE aktivieren!):
☑ Google Analytics
   └── _ga, _ga_XXXXX
☑ Google Ads
   └── _gcl_aw (GCLID Cookie)
   └── _gcl_au (Google Ads User ID)
   └── _gcl_dc (DoubleClick)
☑ Meta / Facebook
   └── _fbc (Facebook Click ID)
   └── _fbp (Facebook Browser ID)
☑ LinkedIn
   └── li_fat_id (LinkedIn Click ID)

Custom Cookies (für Backup):
+ Name: drw_gclid
  Quelle: URL Parameter "gclid"
  TTL: 90 Tage

+ Name: drw_fbclid
  Quelle: URL Parameter "fbclid"
  TTL: 90 Tage

+ Name: drw_li_fat
  Quelle: URL Parameter "li_fat_id"
  TTL: 90 Tage
```

### GTM Web Container: Click ID Erfassung

So werden Click IDs aus der URL gelesen und gespeichert:

```
GTM Web Container - Click ID Variables:
───────────────────────────────────────

Variable 1: URL - GCLID
├── Typ: URL Variable
├── Component: Query
└── Key: gclid

Variable 2: URL - FBCLID
├── Typ: URL Variable
├── Component: Query
└── Key: fbclid

Variable 3: URL - li_fat_id
├── Typ: URL Variable
├── Component: Query
└── Key: li_fat_id

Variable 4: Cookie - GCLID
├── Typ: 1st Party Cookie
└── Name: _gcl_aw

Variable 5: Cookie - FBCLID
├── Typ: 1st Party Cookie
└── Name: _fbc

Variable 6: Persistierte GCLID (mit Fallback-Kette)
├── Typ: Custom JavaScript
└── Logik:
    1. Prüfe URL Parameter gclid
    2. Wenn leer → Prüfe Cookie _gcl_aw
    3. Wenn leer → Prüfe localStorage drw_gclid
    4. Ersten gefundenen Wert zurückgeben

Variable 7: Persistierte FBCLID (mit Fallback-Kette)
├── Typ: Custom JavaScript
└── Logik: (analog zu GCLID)
```

### Was passiert bei einem typischen User-Flow?

```
BEISPIEL: User Journey mit Click ID Persistierung
─────────────────────────────────────────────────

Tag 1: Erstkontakt über Google Ads
┌─────────────────────────────────────────────────────────────┐
│ User klickt Google Ads Anzeige                              │
│ → URL: drwerner.com/malta?gclid=EAIaIQobChMI...            │
│                                                             │
│ GTM Web Container:                                          │
│ 1. Liest gclid aus URL                                      │
│ 2. Speichert in DataLayer                                   │
│ 3. Sendet an Server Container                               │
│                                                             │
│ Stape Server Container:                                     │
│ 1. Cookie Keeper setzt _gcl_aw Cookie (2 Jahre)             │
│ 2. GA4 Event mit gclid Parameter                            │
│ 3. BigQuery: Event mit gclid gespeichert                    │
│                                                             │
│ User liest Seite, verlässt Website                          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
Tag 3: Wiederkehr (ohne Click ID in URL!)
┌─────────────────────────────────────────────────────────────┐
│ User tippt drwerner.com direkt ein                          │
│ → URL: drwerner.com (KEIN gclid!)                           │
│                                                             │
│ GTM Web Container:                                          │
│ 1. Kein gclid in URL                                        │
│ 2. ABER: Findet _gcl_aw Cookie!                             │
│ 3. Schreibt gclid aus Cookie in DataLayer                   │
│                                                             │
│ Stape Server Container:                                     │
│ 1. Empfängt Event MIT gclid (aus Cookie)                    │
│ 2. BigQuery: User-Session mit Original-GCLID verknüpft      │
│                                                             │
│ User schaut sich weitere Seiten an                          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
Tag 5: Conversion (Kontaktformular)
┌─────────────────────────────────────────────────────────────┐
│ User füllt Kontaktformular aus                              │
│                                                             │
│ GTM Web Container:                                          │
│ 1. contact_form_submit Event                                │
│ 2. gclid aus Cookie im DataLayer                            │
│ 3. User-Daten (E-Mail, Name) im DataLayer                   │
│                                                             │
│ Stape Server Container:                                     │
│ 1. Google Ads Enhanced Conversion MIT GCLID                 │
│    → Google kann Conversion der Anzeige zuordnen! ✓         │
│ 2. BigQuery: Lead mit vollständiger Journey                 │
│ 3. Brevo: Neuer Kontakt mit Attribution                     │
│                                                             │
│ Ergebnis: Conversion wird Tag 1 Anzeige zugerechnet         │
└─────────────────────────────────────────────────────────────┘
```

### Übergabe an Server Container

Bei jedem Event werden Click IDs im GA4-Hit mitgesendet:

```
Server Container erhält bei jedem Event:
────────────────────────────────────────

GA4 Event Parameter:
├── event_name: page_view / lead_magnet_download / etc.
├── page_location: aktuelle URL
├── page_referrer: vorherige Seite
│
├── gclid: EAIaIQob... (aus Cookie oder URL)
├── wbraid: ... (falls iOS)
├── gbraid: ... (falls App)
│
├── fbclid: IwAR3x... (aus Cookie oder URL)
├── fbc: fb.1.1234... (Meta Cookie Format)
├── fbp: fb.1.5678... (Meta Browser ID)
│
└── li_fat_id: abc123... (LinkedIn)

Diese Parameter werden dann weitergeleitet an:
→ Google Ads Tag (für Enhanced Conversions)
→ Meta CAPI Tag (für Conversion Attribution)
→ LinkedIn CAPI Tag
→ BigQuery (für eigene Analyse)
```

### Salesforce Integration für Click IDs

Die Click IDs sollen auch im Salesforce Lead-Datensatz landen:

| Salesforce Feld | Quelle | Beschreibung |
|-----------------|--------|--------------|
| `gclid__c` | Bereits vorhanden | Google Click ID |
| `fbclid__c` | **Neu anlegen** | Meta/Facebook Click ID |
| `li_fat_id__c` | **Neu anlegen** | LinkedIn Click ID |
| `wbraid__c` | Optional | Google iOS Attribution |

**Datenfluss:**
1. Lead-Event (Formular) enthält Click IDs
2. Brevo empfängt Event mit Click IDs
3. Brevo-Salesforce Sync überträgt Click IDs
4. Vertrieb sieht: "Dieser Lead kam über Google Ads Kampagne X"

### Safari ITP Workaround: Click ID Parameter-Umbenennung

**Das Problem mit Safari ITP (Intelligent Tracking Prevention):**

Safari erkennt bekannte Tracking-Parameter wie `gclid`, `fbclid` und löscht die daraus erstellten Cookies nach 7 Tagen (oder sogar 24 Stunden bei einigen Parametern).

```
Safari ITP Verhalten:
─────────────────────
URL: drwerner.com/seite?gclid=ABC123&fbclid=XYZ789

Safari erkennt: "gclid" und "fbclid" = bekannte Tracking-Parameter
→ Markiert alle daraus erstellten Cookies als "Tracking"
→ Begrenzt Cookie-Lebensdauer auf 7 Tage (oder weniger)
→ Nach 8 Tagen: Click IDs weg = Attribution verloren!
```

**Die Lösung: Parameter-Umbenennung ("Aliasing")**

Die Idee: Safari kennt `gclid` als Tracking-Parameter, aber NICHT `cid_google`. Wenn wir die Click IDs unter nicht-erkannten Namen speichern, greift ITP nicht.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    CLICK ID ALIASING (Safari ITP Workaround)                 │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  1. USER LANDING                                                             │
│     URL: drwerner.com/seite?gclid=ABC123&fbclid=XYZ789                      │
│                                                                              │
│  2. GTM WEB CONTAINER - Click ID Capture Tag                                 │
│     Liest Original-Parameter und sendet an Server mit Alias-Namen:          │
│                                                                              │
│     ┌─────────────────────────────────────────────────────┐                 │
│     │ Original Parameter │ Alias Parameter                │                 │
│     ├────────────────────┼────────────────────────────────┤                 │
│     │ gclid              │ cid_google                     │                 │
│     │ gbraid             │ cid_gbraid                     │                 │
│     │ wbraid             │ cid_wbraid                     │                 │
│     │ fbclid             │ cid_meta                       │                 │
│     │ li_fat_id          │ cid_linkedin                   │                 │
│     └────────────────────┴────────────────────────────────┘                 │
│                                                                              │
│  3. STAPE SERVER CONTAINER - Cookie Keeper                                   │
│     Speichert unter Alias-Namen (Safari erkennt diese NICHT als Tracking):  │
│                                                                              │
│     ┌─────────────────────────────────────────────────────┐                 │
│     │ Cookie Name        │ TTL        │ Safari ITP?       │                 │
│     ├────────────────────┼────────────┼───────────────────┤                 │
│     │ drw_cid_google     │ 2 Jahre    │ ✓ Nicht erkannt   │                 │
│     │ drw_cid_gbraid     │ 2 Jahre    │ ✓ Nicht erkannt   │                 │
│     │ drw_cid_wbraid     │ 2 Jahre    │ ✓ Nicht erkannt   │                 │
│     │ drw_cid_meta       │ 2 Jahre    │ ✓ Nicht erkannt   │                 │
│     │ drw_cid_linkedin   │ 2 Jahre    │ ✓ Nicht erkannt   │                 │
│     └────────────────────┴────────────┴───────────────────┘                 │
│                                                                              │
│  4. BEI CONVERSION - Rückübersetzung                                         │
│     Server liest Alias-Cookies und sendet mit Original-Parameternamen       │
│     an Ad-Plattformen:                                                       │
│                                                                              │
│     drw_cid_google → gclid (an Google Ads)                                  │
│     drw_cid_meta   → fbclid (an Meta CAPI)                                  │
│     drw_cid_linkedin → li_fat_id (an LinkedIn CAPI)                         │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

**GTM Web Container: Click ID Capture & Rename Tag**

```javascript
// Tag: Click ID Capture (Safari ITP Fix)
// Trigger: All Pages

(function() {
  const params = new URLSearchParams(window.location.search);

  const mapping = {
    'gclid': 'cid_google',
    'gbraid': 'cid_gbraid',
    'wbraid': 'cid_wbraid',
    'fbclid': 'cid_meta',
    'li_fat_id': 'cid_linkedin'
  };

  const capturedIds = {};

  for (const [original, aliased] of Object.entries(mapping)) {
    const value = params.get(original);
    if (value) {
      capturedIds[aliased] = value;
    }
  }

  // Nur senden wenn mindestens eine Click ID gefunden
  if (Object.keys(capturedIds).length > 0) {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: 'click_ids_captured',
      ...capturedIds,
      // Timestamp für Debugging
      click_id_captured_at: new Date().toISOString()
    });
  }
})();
```

**Stape Cookie Keeper Konfiguration (Custom Click IDs)**

```
Stape Dashboard → Container → Power-Ups → Cookie Keeper → Custom
──────────────────────────────────────────────────────────────────

Custom Cookie 1:
├── Name: drw_cid_google
├── Quelle: Event Parameter "cid_google"
└── TTL: 730 Tage (2 Jahre)

Custom Cookie 2:
├── Name: drw_cid_gbraid
├── Quelle: Event Parameter "cid_gbraid"
└── TTL: 730 Tage (2 Jahre)

Custom Cookie 3:
├── Name: drw_cid_wbraid
├── Quelle: Event Parameter "cid_wbraid"
└── TTL: 730 Tage (2 Jahre)

Custom Cookie 4:
├── Name: drw_cid_meta
├── Quelle: Event Parameter "cid_meta"
└── TTL: 730 Tage (2 Jahre)

Custom Cookie 5:
├── Name: drw_cid_linkedin
├── Quelle: Event Parameter "cid_linkedin"
└── TTL: 730 Tage (2 Jahre)
```

**Server Tag: Conversion mit Rückübersetzung**

```javascript
// Stape Server Tag: Google Ads Enhanced Conversion
// Liest Alias-Cookie und sendet als gclid

const getClickId = (aliasedCookieName, originalParamName) => {
  // 1. Prüfe aktuellen Event (frischer Click)
  const fromEvent = eventData[aliasedCookieName.replace('drw_', '')];
  if (fromEvent) return fromEvent;

  // 2. Prüfe Cookie (persistiert)
  const fromCookie = getCookieValues(aliasedCookieName)[0];
  if (fromCookie) return fromCookie;

  return null;
};

// Google Ads Conversion Data
const conversionData = {
  conversion_action: eventData.conversion_action || 'contact_form',
  conversion_time: new Date().toISOString(),

  // Rückübersetzung: Alias → Original
  gclid: getClickId('drw_cid_google', 'gclid'),
  gbraid: getClickId('drw_cid_gbraid', 'gbraid'),
  wbraid: getClickId('drw_cid_wbraid', 'wbraid'),

  // Enhanced Conversion Daten (gehashed)
  user_data: {
    email: hashSHA256(eventData.user_email),
    phone_number: hashSHA256(eventData.user_phone)
  }
};

// An Google Ads senden
sendToGoogleAds(conversionData);

// Meta CAPI Conversion Data
const metaConversionData = {
  event_name: eventData.event_name,
  event_time: Math.floor(Date.now() / 1000),

  // Rückübersetzung für Meta
  fbc: formatFbc(getClickId('drw_cid_meta', 'fbclid')),
  fbp: getCookieValues('_fbp')[0],

  user_data: {
    em: hashSHA256(eventData.user_email),
    ph: hashSHA256(eventData.user_phone)
  }
};

sendToMetaCAPI(metaConversionData);
```

**Wichtig: Parallel-Betrieb**

Während der Umstellung sollten BEIDE Methoden parallel laufen:
1. Standard Click ID Cookies (_gcl_aw, _fbc) - für Browser ohne ITP
2. Aliased Click ID Cookies (drw_cid_*) - für Safari und Firefox

Bei der Conversion werden beide geprüft und der zuerst gefundene Wert verwendet.

## 12.5 UTM Parameter Persistierung (Vollständig)

### Warum UTM Parameter persistieren?

UTM Parameter sind der Schlüssel zur Marketing-Attribution. Sie beantworten die Frage: **Woher kam dieser Lead?**

```
Das Problem ohne Persistierung:
───────────────────────────────

Tag 1: User klickt auf Google Ad
       URL: drwerner.com/malta?utm_source=google&utm_medium=cpc&utm_campaign=malta_2024
       → UTM Parameter sind in der URL

Tag 1: User navigiert zur Startseite
       URL: drwerner.com/
       → UTM Parameter VERLOREN!

Tag 5: User füllt Kontaktformular aus
       → Keine Ahnung mehr, dass er über Google Ads kam

Das Ergebnis: Marketing-Attribution unmöglich
```

### Die 5 Standard-UTM Parameter

| Parameter | Zweck | Beispiel | Priorität |
|-----------|-------|----------|-----------|
| `utm_source` | Woher kommt der Traffic? | google, facebook, linkedin, newsletter | **Pflicht** |
| `utm_medium` | Welcher Kanal-Typ? | cpc, email, social, organic, referral | **Pflicht** |
| `utm_campaign` | Welche Kampagne? | malta_firmengründung_2024, remarketing_q4 | **Pflicht** |
| `utm_term` | Welches Keyword? (bei Suche) | firmengründung malta, auswandern steuern | Optional |
| `utm_content` | Welche Anzeigenvariante? | cta_blau, headline_v2, video_ad | Optional |

### First-Touch vs. Last-Touch Attribution

> **Wichtige Entscheidung**: Welche UTM Parameter speichern wir – die ersten oder die letzten?

```
Beispiel User-Journey:
──────────────────────

Woche 1: Klick auf Google Ad (utm_source=google, utm_campaign=brand)
         → Liest Blog-Artikel, geht wieder

Woche 2: Klick auf LinkedIn Post (utm_source=linkedin, utm_campaign=thought_leadership)
         → Schaut sich Leistungen an

Woche 3: Klick auf Retargeting Ad (utm_source=meta, utm_campaign=retargeting)
         → Lädt Checkliste herunter = CONVERSION

Frage: Welcher Kanal hat die Conversion "verdient"?

FIRST-TOUCH Attribution:    → Google Ads (hat User überhaupt erst gebracht)
LAST-TOUCH Attribution:     → Meta Ads (war direkt vor Conversion)
MULTI-TOUCH Attribution:    → Alle drei haben beigetragen
```

### Empfohlene Strategie für DrWerner.com

**Speichere BEIDE – First-Touch UND Last-Touch:**

```
Cookie-Struktur:
────────────────

First-Touch (wird nur einmal gesetzt, dann nie überschrieben):
├── drw_ft_source     → Erste utm_source dieses Users
├── drw_ft_medium     → Erstes utm_medium
├── drw_ft_campaign   → Erste utm_campaign
├── drw_ft_term       → Erstes utm_term
└── drw_ft_content    → Erstes utm_content

Last-Touch (wird bei jedem Besuch mit UTM überschrieben):
├── drw_lt_source     → Letzte utm_source
├── drw_lt_medium     → Letztes utm_medium
├── drw_lt_campaign   → Letzte utm_campaign
├── drw_lt_term       → Letztes utm_term
└── drw_lt_content    → Letztes utm_content

Timestamp:
└── drw_ft_timestamp  → Wann war der First-Touch?
```

**Vorteil:** Bei der Conversion werden BEIDE übermittelt → Vollständige Attribution möglich.

### Stape Cookie Keeper Konfiguration für UTM

```
Stape Dashboard → Container → Power-Ups → Cookie Keeper
────────────────────────────────────────────────────────

Custom Cookies hinzufügen:

FIRST-TOUCH Cookies (nur setzen wenn leer):
┌─────────────────┬─────────┬──────────────────────────┐
│ Cookie Name     │ TTL     │ Überschreib-Regel        │
├─────────────────┼─────────┼──────────────────────────┤
│ drw_ft_source   │ 180 Tage│ Nur wenn Cookie leer     │
│ drw_ft_medium   │ 180 Tage│ Nur wenn Cookie leer     │
│ drw_ft_campaign │ 180 Tage│ Nur wenn Cookie leer     │
│ drw_ft_term     │ 180 Tage│ Nur wenn Cookie leer     │
│ drw_ft_content  │ 180 Tage│ Nur wenn Cookie leer     │
│ drw_ft_timestamp│ 180 Tage│ Nur wenn Cookie leer     │
└─────────────────┴─────────┴──────────────────────────┘

LAST-TOUCH Cookies (immer überschreiben):
┌─────────────────┬─────────┬──────────────────────────┐
│ Cookie Name     │ TTL     │ Überschreib-Regel        │
├─────────────────┼─────────┼──────────────────────────┤
│ drw_lt_source   │ 30 Tage │ Bei jedem UTM-Besuch     │
│ drw_lt_medium   │ 30 Tage │ Bei jedem UTM-Besuch     │
│ drw_lt_campaign │ 30 Tage │ Bei jedem UTM-Besuch     │
│ drw_lt_term     │ 30 Tage │ Bei jedem UTM-Besuch     │
│ drw_lt_content  │ 30 Tage │ Bei jedem UTM-Besuch     │
└─────────────────┴─────────┴──────────────────────────┘

WARUM unterschiedliche TTLs?
→ First-Touch: 180 Tage, weil initiale Attribution langfristig relevant
→ Last-Touch: 30 Tage, weil nur der letzte Touchpoint vor Conversion zählt
```

### GTM Web Container: UTM Erfassung

**Variables anlegen:**

```
GTM Web Container - Variables:
──────────────────────────────

1. URL Parameter Variables (für aktuelle UTM aus URL):
   ├── Variable: URL - utm_source
   │   Type: URL
   │   Component: Query
   │   Query Key: utm_source
   │
   ├── Variable: URL - utm_medium
   │   Type: URL
   │   Component: Query
   │   Query Key: utm_medium
   │
   ├── Variable: URL - utm_campaign
   │   Type: URL
   │   Component: Query
   │   Query Key: utm_campaign
   │
   ├── Variable: URL - utm_term
   │   Type: URL
   │   Component: Query
   │   Query Key: utm_term
   │
   └── Variable: URL - utm_content
       Type: URL
       Component: Query
       Query Key: utm_content

2. First-Party Cookie Variables (für persistierte UTM):
   ├── Variable: Cookie - First Touch Source
   │   Type: 1st Party Cookie
   │   Cookie Name: drw_ft_source
   │
   ├── Variable: Cookie - First Touch Medium
   │   Type: 1st Party Cookie
   │   Cookie Name: drw_ft_medium
   │
   ├── Variable: Cookie - First Touch Campaign
   │   Type: 1st Party Cookie
   │   Cookie Name: drw_ft_campaign
   │
   ├── Variable: Cookie - Last Touch Source
   │   Type: 1st Party Cookie
   │   Cookie Name: drw_lt_source
   │
   ├── Variable: Cookie - Last Touch Medium
   │   Type: 1st Party Cookie
   │   Cookie Name: drw_lt_medium
   │
   └── Variable: Cookie - Last Touch Campaign
       Type: 1st Party Cookie
       Cookie Name: drw_lt_campaign
```

### Logik: Wann werden UTM Cookies gesetzt?

```
Trigger: UTM Parameter vorhanden
──────────────────────────────────
Trigger Name:    UTM Parameters Present
Trigger Type:    Page View
Bedingung:       {{URL - utm_source}} ist nicht leer
                 ODER {{URL - utm_medium}} ist nicht leer
                 ODER {{URL - utm_campaign}} ist nicht leer

Bei diesem Trigger:
1. Prüfen ob First-Touch Cookies schon existieren
   → JA: Nur Last-Touch Cookies aktualisieren
   → NEIN: First-Touch UND Last-Touch setzen

2. Timestamp für First-Touch setzen (nur beim ersten Mal)
```

### DataLayer Push für UTM (bei Lead-Events)

Bei jeder Conversion werden ALLE UTM-Daten mitgesendet:

```javascript
// Wird automatisch bei Lead-Events gepusht
window.dataLayer.push({
  'event': 'lead_magnet_download',

  // Aktuelle UTM aus URL (falls vorhanden)
  'utm_source': '{{URL - utm_source}}',
  'utm_medium': '{{URL - utm_medium}}',
  'utm_campaign': '{{URL - utm_campaign}}',

  // First-Touch Attribution
  'first_touch_source': '{{Cookie - First Touch Source}}',
  'first_touch_medium': '{{Cookie - First Touch Medium}}',
  'first_touch_campaign': '{{Cookie - First Touch Campaign}}',

  // Last-Touch Attribution
  'last_touch_source': '{{Cookie - Last Touch Source}}',
  'last_touch_medium': '{{Cookie - Last Touch Medium}}',
  'last_touch_campaign': '{{Cookie - Last Touch Campaign}}',

  // User & Lead Data
  'lead_magnet_name': 'Malta Checkliste',
  'user_email': 'max@example.com'
});
```

### Server Container: UTM an Plattformen weiterleiten

```
GTM Server Container - Variable Mapping:
────────────────────────────────────────

Empfangene Parameter:         Weitergabe an:
─────────────────────────────────────────────
utm_source                →   GA4, BigQuery
utm_medium                →   GA4, BigQuery
utm_campaign              →   GA4, BigQuery, Google Ads
first_touch_source        →   BigQuery, Brevo
first_touch_medium        →   BigQuery, Brevo
first_touch_campaign      →   BigQuery, Brevo
last_touch_source         →   BigQuery, Brevo
last_touch_medium         →   BigQuery, Brevo
last_touch_campaign       →   BigQuery, Brevo

WICHTIG für Salesforce:
→ First-Touch UTM in Lead-Felder schreiben
→ Ermöglicht Attribution-Reporting im CRM
```

### Salesforce Felder für UTM Attribution

Die folgenden Felder sollten im Lead-Objekt vorhanden sein bzw. angelegt werden:

| Feld-Name (API) | Label | Typ | Quelle |
|-----------------|-------|-----|--------|
| `Source__c` | UTM Source (First Touch) | Text(100) | Bereits vorhanden? Prüfen |
| `Lead_Channel__c` | Kanal | Text(50) | Bereits vorhanden |
| `utm_medium__c` | UTM Medium | Text(50) | **Neu anlegen** |
| `utm_campaign__c` | UTM Campaign | Text(255) | **Neu anlegen** |
| `utm_term__c` | UTM Term (Keyword) | Text(255) | **Neu anlegen** |
| `utm_content__c` | UTM Content | Text(255) | **Neu anlegen** |
| `First_Touch_Campaign__c` | First Touch Campaign | Text(255) | **Neu anlegen** |
| `Last_Touch_Source__c` | Last Touch Source | Text(100) | **Neu anlegen** |
| `Last_Touch_Campaign__c` | Last Touch Campaign | Text(255) | **Neu anlegen** |

### Praxisbeispiel: Vollständige User-Journey mit UTM

```
═══════════════════════════════════════════════════════════════════════
BEISPIEL: User-Journey mit UTM Tracking
═══════════════════════════════════════════════════════════════════════

WOCHE 1 - ERSTKONTAKT
─────────────────────
Montag: User sieht Google Ad für "Malta Firmengründung"
        Klickt auf Anzeige:
        drwerner.com/malta-firmengründung?utm_source=google&utm_medium=cpc&utm_campaign=malta_2024&utm_term=malta+firma+gründen

        → Server setzt Cookies:
          drw_ft_source = "google"      (First Touch)
          drw_ft_medium = "cpc"
          drw_ft_campaign = "malta_2024"
          drw_ft_term = "malta firma gründen"
          drw_ft_timestamp = "2024-12-01T10:30:00"

          drw_lt_source = "google"      (Last Touch = gleich wie FT)
          drw_lt_medium = "cpc"
          drw_lt_campaign = "malta_2024"

        User liest Artikel, geht dann weg.

WOCHE 2 - RETURN VISIT (ORGANISCH)
──────────────────────────────────
Mittwoch: User googelt "Dr Werner Malta" (organisch)
          Klickt auf organisches Ergebnis:
          drwerner.com/

          → KEINE neuen UTM Parameter in URL
          → Cookies bleiben unverändert!
          → First-Touch Attribution bleibt: Google Ads

WOCHE 3 - RETURN VISIT (LINKEDIN)
─────────────────────────────────
Freitag: User sieht LinkedIn Post von Philipp Sauerborn
         Klickt auf Link:
         drwerner.com/blog/auswandern-2024?utm_source=linkedin&utm_medium=social&utm_campaign=thought_leadership

         → First-Touch Cookies: NICHT überschrieben (existieren bereits)
         → Last-Touch Cookies: WERDEN aktualisiert:
           drw_lt_source = "linkedin"   (neu!)
           drw_lt_medium = "social"     (neu!)
           drw_lt_campaign = "thought_leadership" (neu!)

WOCHE 4 - CONVERSION
────────────────────
Montag: User kommt direkt zurück (Direktaufruf)
        drwerner.com/kontakt

        → Keine UTM in URL
        → Cookies weiterhin gespeichert

        User füllt Kontaktformular aus!

        → DataLayer Push enthält:
          first_touch_source: "google"
          first_touch_medium: "cpc"
          first_touch_campaign: "malta_2024"
          last_touch_source: "linkedin"
          last_touch_medium: "social"
          last_touch_campaign: "thought_leadership"

        → BigQuery erhält vollständige Journey
        → Salesforce Lead wird angelegt mit:
          Lead_Channel__c = "google_ads"  (First Touch)
          First_Touch_Campaign__c = "malta_2024"
          Last_Touch_Source__c = "linkedin"
          Last_Touch_Campaign__c = "thought_leadership"

═══════════════════════════════════════════════════════════════════════
ERGEBNIS:
─────────
Das Marketing-Team sieht:
✓ Google Ads hat den User initial gebracht (First Touch)
✓ LinkedIn hat zur finalen Conversion beigetragen (Last Touch)
✓ Multi-Touch Attribution ist möglich
✓ Budget-Entscheidungen können datenbasiert getroffen werden
═══════════════════════════════════════════════════════════════════════
```

### UTM Parameter Debugging

**So prüfen Sie, ob UTM-Tracking funktioniert:**

1. **Im Browser**:
   - DevTools → Application → Cookies → drwerner.com
   - Suche nach `drw_ft_*` und `drw_lt_*` Cookies

2. **Im GTM Preview Mode**:
   - Tags Tab → Prüfen ob UTM Variables gefüllt sind
   - DataLayer Tab → UTM Werte bei Events prüfen

3. **In GA4 Realtime**:
   - Traffic Source sollte UTM Werte zeigen
   - Session Source / Medium korrekt?

4. **In BigQuery**:
   ```sql
   SELECT
     event_timestamp,
     utm_source,
     utm_medium,
     utm_campaign,
     first_touch_source,
     last_touch_source
   FROM `brixon-analytics.analytics.events`
   WHERE property_id = 'drwerner'
     AND event_name = 'lead_magnet_download'
   ORDER BY event_timestamp DESC
   LIMIT 10;
   ```

### Zusammenfassung: UTM Persistierung Checklist

| Komponente | Aufgabe | Status |
|------------|---------|--------|
| **Stape Cookie Keeper** | First-Touch Cookies (5x) mit 180 Tagen TTL | ⬜ Konfigurieren |
| **Stape Cookie Keeper** | Last-Touch Cookies (5x) mit 30 Tagen TTL | ⬜ Konfigurieren |
| **GTM Web Container** | URL Parameter Variables (5x utm_*) | ⬜ Anlegen |
| **GTM Web Container** | Cookie Variables (10x drw_ft_* + drw_lt_*) | ⬜ Anlegen |
| **GTM Web Container** | Trigger: UTM Parameters Present | ⬜ Anlegen |
| **GTM Server Container** | UTM Variablen an GA4 + BigQuery weiterleiten | ⬜ Konfigurieren |
| **Salesforce** | UTM Felder im Lead-Objekt anlegen | ⬜ Admin-Aufgabe |
| **Brevo** | UTM Felder im Kontakt-Schema | ⬜ Konfigurieren |

### Zusammenfassung: Was muss konfiguriert werden?

| Komponente | Aufgabe | Status |
|------------|---------|--------|
| **Stape Cookie Keeper** | Alle Click ID Cookies aktivieren | ⬜ Konfigurieren |
| **GTM Web Container** | URL-Parameter Variables anlegen | ⬜ Implementieren |
| **GTM Web Container** | Fallback-Logik (URL → Cookie → LocalStorage) | ⬜ Implementieren |
| **GTM Server Container** | Click IDs an alle Tags weitergeben | ⬜ Konfigurieren |
| **Salesforce** | fbclid__c und li_fat_id__c Felder anlegen | ⬜ Admin-Aufgabe |
| **Brevo** | Click ID Felder im Kontakt-Schema | ⬜ Konfigurieren |

---

# 13. Datenschutz & Compliance (Transparenz-First)

> **Hinweis**: DSGVO-Compliance wird in Phase 1 zurückgestellt. Fokus liegt auf vollständiger Datenerfassung für maximale Transparenz. Der Cookie-Banner bleibt für Google Consent Mode aktiv.

## 13.1 Aktueller Ansatz

### Priorisierung

| Priorität | Aspekt | Status |
|-----------|--------|--------|
| **1. Hoch** | Vollständiges Tracking | ✓ Implementieren |
| **1. Hoch** | Consent Mode v2 für Google | ✓ Banner mit Default "granted" |
| **2. Mittel** | Hashing von PII | ✓ Für Ad-Plattformen (Enhanced Conversions) |
| **3. Später** | DSGVO-Dokumentation | Zurückgestellt |
| **3. Später** | Granulare Consent-Optionen | Zurückgestellt |

### Was bleibt aktiv

1. **Cookie-Banner**: Bleibt für Google Consent Mode v2 (technisch erforderlich)
2. **Default Consent = Granted**: Alle Tags feuern sofort
3. **SHA-256 Hashing**: E-Mail/Telefon werden gehashed an Google/Meta/LinkedIn gesendet
4. **AVVs**: Auftragsverarbeitungsverträge mit Stape, Brevo, Google Cloud sollten vorhanden sein

## 13.2 Technische Maßnahmen (aktiv)

### Hashing von PII

E-Mails und Telefonnummern werden für Enhanced Conversions gehashed:

```javascript
// SHA-256 Hashing (server-seitig im GTM)
// Erforderlich für Google Enhanced Conversions und Meta CAPI

function hashPII(value) {
  return crypto
    .createHash('sha256')
    .update(value.trim().toLowerCase())
    .digest('hex');
}

// Beispiel
hashPII('max.mustermann@example.com');
// → "a1b2c3d4e5f6..."
```

**Warum Hashing wichtig:**
- Google und Meta akzeptieren nur gehashte Daten
- Matching mit eingeloggten Nutzern funktioniert trotzdem
- Rohdaten bleiben nur in BigQuery (eigene Kontrolle)

### Speicherfristen

| Datenart | Speicherdauer | Begründung |
|----------|---------------|------------|
| Anonyme Analytics | 26 Monate | GA4 Standard |
| Cookies (mit Consent) | 2 Jahre | Cookie Keeper |
| BigQuery Events | 5 Jahre | Geschäftsanalyse |
| Brevo Kontakte | Bis Widerruf | Nurturing |
| Salesforce Leads | 10 Jahre | Steuerrecht |

---

# 14. Phasenplan zur Umsetzung (Granular)

## Übersicht

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        IMPLEMENTIERUNGS-ROADMAP                              │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  Phase 1: Fundament         ████████░░░░░░░░░░░░░░░░░░░░░░░░░  Wochen 1-3   │
│  Phase 2: Lead Events       ░░░░░░░░████████░░░░░░░░░░░░░░░░░░  Wochen 4-6   │
│  Phase 3: Ad-Plattformen    ░░░░░░░░░░░░░░░░████████░░░░░░░░░░  Wochen 7-9   │
│  Phase 4: CRM & Automation  ░░░░░░░░░░░░░░░░░░░░░░░░██████████  Wochen 10-13 │
│  Phase 5: Custom Analytics  ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░████  Wochen 14-17 │
│  Phase 6: Optimierung       ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░██  Wochen 18-20 │
│                                                                              │
│  Legende: Tracking | Marketing | CRM Admin | Tech | Alle                    │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Phase 1: Fundament (Wochen 1-3)

### Woche 1: Stape & GTM Basis

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Stape Container Review & Cookie Keeper aktivieren | Tracking | ⬜ |
| Di | Click ID Aliasing implementieren (Safari ITP Fix) | Tracking | ⬜ |
| Mi | GTM Web Container: Click ID Capture Tag erstellen | Tracking | ⬜ |
| Do | Server Container: Custom Cookie Variablen anlegen | Tracking | ⬜ |
| Fr | Testing: Safari/Chrome Click ID Persistence validieren | Tracking | ⬜ |

**Wochenziel:** Click IDs werden auch in Safari 2 Jahre persistiert

### Woche 2: Consent & GA4

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Cookiebot Consent Mode v2 Validierung | Tracking | ⬜ |
| Di | GA4 Server Configuration Tag einrichten | Tracking | ⬜ |
| Mi | Basis-Events: page_view, scroll, click, outbound_link | Tracking | ⬜ |
| Do | DataLayer Dokumentation erstellen (Confluence/Notion) | Tracking | ⬜ |
| Fr | GA4 Realtime Testing + Debug Walkthrough | Tracking | ⬜ |

**Wochenziel:** GA4 Server-Side Tracking live mit korrektem Consent

### Woche 3: Multi-Property Setup

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Zweite GA4 Property für philippsauerborn.com anlegen | Tracking | ⬜ |
| Di | Zweiten GTM Web Container erstellen (GTM-PHILIPP) | Tracking | ⬜ |
| Mi | Server Container: Multi-Property Routing Logik | Tracking | ⬜ |
| Do | property_id Custom Dimension in allen Events | Tracking | ⬜ |
| Fr | Testing beider Properties parallel | Tracking | ⬜ |

**Wochenziel:** Separate GA4 Properties, shared Server Container

### Phase 1 Deliverables
- [ ] Server-Side GA4 Tracking live auf drwerner.com
- [ ] Click ID Aliasing funktioniert (Safari ITP gefixt)
- [ ] Consent Mode v2 korrekt implementiert
- [ ] Multi-Property Architektur vorbereitet

---

## Phase 2: Lead Events (Wochen 4-6)

### Woche 4: Lead Magnet Tracking

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Vavolta Account Setup + Konfiguration | Marketing | ⬜ |
| Di | Vavolta ↔ GTM Web Container Integration | Tracking | ⬜ |
| Mi | `lead_magnet_download` Event implementieren | Tracking | ⬜ |
| Do | Event-Parameter validieren (content_name, content_type, user_email) | Tracking | ⬜ |
| Fr | E2E Test: Download → Event → GA4 → Server | Tracking | ⬜ |

**Wochenziel:** Lead Magnet Downloads werden vollständig getrackt

### Woche 5: QuickCheck & Newsletter

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | QuickCheck Form HTML/JS Events identifizieren | Tracking | ⬜ |
| Di | `quickcheck_start` Event implementieren | Tracking | ⬜ |
| Mi | `quickcheck_complete` Event mit allen Antworten | Tracking | ⬜ |
| Do | `newsletter_signup` Event implementieren | Tracking | ⬜ |
| Fr | Alle Lead Events in GA4 Realtime validieren | Tracking | ⬜ |

**Wochenziel:** QuickCheck und Newsletter vollständig getrackt

### Woche 6: Kontaktformular & User ID

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | `contact_form_submit` Event implementieren | Tracking | ⬜ |
| Di | User ID Generation (`brixon_uid`) im Server Container | Tracking | ⬜ |
| Mi | Identity Graph Logik (Server-Side BigQuery Lookup) | Tracking | ⬜ |
| Do | Cross-Domain User ID Linking via URL Parameter | Tracking | ⬜ |
| Fr | User Journey Testing (Anonymus → Identified) | Tracking | ⬜ |

**Wochenziel:** Kontaktformular getrackt, User ID System funktioniert

### Phase 2 Deliverables
- [ ] Alle Lead-Events in GA4 sichtbar
- [ ] Event-Parameter vollständig (E-Mail, Content, Source)
- [ ] Vavolta Integration funktioniert
- [ ] User ID Management implementiert

---

## Phase 3: Ad-Plattformen (Wochen 7-9)

### Woche 7: Google Ads

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Google Ads Server Tag im Stape Container einrichten | Tracking | ⬜ |
| Di | Enhanced Conversions konfigurieren (User Data Hashing) | Tracking | ⬜ |
| Mi | Conversion Actions anlegen (4 Stufen + Unqualified) | Marketing | ⬜ |
| Do | Click ID → gclid Mapping testen (inkl. Alias-Cookies) | Tracking | ⬜ |
| Fr | Google Ads Conversion Testing + Validierung | Tracking + Marketing | ⬜ |

**Wochenziel:** Google Ads Conversions mit Enhanced Conversions live

### Woche 8: Meta CAPI

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Meta CAPI Access Token generieren (Business Manager) | Marketing | ⬜ |
| Di | Meta CAPI Server Tag im Stape Container | Tracking | ⬜ |
| Mi | Event Matching Quality prüfen (Events Manager) | Tracking | ⬜ |
| Do | Deduplizierung via `event_id` implementieren | Tracking | ⬜ |
| Fr | Meta Events Manager Validierung + EMQ Score | Tracking + Marketing | ⬜ |

**Wochenziel:** Meta CAPI live mit hohem Event Match Quality Score

### Woche 9: LinkedIn CAPI

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | LinkedIn CAPI Zugang einrichten (Campaign Manager) | Marketing | ⬜ |
| Di | LinkedIn CAPI Server Tag im Stape Container | Tracking | ⬜ |
| Mi | Conversion Events mappen (Lead, Purchase) | Tracking | ⬜ |
| Do | Testing & Validierung (LinkedIn Insight Tag Events) | Tracking | ⬜ |
| Fr | **Alle Ad-Plattformen E2E Test** (Google, Meta, LinkedIn) | Tracking | ⬜ |

**Wochenziel:** LinkedIn CAPI live, alle Plattformen validiert

### Phase 3 Deliverables
- [ ] Google Ads Enhanced Conversions live
- [ ] Meta CAPI mit EMQ > 7
- [ ] LinkedIn CAPI funktionsfähig
- [ ] Deduplizierung über alle Plattformen
- [ ] Attribution korrekt (Click IDs funktionieren)

---

## Phase 4: CRM & Marketing Automation (Wochen 10-13)

### Woche 10: Brevo Setup

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Brevo Account Setup + API Key generieren | Marketing | ⬜ |
| Di | Kontakt-Schema definieren (Custom Attributes) | Marketing + Tracking | ⬜ |
| Mi | GTM Server → Brevo HTTP Tag konfigurieren | Tracking | ⬜ |
| Do | Lead Scoring Regeln definieren (Punkte pro Aktion) | Marketing | ⬜ |
| Fr | Brevo Kontakt-Erstellung testen | Tracking | ⬜ |

**Wochenziel:** Brevo empfängt Events, Kontakte werden angelegt

### Woche 11: Salesforce Erweiterung

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Neue Lead-Felder anlegen (MQL/SQL Lifecycle) | CRM Admin | ⬜ |
| Di | Brevo ↔ Salesforce Native Integration einrichten | CRM Admin | ⬜ |
| Mi | Sync-Regeln konfigurieren (Score Threshold, Events) | CRM Admin | ⬜ |
| Do | MQL→SQL Flow testen (Score erreicht → Lead erstellt) | Marketing + CRM | ⬜ |
| Fr | Deduplizierung validieren (E-Mail Matching) | CRM Admin | ⬜ |

**Wochenziel:** Brevo-Salesforce Sync funktioniert

### Woche 12: Nurturing Workflows

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Workflow: Lead Magnet Follow-up (3 E-Mails) | Marketing | ⬜ |
| Di | Workflow: QuickCheck Follow-up (personalisiert) | Marketing | ⬜ |
| Mi | Workflow: Newsletter Welcome Series | Marketing | ⬜ |
| Do | Workflow: MQL→SQL Übergabe Notification | Marketing | ⬜ |
| Fr | Workflow Testing (alle Pfade durchspielen) | Marketing | ⬜ |

**Wochenziel:** Automatisierte Nurturing Workflows aktiv

### Woche 13: Feedback Loop & Offline Conversions

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Salesforce "Unqualified" Webhook einrichten | CRM Admin | ⬜ |
| Di | Stape Endpoint für Salesforce Webhook | Tracking | ⬜ |
| Mi | Offline Conversion Tags (Unqualified als negativ) | Tracking | ⬜ |
| Do | E2E Test: Unqualified → Ad Platforms | Tracking | ⬜ |
| Fr | Dokumentation & Handover an Sales Team | Tracking | ⬜ |

**Wochenziel:** Unqualified Feedback Loop funktioniert

### Phase 4 Deliverables
- [ ] Brevo empfängt alle Lead Events
- [ ] Automatisierte E-Mail Nurturing Workflows
- [ ] MQL/SQL Lifecycle in Salesforce
- [ ] Brevo-Salesforce Sync aktiv
- [ ] Unqualified Feedback an Ad-Plattformen

---

## Phase 5: Custom Analytics (Wochen 14-17)

### Woche 14: BigQuery Setup

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | BigQuery Projekt erstellen (GCP Console) | Tech | ⬜ |
| Di | Service Account für GTM erstellen + Berechtigungen | Tech | ⬜ |
| Mi | Tabellen-Schema anlegen (5 Tabellen lt. Konzept) | Tracking | ⬜ |
| Do | BigQuery HTTP Tag im Server Container | Tracking | ⬜ |
| Fr | Event-Streaming testen (page_view → BigQuery) | Tracking | ⬜ |

**Wochenziel:** Events fließen in BigQuery

### Woche 15: User Stitching

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Identity Graph Tabelle befüllen (bei Identifikation) | Tracking | ⬜ |
| Di | User Stitching Query entwickeln (JOIN Logik) | Tracking | ⬜ |
| Mi | Scheduled Query für tägliche Aggregation | Tracking | ⬜ |
| Do | Cross-Domain User Matching validieren | Tracking | ⬜ |
| Fr | User Journey Query testen (Anonymus → Conversion) | Tracking | ⬜ |

**Wochenziel:** User Stitching funktioniert cross-domain

### Woche 16: Reporting Views

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | View: Lead Funnel (MQL → SQL → Opportunity) | Tracking | ⬜ |
| Di | View: Channel Attribution (First/Last Touch) | Tracking | ⬜ |
| Mi | View: User Lifetime Journey (alle Touchpoints) | Tracking | ⬜ |
| Do | View: Campaign Performance (ROI pro Kampagne) | Tracking | ⬜ |
| Fr | Views validieren mit Test-Daten | Tracking | ⬜ |

**Wochenziel:** Reporting Views bereit für Dashboard

### Woche 17: Dashboard

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Looker Studio Projekt erstellen | Tracking | ⬜ |
| Di | BigQuery Connector einrichten | Tracking | ⬜ |
| Mi | Dashboard: Executive Overview (KPIs auf einen Blick) | Tracking | ⬜ |
| Do | Dashboard: Marketing Deep Dive (Channels, Campaigns) | Tracking | ⬜ |
| Fr | Dashboard: Sales Pipeline (MQL→SQL→Won) | Tracking | ⬜ |

**Wochenziel:** Looker Studio Dashboard live

### Phase 5 Deliverables
- [ ] Events fließen in BigQuery
- [ ] User Stitching cross-domain funktioniert
- [ ] Customer Journey abfragbar
- [ ] Looker Studio Dashboard für Marketing/Sales

---

## Phase 6: Optimierung (Wochen 18-20)

### Woche 18: philippsauerborn.com Integration

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | GTM Container auf philippsauerborn.com deployen | Tech | ⬜ |
| Di | Events validieren (Realtime GA4) | Tracking | ⬜ |
| Mi | Ad-Plattform Tags aktivieren (property_id Filter) | Tracking | ⬜ |
| Do | Cross-Domain Journey testen (drwerner ↔ philippsauerborn) | Tracking | ⬜ |
| Fr | Property-übergreifende BigQuery Reports | Tracking | ⬜ |

**Wochenziel:** Beide Properties vollständig integriert

### Woche 19: Performance & QA

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Ladezeit-Optimierung (GTM Container Size) | Tracking | ⬜ |
| Di | Tag Sequencing Review (Race Conditions) | Tracking | ⬜ |
| Mi | Consent Flow Edge Cases (Ablehnung, Widerruf) | Tracking | ⬜ |
| Do | Data Quality Audit (BigQuery vs GA4 Vergleich) | Tracking | ⬜ |
| Fr | Bug Fixes aus QA | Tracking | ⬜ |

**Wochenziel:** System ist stabil und performant

### Woche 20: Dokumentation & Training

| Tag | Aufgabe | Verantwortung | Status |
|-----|---------|---------------|--------|
| Mo | Technische Dokumentation finalisieren | Tracking | ⬜ |
| Di | Runbook für häufige Aufgaben (Tag hinzufügen, Debugging) | Tracking | ⬜ |
| Mi | Marketing Team Training (Dashboard, Events) | Tracking | ⬜ |
| Do | Sales Team Training (Salesforce Integration) | CRM Admin | ⬜ |
| Fr | **Go-Live Bestätigung** | Alle | ⬜ |

**Wochenziel:** Team ist geschult, System ist dokumentiert

### Phase 6 Deliverables
- [ ] Beide Domains vollständig getrackt
- [ ] Cross-Domain Journey funktioniert
- [ ] Performance optimiert
- [ ] Team geschult
- [ ] Dokumentation vollständig

---

## Zusammenfassung: Verantwortlichkeiten

| Rolle | Haupt-Aufgaben | Wochen aktiv |
|-------|----------------|--------------|
| **Tracking** | GTM, Stape, BigQuery, Events, Testing | 1-20 |
| **Marketing** | Vavolta, Brevo, Ad-Accounts, Workflows, Dashboard-Anforderungen | 4-13, 18, 20 |
| **CRM Admin** | Salesforce Felder, Sync-Regeln, Webhooks | 11-13, 20 |
| **Tech** | BigQuery Setup, Deployment, Service Accounts | 14, 18 |

## Kritische Abhängigkeiten

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        KRITISCHE ABHÄNGIGKEITEN                              │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  Woche 4: Vavolta Account muss bereit sein (Marketing)                      │
│           └── Sonst verzögert sich Lead Magnet Tracking                     │
│                                                                              │
│  Woche 7: Google Ads Conversion Actions müssen existieren (Marketing)       │
│           └── Sonst können Server Tags nicht konfiguriert werden            │
│                                                                              │
│  Woche 8: Meta CAPI Access Token erforderlich (Marketing)                   │
│           └── Ohne Token kein CAPI Setup möglich                            │
│                                                                              │
│  Woche 11: Salesforce Admin-Zugang erforderlich (CRM Admin)                 │
│           └── Neue Felder können sonst nicht angelegt werden                │
│                                                                              │
│  Woche 14: GCP Projekt + Billing muss eingerichtet sein (Tech)              │
│           └── BigQuery funktioniert nicht ohne Billing Account              │
│                                                                              │
│  Woche 18: philippsauerborn.com Code-Zugang erforderlich (Tech)             │
│           └── GTM Container kann sonst nicht deployed werden                │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

# 15. Technische Checklisten

## 15.1 GTM Web Container Checklist

- [ ] Consent Initialization Trigger vorhanden
- [ ] Cookiebot Tag lädt als erstes
- [ ] GA4 Configuration Tag → Server-Endpoint
- [ ] DataLayer Variables für alle Events
- [ ] Consent State Variables konfiguriert
- [ ] Debug Mode getestet
- [ ] Container veröffentlicht

## 15.2 GTM Server Container Checklist

- [ ] GA4 Client aktiv
- [ ] Cookie Keeper Power-Up aktiviert
- [ ] GA4 Server Tag → Property verknüpft
- [ ] Google Ads Tag → Conversion ID korrekt
- [ ] Meta CAPI Tag → Access Token gültig
- [ ] LinkedIn CAPI Tag → Account ID korrekt
- [ ] Brevo HTTP Tag → API Key konfiguriert
- [ ] BigQuery Tag → Service Account aktiv
- [ ] Consent-basierte Trigger
- [ ] Error Logging aktiviert

## 15.3 Consent Checklist

- [ ] Banner erscheint vor Tracking
- [ ] Alle Cookie-Kategorien korrekt zugeordnet
- [ ] Consent Mode v2 Signale werden gesendet
- [ ] Server erhält Consent-Parameter
- [ ] Tags respektieren Consent
- [ ] Widerruf funktioniert
- [ ] Datenschutzerklärung aktualisiert

## 15.4 Testing Checklist

- [ ] Anonymer User: Nur Basis-Tracking mit Consent
- [ ] Lead Magnet Download: Event + User Data
- [ ] Kontaktformular: SQL-Event in Salesforce
- [ ] Cross-Domain: User-ID bleibt erhalten
- [ ] Ad-Plattformen: Conversions erscheinen
- [ ] BigQuery: Events werden gestreamt
- [ ] Brevo: Kontakte werden angelegt

---

# Anhang: Glossar

| Begriff | Erklärung |
|---------|-----------|
| **Server-Side Tracking** | Tracking-Daten werden vom eigenen Server (statt Browser) an Plattformen gesendet |
| **CAPI** | Conversions API - Server-zu-Server Schnittstelle für Ad-Plattformen |
| **First-Party Cookie** | Cookie, das von der besuchten Domain gesetzt wird |
| **Third-Party Cookie** | Cookie von einer fremden Domain (z.B. facebook.com auf drwerner.com) |
| **ITP** | Intelligent Tracking Prevention - Apples Cookie-Blockierung in Safari |
| **Consent Mode v2** | Googles Framework zur Übermittlung von Einwilligungs-Signalen |
| **MQL** | Marketing Qualified Lead - Lead mit Engagement-Signal |
| **SQL** | Sales Qualified Lead - Lead mit konkretem Interesse/Anfrage |
| **DataLayer** | JavaScript-Objekt für strukturierte Event-Daten |
| **Enhanced Conversions** | Google Ads Feature für bessere Attribution via gehashte Nutzerdaten |
| **Cookie Keeper** | Stape-Feature zur Verlängerung von Cookie-Lebensdauern |
| **Lead Scoring** | Punktebasierte Bewertung von Leads nach Engagement |
| **Attribution** | Zuordnung von Conversions zu Marketing-Touchpoints |

---

*Dieses Konzept wurde erstellt basierend auf dem aktuellen Stand der Technik (Dezember 2024) und den spezifischen Anforderungen von DrWerner.com.*
