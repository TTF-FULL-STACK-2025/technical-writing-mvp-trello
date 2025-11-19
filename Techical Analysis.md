# 💻 Technical Analysis: Core Backend for Project Management Platform (Trello-like)

**Project Title:** Core Backend for Project Management Platform (Trello-like) <br>
**Version:** 1.0 <br>
**Date:** November 19, 2025 <br>
**Author:** Team Work <br>
**Approved by:** Luca Sacchi Ricciardi <br>

---

## 1. ⚙️ Architecture and Technology Stack

The application will be a **Backend-only** (headless) system exposed via RESTful APIs, designed for **horizontal scalability** and an **API-first** approach.

### 1.1 Recommended Technology Stack

* **Backend Framework:** Node.js/Express or Python/FastAPI.
* **Database (DB):** **PostgreSQL** (preferred for relational integrity) or MongoDB.
* **Authentication:** **JWT** (JSON Web Tokens).
* **Containerization:** **Docker** and orchestration (e.g., Kubernetes) for high availability and scalability.
* **Documentation:** **OpenAPI 3.x / Swagger UI**.

### 1.2 Architectural Principles

* **Stateless Services:** The API service must be *stateless* to facilitate *scaling out* (horizontal scalability).
* **API-First:** All functionalities are exposed via versioned RESTful APIs (`/api/v1/...`).
* **Uptime Target:** Reliability guaranteed with an uptime target of **$\geq 99.5\%$**.

---

## 2. 🔐 Security and Authorization (RBAC)

Security is based on robust authentication and a role-based authorization system applied at the board level.

### 2.1 Authentication (JWT)

* **Flow:** The user logs in (`POST /auth/login`) and receives a **JWT Bearer Token**. All subsequent requests must include this token.
* **Password Protection:** Passwords must be stored using **strong hashing** (e.g., bcrypt/argon2).
* **Password Recovery:** Secure flow managed via a single-use token and email service (FR4, FR5).
* **Rate Limiting:** Implemented to prevent brute-force attacks on login and registration (BR11).

### 2.2 Authorization (Role-Based Access Control - RBAC)

Permissions are applied at the Board level, verifying that the user is a member and has the appropriate role for the requested action.

| Role | Permissions | Example Functions |
|:---|:---|:---|
| **Owner** | Full control over the board, members, and content. Cannot be removed by others. | Creation (FR7), Modification (FR8), Board Deletion (FR9), Member Management (FR10) |
| **Editor** | Creation and modification of content (Lists, Cards, Comments, Assignments). | List Creation/Modification (FR11, FR12), Card Creation/Modification (FR15, FR16, FR20) |
| **Viewer** | Read-only access to board content and the Activity Log (FR26). | Board Viewing |

---

## 3. 💾 Data Model and Core Functionalities

The data model must support the hierarchical organization and defined operations: `Board` $\rightarrow$ `List` $\rightarrow$ `Card`.

### 3.1 CRUD Operations and Structure

| Entity | Key Operations (FR) | Technical Notes |
|:---|:---|:---|
| **Board** | CRUD (FR7, FR8, FR9), Member Management (FR10) | Contains `ownerId` field and `archived/deleted` status. |
| **List** | CRUD, Reordering (FR14, FR13) | Must support a `position` field for ordering within the Board. |
| **Card** | CRUD, Move/Reorder (FR20), Assignments (FR17), Due Date (FR18), Labels (FR19), Comments (FR22, FR23), Attachment Metadata (FR24) | `Move` between lists requires updating `listId` and `position`. |
| **Activity Log** | Tracking (FR25), Retrieval (FR26) | Immutable. Records modifications to Boards, Lists, Cards, Comments. |

### 3.2 Consistency Requirements

* **Archiving Consistency:** Archiving a Board results in the logical cascading archive (**soft delete**) of its related Lists and Cards.
* **Unique Email:** The user's email address must be unique (BR1).

---

## 4. 🚀 Performance, Logistics, and DevOps

### 4.1 Performance (NFR)

