# 🧭 Product Requirements Document (PRD) – Trello-like Backend System

**Project Title:** Core Backend for Project Management Platform (Trello-like)
**Version:** 1.0
**Date:** November 19, 2025
**Author:** AI Assistant
**Approved by:** [To be defined]

---

## 🧱 1. Overview

### 1.1 General Description

This document describes the requirements for developing a scalable, secure, and high-performing **backend-only system** that emulates the core functionalities of a Trello-style project management platform. The system will provide the necessary RESTful APIs to manage users, boards, lists, cards (tasks), and collaboration, excluding any user interface development.

### 1.2 Context / Motivation

* **User Problem:** Organizations and teams need a flexible and centralized platform to track work, manage workflows, and facilitate task collaboration.
* **Business Objectives:** To create a solid backend foundation for future applications (web/mobile), ensuring high **scalability** and **performance** from the start to support rapid growth.
* **Insight:** The architecture must be **API-first** to allow for future integrations and decoupled frontend development.

### 1.3 Key Objectives

* **Develop a scalable, secure, and high-performing backend** (API call $<300$ms).
* **Support all core features** of a task management system (CRUD for Boards, Lists, Cards, Users).
* **Provide complete** and well-documented **RESTful APIs** (OpenAPI 3.x).
* **Enable user management, authentication, and authorization** for collaboration.

### 1.4 Non-Objectives (Out of Scope)

* User Interface / Frontend of any kind.
* Advanced features such as Power-Ups, complex automations, or board templates.
* Integrations with external systems (Slack, GitHub, etc.).
* Physical file management for attachments (metadata only).

---

## 🎯 2. Target & User

### 2.1 Key Personas

* **Marco (Backend Developer):** Role: System implementer. Need: Clear, stable, and well-documented APIs for easy frontend integration. Pain Point: Outdated or incomplete API documentation.
* **Laura (Project Manager):** Role: End user (via future frontend). Need: Manage workflows, assign tasks, and track progress. Goal: Have a reliable tool for planning and execution.
* **Giovanni (System Administrator):** Role: Infrastructure guarantor. Need: Scalable, monitorable system with a maintainable architecture.

### 2.2 Use Cases / Scenarios

* **Scenario 1: Initial Team Setup:** A new user registers, creates a new Board, and adds their team members, assigning them the `Editor` role.
* **Scenario 2: Task Management:** A team member creates a Card, moves it between Lists, adds a `Due Date`, and assigns a `Label`. Another member adds a `Comment`.
* **Scenario 3: Audit:** The Board Owner checks recent activity on the Board via the event log.

---

## 🧩 3. Product Requirements

### 3.1 Functional Requirements

| ID | Name | Description | Priority (MoSCoW) | Notes |
|:---|:---|:---|:---|:---|
| FR1.1 | User Registration | User must be able to register with email and password. | Must | |
| FR1.2 | Login/Logout | Session management and authentication via credentials. | Must | Use of JWT. |
| FR1.3 | Board Management | Complete CRUD for Boards (Create, Edit, Archive/Delete). | Must | |
| FR1.4 | List Management | CRUD and reordering of Lists within a Board. | Must | |
| FR1.5 | Card Management | CRUD and editing of title, description, assignees, due date, labels. | Must | |
| FR1.6 | Card Movement | Ability to reorder Cards and move them between different Lists. | Must | |
| FR1.7 | Comments | Adding and deleting comments on Cards. | Should | |
| FR1.8 | Board Members | Adding, removing, and managing roles (`Owner`, `Editor`, `Viewer`). | Must | |
| FR1.9 | Activity Log | Tracking modifications (CRUD) on Boards, Lists, and Cards. | Must | |
| FR1.10 | Basic Notifications | Notifications for card assignment. | Should | |

### 3.2 Non-Functional Requirements

| Type | Description | Measure/Example |
|:---|:---|:---|
| **Performance** | Low API response latency. | Average response time $<300$ms. |
| **Scalability** | Ability to handle increased workload. | Architecture supporting horizontal scalability. |
| **Security** | Data and communication protection. | JWT Authentication, password encryption, rate limiting. |
| **Reliability** | Service availability and continuity. | $99.5\%$ uptime. Implementation of a backup strategy. |
| **Documentation** | Clear and self-documented APIs. | Use of Swagger/OpenAPI 3.x for automatic documentation. |

---

## 🧠 4. User Experience / Design

### 4.1 User Flow

