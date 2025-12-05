# Dr. Werner & Partner - Website Repository

Monorepo für Website-bezogene Entwicklungen von Dr. Werner & Partner und Philipp Sauerborn.

## Projektstruktur

```
dw-p-website/
├── quickcheck-malta/           # Malta Assessment QuickCheck
│   ├── public/                 # HTML-Dateien (DE, EN, NL)
│   ├── functions-php-integration.php
│   ├── INSTALLATION-OHNE-PLUGIN.md
│   └── README.md               # QuickCheck-spezifische Dokumentation
├── .claude/                    # Claude Code Konfiguration
│   ├── agents/                 # Custom AI Agents
│   └── commands/               # Slash Commands
├── CLAUDE.md                   # Development Guidelines
└── README.md                   # Diese Datei
```

## Projekte

### QuickCheck Malta (`/quickcheck-malta`)
Interaktiver Eignungscheck für Malta-Interessenten mit WordPress-Integration und n8n Webhook.

**Features:**
- 12 Fragen für präzise Eignung
- 3 Sprachen: Deutsch, English, Nederlands
- WordPress AJAX Integration
- n8n Webhook für Lead-Erfassung
- Responsive Design (Mobile-First)

[→ QuickCheck Dokumentation](./quickcheck-malta/README.md)

---

## Geplante Projekte

### Tracking & Analytics
- GTM Server Container Setup
- GA4 Event Tracking
- Ad Platform CAPI Integration (Meta, LinkedIn, Google Ads)

### Lead Magnets
- Vavolta Integration
- PDF Download Tracking
- Form Submissions

### Cross-Domain
- User ID Stitching
- BigQuery Analytics Pipeline

---

## Development

### Prerequisites
- Node.js 18+
- PHP 8.1+ (für WordPress-Integration)
- Git

### Quick Start
```bash
# Repository klonen
git clone https://github.com/Chrimby/dw-p-website.git
cd dw-p-website

# Projekt auswählen und Dokumentation lesen
cd quickcheck-malta
cat README.md
```

### Commands
- `/code-review` - Code Quality Review
- `/design-review` - UI/UX Review

---

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

## Websites

| Website | Domain | Status |
|---------|--------|--------|
| Dr. Werner & Partner | drwerner.com | Production |
| Philipp Sauerborn | philippsauerborn.com | Production |

---

**Repository:** https://github.com/Chrimby/dw-p-website
**Maintainer:** Dr. Werner & Partner / Brixon
