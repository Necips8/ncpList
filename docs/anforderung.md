# Aufgabe: Einkaufslisten-App

## Beschreibung

Erstelle einen kleinen Backend-Service, der mehrere Einkaufslisten verwaltet auf Basis von Symfony. Die gesamte REST-API soll nach den Prinzipien von Symfony aufgebaut sein.

Die Daten sollen in einer einfachen Datenstruktur in einer MySQL Datenbank gespeichert werden.

Für das Warten der Einkaufsliste soll eine Oberfläche gebaut werden, in dem die Einkaufslisten verwaltet werden.

Die Entwicklungsumgebung/Server darf frei gewählt werden. Das Datenmodell der Datenbank soll selbst entworfen werden.

## Anforderungen

Die API soll folgende Funktionen/Endpunkte unterstützen:
1. POST /lists
	a. Erstellt eine Einkaufsliste mit X Einträgen.
2. POST /lists/:id/item
	a. Erstellt einen neuen Eintrag in der jeweiligen Einkaufsliste.
	b. Antwort: die aktualisierte Einkaufsliste.
3. GET /lists/:id/items
	a. Gibt die ganze Einkaufsliste zurück.
4. GET /lists/:id/items/:itemId
	a. Gibt das übergeben Item der Einkaufsliste zurück.
5. PUT /lists/:id/items/:itemId
	a. Aktualisiert das jeweilige Item der Liste.
6. DELETE /lists/:id
	a. Löscht eine Einkaufsliste
7. DELETE /lists/:id/items/:itemId
	a. Löscht ein Item der Einkaufsliste

Die Oberfläche soll selbst konzipiert werden und möglichst User freundlich gestaltet sein.

Das Ergebnis soll in einem Git Repository zur Verfügung gestellt werden inklusive Dokumentation wie das Projekt aufgesetzt werden kann.