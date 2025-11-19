# 🧭 Product Requirements Document (PRD) – Piattaforma di Project Management (Trello-like)

**Project Title:** Piattaforma Completa per il Project Management (Trello-like)
**Version:** 2.0 (Unificata)
**Date:** Novembre 19, 2025
**Author:** Team Work
**Approved by:** Luca Sacchi Ricciardi

***

## 🧱 1. Overview

### 1.1 General Description

Questo documento descrive i requisiti per lo sviluppo di un **sistema full-stack** scalabile, sicuro e performante che emuli le funzionalità core di una piattaforma di project management in stile Trello. Il sistema fornirà sia le API RESTful necessarie a gestire utenti, bacheche, liste e card, sia l'interfaccia utente (UI) per interagire con tali dati.

### 1.2 Context / Motivation

* **User Problem:** Organizzazioni e team necessitano di una piattaforma flessibile e centralizzata per tracciare il lavoro, gestire i flussi operativi e facilitare la collaborazione sui task.
* **Business Objectives:** Creare un'applicazione completa, garantendo un'elevata **scalabilità** e **performance** sin dall'inizio e fornendo un'esperienza utente intuitiva e fluida.
* **Insight:** L'architettura deve rimanere **API-first** per garantire l'integrazione con future applicazioni (web/mobile dedicate).

### 1.3 Key Objectives

* **Sviluppare un backend scalabile, sicuro e performante** (API call <300ms).
* **Sviluppare un'interfaccia utente (Frontend) intuitiva** che implementi il paradigma Kanban.
* **Supportare tutte le funzionalità core** (CRUD per Boards, Lists, Cards, Users).
* **Fornire API RESTful complete** e ben documentate (OpenAPI 3.x).
* **Abilitare la gestione utente, autenticazione e autorizzazione** per la collaborazione.

### 1.4 Non-Objectives (Out of Scope)

* Funzionalità avanzate come Power-Ups, automazioni complesse o template di bacheche.
* Integrazioni con sistemi esterni (Slack, GitHub, ecc.).
* Gestione fisica di file per allegati (solo metadata).

***

## 🎯 2. Target & User

### 2.1 Use Cases / Scenarios

* **Scenario 1: Initial Team Setup:** Un nuovo utente si registra, crea una nuova Bacheca e aggiunge i membri del suo team, assegnando loro il ruolo `Editor`.
* **Scenario 2: Task Management:** Un membro del team crea una Card, la sposta tra Liste, aggiunge una `Due Date` e assegna una `Label`. Un altro membro aggiunge un `Comment`.
* **Scenario 3: Audit:** Il proprietario della Bacheca controlla le attività recenti sulla Bacheca tramite il log eventi visualizzato nell'interfaccia.

***

## 🧩 3. Product Requirements

### 3.1 Functional Requirements

| ID | Name | Description | Priority (MoSCoW) | Notes |
|:---|:---|:---|:---|:---|
| **FR1.1** | User Registration | L'utente deve potersi registrarsi con email e password. | Must | |
| **FR1.2** | Login/Logout | Gestione della sessione e autenticazione tramite credenziali. | Must | Uso di JWT (Backend). |
| **FR1.3** | Board Management | CRUD completo per le Bacheche (Creazione, Modifica, Archiviazione/Eliminazione). | Must | |
| **FR1.4** | List Management | CRUD e riordino delle Liste all'interno di una Bacheca. | Must | |
| **FR1.5** | Card Management | CRUD e modifica di titolo, descrizione, assegnatari, data di scadenza, etichette. | Must | |
| **FR1.6** | Card Movement | Abilità di riordinare le Card e spostarle tra Liste diverse. | Must | Funzionalità supportata dal Backend e implementata tramite Drag & Drop nel Frontend. |
| **FR1.7** | Comments | Aggiunta ed eliminazione di commenti sulle Card. | Should | |
| **FR1.8** | Board Members | Aggiunta, rimozione e gestione dei ruoli (`Owner`, `Editor`, `Viewer`). | Must | |
| **FR1.9** | Activity Log | Tracciamento delle modifiche (CRUD) su Boards, Lists e Cards. | Must | |
| **FR1.10** | Basic Notifications | Notifiche per l'assegnazione di una card. | Should | |
| **FR2.1** | **UI: Dashboard** | Visualizzazione delle bacheche accessibili dall'utente con filtri e ricerca. | Must | |
| **FR2.2** | **UI: Vista Kanban** | Visualizzazione della bacheca con le liste organizzate in colonne e le card al loro interno. | Must | Layout responsive. |
| **FR2.3** | **UI: Drag & Drop Task** | Abilità di spostare le card tra liste diverse e riordinarle all'interno di una lista tramite Drag & Drop. | Must | Interazione fluida e reattiva. |
| **FR2.4** | **UI: Dettaglio Card** | Modale o sidebar per visualizzare e modificare tutti i dettagli di una card (campi FR1.5 e FR1.7). | Must | |

