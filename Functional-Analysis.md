# 💻 Functional Analysis: Full-Stack Project Management Platform (Trello-clone)

**Project Title:** Project Management Platform (Trello-clone) <br>
**Version:** 1.2 (Alignment with PHP/MySQL Stack) <br>
**Date:** December 3, 2025 <br>
**Author:** Team Work <br>
**Approved by:** Luca Sacchi Ricciardi <br>

-----

## 🧭 1. Introduction

### 1.1 Document Purpose

This document defines the detailed functional analysis for the development of the Full-Stack Project Management Platform, specifying the expected behavior of the hybrid system (SSR PHP + AJAX Vanilla JS) in response to the PRD requirements.

### 1.2 Context

The project aims to create a Kanban-based project management platform, implemented on an existing **PHP/MySQL** stack.

  * **Request Origin:** Development of a complete and scalable product for team task management.
  * **References:** PRD v1.2 – Alignment with PHP/MySQL Stack, Section 3 (Functional Requirements) and Section 5 (Technical Architecture).

### 1.3 Main Objectives

  * Implement a robust **Authentication (PHP Sessions) and Authorization (RBAC)** system at the Board level (FR1.1, FR1.2, FR1.8).
  * Ensure **complete CRUD functionalities** for Board and List (FR1.3, FR1.4).
  * Guarantee smooth and responsive **Drag & Drop** for Card movement, with persistence via AJAX (FR1.6, FR2.3).
  * Develop an **Activity Log** to track critical changes (FR1.9).

-----

## 🧩 2. General System Description

### 2.1 Application Context

The platform is a full-stack web application with a hybrid approach:

  * **SSR (Server-Side Rendering) PHP:** Used for the initial page load and static rendering (e.g., `index.php`).
  * **AJAX (Vanilla JavaScript):** Used for all dynamic interactions (e.g., Card creation, detail updates, Drag & Drop).
  * **Interfaces with other modules:** The application is standalone but interfaces with the **MySQL Database** via PDO and the **PHP Session System** for authentication.

### 2.2 Involved Actors

| Attore | Descrizione | Permessi / Ruolo |
|:---|:---|:---|
| Unregistered User | Person attempting to access the system | Registration (FR1.1), Login (FR1.2) |
| Logged-in User | Person authenticated in the system | Access to Dashboard (FR2.1) |
| Owner (Board) | Creator or main manager of the Board | Complete control over Board, members, roles, content. |
| Editor (Board) | Active member of the Board | CRUD on Lists/Cards, Card Movement (FR1.4, FR1.5, FR1.6). |
| Viewer (Board) | Member with read-only access | Viewing Board and Cards (FR2.2). |

### 2.3 General Logical Flow (Flusso logico generale)

1.  **Access:** User (New/Existing) $\rightarrow$ **Registration/Login** $\rightarrow$ Session created.
2.  **Dashboard:** User $\rightarrow$ **View accessible Boards** (FR2.1).
3.  **Task Management:** User (Editor) $\rightarrow$ View Board (FR2.2) $\rightarrow$ **Create/Modify/Move Card** (FR1.5, FR1.6, FR2.3).
4.  **Backend:** Each dynamic action (e.g., movement) $\rightarrow$ **AJAX POST** to PHP Endpoint (`update_card_position.php`) $\rightarrow$ Backend **Authorization (RBAC)** $\rightarrow$ **MySQL DB** (update) $\rightarrow$ Write **Activity Log** (FR1.9) $\rightarrow$ Response Status 200.

-----

## 🧠 3. Detailed Functional Requirements (Requisiti funzionali dettagliati)

The model supports the hierarchy: `Board` $\rightarrow$ `List` $\rightarrow$ `Card`.

### 3.1 CRUD Operations (Existing Implementation)

| Entity | PHP Endpoint | Technical Notes |
|:---|:---|:---|
| **Board** | `add_board.php` | Root entity |
| **List** | `index.php` | Completed |
| **Card** | `add_card.php` / `delete_card.php` / `update_card_details.php` / `get_card_details.php` | Complete CRUD for title and description implemented. |
| **Card Move** | `update_card_position.php` | Handles the modification of `list_id` and `position` in a single operation. |

### 3.2 Consistency Requirements

  * **Positioning:** Cards use a `position` field (integer/float) for ordering. The `update_card_position.php` endpoint manages saving the new position and list.
  * **Soft Delete:** Archiving is not implemented. The `delete_card.php` endpoint performs a **physical deletion** (`DELETE FROM cards`).