* **API Latency:** Average API response latency must be **$<300$ms**.
* **DB Optimization:** Queries and schemas must be optimized for high concurrency. **Sharding/replication** strategies for PostgreSQL will be evaluated in case of aggressive load.

### 4.2 DevOps and Observability

* **CI/CD:** Automated pipeline for testing and deployment (via Docker).
* **Logging:** Implementation of a centralized logging system (e.g., ELK Stack) for monitoring and troubleshooting.
* **Health Check:** `/health` and `/status` endpoints for monitoring by orchestrators and load balancers (FR29).
* **Backup:** Automated and tested backup strategy for the database.

---

## 5. 🛠️ API Specifications and Integration Flows

### 5.1 API Conventions

* **Standard:** REST over HTTPS, JSON payload.
* **Base URL:** `/api/v1`.
* **Documentation:** Accessible via `GET /api-docs` (Swagger UI), always synchronized with the code.
* **Error Handling:** Consistent use of standard HTTP status codes (e.g., `400 Bad Request` for validation, `401 Unauthorized`, `403 Forbidden` for insufficient permissions).

### 5.2 Key Endpoint Examples

| Functionality | Method | Endpoint | Related Requirements (FR) |
|:---|:---|:---|:---|
| Authentication | `POST` | `/v1/auth/login` | FR2 |
| Board Creation | `POST` | `/v1/boards` | FR7 |
| Member Management | `POST/PUT/DELETE` | `/v1/boards/{boardId}/members` | FR10 |
| Card Creation | `POST` | `/v1/lists/{listId}/cards` | FR15 |
| Card Movement | `PUT` | `/v1/cards/{cardId}/move` | FR20 |
| Activity Log | `GET` | `/v1/boards/{boardId}/activity` | FR26 |

---

## 6. ✅ Metrics and Success Criteria

The success of the backend will be evaluated based on achieving the following KPIs (Key Performance Indicators).

### 6.1 Key KPIs

* **Average API Response Time (AART):** Average **$<300$ms**.
* **Board Creation Success Rate:** Success rate for Board creation $\rightarrow$ **$100\%$** (zero critical errors).
* **Critical Vulnerability Count:** Number of critical vulnerabilities detected in security tests $\rightarrow$ **Zero**.

### 6.2 Secondary Metrics

* **Error Rate:** Percentage of API requests returning $5xx$ status codes $\rightarrow$ **$<0.1\%$**.
* **Test Coverage:** Unit and integration test coverage $\rightarrow$ **$>80\%$**.

---

## 7. 📅 Planning and Deliverables

The project is divided into five main Phases, with a total estimated duration of **11 weeks**.

| Phase | Activity | Estimated Duration | Main Deliverables |
|:---|:---|:---|:---|
| **Phase 1** | Architecture Setup, Authentication (JWT, Auth) | **2 Weeks** | Functioning Registration/Login flows, DB Baseline, Project Structure |
| **Phase 2** | Board and List Management (CRUD, Members, Roles) | **3 Weeks** | Complete CRUD APIs for Boards and Lists; RBAC Management (Owner/Editor/Viewer) |
| **Phase 3** | Card and Comment Management (CRUD, Move, Date, Label) | **3 Weeks** | Complete CRUD APIs for Cards and Comments; Card Movement/Reordering Logic (FR20) |
| **Phase 4** | Activity Log, Basic Notifications, Metadata Upload | **2 Weeks** | Logging Service (FR25) and Notifications (FR27); Attachment Metadata API (FR24) |
| **Phase 5** | Security Hardening, Final Testing, Documentation, Deployment | **1 Week** | Penetration Test, Final Performance Tuning; Complete OpenAPI 3.x Documentation |

---

## 8. ⚠️ Risks and Mitigation

| Type | Description | Mitigation |
|:---|:---|:---|
| **Technical** | Insufficient DB scalability (PostgreSQL) under heavy load. | Early stress testing; Query optimization; Evaluation of sharding/replication. |
| **Security** | Vulnerabilities in permission management (Authorization). | In-depth code review of the authorization middleware (RBAC). |
| **Dependency** | OpenAPI documentation not updated with code changes. | Implementation of an automatic documentation generation tool from code annotations. |


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