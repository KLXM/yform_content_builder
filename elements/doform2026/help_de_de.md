# DoForm2026 – Redakteurshandbuch

DoForm2026 ist ein interaktiver Formular-Generator für Redakteure. Formulare werden ohne Programmierkenntnisse in fünf übersichtlichen Schritten konfiguriert.

---

## Schritt 1: Start

| Feld | Beschreibung |
|------|-------------|
| **Formular-Überschrift** | Wird als sichtbare Überschrift über dem Formular angezeigt. |
| **Überschrift HTML-Tag** | Semantische Ebene: H2 für Hauptformulare, H3/H4 bei Unterabschnitten. |
| **Einleitungstext** | Optionaler erklärender Text vor den Feldern (z. B. Hinweis zur Bearbeitungszeit). |

---

## Schritt 2: Felder

Hier legen Sie die eigentlichen Formular-Felder fest. Über **„Feld hinzufügen"** werden neue Zeilen angelegt, die Reihenfolge lässt sich per Pfeil-Buttons verschieben.

### Verfügbare Feldtypen

| Typ | Verwendung |
|-----|-----------|
| **Textfeld** | Name, Betreff, Freitext – einzeiliger Eingabebereich |
| **E-Mail** | Validiert automatisch das E-Mail-Format |
| **Telefon** | Für Telefonnummern, mobile-optimiert |
| **Textbereich** | Mehrzeilige Eingabe, z. B. für Nachrichten |
| **Auswahl (Dropdown)** | Vordefinierte Optionen zur Auswahl |
| **Checkbox** | Einzelnes Häkchen, z. B. für Zustimmungen |
| **Radio-Buttons** | Einfachauswahl aus mehreren sichtbaren Optionen |
| **Datei-Upload** | Erlaubt Anhänge (konfigurierbare Dateitypen) |
| **Kundennummer** | Spezielles Textfeld mit Kundennummer-Validierung |
| **Zählerstand** | Für Zählerstand-Formulare (Komma-/Punkt-Normalisierung) |
| **Versteckt** | Wird nicht angezeigt, überträgt Wert unsichtbar |
| **Fieldset (Gruppierung)** | Öffnet eine visuelle Gruppe (zusammen mit Fieldset Ende) |
| **Fieldset Ende** | Schließt eine Fieldset-Gruppe |
| **Zwischenüberschrift** | Trennt Formularabschnitte optisch mit einer Überschrift |
| **Trennlinie** | Fügt eine horizontale Linie als Abtrennungselement ein |

### Erweiterte Optionen pro Feld

Über den **„Erweiterte Optionen"**-Button eines Feldes lassen sich weitere Einstellungen öffnen:

**Validierungstyp** – legt fest, nach welchem Muster das Feld geprüft wird:

| Typ | Wann verwenden |
|-----|---------------|
| Keine | Nur Pflichtfeld-Prüfung reicht aus |
| IBAN | Bankverbindungen |
| BIC/SWIFT | Bankleitzahlen international |
| PLZ (DE/AT/CH) | Postleitzahlen nach Land |
| Telefonnummer | Formatprüfung Telefon |
| Datum (TT.MM.JJJJ) | Deutsches Datumsformat |
| Datum (JJJJ-MM-TT) | ISO-Datumsformat |
| Uhrzeit (HH:MM) | Uhrzeitformat |
| Nur Zahlen | Reine Ziffernfelder |
| Nur Buchstaben | Keine Zahlen erlaubt |
| Buchstaben und Zahlen | Alfanumerisch |
| Mindestlänge | Kombination mit Validierungs-Parameter (Zahl) |
| Maximallänge | Kombination mit Validierungs-Parameter (Zahl) |
| **Einfache Regel** | Für eigene Muster wie Kundennummern (siehe unten) |
| Eigenes Regex | Für Entwickler: vollständiges Regex-Muster |

---

### Einfache Regel – Platzhalternotation

Mit dem Validierungstyp **„Einfache Regel"** können Redakteure eigene Muster ohne Programmierung definieren. Der Wert wird im Feld **Validierungs-Parameter** eingetragen.

**Platzhalter:**

| Zeichen | Bedeutung |
|---------|-----------|
| `A` | Genau 1 Buchstabe (a–z, A–Z) |
| `9` | Genau 1 Ziffer (0–9) |
| `*` | Buchstabe oder Ziffer |
| Zahl (z. B. `30000`) | Zahl als Maximalwert (nur bei reinen Zahlenwerten) |
| Alle anderen Zeichen | Festes Zeichen (Bindestrich, Schrägstrich, Leerzeichen …) |

**Beispiele:**

| Muster | Passt auf | Passt nicht |
|--------|-----------|-------------|
| `KD-30000-99-AA` | KD-12345-67-AB | KD-99999-00-12 |
| `DE99 9999 9999 9999 9999 99` | DE89 3704 0044 0532 0130 00 | DE1234 |
| `AAA-99999` | ABC-12345 | AB-123 |
| `9999` | 1234 (≤ 9999) | 12345 |

> **Tipp:** Buchstaben werden automatisch in Großschreibung umgewandelt, wenn Sie unter „Schreibweise erzwingen" → **Nur Buchstaben/Zahlen (GROSS)** auswählen.

---

**Schreibweise erzwingen** – wandelt Eingaben automatisch um:

