# **Product Requirement Document (PRD) – Backend Trello-like System**

## **1\. Introduzione**

Il presente documento descrive i requisiti per lo sviluppo esclusivamente backend di un sistema tipo Trello, con tutte le funzionalità di base necessarie alla gestione di board, liste, task (card) e utenti.

## **2\. Obiettivi del Progetto**

* Sviluppare un backend scalabile, sicuro e performante.

* Supportare tutte le funzionalità principali della versione base di Trello.

* Garantire API RESTful complete per integrazione futura con frontend.

* Prevedere gestione utenti, autenticazione, autorizzazioni e collaborazione.

## **3\. Scope**

### **Inclusioni**

* Gestione account e autenticazione.

* Creazione e gestione di board, liste e card.

* Gestione permessi e condivisione board.

* Commenti, etichette, scadenze e attività sulle card.

* Logging attività e notifiche base.

### **Esclusioni**

* Funzionalità avanzate (power-ups, automazioni complesse).

* UI/Frontend.

* Integrazioni con sistemi esterni.

## **4\. Requisiti Funzionali**

### **4.1 Gestione Utenti**

* Registrazione utente (email, password).

* Login/logout.

* Recupero password.

* Modifica profilo (nome, avatar).

### **4.2 Board**

* Creazione board.

* Modifica nome e descrizione.

* Archiviazione/eliminazione board.

* Gestione membri (inviti, ruoli: owner/editor/viewer).

### **4.3 Liste**

* Creazione lista all'interno di una board.

* Modifica nome lista.

* Riordinamento liste.

* Archiviazione/eliminazione lista.

### **4.4 Card (Task)**

* Creazione card.

* Modifica titolo e descrizione.

* Assegnazione utenti.

* Aggiunta scadenza (due date).

* Aggiunta etichette.

* Riordinamento e spostamento tra liste.

* Archiviazione/eliminazione card.

### **4.5 Commenti & Attachments**

* Aggiunta commenti alle card.

* Eliminazione commenti.

* Upload allegati (solo metadati se escluso storage fisico).

### **4.6 Attività & Notifiche**

* Tracking attività (CRUD su board/liste/card).

* Notifiche base (es. assegnazione card).

## **5\. Requisiti Non Funzionali**

* **Performance**: \<300ms per singola chiamata API.

* **Scalabilità**: sistemi orizzontali.

* **Sicurezza**: JWT, password cifrate, rate limiting.

* **Affidabilità**: uptime 99,5%, backup.

* **Documentazione API**: Swagger/OpenAPI.

## **6\. API**

* API RESTful conformi a standard **OpenAPI 3.x**, documentate tramite **Swagger UI**.

* Generazione automatica della documentazione tramite annotazioni o file YAML/JSON.

* Versionamento (es. `/api/v1`).

* Autenticazione via Bearer Token JWT verificata tramite middleware.

* Validazione input/output conforme allo schema OpenAPI.

* Endpoint accessibili anche via SDK generati automaticamente da OpenAPI.

## **7\. Database & Architettura Database & Architettura**

* DB relazionale o NoSQL (preferibilmente PostgreSQL o MongoDB).

* Modello entità-relazioni.

* Microservizi opzionali (base monolite).

## **8\. Ruoli e Permessi**

* Owner: pieno controllo.

* Editor: modifica contenuti.

* Viewer: sola lettura.

## **9\. Flussi Principali**

* Registrazione e autenticazione.

* Creazione board → Aggiunta membri → Creazione liste → Creazione card.

* Logs & aggiornamenti in tempo reale tramite webhook/Socket opzionali.

## **10\. KPI Successo**

* Creazione board senza errori.

* Tempo medio risposta API.

* Numero task gestiti.

* Zero vulnerabilità critiche.

## **11\. Roadmap di Sviluppo**

| Fase | Durata | Attività |
| ----- | ----- | ----- |
| 1 | 2 settimane | Setup architettura, autenticazione |
| 2 | 3 settimane | Gestione Board e Liste |
| 3 | 3 settimane | Gestione Card e Commenti |
| 4 | 2 settimane | Activity Log, notifica |
| 5 | 1 settimana | Hardening, test e deployment |

## **12\. Rischi**

* Scalabilità iniziale insufficiente.

* Problemi sicurezza accessi board condivise.

## **13\. Tecnologie Consigliate**

* **Backend**: Node.js/Express o Python FastAPI.

* **DB**: PostgreSQL o MongoDB.

* **Auth**: OAuth2/JWT.

* **Container**: Docker \+ orchestrator.

---

