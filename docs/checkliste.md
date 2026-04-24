# Manuelle Test-Checkliste – NCP Shopping List App

## Vorbereitung

- [ ] Symfony Dev-Server starten: `symfony serve -d`
- [ ] Datenbankverbindung prüfen (SQLite oder PostgreSQL)
- [ ] JWT-Keys vorhanden: `ls config/jwt/`
- [ ] App erreichbar unter `http://127.0.0.1:8000`

---

## 1. Benutzer-Registrierung

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| Mit gültigen Daten registrieren | 201 Created, Benutzer angelegt | [ ] |
| Mit fehlenden Pflichtfeldern (name) | 400 Bad Request | [ ] |
| Mit fehlenden Pflichtfeldern (password) | 400 Bad Request | [ ] |
| Mit bereits existierendem Namen | 400/500 Error (Unique Constraint) | [ ] |

**Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"testuser","password":"testpass123"}'
```

---

## 2. Anmeldung (JWT-Token)

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| Mit korrekten Credentials anmelden | 200 OK mit token | [ ] |
| Mit falschem Passwort | 401 Unauthorized | [ ] |
| Mit nicht existierendem Benutzer | 401 Unauthorized | [ ] |
| Token in subsequent Requests verwenden | Request erfolgreich | [ ] |

**Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/login_check \
  -H "Content-Type: application/json" \
  -d '{"name":"testuser","password":"testpass123"}'
```

---

## 3. Einkaufslisten (CRUD)

### 3.1 Listen abrufen

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| Ohne Token abrufen | 401 Unauthorized | [ ] |
| Mit gültigem Token abrufen | 200 OK, Array der Listen | [ ] |
| Leere Liste (keine vorhanden) | 200 OK, `[]` | [ ] |

**Request:**
```bash
curl -X GET http://127.0.0.1:8000/api/lists \
  -H "Authorization: Bearer <TOKEN>"
```

### 3.2 Liste erstellen

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| Mit gültigem Token erstellen | 201 Created | [ ] |
| Mit Namen und Beschreibung | 201 Created, Daten gespeichert | [ ] |
| Ohne Namen erstellen | 400/500 Error | [ ] |
| Mit Items erstellen | 201 Created, Items angelegt | [ ] |

**Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/lists \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"name":"Wocheneinkauf","description":"Für nächste Woche","items":[{"name":"Milch","amount":2}]}'
```

### 3.3 Liste löschen

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| Eigene Liste löschen | 204 No Content | [ ] |
| Liste eines anderen Benutzers löschen | 403 Forbidden | [ ] |
| Nicht existierende Liste löschen | 404 Not Found | [ ] |

**Request:**
```bash
curl -X DELETE http://127.0.0.1:8000/api/lists/<LIST_ID> \
  -H "Authorization: Bearer <TOKEN>"
```

---

## 4. Zugriffskontrolle (Security)

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| Als Benutzer A eigene Listen abrufen | Nur eigene Listen sichtbar | [ ] |
| Als Benutzer A auf Listen von Benutzer B zugreifen | 403 Forbidden | [ ] |
| Als Benutzer A versuchen, Liste von Benutzer B zu löschen | 403 Forbidden | [ ] |
| Unauthentifizierter Zugriff auf `/api/*` | 401 Unauthorized | [ ] |

---

## 5. Web-Interface (falls vorhanden)

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| Startseite `/` aufrufen | 200 OK, Seite lädt | [ ] |
| Login-Seite `/login` aufrufen | 200 OK, Formular sichtbar | [ ] |
| Registrierungsseite `/register` aufrufen | 200 OK, Formular sichtbar | [ ] |
| Nach Login zur Listen-Übersicht | 200 OK, Listen angezeigt | [ ] |
| Neue Liste über Web-Interface erstellen | Liste in DB gespeichert | [ ] |
| Liste über Web-Interface löschen | Liste entfernt | [ ] |

---

## 6. Datenbank-Integrität

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| Benutzer in DB vorhanden | `SELECT * FROM ncp_user` | [ ] |
| Listen in DB vorhanden | `SELECT * FROM ncp_list` | [ ] |
| Items in DB vorhanden | `SELECT * FROM ncp_list_item` | [ ] |
| Fremdschlüssel (owner_id) korrekt | Join funktioniert | [ ] |
| Timestamps (created_at, updated_at) | Automatisch gesetzt | [ ] |

**SQL-Abfragen:**
```bash
bin/console dbal:run-sql "SELECT * FROM ncp_user"
bin/console dbal:run-sql "SELECT * FROM ncp_list"
bin/console dbal:run-sql "SELECT * FROM ncp_list_item"
```

---

## 7. Fehlerbehandlung

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| Invalid JSON an API senden | 400 Bad Request | [ ] |
| Ungültige UUID als ID verwenden | 404 Not Found oder 400 | [ ] |
| Fehlende Header (Content-Type) | 415 Unsupported Media Type | [ ] |
| Übermäßig lange Eingaben | 400 Bad Request (Validation) | [ ] |

---

## 8. Performance (optional)

| Test | Erwartetes Ergebnis | Status |
|------|---------------------|--------|
| 100 Listen erstellen | < 2 Sekunden | [ ] |
| Gleichzeitige API-Requests (10 parallel) | Keine Timeouts | [ ] |
| Große Antwort (viele Listen/Items) | < 5 Sekunden Ladezeit | [ ] |

---

## Zusammenfassung

| Kategorie | Tests gesamt | Bestanden | Fehlgeschlagen |
|-----------|--------------|-----------|-----------------|
| Registrierung | 4 | | |
| Anmeldung | 4 | | |
| Listen CRUD | 9 | | |
| Zugriffskontrolle | 4 | | |
| Web-Interface | 6 | | |
| Datenbank | 5 | | |
| Fehlerbehandlung | 4 | | |
| **Gesamt** | **36** | | |

---

## Notizen

_Für jeden fehlgeschlagenen Test:_

- Testdatum: __________
- Testschritt: ________________
- Erwartetes Ergebnis: ________________
- Tatsächliches Ergebnis: ________________
- Fehlermeldung: ________________
- Screenshot/Log: ________________

---

**Test abgeschlossen am:** _____________
**Getestet von:** ______________________