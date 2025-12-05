# DW&P Server-Side Tracking Implementation Plan

## Status: PAUSIERT - Strategie-Interview erforderlich

**Aktualisiert:** 4. Dezember 2024

---

## Erkenntnisse aus Website-Analyse

### drwerner.com - B2B Service-Website
- **Positionierung:** Lizenzierter Company Service Provider (MFSA DSER-23577)
- **Haupt-Conversion:** "Schedule now" / Appointment Booking
- **Lead Magnet:** Kostenlose Erstberatung (kein PDF-Download sichtbar)
- **Content:** 3 Expert Articles auf Homepage
- **Zielgruppe:** Unternehmer, HNW Individuals, Gaming Operators

### philippsauerborn.com - Content Authority Blog
- **Positionierung:** Thought Leadership / Personal Brand
- **Haupt-Conversions:** Newsletter + Kontaktformular
- **Lead Magnet:** "Insider News" Newsletter + 10+ detaillierte Guides
- **Content:** SEO-getrieben, Nischen-fokussiert (Crypto, Poker, Streamer)
- **Zielgruppe:** Digital Creators, Crypto Traders, Poker Players, Digital Nomads

---

## INTERVIEW-FRAGEN FÜR TRACKING-STRATEGIE

### 1. Customer Journey & Funnels

**1.1 Welche Haupt-Conversion-Pfade existieren?**
- [ ] drwerner.com → Appointment Booking → Sales Call → Kunde
- [ ] philippsauerborn.com → Newsletter → Nurturing → Appointment
- [ ] Andere Pfade?

**1.2 Wie funktioniert der Handoff zwischen beiden Websites?**
- Verlinken Blog-Artikel zu drwerner.com Services?
- Wird der Newsletter-Subscriber an DW&P übergeben?

**1.3 Was ist der typische Zeitraum vom Erstkontakt bis Vertragsabschluss?**
- Tage / Wochen / Monate?

### 2. Lead Magnets & Assets

**2.1 Existieren PDF-Downloads / Guides?**
- Malta Guide PDF?
- Tax Guides?
- Checklisten?
- Wenn ja: Wo gehostet? Wie getrackt?

**2.2 Welche Lead Magnet Downloads sind am wichtigsten?**
- Mit Wert für Tracking (€50, €100, etc.)?

**2.3 Wie werden Newsletter-Subscriber in Brevo segmentiert?**
- Nach Interesse (Malta, Dubai, Crypto, etc.)?
- Nach Source (drwerner.com vs. philippsauerborn.com)?

### 3. Formulare & Conversion Points

**3.1 Welche Formular-Typen gibt es?**
- [ ] Appointment Booking (drwerner.com)
- [ ] Kontaktformular (philippsauerborn.com)
- [ ] Newsletter Signup (philippsauerborn.com)
- [ ] Andere?

**3.2 Welche Formular-Felder werden erfasst?**
- Email, Phone, Name - was noch?
- Werden Interessen abgefragt?

**3.3 Was passiert nach Formular-Submit?**
- Automatische Email?
- CRM-Eintrag?
- Sales Team Benachrichtigung?

### 4. Brevo Integration

**4.1 Wie wird Brevo aktuell genutzt?**
- [ ] Newsletter versenden
- [ ] Automation Workflows
- [ ] Transactional Emails
- [ ] CRM Funktionen

**4.2 Welche Events sollen an Brevo gesendet werden?**
- form_submit (alle Formulare?)
- newsletter_signup
- appointment_booked
- lead_magnet_download
- page_view (für Tracking)?

**4.3 Wie werden Kontakte in Brevo strukturiert?**
- Listen?
- Tags?
- Custom Attributes?

### 5. Google Ads & Conversion Tracking

**5.1 Welche Google Ads Kampagnen laufen?**
- Search Ads?
- Display?
- YouTube?

**5.2 Welche Conversions sollen getrackt werden?**
- [ ] Appointment Booking (Hauptconversion)
- [ ] Kontaktformular Submit
- [ ] Newsletter Signup
- [ ] Phone Calls?
- [ ] Lead Magnet Downloads?

**5.3 Conversion Values:**
- Was ist ein Appointment wert? (€50? €100? €500?)
- Was ist ein Newsletter-Subscriber wert?
- Was ist ein Kontaktformular-Lead wert?

### 6. Meta & LinkedIn (für später)

**6.1 Laufen Meta Ads?**
- Für welche Zielgruppe?
- Retargeting?

**6.2 Laufen LinkedIn Ads?**
- B2B-fokussiert?

**6.3 Welche Custom Audiences existieren?**
- Website Visitors?
- Newsletter Subscribers?
- Lookalike Audiences?

