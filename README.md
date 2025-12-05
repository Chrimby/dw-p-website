# Dr. Werner & Partner - Website Repository

Zentrales Monorepo für Website-Entwicklung, Tracking und Tools von Dr. Werner & Partner und Philipp Sauerborn.

## Projektstruktur

```
dw-p-website/
├── docs/                           # Dokumentation & Konzepte
│   └── tracking/                   # Tracking-Konzepte & Briefings
│       ├── tracking-konzept-de.md  # Haupt-Tracking-Konzept (DE)
│       ├── tracking-konzept-en.md  # Main Tracking Concept (EN)
│       ├── developer-briefing-bigquery.md
│       ├── server-side-implementation-plan.md
│       └── server-side-migration.md
├── tools/                          # Interaktive Tools
│   └── quickcheck-malta/           # Malta Assessment QuickCheck
├── plugins/                        # WordPress Custom Plugins
├── tracking/                       # GTM Templates, Stape Config
├── themes/                         # Theme Customizations
├── .claude/                        # Claude Code Konfiguration
└── CLAUDE.md                       # Development Guidelines
```

## Hauptbereiche

### `/docs/tracking` - Tracking-Dokumentation
Vollständige Dokumentation des Tracking-Setups:
- **Tracking-Konzept**: Server-Side GTM, GA4, Ad Platform CAPI
- **BigQuery Analytics**: Custom Analytics Pipeline, User Stitching, Lead Scoring
- **CRM Integration**: Brevo, Salesforce, Offline Conversions

[→ Tracking Dokumentation](./docs/tracking/README.md)

### `/tools` - Interaktive Tools
Website-Tools wie QuickChecks, Calculators, Assessments:
- **QuickCheck Malta**: Eignungscheck für Malta-Interessenten (DE/EN/NL)

[→ QuickCheck Malta](./tools/quickcheck-malta/README.md)

### `/plugins` - WordPress Plugins
Custom WordPress Plugins für spezifische Funktionalitäten.

### `/tracking` - Tracking Implementation
GTM Container Exports, Stape Konfigurationen, Tag Templates.

### `/themes` - Theme Customizations
Child Themes, Custom CSS, Elementor Widgets.

---

## Websites

| Website | Domain | GA4 Property |
|---------|--------|--------------|
| Dr. Werner & Partner | drwerner.com | G-DRWERNER |
| Philipp Sauerborn | philippsauerborn.com | G-PHILIPP |

## Tech Stack

| Komponente | Technologie |
|------------|-------------|
| CMS | WordPress + Elementor |
| Hosting | (tbd) |
| GTM Server | Stape.io |
| Analytics | GA4 + BigQuery |
| Marketing Automation | Brevo |
| CRM | Salesforce |
| Ad Platforms | Google Ads, Meta, LinkedIn |

---

## Quick Start

```bash
# Repository klonen
git clone https://github.com/Chrimby/dw-p-website.git
cd dw-p-website

# Dokumentation lesen
cat docs/tracking/README.md
```

## Contributing

1. Branch erstellen: `git checkout -b feature/mein-feature`
2. Änderungen committen: `git commit -m "feat: Beschreibung"`
3. Push: `git push origin feature/mein-feature`
4. Pull Request erstellen

### Commit Convention
- `feat:` Neue Features
- `fix:` Bug Fixes
- `docs:` Dokumentation
- `refactor:` Code Refactoring
- `chore:` Maintenance

---

**Repository:** https://github.com/Chrimby/dw-p-website
**Maintainer:** Dr. Werner & Partner / Brixon
