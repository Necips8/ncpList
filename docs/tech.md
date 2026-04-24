# Technische Dokumentation - NCP Shopping List App

## 1. Überblick

Die **NCP Shopping List App** ist eine REST-API-basierte Anwendung zur Verwaltung von Einkaufslisten. Benutzer können Einkaufslisten erstellen, verwalten und mit Listenelementen versehen. Die Authentifizierung erfolgt über JWT-Token.

## 2. Technologie-Stack

| Komponente | Technologie |
|------------|-------------|
| Framework | Symfony 7.x |
| Datenbank | SQLite (dev) / PostgreSQL (prod) |
| Authentifizierung | JWT (LexikJWTAuthenticationBundle) |
| ORM | Doctrine ORM |
| Serialisierung | Symfony Serializer |
| API-Format | JSON |

## 3. System-Architektur

```mermaid
graph TB
    subgraph "Client Layer"
        Web[Web Browser]
        Mobile[Mobile App]
        APIClient[API Client]
    end

    subgraph "Application Layer"
        SF[Symfony Framework]
        JWT[JWT Auth]
        Controllers[API Controllers]
    end

    subgraph "Business Logic Layer"
        User[User Entity]
        List[ShoppingList Entity]
        Item[ListItem Entity]
        Voter[Security Voter]
    end

    subgraph "Data Layer"
        DB[(SQLite/PostgreSQL)]
        Repo[Repositories]
    end

    Web --> SF
    Mobile --> SF
    APIClient --> SF
    SF --> JWT
    JWT --> Controllers
    Controllers --> User
    Controllers --> List
    Controllers --> Item
    User --> Voter
    List --> Voter
    User --> Repo
    List --> Repo
    Item --> Repo
    Repo --> DB
```

## 4. Datenbank-Modell

### 4.1 Entity-Beziehungen

```mermaid
erDiagram
    USER {
        uuid id PK
        string name UK
        string roles
        string password
        datetime created_at
        datetime updated_at
    }

    SHOPPING_LIST {
        uuid id PK
        string name
        string description
        string state
        uuid owner_id FK
        datetime created_at
        datetime updated_at
    }

    LIST_ITEM {
        uuid id PK
        uuid list_id FK
        string name
        string description
        string state
        float amount
        datetime created_at
        datetime updated_at
    }

    USER ||--o{ SHOPPING_LIST : "owns"
    SHOPPING_LIST ||--o{ LIST_ITEM : "contains"
```

### 4.2 Datenbank-Tabellen

| Tabelle | Beschreibung |
|---------|--------------|
| `ncp_user` | Benutzer mit Authentifizierungsdaten |
| `ncp_list` | Einkaufslisten |
| `ncp_list_item` | Elemente einer Einkaufsliste |

## 5. API-Endpunkte

### 5.1 Authentifizierung

```mermaid
sequenceDiagram
    participant Client
    participant Server
    
    Client->>Server: POST /api/register
    Server->>Server: Create User
    Server-->>Client: 201 Created
    
    Client->>Server: POST /api/login_check
    Server->>Server: Validate Credentials
    Server-->>Client: JWT Token
    
    Client->>Server: GET /api/lists (with JWT)
    Server-->>Client: JSON Response
```

| Endpunkt | Methode | Auth | Beschreibung |
|----------|---------|------|--------------|
| `/api/register` | POST | Nein | Benutzer registrieren |
| `/api/login_check` | POST | Nein | JWT-Token erhalten |
| `/api/lists` | GET | JWT | Alle Listen des Benutzers |
| `/api/lists` | POST | JWT | Neue Liste erstellen |
| `/api/lists/{id}` | DELETE | JWT | Liste löschen |

### 5.2 Request/Response Beispiele

**Registrierung:**
```json
POST /api/register
{
    "name": "testuser",
    "password": "securepassword"
}
```

**Login:**
```json
POST /api/login_check
{
    "name": "testuser",
    "password": "securepassword"
}
```

**Response:**
```json
{
    "token": "eyJhbGciOiJSUzI1NiJ9...",
    "user": {
        "id": "...",
        "name": "testuser"
    }
}
```

**Listen abrufen:**
```json
GET /api/lists
Authorization: Bearer <token>

Response:
[
    {
        "id": "...",
        "name": "Einkauf",
        "description": "Wocheneinkauf",
        "state": "active",
        "items": [...]
    }
]
```

## 6. Sicherheit

### 6.1 Access Control

```mermaid
flowchart LR
    A[Request] --> B{Path Match?}
    B -->|^/api/register| C[PUBLIC_ACCESS]
    B -->|^/api/login| D[PUBLIC_ACCESS]
    B -->|^/api| E[IS_AUTHENTICATED_FULLY]
    C --> F[Allow]
    D --> F
    E --> G{Valid JWT?}
    G -->|Yes| F
    G -->|No| H[401 Unauthorized]
```

### 6.2 Security Voter

Der `ShoppingListVoter` prüft Zugriffsrechte:

- Nur der Besitzer einer Liste kann diese lesen, ändern oder löschen.
- Rollenbasierte Rechte werden über Symfony Security verwaltet.

## 7. Projekt-Struktur

```
src/
├── Controller/
│   ├── ListController.php      # CRUD für Listen
│   ├── ListItemController.php  # CRUD für Items
│   └── RegistrationController.php
├── Entity/
│   ├── User.php                # Benutzer-Entity
│   ├── ShoppingList.php         # Listen-Entity
│   └── ListItem.php            # Item-Entity
├── Repository/
│   ├── UserRepository.php
│   ├── ShoppingListRepository.php
│   └── ListItemRepository.php
└── Security/
    └── Voter/
        └── ShoppingListVoter.php
```

## 8. Konfiguration

### 8.1 Datenbank (.env)

```env
# SQLite (Development)
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"

# PostgreSQL (Production)
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

### 8.2 JWT-Konfiguration

JWT-Keys werden generiert mit:
```bash
php bin/console lexik:jwt:generate-keypair
```

## 9. Testen

Tests befinden sich im `tests/`-Verzeichnis:

- `Controller/ShoppingListApiTest.php` - API-Integrationstests
- `Entity/ShoppingListTest.php` - Entity-Unit-Tests
- `Security/ShoppingListVoterTest.php` - Sicherheitstests

Ausführung:
```bash
php bin/phpunit
```

## 10. Migrationen

Datenbank-Migrationen liegen in `migrations/`:

- `Version20260424104957.php` - Initiale Migration

Ausführung:
```bash
php bin/console doctrine:migrations:migrate
```