### 7. philippsauerborn.com Spezifisch

**7.1 Soll dieselbe GA4 Property verwendet werden?**
- Oder separate Property für saubere Trennung?

**7.2 Welche Artikel-Kategorien sind wichtig für Tracking?**
- Malta, Dubai, Cyprus, Portugal
- Crypto, Poker, Streamer
- Andere?

**7.3 Content-Engagement Tracking:**
- Scroll Depth wichtig?
- Time on Page?
- Internal Link Clicks?

### 8. Reporting & KPIs

**8.1 Welche KPIs sind am wichtigsten?**
- [ ] Appointment Bookings
- [ ] Newsletter Signups
- [ ] Cost per Lead
- [ ] Cost per Appointment
- [ ] Andere?

**8.2 Wer braucht Zugang zu den Reports?**
- Marketing Team?
- Sales Team?
- Management?

**8.3 Reporting-Frequenz?**
- Täglich / Wöchentlich / Monatlich?

---

## TECHNISCHE IMPLEMENTIERUNG - Bisheriger Stand

### Key Identifiers
| System | ID |
|--------|-----|
| Stape Container | `rxkbcogt` |
| Server GTM | `GTM-5CCL9VFS` (Container ID: 236893890) |
| Web GTM (drwerner) | `GTM-KL9XDC6` (Container ID: 30061199) |
| Web GTM (philipp) | `GTM-MXGCD9N` |
| GA4 Property | `G-W5VN74VNPK` |
| Google Ads | `693597954` |
| Meta Pixel | `2489727324637899` |
| LinkedIn | `1682193` |
| Server URL | `https://s.drwerner.com` |
| Account ID | `6000058801` |

### Credentials (bereitgestellt)
- GA4 API Secret: `[GA4_API_SECRET]` *(in 1Password gespeichert)*
- Brevo API Key: `[BREVO_API_KEY]` *(in 1Password gespeichert)*

### Server GTM (GTM-5CCL9VFS) - ERSTELLT
| Element | Status | ID |
|---------|--------|-----|
| GA4 Client | ✅ Vorhanden | 3 |
| Trigger "All GA4 Events" | ✅ Erstellt | 4 |
| Trigger "Lead Events" | ✅ Erstellt | 5 |
| GA4 Server Tag | ✅ Erstellt | 6 |
| Google Ads Tag | ⚠️ Manuell | Template vorhanden |
| Brevo Tag | ⚠️ Manuell | Template vorhanden |
| **Version** | ✅ Version 3 erstellt | Nicht published |

### Client GTM (GTM-KL9XDC6) - ERSTELLT
| Element | Status | ID |
|---------|--------|-----|
| Trigger "Form Submit Success - All" | ✅ Erstellt | 249 |
| Tag "GA4 - generate_lead" | ✅ Erstellt | 250 |
| Tag "User Data - Enhanced Conversions" | ✅ Erstellt | 251 |
| **Version** | ✅ Version 223 erstellt | Nicht published |

### Manuelle Konfiguration erforderlich

**Brevo Tag (JSON HTTP Request):**
```
Destination URL: https://in-automate.brevo.com/api/v2/trackEvent
Include all Event Data: ✅
Request Method: POST
Headers:
  - api-key: [BREVO_API_KEY]
  - Content-Type: application/json
Trigger: Lead Events (ID: 5)
```

**Google Ads Tag (Conversion Improver):**
```
Conversion ID: 693597954
Trigger: Lead Events (ID: 5)
```

**GA4 Server Tag - API Secret hinzufügen:**
```
Measurement Protocol API Secret: eoSciVHmQBqIny6wTuP7cw
```

---

## NÄCHSTE SCHRITTE

### Morgen: Interview durchführen
1. Interview-Fragen oben durchgehen
2. Customer Journey dokumentieren
3. Conversion Values festlegen
4. Brevo-Integration definieren

### Nach Interview: Strategie finalisieren
1. Event-Schema basierend auf Antworten erstellen
2. Conversion Values zuweisen
3. Brevo Automation Workflows planen
4. Reporting-Dashboard definieren

### Danach: Technische Implementierung abschließen
1. Manuelle Tags konfigurieren (Brevo, Google Ads)
2. GA4 API Secret eintragen
3. Server Container publishen
4. Client Container publishen
5. Testing durchführen

---

## OFFENE FRAGEN

- [ ] Separate GA4 Property für philippsauerborn.com?
- [ ] Welche Conversion Values?
- [ ] Brevo Automation Workflows?
- [ ] Lead Magnet PDFs vorhanden?
- [ ] Cross-Domain Tracking nötig?