-----

## ⚙️ 4. Non-Functional Requirements (Requisiti non funzionali)

| Tipo | Descrizione | Valore / Target | Note |
|:---|:---|:---|:---|
| **Performance (BE)** | Latency of AJAX calls for dynamic interactions. | $<300$ms (AART) | Essential for responsive D\&D. |
| **Security (BE)** | Protection against SQL Injection and XSS attacks. | Use of PDO Prepared Statements. Bcrypt hashing for passwords. | Implementation of strict RBAC and Rate Limiting. |
| **Usability (FE)** | Speed of initial setup completion. | 90% of users complete Registration + Board + Card in $<60$ seconds. | Measures user flow effectiveness. |
| **Performance (FE)** | Interface loading speed. | First Contentful Paint (FCP) $<1.5$s. | Favored by the SSR/Vanilla JS approach. |
| **Documentation (BE)**| No OpenAPI, need for manual documentation. | Clear documentation for all endpoints in Section 5.2. | Risk: dependence on internal knowledge (Risk 8.1). |

-----

## 🧾 5. Detailed Flows (Use Case / Diagrams) (Flussi dettagliati)

### 5.1 Use Case 1 – Card Movement (Drag & Drop)

**Actors:** Editor User (Authenticated)
**Description:** A user moves a Card from one List to another and updates the DB.

**Main Flow (Flusso principale):**

1.  The user views the Board (`index.php`).
2.  The user starts the **Drag** of the Card (Vanilla JS: `handleDragStart`).
3.  The user drops the Card onto a new List/position (Vanilla JS: `handleDrop`).
4.  **FE Call:** JavaScript sends an **AJAX POST** request to `/update_card_position.php`.
5.  **BE Process (BE Processo):**
      * Verification of Authentication (PHP Session).
      * Verification of Authorization (RBAC): The user has the `Editor` or `Owner` role on the Board.
      * Executes DB Update: `UPDATE cards SET list_id = :newListId, position = :newPosition WHERE card_id = :cardId`.
      * Records the action in the Activity Log (FR1.9).
6.  **Response (Risposta):** The Backend returns **Status 200**.
7.  **FE Update:** No DOM update necessary, the element has already been moved client-side.

**Alternative Flow (Authorization Error) (Flusso alternativo - Errore di Autorizzazione):**

  * If the user is a `Viewer` or is not a member $\rightarrow$ BE returns **Status 403 Forbidden**. The Card is not moved in the DB, and the FE should revert the Card to its previous position (Undo DOM move).

**Related Requirements (Requisiti collegati):** FR1.6, FR2.3, FR1.8, FR1.9, Performance (BE).

### 5.2 Use Case 2 – Access and Authorization (RBAC Implementation)

**Actors:** Logged-in User
**Description:** After Login, the user attempts to perform an action that requires a certain Role.

**Main Flow (Flusso principale):**

1.  The user Logs in (FR1.2), obtaining a PHP Session.
2.  The user attempts a protected action (e.g., `/delete_card.php`).
3.  **BE Middleware:** The PHP script intercepts the request.
4.  Checks the PHP Session for the User ID.
5.  DB Query: `SELECT role FROM board_members WHERE user_id = :userId AND board_id = :boardId`.
6.  **Verification (Verifica):** If the `role` is `Owner` or `Editor` $\rightarrow$ Proceeds to the action.
7.  Otherwise $\rightarrow$ Returns **Status 403 Forbidden** and terminates.

**Related Requirements (Requisiti collegati):** FR1.2, FR1.8, Security (BE).

-----

## 🧩 6. User Interface (Interfaccia utente)

### 6.1 Screen Descriptions (Descrizione schermate)

| ID | Nome schermata | Descrizione | Elementi principali |
|:---|:---|:---|:---|
| UI1 | Login/Registration | Pages for system access. | Email/Password Fields, Login/Registration Buttons. |
| UI2 | Dashboard (FR2.1) | List of all Boards accessible to the user. | Board List, Filters/Search, "Create New Board" Button. |
| UI3 | Kanban View (FR2.2) | Main view of a Board. | Horizontally scrollable container, Lists (columns), Cards (Draggable elements). |
| UI4 | Card Details Modal (FR2.4) | Modal for details and interactions with a single Card. | Title, Description, Comments Section (FR1.7), Assignees/Labels (future). |
| UI5 | Activity Log (FR1.9) | Sidebar or Modal to view the action history. | Chronological list of events (Text: "User X moved Card Y"). |

