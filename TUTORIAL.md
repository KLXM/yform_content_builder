# 🚀 Einsteiger-Tutorial: Dein erstes Content-Element

Willkommen! In diesem Tutorial erstellen wir **Schritt für Schritt** dein erstes eigenes Element für den YForm Content Builder.

Wir bauen eine **"Team-Box"** (Bild, Name, Jobtitel).

---

## 📋 Vorbereitung

1. Stelle sicher, dass das Addon **YForm Content Builder** installiert und aktiviert ist.
2. Wir arbeiten im Dateisystem. Du brauchst Zugriff auf deine REDAXO-Installation (per FTP oder VS Code).

---

## Schritt 1: Der richtige Ordner

REDAXO braucht einen Ort für deine eigenen Elemente. Der beste Platz ist das **Project-Addon** (oder ein eigenes Theme-Addon).

1. Gehe in den Ordner: `redaxo/src/addons/project/`
2. Erstelle dort einen Ordner namens `elements` (falls nicht vorhanden).
3. Erstelle darin einen Ordner für dein neues Element: `team_member`

Deine Struktur sollte so aussehen:
```text
redaxo/src/addons/project/elements/team_member/
```

---

## Schritt 2: Die Konfiguration (`config.php`)

REDAXO muss wissen, welche Felder dein Element hat. Das definieren wir in der `config.php`.

Erstelle die Datei: `redaxo/src/addons/project/elements/team_member/config.php`

Kopiere diesen Code hinein:

```php
<?php
return [
    // Der Name, der im Backend angezeigt wird
    'label' => 'Team Mitglied',
    
    // Ein Icon (von FontAwesome)
    'icon' => 'fa-user',
    
    // Die Eingabefelder
    'fields' => [
        'name' => [
            'type' => 'text',
            'label' => 'Name des Mitarbeiters',
        ],
        'job' => [
            'type' => 'text',
            'label' => 'Jobtitel',
        ],
        'photo' => [
            'type' => 'be_media', // Das REDAXO Medien-Widget
            'label' => 'Profilfoto',
        ]
    ]
];
```

---

## Schritt 3: Die Ausgabe (`templates/bootstrap.php`)

Jetzt bestimmen wir, wie das Element aussieht (HTML).

1. Erstelle im Ordner `team_member` einen Unterordner `templates`.
2. Erstelle darin die Datei `bootstrap.php`.

Pfad: `redaxo/src/addons/project/elements/team_member/templates/bootstrap.php`

Kopiere diesen Code hinein:

```php
<?php
// 1. Daten aus den Variablen holen (mit Fallback, falls leer)
$name = $elementData['name'] ?? '';
$job = $elementData['job'] ?? '';
$photo = $elementData['photo'] ?? '';
?>

<!-- 2. Das HTML Gerüst -->
<div class="panel panel-default">
    <div class="panel-body text-center">
        
        <!-- Foto anzeigen (wenn eines ausgewählt wurde) -->
        <?php if ($photo): ?>
            <img src="<?= rex_url::media($photo) ?>" 
                 alt="<?= rex_escape($name) ?>" 
                 style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 15px;">
        <?php endif; ?>

        <!-- Name und Job -->
        <h3><?= rex_escape($name) ?></h3>
        <p class="text-muted"><?= rex_escape($job) ?></p>
        
    </div>
</div>
```

🎉 **Glückwunsch!** Dein Element ist fertig programmiert.

---

## Schritt 4: Element nutzen (Variante A: Im Modul)

Du möchtest dieses Element nun in einem REDAXO-Modul verwenden, damit Redakteure es auf einer Seite einfügen können.

1. Gehe im REDAXO Backend zu **Module**.
2. Erstelle ein neues Modul "Team Box".

**Eingabe (Input):**
```php
<?php
// Wir rufen unser Element 'team_member' auf.
// 'bootstrap' sorgt dafür, dass es im Backend hübsch aussieht.
echo yform_content_builder_module::create('team_member', 'REX_VALUE[1]', 'bootstrap')->renderInput();
?>
```

