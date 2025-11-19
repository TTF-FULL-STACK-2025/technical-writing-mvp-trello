# 💻 Technical Analysis: Full-Stack Project Management Platform (Trello-like)

**Project Title:** Project Management Platform (Trello-like) <br>
**Version:** 1.1 (Full-Stack Update) <br>
**Date:** November 19, 2025 <br>
**Author:** Team Work <br>
**Approved by:** Luca Sacchi Ricciardi <br>

---

## 1. ⚙️ Architecture and Technology Stack

The application is designed as a **Full-Stack System** consisting of a RESTful API Backend and a modern Single Page Application (SPA) Frontend.

### 1.1 Recommended Technology Stack

#### Backend (API)
* **Framework:** Node.js/Express or Python/FastAPI.
* **Database (DB):** **PostgreSQL** (preferred for relational integrity).
* **Authentication:** **JWT** (JSON Web Tokens).
* **Containerization:** Docker & Kubernetes.
* **Documentation:** OpenAPI 3.x / Swagger UI.

#### Frontend (Client)
* **Framework:** **React** (v18+) with **TypeScript** for type safety.
* **Build Tool:** Vite (for high-performance development).
* **State Management:** **Zustand** or **Redux Toolkit** (for global client state) + **TanStack Query** (for server state synchronization).
* **Styling:** **Tailwind CSS** (utility-first) for rapid UI development.
* **Drag & Drop Engine:** **dnd-kit** or **react-beautiful-dnd** (specialized for Kanban interactions).

### 1.2 Architectural Principles

* **Decoupled Architecture:** The Frontend and Backend are completely decoupled, communicating exclusively via RESTful APIs.
* **Stateless Backend:** The API remains stateless to facilitate horizontal scaling.
* **Optimistic UI:** The Frontend implements "Optimistic Updates" to ensure the interface feels instant (e.g., moving a card updates the UI immediately before the server confirms).

---

## 2. 🔐 Security and Authorization (RBAC)

Security is based on robust authentication and a role-based authorization system applied at the board level.

### 2.1 Authentication (JWT)

* **Flow:** The user logs in (`POST /auth/login`) and receives a **JWT Bearer Token**. The Frontend stores this securely (e.g., HttpOnly Cookie or memory) and attaches it to API headers.
* **Password Protection:** Passwords must be stored using **strong hashing** (e.g., bcrypt/argon2).
* **Rate Limiting:** Implemented on API to prevent brute-force attacks.

### 2.2 Authorization (Role-Based Access Control - RBAC)

Permissions are applied at the Board level.

| Role | Permissions | Example Functions |
|:---|:---|:---|
| **Owner** | Full control over the board, members, and content. | Board Deletion (FR9), Member Management (FR10) |
| **Editor** | Creation and modification of content. | List/Card Creation, Drag & Drop operations |
| **Viewer** | Read-only access to board content. | Board Viewing |

---

## 3. 💾 Data Model and Core Functionalities (Backend)

The data model supports the hierarchy: `Board` $\rightarrow$ `List` $\rightarrow$ `Card`.

### 3.1 CRUD Operations

| Entity | Key Operations (FR) | Technical Notes |
|:---|:---|:---|
| **Board** | CRUD, Member Management | Root entity. |
| **List** | CRUD, Reordering | Must support `position` (float/integer) for ordering. |
| **Card** | CRUD, Move/Reorder, Assignments | `Move` changes `listId` and `position`. |

### 3.2 Consistency Requirements

* **Soft Delete:** Archiving entities results in a soft delete (flag update), not physical removal.

---

## 4. 🖥️ Frontend Architecture & User Experience

### 4.1 Component Structure
The UI is built using reusable atomic components.
* **Layouts:** Authenticated Layout (Sidebar + Topbar) vs Public Layout (Login/Register).
* **Kanban Board:** A horizontal scrollable container holding `List` components.
* **Draggables:** `Card` components must be wrapped in draggable providers.

