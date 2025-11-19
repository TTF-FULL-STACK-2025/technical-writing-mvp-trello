# 🧭 Product Requirements Document (PRD) – Backend Trello-like System

**Titolo del progetto:** Backend Core per Piattaforma di Gestione Progetti (Trello-like) <br>
**Versione:** 1.0<br>
**Data:** 19/11/2025<br>
**Autore:** Team Work<br>
**Approvato da:** Luca Sacchi Ricciardi

---

## 🧱 1. Overview

### 1.1 Descrizione generale

Questo documento descrive i requisiti per lo sviluppo di un **sistema backend-only** scalabile, sicuro e performante, che emuli le funzionalità di base di una piattaforma di gestione progetti in stile Trello. Il sistema fornirà le API RESTful necessarie per gestire utenti, board, liste, card (task) e la collaborazione, escludendo qualsiasi sviluppo di interfaccia utente.

### 1.2 Contesto / Motivazione

* **Problema Utente:** Le organizzazioni e i team necessitano di una piattaforma flessibile e centralizzata per tracciare il lavoro, gestire i flussi e facilitare la collaborazione sui task.
* **Obiettivi Aziendali:** Creare un solido backend come base per future applicazioni (web/mobile), garantendo **scalabilità** e **performance** elevate fin dall'inizio per supportare una rapida crescita.
* **Insight:** L'architettura deve essere **API-first** per consentire integrazioni future e uno sviluppo frontend disaccoppiato.

### 1.3 Obiettivi principali

* **Sviluppare un backend** scalabile, sicuro e ad alte prestazioni (API call $<300$ms).
* **Supportare tutte le funzionalità core** di un sistema di gestione task (CRUD per Board, Liste, Card, Utenti).
* **Fornire API RESTful complete** e ben documentate (OpenAPI 3.x).
* **Abilitare la gestione di utenti, autenticazione e autorizzazione** per la collaborazione.

### 1.4 Non obiettivi (Out of Scope)

* Interfaccia Utente / Frontend di qualsiasi tipo.
* Funzionalità avanzate tipo Power-Ups, automazioni complesse o template di board.
* Integrazioni con sistemi esterni (Slack, GitHub, ecc.).
* Gestione fisica di file per gli allegati (solo metadata).

---

## 🎯 2. Target & Utente

### 2.1 Personas principali

* **Marco (Developer Backend):** Ruolo: Implementatore del sistema. Bisogno: API chiare, stabili e ben documentate per la facile integrazione con il frontend. Pain Point: Documentazione API obsoleta o incompleta.
* **Laura (Project Manager):** Ruolo: Utilizzatore finale (tramite futuro frontend). Bisogno: Gestire i flussi di lavoro, assegnare task e tracciare lo stato di avanzamento. Obiettivo: Avere uno strumento affidabile per la pianificazione e l'esecuzione.
* **Giovanni (Amministratore di Sistema):** Ruolo: Garante dell'infrastruttura. Bisogno: Sistema scalabile, monitorabile e con un'architettura manutenibile.

### 2.2 Use cases / Scenari

* **Scenario 1: Setup Iniziale del Team:** Un nuovo utente si registra, crea una nuova Board, e aggiunge i membri del suo team assegnando loro il ruolo di `Editor`.
* **Scenario 2: Gestione del Task:** Un membro del team crea una Card, la sposta tra Liste, aggiunge una `Due Date` e assegna un `Label`. Un altro membro aggiunge un `Commento`.
* **Scenario 3: Audit:** L'Owner della Board verifica l'attività recente sulla Board attraverso il log degli eventi.

---

## 🧩 3. Requisiti di prodotto

### 3.1 Functional Requirements (Funzionali)

| ID | Nome | Descrizione | Priorità (MoSCoW) | Note |
|:---|:---|:---|:---|:---|
| FR1.1 | Registrazione Utente | L'utente deve potersi registrarsi con email e password. | Must | |
| FR1.2 | Login/Logout | Gestione sessione e autenticazione tramite credenziali. | Must | Uso di JWT. |
| FR1.3 | Gestione Board | CRUD completo per le Board (Creazione, Modifica, Archiviazione/Eliminazione). | Must | |
| FR1.4 | Gestione Liste | CRUD e riordinamento delle Liste all'interno di una Board. | Must | |
| FR1.5 | Gestione Card | CRUD e modifica di titolo, descrizione, assegnatari, due date, label. | Must | |
| FR1.6 | Spostamento Card | Possibilità di riordinare le Card e spostarle tra Liste diverse. | Must | |
| FR1.7 | Commenti | Aggiunta ed eliminazione di commenti sulle Card. | Should | |
| FR1.8 | Membri Board | Aggiunta, rimozione e gestione dei ruoli (`Owner`, `Editor`, `Viewer`). | Must | |
| FR1.9 | Log Attività | Tracciamento delle modifiche (CRUD) su Board, Liste e Card. | Must | |
| FR1.10 | Notifiche Base | Notifiche per assegnazione Card. | Should | |

### 3.2 Non-Functional Requirements (Non Funzionali)

