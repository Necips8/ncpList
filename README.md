# ncpList - Die schlaue Einkaufsliste

ncpList ist eine moderne, mobile-first Einkaufslisten-App, die auf Symfony 8 basiert. Sie bietet volle Offline-Fähigkeit durch IndexedDB und synchronisiert sich automatisch mit einem REST-Backend, sobald eine Internetverbindung besteht.

---

## 🛠️ Voraussetzungen

*   **PHP:** 8.4 oder höher
*   **Composer:** Aktuelle Version
*   **SQLite:** (Standardmäßig konfiguriert)
*   **OpenSSL:** Für die JWT-Verschlüsselung

---

## 🚀 Installation & Start

### 1. Repository klonen & Abhängigkeiten installieren
```bash
composer install
```

### 2. Datenbank vorbereiten
Die App nutzt SQLite. Führe die Migrationen aus, um die Tabellen zu erstellen:
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

### 3. Webserver starten
Nutze die Symfony CLI (empfohlen):
```bash
symfony serve -d
```
Oder den eingebauten PHP-Server:
```bash
php -S localhost:8000 -t public
```
Die App ist nun unter [http://localhost:8000](http://localhost:8000) erreichbar.

---

## 🔐 Zugangsdaten für Tests

Die folgenden Testbenutzer wurden bereits in der Datenbank angelegt:

| Benutzername | Passwort | Rolle |
| :--- | :--- | :--- |
| `testuser` | `testpass` | Standard-Nutzer |
| `testuser2` | `testpass` | Standard-Nutzer |
| `testuser3` | `testpass` | Standard-Nutzer |

*Hinweis: Falls du einen neuen Benutzer anlegen möchtest, nutze den `/api/register` Endpunkt via Postman oder PowerShell.*

---

## 📱 Bedienungsanleitung

### 1. Login
Rufe `/login` im Browser auf und melde dich mit den oben genannten Daten an. Der Token wird sicher im Browser gespeichert.

### 2. Listen verwalten
*   **Erstellen:** Gib einen Namen im Feld "Neue Liste" ein und klicke auf "Erstellen". Die Liste erscheint sofort, auch wenn du offline bist.
*   **Öffnen:** Klicke auf "Öffnen", um zu den Artikeln dieser Liste zu gelangen.
*   **Löschen:** Mit dem roten "Löschen"-Button entfernst du eine Liste unwiderruflich.

### 3. Artikel (Items) verwalten
*   **Hinzufügen:** Name und Menge eingeben und auf "Hinzufügen" klicken.
*   **Abhaken:** Klicke auf die Checkbox neben einem Artikel, um ihn als "erledigt" zu markieren. Der Text wird durchgestrichen.
*   **Entfernen:** Nutze das rote "X", um einzelne Artikel zu löschen.

### 4. Offline-Modus & Synchronisation
*   **Offline-Status:** Wenn du kein Internet hast, erscheint oben ein roter "Offline"-Badge. Du kannst die App trotzdem ganz normal weiterbenutzen.
*   **Auto-Sync:** Sobald du wieder online bist, versucht die App automatisch, alle lokalen Änderungen an den Server zu übertragen.
*   **Manueller Sync:** In der Listen- und Detailansicht findest du einen Button "Synchronisieren", um den Vorgang manuell anzustoßen.

---

## 🛠️ Technische Details

*   **Backend:** Symfony 8, Doctrine ORM, LexikJWTAuthenticationBundle.
*   **Frontend:** Twig, Vanilla CSS (Mobile First), Stimulus JS.
*   **Storage:** IndexedDB (Browser-Datenbank) für Offline-Support.
*   **API:** RESTful API unter `/api/...`.

---

## 🧪 API Testen

Die API kann auch direkt (z.B. via Postman) angesprochen werden. Denke daran, den JWT-Token im Header mitzusenden:
`Authorization: Bearer <dein_token>`

**Endpunkte:**
*   `POST /api/login_check` (Authentifizierung)
*   `GET /api/lists` (Listen abrufen)
*   `POST /api/lists` (Liste erstellen)
*   `POST /api/lists/{id}/items` (Artikel hinzufügen)
