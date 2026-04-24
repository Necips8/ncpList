# Projektplan: Einkaufslisten-App (ncpList)

Dieses Dokument beschreibt die schrittweise Umsetzung der Einkaufslisten-App basierend auf Symfony.

---

## Etappe 1: Setup & Infrastruktur
**Ziel:** Lauffähige Basis-Anwendung mit Datenbank-Anbindung und Entities.

1.  **Projekt-Initialisierung:**
    *   Symfony Skeleton aufsetzen.
    *   Benötigte Bundles installieren: `orm`, `migrations`, `maker`, `security`, `validator`, `ramsey/uuid-doctrine` (für UUIDs).
2.  **Datenbank-Design (Doctrine Entities):**
    *   `User`: id (UUID), name, password, timestamps.
    *   `ShoppingList`: id (UUID), name, description, state, owner (User), timestamps.
    *   `ListItem`: id (UUID), list (ShoppingList), name, description, state, amount, timestamps.
    *   `UserToList`: (Optional für Sharing) relationale Tabelle mit Rollen.
3.  **Timestamps:** Nutzung von Doctrine Lifecycle Callbacks (`PrePersist`, `PreUpdate`) oder Gedmo Timestampable.
4.  **Migrationen:** Erstellung der initialen Tabellenstruktur.

---

## Etappe 2: Authentifizierung & Sicherheit
**Ziel:** Sicherer Zugriff auf die API und das Frontend.

1.  **User Provider & Hashing:** Konfiguration von `security.yaml` (argon2id).
2.  **JWT Integration:** Setup von `lexik/jwt-authentication-bundle` für zustandslose API-Requests.
3.  **Voter-System:** Implementierung von Symfony Votern, um sicherzustellen, dass Nutzer nur ihre eigenen Listen/Items sehen, bearbeiten oder löschen können.
4.  **Rate Limiting:** Schutz der Login-Endpunkte gegen Brute-Force.

---

## Etappe 3: REST API Entwicklung (CRUD)
**Ziel:** Voll funktionsfähige Endpunkte für die mobile App und das Web-Frontend.

1.  **Listen-Endpunkte:**
    *   `GET /api/lists`: Eigene Listen abrufen.
    *   `POST /api/lists`: Neue Liste erstellen.
    *   `DELETE /api/lists/{id}`: Liste löschen.
2.  **Item-Endpunkte:**
    *   `GET /api/lists/{id}/items`: Items einer Liste abrufen.
    *   `POST /api/lists/{id}/items`: Item hinzufügen.
    *   `PUT /api/items/{id}`: Item aktualisieren (Name, Status, Menge).
    *   `DELETE /api/items/{id}`: Item löschen.
3.  **Validierung:** Einsatz der `Symfony Validator` Component für alle Eingangsdaten.

---

## Etappe 4: Frontend & UI (Mobile First)
**Ziel:** Intuitive Bedienung auf mobilen Geräten.

1.  **Technologie:** Symfony UX (Twig + Stimulus) für schnelle Interaktionen ohne komplettes SPA-Overhead.
2.  **Design-Prinzipien:**
    *   Schlichtes, helles Design (Fokus auf Content).
    *   Große Klickflächen für mobile Nutzung.
    *   **Inline-Editing:** Namen von Items direkt in der Liste ändern (Stimulus Controller).
3.  **Features:**
    *   Checkboxen für "Erledigt"-Status.
    *   Swipe-to-Delete (via CSS/JS).

---

## Etappe 5: Offline-Fähigkeit & Synchronisation
**Ziel:** Funktion bei fehlender Internetverbindung.

1.  **Client-Side Storage:** Nutzung von `IndexedDB` im Browser.
2.  **UUID-Strategie:** IDs werden bereits auf dem Client generiert, um Kollisionen zu vermeiden.
3.  **Sync-Logik:**
    *   Client speichert `last_synced_at`.
    *   Beim Sync: Sende alle lokalen Änderungen seit `last_synced_at`.
    *   Server nutzt "Last Write Wins" basierend auf `updated_at`.
4.  **Konfliktmanagement:** Loggen von Konflikten, falls Daten gleichzeitig auf verschiedenen Geräten geändert wurden.

---

## Etappe 6: Testing & Qualitätssicherung
**Ziel:** Stabilität und Korrektheit der Kernfunktionen.

1.  **Unit Tests:**
    *   Validierung der Entities (Constraint-Prüfungen).
    *   Berechnungslogik (z.B. Mengen-Summen).
2.  **Integration Tests:**
    *   API-Endpunkte testen (Authentifizierung, Response-Struktur).
    *   Voter-Tests (Zugriffsrechte prüfen).
3.  **Sync Tests:** Simulation von Offline-Szenarien und anschließender Synchronisation.

---

## Etappe 7: Zusätzliche Features (Optionale Erweiterungen)
1.  **Teilen-Funktion:** Einladung anderer Nutzer zu einer Liste (via `UserToList`).
2.  **Kategorisierung:** Automatisches Sortieren nach Supermarkt-Abteilungen.
3.  **Dark Mode:** Unterstützung für Systemeinstellungen.
4.  **Push-Notifications:** Erinnerung, wenn jemand ein Item auf einer geteilten Liste abhakt.

---

## Anhang: Beispieldaten

### ncp_user
| id (UUID) | name | password (hashed) | created_at |
| :--- | :--- | :--- | :--- |
| `550e8400-e29b-41d4-a716-446655440000` | max_mustermann | `$argon2id$v=19$m=65536,t=4,p=1$...` | 2023-10-01 10:00:00 |
| `6ba7b810-9dad-11d1-80b4-00c04fd430c8` | erika_test | `$argon2id$v=19$m=65536,t=4,p=1$...` | 2023-10-02 11:30:00 |

### ncp_list
| id (UUID) | name | description | owner_id | created_at |
| :--- | :--- | :--- | :--- | :--- |
| `a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11` | Wocheneinkauf | Alles für die Woche | `550e8400...` | 2023-10-05 08:00:00 |
| `b1f0cd11-8d1c-5fb9-cc7e-7cc0ce491b22` | Baumarkt | Projekt Gartenhaus | `550e8400...` | 2023-10-06 14:20:00 |

### ncp_list_item
| id (UUID) | list_id | name | amount | state | updated_at |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `f47ac10b-58cc-4372-a567-0e02b2c3d479` | `a0eebc99...` | Milch | 2 | open | 2023-10-05 08:10:00 |
| `d9b23b32-9759-4d93-9e6b-072a884d9133` | `a0eebc99...` | Brot | 1 | done | 2023-10-05 09:15:00 |
| `e2a3c4d5-f6a7-4b8c-9d0e-1f2a3b4c5d6e` | `b1f0cd11...` | Holzschrauben | 50 | open | 2023-10-06 14:30:00 |