| Option | Effekt |
|--------|--------|
| Leerzeichen entfernen | Entfernt führende/nachfolgende Leerzeichen |
| GROSSBUCHSTABEN | Wandelt alle Zeichen in Großbuchstaben um |
| kleinbuchstaben | Wandelt alle Zeichen in Kleinbuchstaben um |
| Alle Leerzeichen entfernen | Kein Leerzeichen in der Eingabe (z. B. IBAN) |
| Nur Ziffern | Entfernt alle Nicht-Ziffern |
| Nur Buchstaben/Zahlen (GROSS) | Entfernt Sonderzeichen, macht alles groß |
| Zählerstand normalisieren | Wandelt Punkt in Komma um (1.234 → 1,234) |

---

## Schritt 3: Versand

| Feld | Beschreibung |
|------|-------------|
| **Empfänger E-Mail** | Wohin die Formular-Einsendung geschickt wird. Mehrere Adressen mit Komma oder Semikolon trennen. |
| **E-Mail Betreff** | Betreff der Benachrichtigungs-E-Mail. Platzhalter: `{name}`, `{email}`, `{subject}` |
| **Absender** | „E-Mail aus Formular" nutzt die Adresse des Nutzers als Absender. „System E-Mail" verwendet die System-E-Mail-Adresse. |
| **Erfolgsmeldung** | Text nach erfolgreicher Übermittlung. |
| **Fehlermeldung** | Text bei technischem Fehler (z. B. Mail-Server nicht erreichbar). |

---

## Schritt 4: Sicherheit

### Spam-Schutz

| Option | Wie es funktioniert |
|--------|-------------------|
| **Honeypot** | Ein unsichtbares Feld – nur Bots füllen es aus. Kein Aufwand für Nutzer. |
| **Zeit-Check** | Das Formular muss mindestens 3 Sekunden offen sein. Bots sind oft schneller. |
| **Beide** | Empfohlene Kombination für maximalen Schutz. |
| Keiner | Nur für interne oder nicht öffentliche Seiten. |

### Datenschutz-Checkbox

Wenn aktiviert, muss der Nutzer explizit zustimmen, bevor er abschicken kann.

- **Datenschutz-Text**: Text neben der Checkbox. Verwenden Sie `{link}` als Platzhalter für den verlinkten Begriff.
- **Datenschutz-Link**: URL zur Datenschutzseite (z. B. `/datenschutz/`).

**Beispiel:** `Ich habe die {link} gelesen und akzeptiere sie.`  
→ „Ich habe die [Datenschutzerklärung](/datenschutz/) gelesen und akzeptiere sie."

### Kopie an Absender

| Feld | Beschreibung |
|------|-------------|
| **Kopie aktivieren** | Schickt dem Absender eine Kopie seiner Einsendung |
| **Kopie Betreff** | Betreff der Bestätigungs-E-Mail |
| **Kopie Einleitung** | Begrüßungstext in der Bestätigungs-E-Mail |
| **Kopie Fußzeile** | Abschlusstext (Signatur, Kontaktdaten des Unternehmens) |
| **IBAN maskieren** | Schützt sensible Bankdaten in der Kopie (z. B. DE** **** **** **** **** 00) |

---

## Schritt 5: Layout

### Grundeinstellungen

| Feld | Beschreibung |
|------|-------------|
| **Layout** | **Standard**: Felder untereinander. **Grid**: Felder nebeneinander entsprechend der Feld-Breite. |
| **Button-Stil** | Farbe und Stil des Absende-Buttons (primary, secondary, danger usw.) |
| **Container-Breite** | Maximale Breite des Formular-Blocks (z. B. `uk-container-small`) |
| **Abstand oben/unten** | Vertikaler Abstand des Bereichs zur Umgebung |

### AJAX-Verbesserung

Wenn aktiviert, wird das Formular ohne Seitenneuladen abgeschickt. Die Erfolgs- oder Fehlermeldung erscheint direkt im Formularblock.

### Mehrstufiges Formular (Multistep)

Teilt das Formular in mehrere Seiten auf. Jedes **Fieldset** wird zu einem eigenen Schritt. Zwischen den Schritten kann der Nutzer vor- und zurücknavigieren.

| Feld | Beschreibung |
|------|-------------|
| **Mehrstufig aktivieren** | Schaltet das Multistep-Verhalten ein |
| **Zurück-Button Text** | Beschriftung des „Zurück"-Buttons (Standard: „Zurück") |
| **Weiter-Button Text** | Beschriftung des „Weiter"-Buttons (Standard: „Weiter") |

> **Tipp für Multistep:** Strukturieren Sie die Felder mit **Fieldset**-Elementen. Jeder Fieldset-Block (Fieldset bis Fieldset Ende) wird zu einem eigenen Formular-Schritt.

---

## Häufige Fragen

**Warum erscheint keine E-Mail?**  
Überprüfen Sie, ob im Schritt 3 eine Empfänger-Adresse eingetragen ist. Prüfen Sie auch die SPAM-Einstellungen des Postfachs.

**Können Felder nebeneinander stehen?**  
Ja – wählen Sie in Schritt 5 das Layout „Grid" und stellen Sie bei jedem Feld unter „Breite" die gewünschte Spaltenbreite ein (z. B. „Halbe Breite" für zwei Felder nebeneinander).

**Wie baue ich ein mehrstufiges Formular?**  
Fügen Sie **Fieldset**-Felder als Trenner ein. Aktivieren Sie in Schritt 5 „Mehrstufig". Jeder Fieldset-Block wird dann zu einem eigenen Schritt.

**Kann ich eigene Validierungsmuster nutzen?**  
Ja – wählen Sie als Validierungstyp „Einfache Regel" und tragen Sie das Muster im Feld „Validierungs-Parameter" ein (siehe Platzhalter-Tabelle oben).
