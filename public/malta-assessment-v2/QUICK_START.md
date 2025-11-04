# Malta Eignungscheck - Quick Start Guide

## In 5 Minuten Live!

### 1. Datei vorbereiten (1 Minute)
```bash
# Webhook-URL in index.html anpassen (Zeile ~1786)
# Suchen Sie nach:
webhookUrl: 'https://brixon.app.n8n.cloud/webhook/malta-eignungscheck'

# Ersetzen Sie mit Ihrer URL:
webhookUrl: 'IHRE_WEBHOOK_URL_HIER'
```

### 2. In WordPress hochladen (2 Minuten)
```bash
# Option A: Via FTP
1. Ordner erstellen: /wp-content/uploads/malta-assessment/
2. index.html hochladen

# Option B: Via WordPress Media Library
1. WordPress → Medien → Datei hinzufügen
2. index.html hochladen
3. URL kopieren
```

### 3. Seite erstellen (2 Minuten)
```
1. WordPress → Seiten → Neu hinzufügen
2. Titel: "Malta Eignungscheck"
3. Block hinzufügen: "Custom HTML"
4. Code einfügen:

<iframe
    src="/wp-content/uploads/malta-assessment/index.html"
    style="width: 100%; min-height: 100vh; border: none;"
    title="Malta Eignungscheck">
</iframe>

5. Veröffentlichen
```

## Fertig! 🎉

Ihre Seite ist jetzt live unter:
```
https://ihre-domain.de/malta-eignungscheck/
```

---

## Webhook Testing (Optional)

### Schritt 1: Test-Webhook erstellen
Gehen Sie zu [webhook.site](https://webhook.site) und kopieren Sie die "Your unique URL"

### Schritt 2: In index.html einfügen
```javascript
webhookUrl: 'https://webhook.site/IHRE-ID-HIER'
```

### Schritt 3: Assessment durchführen
Gehen Sie durch den kompletten Assessment-Prozess

### Schritt 4: Daten prüfen
Zurück zu webhook.site → Sie sehen alle empfangenen Daten

---

## Häufige Fragen

**Q: Kann ich das Design anpassen?**
A: Ja! Suchen Sie nach `:root {` in der CSS-Sektion und ändern Sie die Farben.

**Q: Wie füge ich Google Analytics hinzu?**
A: Fügen Sie Ihren GA-Tag im `<head>` Bereich ein.

**Q: Funktioniert es mit jedem WordPress Theme?**
A: Ja! Das Assessment ist vollständig isoliert im iframe.

**Q: Brauche ich ein Plugin?**
A: Nein! Es ist eine standalone HTML-Datei.

**Q: Wie speichere ich die Ergebnisse?**
A: Der Webhook sendet alle Daten an Ihr Backend/CRM. Siehe README.md für Details.

---

## Support

Bei Problemen:
1. Lesen Sie README.md für detaillierte Anleitung
2. Prüfen Sie Browser Console auf Fehler (F12)
3. Testen Sie Webhook mit webhook.site

**Viel Erfolg!** 🚀