1.  **Registration:** User $\rightarrow$ `POST /register` $\rightarrow$ Receives JWT token.
2.  **Board Setup:** User $\rightarrow$ `POST /boards` $\rightarrow$ `POST /boards/{id}/members` (add members) $\rightarrow$ `POST /boards/{id}/lists`.
3.  **Task Management:** User $\rightarrow$ `POST /lists/{id}/cards` $\rightarrow$ `PATCH /cards/{id}` (move/edit) $\rightarrow$ `POST /cards/{id}/comments`.

### 4.2 Wireframes / Mockups

> *Not applicable for a strictly Backend-only project.*

### 4.3 Copy & Tone of Voice

> *All error messages (HTTP status codes) and validation messages must be clear and standardized.*

---

## ⚙️ 5. Technical Architecture / API

### 5.1 Technical Dependencies

* **Backend:** Node.js/Express or Python/FastAPI (recommended choice).
* **Database:** PostgreSQL (preferred) or MongoDB.
* **Authentication:** OAuth2/JWT Bearer Token.
* **Containerization:** Docker.

### 5.2 Planned Endpoints (if relevant)

| Endpoint | Method | Description | Input | Output |
|:---|:---|:---|:---|:---|
| `/v1/auth/login` | POST | User Authentication | email, password | JWT Token |
| `/v1/boards` | POST | Creates a new Board | name, description | Board ID |
| `/v1/boards/{id}/lists` | POST | Adds a List to the Board | name | List ID |
| `/v1/cards` | POST | Creates a Card in a List | title, description, List ID | Card ID |
| `/v1/cards/{id}/move` | PATCH | Moves a Card | New List ID, position | Status 200 |

### 5.3 DevOps / Scalability Considerations

* **Scalability:** Application and database design to support horizontal scalability.
* **CI/CD:** Automated pipeline for testing and deployment (via Docker).
* **Logging:** Implementation of a centralized logging system (e.g., ELK Stack) for monitoring.

---

## 🧪 6. Metrics and Success Criteria

### 6.1 Key KPIs

* **Board Creation Success Rate:** Success rate for Board creation $\rightarrow$ $100\%$ (zero critical errors).
* **Average API Response Time (AART):** Average API response time $\rightarrow <300$ms.
* **Critical Vulnerability Count:** Number of critical vulnerabilities detected in security tests $\rightarrow$ Zero.

### 6.2 Secondary Metrics

* **Error Rate:** Percentage of API requests returning $5xx$ status codes $\rightarrow <0.1\%$.
* **Test Coverage:** Unit and integration test coverage $\rightarrow >80\%$.

---

## 📅 7. Planning and Deadlines

| Phase | Activity | Responsible | Start Date | End Date |
|:---|:---|:---|:---|:---|
| **Phase 1** | Architecture Setup, Authentication (JWT, Auth) | Dev Team | [To be defined] | 2 Weeks |
| **Phase 2** | Board and List Management (CRUD, Members, Roles) | Dev Team | [After Phase 1] | 3 Weeks |
| **Phase 3** | Card and Comment Management (CRUD, Move, Due Date, Label) | Dev Team | [After Phase 2] | 3 Weeks |
| **Phase 4** | Activity Log, Basic Notifications, Metadata Upload | Dev Team | [After Phase 3] | 2 Weeks |
| **Phase 5** | Security Hardening, Final Testing, Documentation, Deployment | Dev Team | [After Phase 4] | 1 Week |

---

## ⚠️ 8. Risks and Dependencies

| Type | Description | Mitigation |
|:---|:---|:---|
| **Technical** | Insufficient initial scalability of the relational database (PostgreSQL) under heavy load. | Early stress testing; Query optimization; Evaluation of sharding/replication. |
| **Security** | Vulnerabilities in permission management for shared Boards (authorization). | In-depth code review of the authorization middleware (Role-Based Access Control). |
| **Dependency** | OpenAPI documentation not updated with code changes. | Implementation of an automatic documentation generation tool from code annotations. |

---

## ✅ 9. Approvals

| Name | Role | 
|:---|:---|:---|:---|
| Luca Sacchi Ricciardi | CEO |
| Matteo Fiorio | Team Leader |
| Tommaso Villa | Member |
| Samuele Piazzi | Member |
| Filippo Granata | Member |
| Samuele Gonnella | Member |
| Corinna Buzzi | Member |
| MariaLia Pero | Member |
| Alessandro Cervini | Member |
| Gloria Candela | Member |