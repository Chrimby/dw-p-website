# DW&P Server-Side Tracking Migration

## Dokumentation der GTM-Umstellung

**Datum:** 2024-12-04
**Account:** DWP (6000058801)
**Web-Container:** GTM-KL9XDC6 (www.drwerner.com)
**Server-Container:** GTM-5CCL9VFS (DW&P Server Side)
**Stape Container:** rxkbcogt (s.drwerner.com)

---

## 1. Übersicht der Änderungen

### Was wird gemacht?

| Bereich | Aktion | Status |
|---------|--------|--------|
| Consent Mode v2 | Neu einrichten mit Cookiebot | ✅ Erledigt (Tag 247) |
| Google Ads | Consent Settings korrigieren | ✅ Erledigt (8 Tags) |
| GA4 | Server-Side URL hinzufügen | ✅ Erledigt (Tag 134) |
| Universal Analytics | Tags entfernen (veraltet) | ✅ Pausiert (6 Tags) |
| Google Optimize | Tag pausieren (veraltet) | ✅ Pausiert (Tag 56) |
| Server-Side Tags | GA4, Google Ads Enhanced | Offen (manuell) |

---

## 2. Manuelle Schritte (GTM UI erforderlich)

### 2.1 Community Templates installieren

Diese Templates müssen manuell im GTM installiert werden:

**Im Web-Container (GTM-KL9XDC6):**

1. **Cookiebot CMP**
   - GTM → Templates → Tag Templates → Search Gallery
   - Suche: "Cookiebot CMP"
   - Herausgeber: Cybot A/S
   - Installieren

**Im Server-Container (GTM-5CCL9VFS):**

1. **Google Analytics 4**
   - Bereits als Client vorhanden
   - Tag Template für GA4 hinzufügen

2. **Facebook Conversions API** (optional, für Meta CAPI)
   - Suche: "Facebook Conversions API Tag"
   - Für Server-Side Facebook Events

---

## 3. Consent Mode v2 - Erklärung

### Was ist Consent Mode v2?

Google Consent Mode v2 ist seit März 2024 **Pflicht** für Google Ads in der EU. Es teilt Google mit, ob der Nutzer Tracking zugestimmt hat.

### Die Consent-Typen

| Consent-Typ | Bedeutung | Wer braucht es? |
|-------------|-----------|-----------------|
| `ad_storage` | Cookies für Werbung | Google Ads, Meta, LinkedIn |
| `ad_user_data` | Nutzerdaten an Google senden | Google Ads (NEU in v2!) |
| `ad_personalization` | Personalisierte Werbung | Google Ads Remarketing |
| `analytics_storage` | Cookies für Analytics | GA4, Clarity |
| `functionality_storage` | Funktionale Cookies | Chat-Widgets, etc. |
| `security_storage` | Sicherheits-Cookies | Immer granted |

### Default vs. Update

1. **Default** (vor Consent-Entscheidung):
   - Alle Marketing/Analytics auf `denied`
   - Wird beim Seitenaufruf gesetzt

2. **Update** (nach Consent-Entscheidung):
   - Cookiebot sendet automatisch Updates
   - Nur genehmigte Kategorien werden `granted`

---

## 4. Tag-Änderungen im Detail

### 4.1 Consent Initialization Tag (NEU)

**Zweck:** Setzt die Standard-Consent-Werte bevor irgendein anderer Tag feuert.

**Typ:** Custom HTML Tag
**Name:** `Consent Mode - Default`
**Trigger:** Consent Initialization - All Pages
**Priorität:** Muss als ERSTES feuern!

**Code:**
```html
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}

  // Consent Mode v2 Default - ALLES DENIED
  gtag('consent', 'default', {
    'ad_storage': 'denied',
    'ad_user_data': 'denied',        // NEU in v2!
    'ad_personalization': 'denied',
    'analytics_storage': 'denied',
    'functionality_storage': 'granted',
    'security_storage': 'granted',
    'wait_for_update': 500           // Wartet 500ms auf Cookiebot
  });

  // URL Passthrough für Conversion-Tracking ohne Cookies
  gtag('set', 'url_passthrough', true);

  // Ads Data Redaction bei fehlender Zustimmung
  gtag('set', 'ads_data_redaction', true);
</script>
```

**Erklärung der Werte:**
- `denied` = Keine Cookies setzen, keine Daten senden
- `granted` = Cookies erlaubt, Daten werden gesendet
- `wait_for_update` = Wartet auf Cookiebot-Antwort (in ms)
- `url_passthrough` = Überträgt Click-IDs (gclid) über URL statt Cookie
- `ads_data_redaction` = Entfernt Ads-Identifier wenn denied

