# Projektplan: Einkaufslisten-App (Symfony)

## 1. Zielsetzung

Entwicklung einer mobilen, performanten und sicheren Einkaufslisten-App mit Offline-Fähigkeit und Synchronisation. Fokus liegt auf einfacher Bedienung, schneller Interaktion und klarer Datenstruktur.

---

## 2. Datenbank- und Architekturdesign

### 2.1 Tabellenstruktur (optimiert)

#### ncp_user

* id (UUID, PK)
* name (unique)
* password (hashed)
* created_at
* updated_at

#### ncp_list

* id (UUID, PK)
* name
* description (nullable)
* state (z. B. active, archived)
* owner_id (FK → ncp_user)
* created_at
* updated_at

#### ncp_list_item

* id (UUID, PK)
* list_id (FK → ncp_list)
* name
* description (nullable)
* state (z. B. open, done)
* amount (int/float optional)
* created_at
* updated_at

#### (Optional, falls mehrere Nutzer pro Liste)

#### ncp_user_to_list

* id (UUID, PK)
* user_id (FK)
* list_id (FK)
* role (owner, editor, viewer)
* created_at
* updated_at

➡️ **Hinweis:**
Die Relation `ncpListToItem` ist nicht nötig, da `list_id` direkt im Item gespeichert wird (1:n Beziehung).

---

## 3. Backend (Symfony Standards)

### 3.1 Setup

* Symfony CLI verwenden
* Doctrine ORM für Datenbankzugriff
* UUID via `ramsey/uuid` oder Symfony UID Component
* Migrationen mit Doctrine Migrations

### 3.2 Entity Best Practices

* Typed Properties
* Lifecycle Callbacks für `created_at` / `updated_at`
* Soft Delete optional (z. B. via `deleted_at`)

### 3.3 Routing

* RESTful API (empfohlen für mobile Nutzung)

Beispiele:

* `POST /api/login`
* `GET /api/lists`
* `POST /api/lists`
* `GET /api/lists/{id}/items`
* `POST /api/items`

### 3.4 Security (Symfony Security Component)

* Passwort-Hashing: `password_hash` (argon2id)
* Auth: JWT (z. B. lexik/jwt-authentication-bundle)
* Zugriffsschutz:

  * Voter-System für Listen (User darf nur eigene sehen)
* Rate Limiting für Login

---

## 4. Programmablauf

### 4.1 Login

* Benutzer authentifiziert sich
* JWT Token wird gespeichert

### 4.2 Listenübersicht

* Nur eigene Listen laden
* Sortierung nach `updated_at`

### 4.3 Listendetail

* Items anzeigen
* Inline Editing ermöglichen

### 4.4 CRUD Aktionen

* Offline zuerst lokal speichern
* Sync später durchführen

---

## 5. Offlinefähigkeit & Synchronisation

### 5.1 Lokale Speicherung

* IndexedDB (Web) oder SQLite (Mobile Wrapper)
* Lokale IDs = UUID (gleich wie Server)

### 5.2 Sync-Strategie (wichtig verbessert)

**Empfohlen: "Last Write Wins" + Conflict Logging**

#### Felder ergänzen:

* `last_synced_at`
* `is_deleted` (für Soft Deletes)

### 5.3 Sync Ablauf

1. Änderungen lokal sammeln
2. Bei Verbindung:

   * alle Datensätze mit `updated_at > last_synced_at` senden
3. Server merged Daten
4. Server sendet aktualisierte Datensätze zurück

### 5.4 Konfliktlösung

* Standard: jüngeres `updated_at` gewinnt
* Optional: Konfliktliste anzeigen

### 5.5 Verbindungslogik

* Retry max. 3x (konfigurierbar)
* Exponential Backoff sinnvoll

### 5.6 Performance

* Batch Sync (ein Paket ist OK)
* Später ggf. Delta Sync einführen

---

## 6. Sicherheit

### 6.1 Zugriffsschutz

* Jede Anfrage wird gegen User geprüft
* Keine direkte ID-Zugriffe ohne Validierung

### 6.2 Datenvalidierung

* Symfony Validator Component
* Server-seitige Validierung Pflicht

### 6.3 Weitere Maßnahmen

* HTTPS erzwingen
* CORS korrekt konfigurieren
* CSRF (bei Session-basierten Requests)

---

## 7. Frontend / UI

### 7.1 Prinzipien

* Mobile First
* Minimalistisches Design
* Fokus auf Geschwindigkeit

### 7.2 UX Features

* Inline Editing (keine extra Seiten)
* Checkbox für erledigt
* Swipe-Gesten (optional)

### 7.3 Pagination

* Infinite Scroll statt klassischer Pagination

### 7.4 Empfohlene Tools

* Twig + Stimulus (Symfony UX)
* Alternativ: Vue.js / React (API-first Ansatz)

---

## 8. Entwicklungsphasen

### Phase 1: Grundlagen

* Symfony Setup
* Datenbank + Entities
* Migrationen

### Phase 2: Auth & Sicherheit

* Login
* JWT Integration
* Zugriffsschutz

### Phase 3: Kernfunktionalität

* Listen CRUD
* Items CRUD
* UI minimal

### Phase 4: Offline-Modus

* Lokale Speicherung
* Sync-Mechanismus

### Phase 5: Optimierung

* Performance verbessern
* UX verfeinern

### Phase 6: Erweiterungen

* Features erweitern
* Testing & Deployment

---

## 9. Testing

* PHPUnit für Backend
* API Tests (z. B. mit Symfony Panther)
* Manuelle Tests für Sync (kritisch!)

---

## 10. Deployment

* Docker Setup empfohlen
* CI/CD Pipeline (GitHub Actions)
* Environment Variablen für Secrets

---

## 11. Erweiterbare Features (optional)

### Funktional

* Teilen von Listen mit anderen Usern
* Echtzeit-Sync (WebSockets)
* Kategorien für Items
* Favoriten / häufig gekaufte Produkte
* Barcode-Scanner
* Erinnerungen / Notifications

### UX

* Dark Mode
* Drag & Drop Sortierung
* Sprachsteuerung

### Technisch

* PWA Support (installierbar)
* Push Notifications
* Caching mit Redis

