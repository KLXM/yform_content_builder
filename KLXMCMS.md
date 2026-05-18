# KLXMCMS – CMS-Konzept auf Basis von YForm Content Builder

Dieses Repository liefert den Content-Builder-Kern für ein modulares CMS auf **PHP + MySQL**.  
Das folgende Zielbild definiert ein vollständiges, per Composer verwaltbares KLXMCMS mit kleinem Kern und Extensions.

## 1) Architektur

- **Core-Kernel (klein):**
  - Routing/Request-Lifecycle
  - Service-Container
  - Rechte-/Auth-Hooks
  - Extension-Lifecycle (`install`, `activate`, `update`, `uninstall`)
  - Event-/Hook-System
- **Extension-first Ansatz:** Fachfunktionen liegen in separaten Erweiterungen.

## 2) Erste Extensions (MVP)

1. **Media**  
2. **Users / Groups**  
3. **Languages**  
4. **Content mit Frontend-Editor** (durch den Content Builder Kern)  
5. **Design System**  
6. **Backend**  
7. **Mail**  
8. **Custom Fields** (Taxonomie, SEO, CRUD-Builder)  
9. **ORM**  

## 3) Composer-Setup

- Verwaltung und Verteilung des CMS über Composer-Pakete:
  - `klxmcms/core`
  - `klxmcms/ext-media`
  - `klxmcms/ext-users`
  - `klxmcms/ext-languages`
  - `klxmcms/ext-content`
  - `klxmcms/ext-design-system`
  - `klxmcms/ext-backend`
  - `klxmcms/ext-mail`
  - `klxmcms/ext-custom-fields`
  - `klxmcms/ext-orm`

## 4) Installer

- Einfacher Installer-Flow:
  1. DB-Verbindung testen (MySQL)
  2. Tabellenmigrationen ausführen
  3. Admin-User anlegen
  4. Standardsprache setzen
  5. Basis-Extensions aktivieren

## 5) UI und Widgets

- **Modernes, austauschbares UI** (Theme-/Skin-fähig)
- **Widgets als Web Components** für wiederverwendbare Backend-/Frontend-Bausteine

## 6) Status in diesem Repository

Der in diesem Repository enthaltene **YForm Content Builder** bildet den Kernbaustein für die Content-Extension (inkl. Editor, Feldsystem und Rendering) und kann als Grundlage für den KLXMCMS-Core + Extension-Setup verwendet werden.