---

### 4.2 Google Ads Conversion Tags - Consent Settings

**Problem:** Aktuell steht `consentStatus: "notNeeded"` - das ist FALSCH!

**Betroffene Tags:**
1. `Google Ads - Conversion` (693597954)
2. `Google Ads - Conversion KontaktFormular` (11246713731)
3. `Google Ads - Conversion Termin Vereinbart` (11548514261)

**Änderung für jeden Tag:**

| Setting | Alt | Neu |
|---------|-----|-----|
| Consent Status | `notNeeded` | `needed` |
| Consent Type | (leer) | `ad_storage`, `ad_user_data` |

**Was bedeutet das?**
- `needed` = Tag feuert NUR wenn Consent gegeben wurde
- Ohne Consent: Tag feuert im "Consent Mode" (modellierte Conversions)

---

### 4.3 GA4 Configuration Tag - Server-Side URL

**Tag:** `GA4 - Configuration Tag` (aktuell pausiert!)

**Änderung:**

| Setting | Alt | Neu |
|---------|-----|-----|
| Status | Paused | Active |
| Server Container URL | (leer) | `https://s.drwerner.com` |
| Send to Server | false | true |

**Was passiert dann?**
- GA4-Daten gehen zuerst an euren Server (Stape)
- Server leitet an Google weiter
- Vorteile: Bessere Datenqualität, Ad-Blocker-Resistenz, First-Party Cookies

---

### 4.4 Tags zum Entfernen

**Universal Analytics (veraltet seit Juli 2024):**

| Tag-Name | Tag-ID | Grund |
|----------|--------|-------|
| Universal Analytics | (alle UA-Tags) | Service eingestellt |

**Borlabs Cookie (wird durch Cookiebot ersetzt):**

| Element | Typ | Grund |
|---------|-----|-------|
| borlabs-cookie-opt-in-google-analytics | Trigger | Cookiebot übernimmt |
| Borlabs - GA4 | Variable | Cookiebot übernimmt |
| Borlabs - Meta | Variable | Cookiebot übernimmt |
| Borlabs - Clarity | Variable | Cookiebot übernimmt |
| Borlabs - Hubspot | Variable | Cookiebot übernimmt |
| Borlabs - Linkedin | Variable | Cookiebot übernimmt |
| Borlabs - Facebook Pixel | Variable | Cookiebot übernimmt |
| Borlabs - Google Ads | Variable | Cookiebot übernimmt |

**Google Optimize (Service eingestellt):**

| Tag-Name | Grund |
|----------|-------|
| Google Optimize | Service von Google eingestellt |

---

## 5. Server-Side Container (GTM-5CCL9VFS)

### Aktuelle Konfiguration

| Element | Wert |
|---------|------|
| GA4 Client | Aktiv mit Server-Managed Cookies |
| FPID Cookie | `_ga_server` |
| Session Cookie | `_ga_session` |

### Noch zu erstellen

| Tag | Zweck |
|-----|-------|
| GA4 Tag | Events an Google Analytics senden |
| Google Ads Conversion | Enhanced Conversions |
| (Optional) Meta CAPI | Server-Side Facebook Events |

---

## 6. DNS-Konfiguration (NOCH OFFEN!)

**Domain:** drwerner.com
**Subdomain:** s.drwerner.com

| Record | Typ | Name | Ziel |
|--------|-----|------|------|
| Tracking | CNAME | `s` | `euj.stape.io` |
| Loader | CNAME | `load.s` | `leuj.stape.io` |

**Status:** Noch nicht konfiguriert - muss im DNS-Provider gemacht werden.

---

## 7. Cookiebot Einrichtung

### Schritt 1: Cookiebot Account

1. Account bei usercentrics.com/cookiebot erstellen
2. Domain `drwerner.com` hinzufügen
3. Cookie-Scan durchführen lassen

### Schritt 2: GTM Integration

1. Cookiebot CMP Template installieren (siehe oben)
2. Cookiebot Tag erstellen:
   - Tag Type: Cookiebot CMP
   - Cookiebot ID: [Deine Cookiebot ID]
   - Trigger: Consent Initialization - All Pages

### Schritt 3: Consent Mode Mapping

Cookiebot mappt automatisch:

| Cookiebot Kategorie | Consent Mode Signal |
|---------------------|---------------------|
| Necessary | `functionality_storage`, `security_storage` |
| Statistics | `analytics_storage` |
| Marketing | `ad_storage`, `ad_user_data`, `ad_personalization` |

---