### 3.2 Non-Functional Requirements

| Type | Description | Measure/Example |
|:---|:---|:---|
| **Performance (BE)** | Bassa latenza delle risposte API. | Tempo medio di risposta <300ms. |
| **Scalability (BE)** | Abilità di gestire carichi di lavoro crescenti. | Architettura che supporti la scalabilità orizzontale. |
| **Security (BE)** | Protezione dei dati e della comunicazione. | JWT Authentication, password encryption, rate limiting. |
| **Reliability (BE)** | Disponibilità e continuità del servizio. | 99.5% uptime. Implementazione di una strategia di backup. |
| **Usability (FE)** | Interfaccia intuitiva e responsive. | 90% dei nuovi utenti completa il flusso 'Registrazione + Creazione Bacheca + Creazione Card' in <60 secondi. |
| **Responsiveness (FE)** | L'applicazione deve funzionare correttamente su tutti i principali dispositivi. | Pieno supporto per schermi da 375px (mobile) in su. |
| **Performance (FE)** | Caricamento rapido dell'interfaccia. | First Contentful Paint (FCP) <1.5s. |
| **Documentation** | API chiare e auto-documentate. | Uso di Swagger/OpenAPI 3.x per la documentazione automatica del Backend. |

***

## 🧠 4. User Experience / Design

### 4.1 User Flow

1.  **Registrazione:** Utente $\rightarrow$ `POST /register` $\rightarrow$ Riceve JWT token.
2.  **Login:** Utente $\rightarrow$ `POST /login` $\rightarrow$ Riceve JWT token.
3.  **Setup Bacheca:** Utente $\rightarrow$ **UI Dashboard** $\rightarrow$ `POST /boards` $\rightarrow$ `POST /boards/{id}/members` (aggiunge membri) $\rightarrow$ **UI Board View** $\rightarrow$ `POST /boards/{id}/lists`.
4.  **Gestione Task:** Utente $\rightarrow$ **UI Drag & Drop** $\rightarrow$ `POST /lists/{id}/cards` $\rightarrow$ `PATCH /cards/{id}/move` (sposta/modifica) $\rightarrow$ **UI Dettaglio Card** $\rightarrow$ `POST /cards/{id}/comments`.

### 4.2 Wireframes / Mockups

> *Sarà necessario un set completo di **Wireframes e Mockups** per definire l'esperienza utente delle bacheche, delle card e della dashboard, da produrre prima dell'inizio dello sviluppo Frontend (Vedi Fase 0 della pianificazione).*

### 4.3 Copy & Tone of Voice

> *Tutti i messaggi di errore (codici di stato HTTP) e i messaggi di validazione devono essere chiari e standardizzati sia lato Backend che Frontend.*

***

## ⚙️ 5. Technical Architecture / API

### 5.1 Technical Dependencies

* **Backend:** Node.js/Express o Python/FastAPI (scelta raccomandata).
* **Database:** PostgreSQL (preferito) o MongoDB.
* **Authentication:** OAuth2/JWT Bearer Token.
* **Containerization:** Docker.
* **Frontend Framework:** **React** (con Redux Toolkit o equivalente per la gestione dello stato) o **Vue.js** (con Pinia).
* **Frontend Styling:** Tailwind CSS (utility-first) o Styled Components (CSS-in-JS).

### 5.2 Planned Endpoints (Backend)