| Tipo | Descrizione | Misura/Esempio |
|:---|:---|:---|
| **Performance** | Bassa latenza delle risposte API. | Tempo di risposta medio $<300$ms. |
| **Scalabilità** | Capacità di gestire un aumento del carico di lavoro. | Architettura che supporti la scalabilità orizzontale. |
| **Sicurezza** | Protezione dei dati e delle comunicazioni. | Autenticazione JWT, crittografia password, rate limiting. |
| **Affidabilità** | Disponibilità e continuità del servizio. | $99.5\%$ di uptime. Implementazione di una strategia di backup. |
| **Documentazione** | API chiare e auto-documentate. | Uso di Swagger/OpenAPI 3.x per la documentazione automatica. |

---

## 🧠 4. User Experience / Design

### 4.1 Flusso utente (User Flow)

1.  **Registrazione:** Utente $\rightarrow$ `POST /register` $\rightarrow$ Riceve token JWT.
2.  **Board Setup:** Utente $\rightarrow$ `POST /boards` $\rightarrow$ `POST /boards/{id}/members` (aggiunta membri) $\rightarrow$ `POST /boards/{id}/lists`.
3.  **Task Management:** Utente $\rightarrow$ `POST /lists/{id}/cards` $\rightarrow$ `PATCH /cards/{id}` (spostamento/modifica) $\rightarrow$ `POST /cards/{id}/comments`.

### 4.2 Wireframe / Mockup

> *Non applicabile per un progetto strettamente Backend-only.*

### 4.3 Copy & Tone of Voice

> *Tutti i messaggi di errore (status code HTTP) e di validazione devono essere chiari e standardizzati.*

---

## ⚙️ 5. Architettura tecnica / API

### 5.1 Dipendenze tecniche

* **Backend:** Node.js/Express o Python/FastAPI (scelta raccomandata).
* **Database:** PostgreSQL (preferito) o MongoDB.
* **Autenticazione:** OAuth2/JWT Bearer Token.
* **Containerizzazione:** Docker.

### 5.2 Endpoint previsti (se rilevante)

| Endpoint | Metodo | Descrizione | Input | Output |
|:---|:---|:---|:---|:---|
| `/v1/auth/login` | POST | Autenticazione utente | email, password | JWT Token |
| `/v1/boards` | POST | Crea una nuova Board | nome, descrizione | ID Board |
| `/v1/boards/{id}/lists` | POST | Aggiunge una Lista alla Board | nome | ID Lista |
| `/v1/cards` | POST | Crea una Card in una Lista | titolo, descrizione, ID Lista | ID Card |
| `/v1/cards/{id}/move` | PATCH | Sposta una Card | ID nuova Lista, posizione | Status 200 |

### 5.3 Considerazioni DevOps / Scalabilità

* **Scalabilità:** Design dell'applicazione e del database per supportare la scalabilità orizzontale.
* **CI/CD:** Pipeline automatizzata per test e deploy (tramite Docker).
* **Logging:** Implementazione di un sistema di logging centralizzato (es. ELK Stack) per il monitoraggio.

---

## 🧪 6. Metriche e Success Criteria

### 6.1 KPI principali

* **Board Creation Success Rate:** Tasso di successo nella creazione di Board $\rightarrow$ $100\%$ (zero errori critici).
* **Average API Response Time (AART):** Tempo medio di risposta delle API $\rightarrow <300$ms.
* **Critical Vulnerability Count:** Numero di vulnerabilità critiche rilevate $\rightarrow$ Zero.

### 6.2 Metriche secondarie

* **Error Rate:** Percentuale di richieste API che ritornano status code $5xx \rightarrow <0.1\%$.
* **Test Coverage:** Copertura dei test unitari e di integrazione $\rightarrow >80\%$.

---

## 📅 7. Pianificazione e Scadenze

| Fase | Attività | Responsabile | Data inizio | Data fine |
|:---|:---|:---|:---|:---|
| **Fase 1** | Setup Architettura, Autenticazione (JWT, Auth) | Dev team | [Da definire] | 2 Settimane |
| **Fase 2** | Gestione Board e Liste (CRUD, Membri, Ruoli) | Dev team | [Dopo Fase 1] | 3 Settimane |
| **Fase 3** | Gestione Card e Commenti (CRUD, Spostamento, Due Date, Label) | Dev team | [Dopo Fase 2] | 3 Settimane |
| **Fase 4** | Activity Log, Notifiche Base, Upload Metadata | Dev team | [Dopo Fase 3] | 2 Settimane |
| **Fase 5** | Hardening Sicurezza, Testing Finale, Documentazione, Deployment | Dev team | [Dopo Fase 4] | 1 Settimana |

---

## ⚠️ 8. Rischi e Dipendenze

| Tipo | Descrizione | Mitigazione |
|:---|:---|:---|
| **Tecnico** | Insufficiente scalabilità iniziale del database relazionale (PostgreSQL) in caso di forte carico. | Stress test anticipati; Ottimizzazione delle query. |
| **Sicurezza** | Vulnerabilità nella gestione dei permessi per le Board condivise (autorizzazione). | Revisione del codice approfondita sul middleware di autorizzazione (Role-Based Access Control). |
| **Dipendenza** | La documentazione OpenAPI non viene aggiornata con le modifiche. | Implementazione di uno strumento di generazione automatica della documentazione. |

---

## ✅ 9. Approvazioni

| Nome | Ruolo | Firma | Data |
|:---|:---|:---|:---|
| | Product Manager | | |
| | Tech Lead | | |