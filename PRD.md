# 🧭 Product Requirements Document (PRD) Full-Stack Project Management Platform (Trello-clone)

**Project Title:** Project Management Platform (Trello-clone) <br>
**Version:** 1.2 (Alignment with PHP/MySQL Stack) <br>
**Date:** December 3, 2025 <br>
**Author:** Team Work <br>
**Approved by:** Luca Sacchi Ricciardi <br>

***

## 🧱 1. Overview

### 1.1 General Description

This document describes the requirements for the development of a scalable, secure, and performant **full-stack system** that emulates the core functionalities of a Trello-style project management platform. The system is based on **PHP and MySQL** for the backend and **Vanilla JavaScript** for the frontend, adopting a **hybrid Server-side Rendering (PHP) with AJAX (JavaScript)** approach for dynamic interactions.

### 1.2 Context / Motivation

* **User Problem:** Organizations and teams require a flexible and centralized platform to track work, manage workflows, and facilitate collaboration on tasks.
* **Business Objectives:** Create a complete application, ensuring high **reliability** and **performance** from the outset and providing an intuitive and smooth user experience.
* **Insight:** The current architecture uses a **hybrid Server-side Rendering (PHP) with AJAX (JavaScript)** approach for dynamic interactions (Drag & Drop, Modals).

### 1.3 Key Objectives

* **Develop a robust and maintainable backend using PHP/MySQL.**
* **Develop an intuitive User Interface (Frontend)** that implements the Kanban paradigm.
* **Support all core Card functionalities** (CRUD and Movement) leveraging existing PHP scripts.
* **Implement language management (i18n)** for the user interface.
* **Enable user management, authentication, and authorization** for collaboration (to be developed).

### 1.4 Non-Objectives (Out of Scope)

* Advanced features such as Power-Ups, complex automations, or board templates.
* Integrations with external systems (Slack, GitHub, etc.).
* Physical file handling for attachments (metadata only).

***

## 🎯 2. Target & User

### 2.1 Use Cases / Scenarios

* **Scenario 1: Initial Team Setup:** A new user registers, creates a new Board, and adds their team members, assigning them the `Editor` role.
* **Scenario 2: Task Management:** A team member creates a Card, moves it between Lists, edits the **title and description** (`update_card_details.php`), and assigns a `Label`. Another member adds a `Comment`.
* **Scenario 3: Audit:** The Board owner checks recent activities on the Board via the event log displayed in the interface.

***

## 🧩 3. Product Requirements

### 3.1 Functional Requirements

| ID | Name | Description | Priority (MoSCoW) | Notes |
|:---|:---|:---|:---|:---|
| **FR1.1** | User Registration | The user must be able to register with email and password. | Must | To be developed (Not present in the current stack) |
| **FR1.2** | Login/Logout | Session management and authentication using credentials. | Must | To be developed (Not present in the current stack) |
| **FR1.3** | Board Management | Complete CRUD for Boards (Creation, Modification, Deletion). | Must | Developed Completaly |
| **FR1.4** | List Management | CRUD and reordering of Lists within a Board. | Must | To be developed |
| **FR1.5** | Card Management | CRUD and modification of **title and description** (`add_card.php`, `update_card_details.php`, `delete_card.php`, `get_card_details.php`). | Must | Support for assignees, due date, and labels is to be developed. |
| **FR1.6** | Card Movement | Ability to reorder Cards and move them between different Lists (`update_card_position.php`). | Must | Functionality supported by the Backend and implemented via Drag & Drop in the Frontend (`index.php`). |
| **FR1.7** | Comments | Addition and deletion of comments on Cards. | Should | To be developed (Not present in the current stack) |
| **FR1.8** | Board Members | Adding, removing, and managing roles (`Owner`, `Editor`, `Viewer`). | Must | Developed |
| **FR1.9** | Activity Log | Tracking of changes (CRUD) on Boards, Lists, and Cards. | Must | To be developed (Not present in the current stack) |
| **FR1.10** | Basic Notifications | Notifications for card assignment. | Should | To be developed (Not present in the current stack) |
| **FR1.11** | Multilingual (i18n) | The frontend must support multiple languages. | Must | Initial i18n handling present in the `index.php` file. |
| **FR2.1** | **UI: Dashboard** | Display of boards accessible to the user with filters and search. | Must | Currently only implements the display of the single board (`index.php`). |
| **FR2.2** | **UI: Kanban View** | Display of the board with lists organized in columns and cards within them. | Must | Basic Kanban layout present (`index.php`). |
| **FR2.3** | **UI: Drag & Drop Task** | Ability to move cards between different lists and reorder them within a list via Drag & Drop. | Must | Smooth and reactive interaction (`index.php`). |
| **FR2.4** | **UI: Card Details** | Modal to view and modify all card details (title and description). | Must | Interaction via `get_card_details.php` and `update_card_details.php`. |