## 8. Checkliste vor Go-Live

### Vorbereitung
- [ ] DNS CNAME Records gesetzt
- [ ] SSL-Zertifikat aktiv (ca. 60 Min nach DNS)
- [ ] Cookiebot Account eingerichtet
- [ ] Cookiebot Template in GTM installiert

### GTM Web-Container
- [x] Consent Default Tag erstellt (Tag 247)
- [ ] Cookiebot Tag erstellt (manuell im GTM UI)
- [x] Google Ads Consent Settings korrigiert (8 Tags)
- [x] GA4 Server-Side URL konfiguriert (Tag 134)
- [x] Alte Tags entfernt/pausiert (7 Tags)
- [x] Version erstellt (Version 222 - Review)

### GTM Server-Container
- [ ] GA4 Tag erstellt
- [ ] Google Ads Tag erstellt (Enhanced Conversions)
- [ ] Version erstellt (Review)

### Testing
- [ ] GTM Preview Mode getestet
- [ ] Consent Banner erscheint
- [ ] Tags feuern nur mit Consent
- [ ] GA4 Daten kommen im Server an
- [ ] GA4 DebugView zeigt Events

---

## 9. Glossar

| Begriff | Erklärung |
|---------|-----------|
| **sGTM** | Server-Side Google Tag Manager |
| **CAPI** | Conversions API (Server-Side Events) |
| **Consent Mode** | Google-System für datenschutzkonformes Tracking |
| **FPID** | First-Party ID (Server-generierte User-ID) |
| **Enhanced Conversions** | Zusätzliche User-Daten für besseres Conversion-Tracking |
| **Stape** | Hosting-Anbieter für sGTM Container |

---

## 10. Änderungsprotokoll

| Datum | Änderung | Status |
|-------|----------|--------|
| 2024-12-04 | Server-Container zurückgesetzt | ✅ Erledigt |
| 2024-12-04 | Stape Container erstellt | ✅ Erledigt |
| 2024-12-04 | GA4 Client im sGTM | ✅ Erledigt |
| 2024-12-04 | Dokumentation erstellt | ✅ Erledigt |
| 2024-12-04 | Consent Initialization Trigger (246) | ✅ Erledigt |
| 2024-12-04 | Consent Default Tag (247) | ✅ Erledigt |
| 2024-12-04 | Google Ads Tags Consent v2 (8 Tags) | ✅ Erledigt |
| 2024-12-04 | Universal Analytics Tags pausiert (6) | ✅ Erledigt |
| 2024-12-04 | Google Optimize pausiert (1) | ✅ Erledigt |
| 2024-12-04 | GA4 Server-Side URL konfiguriert | ✅ Erledigt |
| 2024-12-04 | GTM Version 222 erstellt | ✅ Erledigt |
| 2024-12-04 | **Version NICHT publiziert** | ⏸️ Bereit |

### Erstellte GTM Version

**Version 222: "Server-Side Tracking + Consent Mode v2"**

Die Version enthält alle Änderungen und ist bereit zur Überprüfung und Veröffentlichung.

### Details der Google Ads Tag-Updates

| Tag ID | Tag Name | Consent Types |
|--------|----------|---------------|
| 132 | GAd-Ereignis-Lead-Kontakt-DE | ad_storage, ad_user_data |
| 133 | GAd-Ereignis-Lead-Kontakt-EN | ad_storage, ad_user_data |
| 139 | DE-GAds-Telefontermin-Conversion | ad_storage, ad_user_data |
| 154 | GAd-DE-Webinar-Lead | ad_storage, ad_user_data |
| 245 | Google Ads - QuickCheck Submit | ad_storage, ad_user_data |
| 128 | Remarketing | ad_storage, ad_user_data, ad_personalization |
| 205 | GAds Remarketing | ad_storage, ad_user_data, ad_personalization |
| 169 | Conversion Linker | ad_storage |

### Pausierte Tags

| Tag ID | Tag Name | Typ | Grund |
|--------|----------|-----|-------|
| 56 | Google Optimize | opt | Service eingestellt (Sep 2023) |
| 93 | GA-Ereignis-Lead-Kontakt-EN | ua | UA eingestellt (Jul 2024) |
| 103 | EN-Excel-download | ua | UA eingestellt |
| 123 | GA GE GE_Conversion_Telephone | ua | UA eingestellt |
| 125 | GA-Ereignis-Lead-Kontakt-DE | ua | UA eingestellt |
| 127 | EN-Telefontermin-Conversion | ua | UA eingestellt |
| 141 | GA Conversion Joboffer EN | ua | UA eingestellt |