**Ausgabe (Output):**
```php
<?php
// Hier geben wir das Element aus.
// Du kannst hier auch 'uikit' oder 'tailwind' angeben, wenn du dafür Templates angelegt hast.
echo yform_content_builder_module::create('team_member', 'REX_VALUE[1]', 'bootstrap')->renderOutput();
?>
```

3. Speichere das Modul und füge es in einem Artikel (Slice) hinzu. Du solltest nun dein Formular sehen!

---

## Schritt 5: Element nutzen (Variante B: In YForm)

Du kannst das Element auch in einer YForm-Tabelle (z.B. für News oder Produkte) nutzen.

1. Gehe zum **YForm Table Manager**.
2. Wähle deine Tabelle.
3. Füge ein neues Feld hinzu.
4. Wähle den Typ: `content_builder`.
5. Bei "Erlaubte Elemente" kannst du nun dein "Team Mitglied" auswählen (oder leer lassen für alle).

**Frontend Ausgabe (PHP):**
```php
<?php
use KLXM\YformContentBuilder\Helper;

// Angenommen, wir sind auf einer Detailseite und haben den Datensatz
$data = $dataset->getValue('mein_content_feld');

// Alles rendern
echo Helper::render($data, 'bootstrap');
?>
```

---

## 💡 Tipps für Profis (die es werden wollen)