***

### 3.2 Non-Functional Requirements

| Type | Description | Measure/Example |
|:---|:---|:---|
| **Performance (BE)** | Low latency of API (AJAX) responses. | Average response time <300ms. |
| **Scalability (BE)** | Ability to handle increasing workloads. | Architecture supporting vertical scalability (MySQL/PHP optimization). |
| **Security (BE)** | Data and communication protection. | **Use of PDO to prevent SQL Injection** (present in files). Implementation of authentication and rate limiting required. |
| **Reliability (BE)** | Service availability and continuity. | 99.5% uptime. Implementation of a backup strategy. |
| **Usability (FE)** | Intuitive and responsive interface. | 90% of new users complete the 'Registration + Board Creation + Card Creation' flow in <60 seconds. |
| **Responsiveness (FE)** | The application must work correctly on all major devices. | Full support for screens from 375px (mobile) upwards. |
| **Performance (FE)** | Fast interface loading. | First Contentful Paint (FCP) <1.5s. |
| **Documentation** | Clear and self-documented API. | The PHP/MySQL code is **lacking OpenAPI documentation**. Manual documentation is required. |

***

## 🧠 4. User Experience / Design

### 4.1 User Flow

1.  **Registration:** User $\rightarrow$ `POST /register` $\rightarrow$ Receives session/token. (To be developed)
2.  **Login:** User $\rightarrow$ `POST /login` $\rightarrow$ Receives session/token. (To be developed)
3.  **Board Setup:** User $\rightarrow$ **Kanban UI** $\rightarrow$ `POST /boards` $\rightarrow$ `POST /boards/{id}/members` (adds members) $\rightarrow$ **Board View UI** $\rightarrow$ `POST /boards/{id}/lists`.
4.  **Task Management:** User $\rightarrow$ **Drag & Drop UI** $\rightarrow$ `POST /add_card.php` $\rightarrow$ `POST /update_card_position.php` (moves) $\rightarrow$ **Card Detail UI** $\rightarrow$ `POST /update_card_details.php`.

### 4.2 Wireframes / Mockups

> *A complete set of **Wireframes and Mockups** will be needed to define the user experience for boards, cards, and the dashboard, to be produced before the start of Frontend development (See Planning Phase 0).*

### 4.3 Copy & Tone of Voice

> *All error messages and validation messages are partially handled in PHP and translated via the inclusion of language files in the frontend (i18n). Database error messages should not be exposed to the end-user.*

***

## ⚙️ 5. Technical Architecture / API

### 5.1 Technical Dependencies (Updated)

* **Backend:** **PHP** (Existing stack).
* **Database:** **MySQL** (Assumed via `config.php`).
* **Database Driver:** **PDO** (Used for secure queries).
* **Authentication:** Based on **PHP Sessions** (implementation required) or Tokens (not implemented).
* **Containerization:** Docker (maintained for the development/production environment).
* **Frontend Framework:** **Vanilla JavaScript** and Server-Side Rendering (SSR) PHP.
* **Frontend Styling:** Custom CSS.

### 5.2 Planned Endpoints (Backend) (Updated to actual files)

