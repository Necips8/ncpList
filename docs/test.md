# Dokumentation der Qualitätssicherung (Testing)

Dieses Dokument beschreibt die Teststrategie, die Durchführung der Tests und die aktuellsten Testergebnisse für die ncpList-Anwendung.

---

## 🛠️ Testumgebung vorbereiten

Bevor die Tests ausgeführt werden können, muss die Test-Datenbank initialisiert werden. Die App nutzt eine separate SQLite-Datenbank (`var/test.db`), um die Entwicklungsdaten nicht zu beeinflussen.

1.  **Test-Schema erstellen:**
    ```bash
    php bin/console doctrine:schema:create --env=test
    ```

---

## 🚀 Durchführung der Tests

Führe den folgenden Befehl im Projektverzeichnis aus, um alle automatisierten Tests (Unit-, Voter- und API-Tests) zu starten:

```bash
php vendor/bin/phpunit tests
```

---

## 📊 Aktuelle Testergebnisse

**Datum:** 24. April 2026  
**Status:** ✅ Erfolgreich bestanden

| Test-Kategorie | Beschreibung | Status |
| :--- | :--- | :--- |
| **Unit Tests** | Validierung der Entities (User, ShoppingList, ListItem) | ✅ OK |
| **Security Voter** | Prüfung der Zugriffsberechtigungen (Besitzer-Prinzip) | ✅ OK |
| **API Functional** | Registrierung, Login (JWT) und Authentifizierung | ✅ OK |
| **CRUD Functional** | Endpunkte für Listen- und Item-Verwaltung | ✅ OK |

### Zusammenfassung der Ausführung:
*   **Anzahl Tests:** 11
*   **Anzahl Assertions:** 33
*   **Erfolgsquote:** 100%

---

## 🧪 Abgedeckte Szenarien

### 1. Entity-Integrität
*   Automatische Generierung von Timestamps (`createdAt`, `updatedAt`).
*   Verwaltung von UUIDs.
*   Zuweisung von Rollen (Standardmäßig `ROLE_USER`).

### 2. Sicherheit (Voter)
*   Gewährung des Zugriffs (`VIEW`, `EDIT`, `DELETE`) für den Besitzer einer Liste.
*   Verweigerung des Zugriffs für andere Benutzer.

### 3. REST-API
*   Benutzerregistrierung mit Passwort-Hashing.
*   JWT-Token-Generierung bei korrektem Login.
*   Erstellung von Listen und Items über geschützte Endpunkte.
*   Aktualisierung des Status (z.B. von "open" auf "done").
