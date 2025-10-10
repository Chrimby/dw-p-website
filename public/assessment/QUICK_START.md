# Quick Start Guide - B2B Marketing Assessment

## 🚀 In 3 Schritten live gehen

### Schritt 1: Webhook-URL konfigurieren (2 Minuten)

1. Öffnen Sie `index.html` in einem Text-Editor
2. Suchen Sie nach Zeile ~430: `webhookUrl: 'YOUR_WEBHOOK_URL_HERE'`
3. Ersetzen Sie mit Ihrer Webhook-URL

**Webhook-Dienste (kostenlos starten):**
- **Make.com**: Erstellen Sie einen neuen Szenario → Webhook → Kopieren Sie die URL
- **Zapier**: Neuer Zap → Webhook → Kopieren Sie die URL  
- **n8n**: Webhook Node → Kopieren Sie die URL
- **Webhook.site**: Sofort URL zum Testen (keine Anmeldung nötig)

### Schritt 2: In WordPress hochladen (1 Minute)

**Methode A - Als Seite (Empfohlen):**
```
1. WordPress → Medien → Datei hinzufügen
2. Laden Sie index.html hoch
3. Kopieren Sie die URL der Datei
4. Neue Seite → Custom HTML Block
5. Einfügen: <iframe src="IHRE_URL" style="width:100%; min-height:100vh; border:none;"></iframe>
```

**Methode B - Direkt einbetten:**
```
1. Neue WordPress Seite
2. Custom HTML Block hinzufügen
3. Kompletten Inhalt von index.html kopieren und einfügen
```

### Schritt 3: Testen (30 Sekunden)

1. Öffnen Sie die Seite in Ihrem Browser
2. Beantworten Sie einige Test-Fragen
3. Prüfen Sie ob Webhook Daten empfängt
4. ✅ Fertig!

---

## 📊 Was Sie mit den Daten machen können

Die Webhook-Daten enthalten:
- **Alle Antworten** der Teilnehmer
- **Berechnete Scores** für alle 4 Phasen
- **Kontaktdaten** (bei Opt-in)
- **Interpretationen** und Empfehlungen

### Integration Beispiele:

**➜ In CRM speichern** (z.B. HubSpot, Pipedrive)
```
Make.com/Zapier: Webhook → CRM "Create Contact"
Felder mappen: Email, Name, Score, etc.
```

**➜ Per E-Mail benachrichtigen**
```
Webhook → Gmail/Outlook "Send Email"
Betreff: "Neues Assessment: {firstName} {lastName}"
Inhalt: {totalScore} Punkte, {totalInterpretation}
```

**➜ In Google Sheets loggen**
```
Webhook → Google Sheets "Add Row"
Spalten: Timestamp, Name, Email, Scores...
```

**➜ Slack Notification**
```
Webhook → Slack "Post Message"
Text: "🎯 Neues Assessment: {totalScore}/121 Punkte"
```

---

## 🎨 Branding anpassen (optional)

Die App funktioniert sofort mit **Work Sans** - keine Konfiguration nötig!

### Farben ändern (in index.html):
```css
:root {
    --color-yellow: #f7e74f;  /* Ihre Akzentfarbe */
    --color-black: #000000;   /* Hauptfarbe */
}
```

### Logo hinzufügen:
Suchen Sie nach `<h1>B2B Marketing Assessment` und ersetzen Sie mit:
```html
<img src="ihr-logo.png" alt="Logo" style="max-width: 200px;">
<h1>B2B Marketing Assessment</h1>
```

---

## ❓ Häufige Fragen

**Q: Wie viele Dateien sind es?**  
A: Nur 3 Dateien: `index.html` (Haupt-App), `README.md` (Anleitung), `QUICK_START.md` (dieser Guide)

**Q: Funktioniert es auf Mobile?**  
A: Ja! Vollständig responsive für alle Geräte.

**Q: Kann ich Fragen ändern?**  
A: Ja, im `questions` Array in index.html. Gut dokumentiert!

**Q: DSGVO konform?**  
A: Datenschutz-Checkbox ist integriert. Link zu Ihrer Datenschutzerklärung hinzufügen.

**Q: Funktioniert ohne Webhook?**  
A: Ja, Daten werden in Browser Console geloggt und in localStorage gespeichert als Backup.

**Q: Kann ich das Design ändern?**  
A: Absolut! Alle Styles sind in CSS Custom Properties definiert.

---

## 🔧 Support & Tipps

### Debugging:
1. Browser Console öffnen (F12)
2. Network Tab → Webhook Request prüfen
3. Console Tab → Fehler checken

### Performance:
- Datei ist ~60KB (sehr klein!)
- Lädt in <1 Sekunde
- Keine externen Dependencies

### Browser Support:
✅ Chrome, Firefox, Safari, Edge (alle aktuellen Versionen)  
✅ Mobile: iOS Safari, Chrome Mobile

---

## 📈 Nächste Schritte nach Go-Live

1. **Analytics einrichten** (Google Analytics)
2. **E-Mail-Automation** konfigurieren (für Opt-in Nutzer)
3. **A/B Tests** durchführen (verschiedene Fragen)
4. **Lead Scoring** in CRM basierend auf Assessment-Scores

---

**Viel Erfolg! 🚀**

Bei Problemen: Siehe ausführliche `README.md`