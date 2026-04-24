# Tabellen

    ncpUser  (Benutzer)
        #id, name, passwort (verschlüsselt), createdAt, updatedAt

    ncpList (Einkaufsliste)
        #id, name, desc, state, createAt, updatedAt

    ncpListItem	(Eintrag/Artikel)
        #id, name, desc, state, createAt, updatedAt


    ncpUserToList (Relation Benutzer zu Liste)
		#id, #idUser, #List, createAt, updatedAt


    ncpListToItem (Relation Liste zu Eintrag)
		#id, #idList, #ListItem, amount, createAt, updatedAt
	

Hinweis:
    - Alle id als UUID anlegen
    
# Programmablauf

    Nach erfolgreichem Login die Listen des Users anzeigen.
    Ein Button

    Nach Auswahl einer Liste die Listeneinträge anzeigen. 



# Sicherheit    

- Sicherstellen, dass die Listen von anderen Useren nicht einsehbar sind.

# Mobilität

- Die App soll auch bei Ausfall zum Server weiterlaufen. Daten lokal speichern. Bei erfolgreicher Verbindung zum Server Daten synchronisieren. Abbruch der Verbindungsversuche nach drei mal (Konfigurierbar). Achte auf das jüngste Datum (createdAt und updatedAt) der Daten. Datenobrigkeit liegt auf den Daten der App. Die Datenbank spiegelt nur diese Daten. 

Datum der letzten Syncrhonisation am Datensatz merken und nur diejenigen  Datensätze synchronisieren, die jünger sind. 

Synchronisation erfolgt als CronJob und übermittelt, da wir hier es mit wenigen Datensätzen haben, als ein Datenpaket. Keine Chunks in diesem Stadium nötig.

# Design

- Mobil First	
- keep it simple (Fokus Listeneinträge, dezente Farben, inline Editing)
- Pagination





Bitte erstelle in docs/plan.md einen ausführlichen Plan in mehreren Etappen. Ergänze gegebenfalls unberücksichtigte wichtige Punkte. Führe noch einige interessante Features auf, die man zusätzlich einbauen könnte. Beachte die Sicherheit, das schlichte Design, die einfache Handhabung, die optimale Geschwindigkeit bei der syncrhonsieriung. Verwende symfony Standards von der Erstellung der Datenbankstrukturen, über das Routing, über die Verwendung von Sicherheitsmaßnahmen, bis hin zu empfohlenen Bibliotheken zum Rendern der Webseiten.