### 4.2 Drag and Drop Logic (Complex Interaction)
The core "Trello-feel" relies on the Drag and Drop (DnD) implementation.

* **Library:** Use `dnd-kit` for accessibility and performance.
* **Logic:**
    1.  **OnDragStart:** Capture the card ID and original position.
    2.  **OnDragOver:** Calculate potential new index visually (Client-side calculation).
    3.  **OnDragEnd:**
        * **Optimistic Update:** Immediately update the local state to reflect the move.
        * **API Call:** Send `PUT /cards/{id}/move` with `newListId` and `newPosition`.
        * **Rollback:** If the API fails (non-200 status), revert the UI to the original state and show a generic error toast.

### 4.3 State Management Strategy
* **Server State (React Query):** Used for fetching Boards, Lists, and Cards. Handles caching, background refetching, and stale data invalidation.
* **Global UI State (Zustand):** Used for managing sidebar toggle, current modal open/close state, and user session data.

---

## 5. 🚀 Performance, Logistics, and DevOps

### 5.1 Performance (NFR)

* **API Latency:** Average $<300$ms.
* **Frontend Bundle Size:** Initial load chunk should be **$<500$KB** (Gzipped). Lazy loading used for non-critical routes.
* **Rendering:** Prevent unnecessary re-renders in the Kanban board using `React.memo` or virtualization if lists exceed 50+ cards.

### 5.2 DevOps

* **CI/CD:**
    * **Backend:** Docker builds + Tests.
    * **Frontend:** Build + Lint + Deploy to CDN/Static Host (e.g., Vercel, Netlify, AWS S3).
* **Monitoring:** Sentry (or similar) for Frontend error tracking.

---

## 6. 🛠️ API Specifications (Integration)

* **Base URL:** `/api/v1`
* **CORS:** Backend must be configured to allow requests from the Frontend domain.
* **Error Handling:** Frontend intercepts `401` errors to trigger logout or token refresh.

---

## 7. ✅ Metrics and Success Criteria

### 7.1 Key KPIs

* **Average API Response Time:** $<300$ms.
* **First Contentful Paint (FCP):** Frontend loads in **$<1.5$s**.
* **Interaction to Next Paint (INP):** Drag and drop interactions must feel instant ($<200$ms).

---

## 8. 📅 Planning and Deliverables

The project is divided into five main Phases (11 Weeks).

| Phase | Backend Activity | Frontend Activity | Main Deliverables |
|:---|:---|:---|:---|
| **Phase 1** (2 Weeks) | Architecture, Auth API, DB Setup | Project Init, UI Kit Setup, Login/Register Pages | Auth Flow (E2E), Repository Setup |
| **Phase 2** (3 Weeks) | Board/List CRUD, RBAC Middleware | Dashboard Layout, Board Creation, Board View | Board Management, Role Handling UI |
| **Phase 3** (3 Weeks) | Card CRUD, Move Logic, Comments | **Drag & Drop Implementation**, Card Modals | **Functional Kanban Board** |
| **Phase 4** (2 Weeks) | Activity Log, Notifications, Metadata | Activity Sidebar, Notifications UI, Attachment UX | Complete User Experience |
| **Phase 5** (1 Week) | Security Hardening, API Freeze | Performance Tuning, Bundle Optimization, E2E Tests | Production Deployment |

---

## 9. ⚠️ Risks and Mitigation

| Type | Description | Mitigation |
|:---|:---|:---|
| **UX/Technical** | "Janky" Drag and Drop on mobile devices. | Use `touch-action: none` CSS properties and test specifically on iOS/Android touch events. |
| **Data Integrity** | Race conditions when two users move the same card. | Backend handles concurrency; Frontend uses WebSockets or Polling for near-real-time updates. |
| **Security** | XSS attacks via User Comments. | Sanitize all Markdown/HTML input on both Client and Server. |

---

## ✅ 10. Approvals

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