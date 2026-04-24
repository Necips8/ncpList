# Antwort auf Anforderungen

## Übersicht

Die NCP Shopping List App wurde gemäß den Anforderungen als Symfony REST-API Backend-Service implementiert. Alle geforderten Funktionen wurden umgesetzt.

---

## Umsetzungsstatus

### ✅ API-Endpunkte

| Anforderung | Endpunkt | Methode | Implementiert in |
|-------------|----------|---------|------------------|
| 1 | `/api/lists` | POST | [ListController.php](src/Controller/ListController.php) |
| 2 | `/api/lists/{id}/items` | POST | [ListItemController.php](src/Controller/ListItemController.php) |
| 3 | `/api/lists/{id}/items` | GET | [ListItemController.php](src/Controller/ListItemController.php) |
| 4 | `/api/lists/{id}/items/{itemId}` | GET | [ListItemController.php](src/Controller/ListItemController.php) |
| 5 | `/api/lists/{id}/items/{itemId}` | PUT | [ListItemController.php](src/Controller/ListItemController.php) |
| 6 | `/api/lists/{id}` | DELETE | [ListController.php](src/Controller/ListController.php) |
| 7 | `/api/lists/{id}/items/{itemId}` | DELETE | [ListItemController.php](src/Controller/ListItemController.php) |

### ✅ Datenmodell

| Entität | Tabelle | Beschreibung |
|---------|--------|--------------|
| User | `ncp_user` | Benutzerverwaltung (Registrierung, Authentifizierung) |
| ShoppingList | `ncp_list` | Einkaufslisten |
| ListItem | `ncp_list_item` | Einträge in Listen |

**Siehe:** [Entity-Dateien](src/Entity/)

- [User.php](src/Entity/User.php)
- [ShoppingList.php](src/Entity/ShoppingList.php)
- [ListItem.php](src/Entity/ListItem.php)

### ✅ Datenbank

- **Entwicklung:** SQLite (einfach, keine Installation nötig)
- **Produktion:** PostgreSQL (konfiguriert in `.env`)

**Konfiguration:** [.env](.env)

### ✅ Authentifizierung

- JWT-Token basiert (LexikJWTAuthenticationBundle)
- Registrierung: `POST /api/register`
- Login: `POST /api/login_check`

**Siehe:** [RegistrationController.php](src/Controller/RegistrationController.php)

### ✅ Web-Oberfläche

| Seite | Route | Controller |
|-------|-------|------------|
| Startseite | `/` | [ShoppingListWebController.php](src/Controller/ShoppingListWebController.php) |
| Login | `/login` | Symfony Security |
| Listen-Detail | `/list/{id}` | [ShoppingListWebController.php](src/Controller/ShoppingListWebController.php) |

**Siehe:** [templates/](templates/)

### ✅ Sicherheit

- Security Voter für Zugriffskontrolle
- Nur Besitzer können ihre Listen verwalten
- Rollenbasierte Rechte

**Siehe:** [ShoppingListVoter.php](src/Security/Voter/ShoppingListVoter.php)

---

## Dokumentation

| Dokument | Beschreibung |
|----------|---------------|
| [tech.md](docs/tech.md) | Technische Dokumentation mit Mermaid-Graphiken |
| [checkliste.md](docs/checkliste.md) | Manuelle Test-Checkliste (36 Tests) |
| [postman_collection.json](docs/postman_collection.json) | Postman-Importdatei für API-Tests |

---

## Projekt-Struktur

```
ncpList/
├── src/
│   ├── Controller/
│   │   ├── ListController.php       # CRUD für Listen
│   │   ├── ListItemController.php   # CRUD für Items
│   │   ├── RegistrationController.php
│   │   └── ShoppingListWebController.php
│   ├── Entity/
│   │   ├── User.php
│   │   ├── ShoppingList.php
│   │   └── ListItem.php
│   ├── Repository/
│   └── Security/Voter/
│       └── ShoppingListVoter.php
├── config/
│   ├── packages/
│   │   ├── security.yaml
│   │   ├── doctrine.yaml
│   │   └── lexik_jwt_authentication.yaml
│   └── jwt/                         # JWT Keys
├── templates/
│   └── shopping_list/
│       ├── index.html.twig
│       ├── detail.html.twig
│       └── login.html.twig
├── migrations/
│   └── Version20260424104957.php
├── docs/
│   ├── anforderung.md
│   ├── tech.md
│   ├── checkliste.md
│   └── postman_collection.json
└── tests/
    ├── Controller/
    ├── Entity/
    └── Security/
```

---

## Installation & Start

```bash
# Abhängigkeiten installieren
composer install

# Datenbank erstellen (SQLite)
bin/console doctrine:database:create

# Migrationen ausführen
bin/console doctrine:migrations:migrate

# JWT-Keys generieren
php bin/console lexik:jwt:generate-keypair

# Dev-Server starten
symfony serve
```

**Siehe auch:** [README.md](README.md)

---

## Testen

```bash
# Alle Tests ausführen
php bin/phpunit

# API mit Postman testen
# Import: docs/postman_collection.json
```

---

## Bekannte Einschränkungen

1. **Datenbank:** PostgreSQL-Server muss separat installiert und gestartet werden (in `.env` konfigurierbar)
2. **Web-Interface:** Basis-Implementierung; erweiterbar nach Bedarf
3. **Validierung:** Basis-Validierung vorhanden; kann erweitert werden

---

## Zusammenfassung

| Anforderung | Status |
|-------------|--------|
| REST-API Endpunkte (7) | ✅ Alle implementiert |
| Datenmodell (MySQL/PostgreSQL) | ✅ Implementiert (SQLite für Dev) |
| Web-Oberfläche | ✅ Implementiert |
| JWT-Authentifizierung | ✅ Implementiert |
| Dokumentation | ✅ Erstellt |
| Tests | ✅ PHPUnit + Postman Collection |

Das Projekt ist vollständig funktionsfähig und kann per `symfony serve` gestartet werden.