### 6.2 Mockup / Wireframe

*Upon completion of this phase, refer to **Phase 0 (Design)** of the development plan for the production of a complete set of Wireframes and Mockups.*

-----

## 🔗 7. Integrations and Dependencies (Integrazioni e dipendenze)

| Servizio / Modulo | Tipo integrazione | Protocollo / API | Note |
|:---|:---|:---|:---|
| **MySQL Database** | Data Persistence | PDO (Prepared Statements) | Connection managed via `config.php`. |
| **PHP Sessions** | Authentication | Cookie / Server-Side State | Basic implementation in Phase 1. |
| **Frontend Renderer** | Initial Rendering | Server-Side Rendering (PHP) | Basic structure loading. |
| **Dynamic Interaction** | D\&D, Modals, CRUD | AJAX / Fetch API (Vanilla JS) | Direct communication with PHP endpoints. |

-----

## 🧰 8. Business Rules (Regole di business)

| ID | Nome | Descrizione |
|:---|:---|:---|
| **RB1** | Board Roles | Roles (`Owner`, `Editor`, `Viewer`) define access permissions. A User must have a role to interact with a Board (FR1.8). |
| **RB2** | Card Positioning | The Card `position` is saved as a float/integer to facilitate reordering without massive recalculations (FR1.6). |
| **RB3** | Password Hashing | All passwords must be stored with secure hashing (Bcrypt) (Security NFR). |
| **RB4** | Card Deletion | Deletion via `/delete_card.php` is a **physical deletion** from the DB (3.2 Consistency). |

-----

## 🧪 9. Functional Test Cases (UAT / QA) (Casi di test funzionali)

| ID | Caso di test | Step | Risultato atteso |
|:---|:---|:---|:---|
| **TC1** | Login and Access | Enter valid credentials, Login. | Access granted, Session created, Redirect to Dashboard (UI2). |
| **TC2** | Card Creation | From UI3, click "Add Card" in List A, enter Title, Submit. | Card appears in List A's DOM, `/add_card.php` returns Status 200, Activity Log (FR1.9) recorded. |
| **TC3** | Inter-List Drag & Drop | Drag Card from List A to List B. | Card moves in DOM, `/update_card_position.php` sent with `newListId=B`, DB updated (AART $<300$ms). |
| **TC4** | Authorization Denied | `Viewer` User attempts to delete a Card (`/delete_card.php`). | BE returns Status 403 Forbidden. Card not deleted, error shown in FE (if applicable). |
| **TC5** | I18n switch | (If selector is implemented) Select alternate language (e.g., English). | Interface texts update according to the global object `TRANSLATIONS`. |

-----

## 📅 10. Planning / Release (Pianificazione / Release)

| Milestone | Descrizione | Data prevista | Responsabile |
|:---|:---|:---|:---|
| AF completed | Review and approval | 10/12/2025 | PM |
| **Phase 1** (BE) | Setup Auth (Sessions, Hashing) | 2 weeks | Dev Team |
| **Phase 2** (BE) | Board CRUD, List, RBAC Middleware | 3 weeks | Dev Team |
| **Phase 3** (BE) | Card CRUD/Move, Comments | 3 weeks | Dev Team |
| **Phase 4** (FE) | Frontend Core + Drag & Drop | 4 weeks | Dev Team |
| **Phase 5** (FE+BE) | Integration, Activity Log, Notifications | 3 weeks | Dev Team |
| **Phase 6** | Security, Testing, Deploy | 1 week | Dev Team |

-----

## ⚠️ 11. Risks and Operational Notes (Rischi e note operative)

| Tipo | Descrizione | Mitigazione |
|:---|:---|:---|
| **Technical (FE)** | "Janky" Drag & Drop with Vanilla JS cross-browser. | **Intensive Cross-Browser Testing** (Chrome, Firefox, Safari). Consider lightweight libraries if custom logic fails. |
| **Security (BE)** | Vulnerabilities in permissions (RBAC) for CRUD actions. | Deep code review on the authorization middleware in Phase 2. |
| **Scalability (BE)** | Pressure on DB and PHP Session system. | Implementation of caching (e.g., Redis) in Phase 5; Query Optimization on `list_id` and `position`. |
| **Organizational** | Lack of OpenAPI documentation. | Maintaining rigorous and updated manual documentation during Phases 2 and 3. |

-----

## ✅ 12. Final Approvals (Approvazioni Finali)

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
| Nicole Caravello | Member |