| Endpoint | Method | Description | Input | Output |
|:---|:---|:---|:---|:---|
| `/auth/login` | POST | User Authentication | email, password | Session/Token (To be developed) |
| **`/get_card_details.php`** | GET | Gets a Card's details | cardId | Card Object |
| **`/add_card.php`** | POST | Creates a Card in a List | listId, title, description | Card ID |
| **`/update_card_details.php`** | POST | Modifies a Card's title and description | cardId, title, description | Status 200 |
| **`/update_card_position.php`** | POST | Moves/Reorders a Card | cardId, newListId, newPosition | Status 200 |
| **`/delete_card.php`** | POST | Deletes a Card | cardId | Status 200 |
| `/v1/boards` | POST | Creates a new Board | name, description | Board ID (To be developed) |

### 5.3 DevOps / Scalability Considerations

* **Scalability:** Application and database design to support vertical scalability (query optimization).
* **CI/CD:** Automated pipeline for Backend and Frontend testing and deployment (via Docker).
* **Logging:** Implementation of a centralized logging system (e.g., ELK Stack) for monitoring.

***

## 🧪 6. Metrics and Success Criteria

### 6.1 Key KPIs

* **Board Creation Success Rate:** Success rate for Board creation $\rightarrow$ 100% (zero critical errors).
* **Average AJAX Response Time (AART):** Average response time for AJAX calls $\rightarrow$ <300ms.
* **Loading Time (FE):** First Contentful Paint (FCP) time $\rightarrow$ <1.5s.
* **Critical Vulnerability Count:** Number of critical vulnerabilities detected in security tests $\rightarrow$ Zero.

### 6.2 Secondary Metrics

* **Error Rate:** Percentage of API/AJAX requests that return 5xx status codes $\rightarrow$ <0.1%.
* **Test Coverage:** Coverage of unit and integration tests $\rightarrow$ **To be introduced for the PHP/JS stack.**

***

## 📅 7. Planning and Deadlines

| Phase | Activity | Responsible | Start Date | End Date |
|:---|:---|:---|:---|:---|
| **Phase 0 (Design)** | **UI/UX Design (Wireframes & Mockups, Design System)** | Design Team | [To be defined] | 1 Week |
| **Phase 1 (BE)** | Architecture Setup, **Authentication (PHP Sessions, Auth) and Authorization** | Dev Team | [After Phase 0] | 2 Weeks |
| **Phase 2 (BE)** | Board and List Management (CRUD, Members, Roles) | Dev Team | [After Phase 1] | 3 Weeks |
| **Phase 3 (BE)** | Card and Comment Management (CRUD, Move, Due Date, Label) | Dev Team | [After Phase 2] | 3 Weeks |
| **Phase 4 (FE)** | **Frontend Core Development (Setup, SSR, Dynamic AJAX, i18n)** | Dev Team | [Concurrent w/ Phase 2] | 4 Weeks |
| **Phase 5 (FE+BE)** | **Full Integration, UI refinement, Activity Log Visualization, Notifications** | Dev Team | [After Phase 3 & 4] | 3 Weeks |
| **Phase 6** | Security Hardening, Final Testing, Documentation, Deployment | Dev Team | [After Phase 5] | 1 Week |

***

## ⚠️ 8. Risks and Dependencies

| Type | Description | Mitigation |
|:---|:---|:---|
| **Technical (BE)** | Insufficient initial scalability of the relational database (MySQL) under high load. | Early stress testing; Query optimization; Caching evaluation. |
| **Security (BE)** | Vulnerabilities in permission management for shared boards (authorization). | In-depth code review of the authorization middleware (Role-Based Access Control). |
| **Technical (FE)** | Difficulty in implementing smooth and cross-browser Drag & Drop. | The use of Vanilla JavaScript and the current logic are **sensitive to cross-browser differences**. Maintain rigorous testing. |
| **Dependency** | Absence of an automatic documentation system for the PHP stack. | Maintain rigorous manual documentation for AJAX endpoints. |

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