| Endpoint | Method | Description | Input | Output |
|:---|:---|:---|:---|:---|
| `/v1/auth/login` | POST | User Authentication | email, password | JWT Token |
| `/v1/boards` | POST | Creates a new Board | name, description | Board ID |
| `/v1/boards/{id}/lists` | POST | Adds a List to the Board | name | List ID |
| `/v1/cards` | POST | Creates a Card in a List | title, description, List ID | Card ID |
| `/v1/cards/{id}/move` | PATCH | Moves a Card | New List ID, position | Status 200 |

### 5.3 DevOps / Scalability Considerations

* **Scalability:** Design dell'applicazione e del database per supportare la scalabilità orizzontale.
* **CI/CD:** Pipeline automatizzata per il testing e il deployment di Backend e Frontend (tramite Docker).
* **Logging:** Implementazione di un sistema di logging centralizzato (es. ELK Stack) per il monitoraggio.

***

## 🧪 6. Metrics and Success Criteria

### 6.1 Key KPIs

* **Board Creation Success Rate:** Tasso di successo per la creazione di Bacheche $\rightarrow$ 100% (zero errori critici).
* **Average API Response Time (AART):** Tempo medio di risposta API $\rightarrow$ <300ms.
* **Tempo di Caricamento (FE):** Tempo di First Contentful Paint (FCP) $\rightarrow$ <1.5s.
* **Critical Vulnerability Count:** Numero di vulnerabilità critiche rilevate nei test di sicurezza $\rightarrow$ Zero.

### 6.2 Secondary Metrics

* **Error Rate:** Percentuale di richieste API che restituiscono codici di stato 5xx $\rightarrow$ <0.1%.
* **Test Coverage:** Copertura dei test unitari e di integrazione (sia FE che BE) $\rightarrow$ >80%.

***

## 📅 7. Planning and Deadlines

| Phase | Activity | Responsible | Start Date | End Date |
|:---|:---|:---|:---|:---|
| **Phase 0 (Design)** | **UI/UX Design (Wireframes & Mockups, Design System)** | Design Team | [To be defined] | 1 Week |
| **Phase 1 (BE)** | Architecture Setup, Authentication (JWT, Auth) | Dev Team | [After Phase 0] | 2 Weeks |
| **Phase 2 (BE)** | Board and List Management (CRUD, Members, Roles) | Dev Team | [After Phase 1] | 3 Weeks |
| **Phase 3 (BE)** | Card and Comment Management (CRUD, Move, Due Date, Label) | Dev Team | [After Phase 2] | 3 Weeks |
| **Phase 4 (FE)** | **Frontend Core Development (Setup, Routing, State Mgt, Basic Views)** | Dev Team | [Concurrent w/ Phase 2] | 4 Weeks |
| **Phase 5 (FE+BE)** | **Integrazione completa, UI refinement, Visualizzazione Log Attività, Notifiche** | Dev Team | [After Phase 3 & 4] | 3 Weeks |
| **Phase 6** | Security Hardening, Final Testing, Documentazione, Deployment | Dev Team | [After Phase 5] | 1 Week |

***

## ⚠️ 8. Risks and Dependencies

| Type | Description | Mitigation |
|:---|:---|:---|
| **Technical (BE)** | Insufficiente scalabilità iniziale del database relazionale (PostgreSQL) sotto carico elevato. | Stress test anticipati; Ottimizzazione delle query; Valutazione di sharding/replicazione. |
| **Security (BE)** | Vulnerabilità nella gestione dei permessi per le bacheche condivise (autorizzazione). | Code review approfondita del middleware di autorizzazione (Role-Based Access Control). |
| **Technical (FE)** | Difficoltà nell'implementare Drag & Drop fluida e cross-browser. | Utilizzo di librerie Drag & Drop mature (es. `react-beautiful-dnd` o equivalenti). |
| **Dependency** | Documentazione OpenAPI non aggiornata con le modifiche al codice. | Implementazione di uno strumento di generazione automatica della documentazione dalle annotazioni del codice. |

***

## ✅ 9. Approvals

| Name | Role |
|:---|:---|
| Luca Sacchi Ricciardi | CEO |
| Matteo Fiorio | Team Leader |
| Tommaso Villa | Member |
| Samuele Piazzi | Member |
| Filippo Granata | Member |
| Samuele Gonnella | Member |
| Corinna Buzzi | Member |
| Marialia Pero | Member |
| Alessandro Cervini | Member |
| Gloria Candela | Member |