*   **Eigene Icons:** Du kannst alle [FontAwesome 4.7 Icons](https://fontawesome.com/v4/icons/) nutzen.
*   **Mehr Felder:** Schau in die `SCHEMA.md` oder `API.md`, welche Feldtypen es noch gibt (z.B. `textarea`, `choice`, `repeater`).
*   **Frameworks:** Wenn du Tailwind nutzt, erstelle einfach eine `templates/tailwind.php` und nutze Tailwind-Klassen statt Bootstrap-Klassen.

Viel Erfolg beim Bauen! 🚀

---

# 🎓 Teil 2: Fortgeschrittene Elemente

Jetzt, wo du die Grundlagen kennst, bauen wir etwas Komplexeres: Eine **Bilder-Galerie** mit Tabs und Einstellungen.

## 1. Repeater (Wiederholbare Elemente)

Ein Repeater ist eine Liste von Dingen. Perfekt für Galerien, Slider oder Feature-Listen.

Erstelle einen neuen Ordner: `project/elements/gallery/`
Erstelle die `config.php`:

```php
<?php
return [
    'label' => 'Bilder Galerie',
    'icon' => 'fa-images',
    'fields' => [
        // Ein normales Feld für die Überschrift
        'headline' => [
            'type' => 'text',
            'label' => 'Galerie Titel'
        ],
        
        // Der Repeater
        'images' => [
            'type' => 'repeater', // Wichtig!
            'label' => 'Bilder',
            'add_label' => 'Bild hinzufügen', // Text auf dem Button
            
            // Hier definieren wir, was EIN Eintrag hat
            'fields' => [
                'file' => [
                    'type' => 'be_media',
                    'label' => 'Bilddatei'
                ],
                'caption' => [
                    'type' => 'text',
                    'label' => 'Bildunterschrift'
                ]
            ]
        ]
    ]
];
```

**Das Template (`templates/bootstrap.php`):**

```php
<?php
$headline = $elementData['headline'] ?? '';
$images = $elementData['images'] ?? []; // Repeater liefert immer ein Array
?>

<div class="gallery-element">
    <?php if ($headline): ?>
        <h2><?= rex_escape($headline) ?></h2>
    <?php endif; ?>

    <div class="row">
        <!-- Wir laufen durch das Array -->
        <?php foreach ($images as $image): ?>
            <?php 
            // Zugriff auf die Unter-Felder
            $file = $image['file'] ?? '';
            $caption = $image['caption'] ?? '';
            
            if (!$file) continue; // Kein Bild? Überspringen.
            ?>
            
            <div class="col-md-4">
                <img src="<?= rex_url::media($file) ?>" class="img-responsive">
                <?php if ($caption): ?>
                    <p><small><?= rex_escape($caption) ?></small></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

---

## 2. Ordnung schaffen mit Tabs

Wenn ein Element viele Felder hat, wird es schnell unübersichtlich. Tabs helfen!

Wir erweitern unsere Galerie um Tabs: "Inhalt" und "Einstellungen".

Passe die `config.php` an:

```php
<?php
return [
    'label' => 'Bilder Galerie Pro',
    'icon' => 'fa-images',
    
    // 1. Tabs definieren
    'field_groups' => [
        'content' => [
            'label' => 'Inhalt',
            'icon' => 'fa-file-image-o'
        ],
        'settings' => [
            'label' => 'Einstellungen',
            'icon' => 'fa-cogs'
        ]
    ],
    
    'fields' => [
        'headline' => [
            'type' => 'text',
            'label' => 'Titel',
            'tab' => 'content' // 2. Feld einem Tab zuweisen
        ],
        'images' => [
            'type' => 'repeater',
            'label' => 'Bilder',
            'fields' => [ ... ], // (wie oben)
            'tab' => 'content'
        ],
        
        // Neue Felder für den Settings-Tab
        'columns' => [
            'type' => 'choice',
            'label' => 'Spaltenanzahl',
            'choices' => [
                '2' => '2 Spalten',
                '3' => '3 Spalten',
                '4' => '4 Spalten'
            ],
            'default' => '3',
            'tab' => 'settings' // Ab in den Settings-Tab!
        ]
    ]
];
```

Das Addon baut nun automatisch Tabs im Backend. Du musst dich um nichts kümmern!

---

## 3. Das Settings-Modal (Für Profis)

Manchmal willst du Einstellungen verstecken, damit das Formular sauber bleibt. Dafür gibt es das **Settings Modal**. Das ist ein Button, der ein Popup öffnet.

Füge dies zu deiner `config.php` hinzu (auf gleicher Ebene wie 'fields'):

```php
    // ... fields ...

    'settings_modal' => [
        'label' => 'Layout Optionen',
        'icon' => 'fa-sliders',
        'fields' => ['margin_top', 'margin_bottom', 'background_color'] // Diese Felder kommen ins Modal
    ],
    
    'fields' => [
        // ... deine normalen Felder ...
        
        // Diese Felder werden im Hauptformular NICHT angezeigt, sondern nur im Modal
        'margin_top' => [
            'type' => 'choice',
            'label' => 'Abstand oben',
            'choices' => ['0' => 'Kein', '20' => 'Klein', '50' => 'Groß']
        ],
        'margin_bottom' => [
            'type' => 'choice',
            'label' => 'Abstand unten',
            'choices' => ['0' => 'Kein', '20' => 'Klein', '50' => 'Groß']
        ],
        'background_color' => [
            'type' => 'text',
            'label' => 'Hintergrundfarbe (Hex)'
        ]
    ]
```

**Ergebnis:** Im Backend erscheint ein Button "Layout Optionen". Klickt man darauf, öffnet sich ein Fenster mit den Abstands-Einstellungen.

---

## Zusammenfassung

Du kannst nun:
1.  Einfache Elemente erstellen (`text`, `media`).
2.  Listen erstellen (`repeater`).
3.  Formulare aufräumen (`tabs`).
4.  Komplexe Optionen verstecken (`settings_modal`).

Damit kannst du fast jede Anforderung umsetzen! Viel Spaß beim Coden. 💻

---

# 🎁 Bonus: Spicken erlaubt!

Das Addon liefert bereits **9 fertige Elemente** mit, die fast alles abdecken, was man im Alltag braucht:

*   **Text & Bild** (mit verschiedenen Layouts)
*   **Galerie** (Grid & Masonry)
*   **Akkordeon**
*   **Cards**
*   **Slideshow**
*   **Headline, Divider, Section...**

### Der beste Weg zu lernen:
Schau dir an, wie wir diese Elemente gebaut haben! Du findest sie im Ordner:

`redaxo/src/addons/yform_content_builder/elements/`

**Pro-Tipp:**
Wenn du ein Element brauchst, das *fast* so ist wie eines der Vorhandenen (z.B. eine Galerie, aber mit Video-Support), dann:
1.  Kopiere den Ordner aus dem Addon in dein `project/elements/` Verzeichnis.
2.  Benenne ihn um.
3.  Passe die `config.php` und das Template an.

So sparst du dir viel Arbeit und lernst direkt von funktionierendem Code.
