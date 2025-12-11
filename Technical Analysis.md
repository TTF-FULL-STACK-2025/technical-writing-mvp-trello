# 💻 Technical Analysis: Full-Stack Project Management Platform (Trello-clone)

**Project Title:** Project Management Platform (Trello-clone) <br>
**Version:** 1.2 (Alignment with PHP/MySQL Stack) <br>
**Date:** December 3, 2025 <br>
**Author:** Team Work <br>
**Approved by:** Luca Sacchi Ricciardi <br>

-----

## 1\. ⚙️ Architecture and Technology Stack (Revised)

The application is designed as a **hybrid system (SSR PHP + AJAX Vanilla)**, utilizing PHP for the initial rendering and Vanilla JavaScript for dynamic interactions (Drag & Drop, Modals).

### 1.1 Effective Technology Stack

#### Backend (Server-Side Logic/API)

  * **Language/Runtime:** **PHP** (used for all Card CRUD operations).
  * **Database (DB):** **MySQL** (assumed via `config.php`).
  * **DB Connection:** **PDO** (used for secure queries).
  * **Authentication:** **PHP Sessions** (To be implemented).
  * **Containerization:** Docker (maintained for the environment).
  * **Documentation:** Manual (Lack of standards like OpenAPI).

#### Frontend (Client)

  * **Framework:** **Vanilla JavaScript** and **Server-Side Rendering (SSR) PHP**.
  * **State Management:** **Global JavaScript variables and classes** (procedural approach).
  * **Styling:** Custom CSS (present in `index.php`).
  * **Drag & Drop Engine:** **D\&D logic implemented in Vanilla JS** (present in `index.php`).

### 1.2 Architectural Principles

  * **Hybrid SSR/AJAX:** Initial loading and list rendering are handled by PHP. Dynamic modifications (Card creation, editing, movement) occur via **AJAX/Fetch API** calls to the PHP endpoints.
  * **Stateful Backend:** Authentication will rely on server-side sessions, making the backend stateful (To be implemented).
  * **Immediate UI Update (Vanilla JS):** The user interface is updated immediately following a successful AJAX call.

-----

## 2\. 🔐 Security and Authorization (RBAC) (To Be Implemented)

Security will be based on session authentication and a role-based authorization system applied at the board level.

### 2.1 Authentication (PHP Sessions)

  * **Flow:** The user logs in. The server creates a **PHP session** and stores its ID in a cookie. The session is used to authenticate AJAX requests.
  * **Password Protection:** Passwords must be stored using **strong hashing** (e.g., `password_hash()` with **Bcrypt**).
  * **SQL Injection:** Protection is ensured by using **Prepared Statements (PDO)** in all existing endpoints (`add_card.php`, `delete_card.php`, etc.).

### 2.2 Authorization (Role-Based Access Control - RBAC)

Permissions must be applied at the Board level, checking the session user ID against the assigned roles. (Currently not implemented).

| Role | Permissions | Example Functions |
|:---|:---|:---|
| **Owner** | Full control over the board, members, and content. | Board Deletion, Member Management |
| **Editor** | Creation and modification of content. | List/Card Creation, Drag & Drop operations (Supported by existing files) |
| **Viewer** | Read-only access to board content. | Board Viewing |

-----

## 3\. 💾 Data Model and Core Functionalities (Backend)

The model supports the hierarchy: `Board` $\rightarrow$ `List` $\rightarrow$ `Card`.

### 3.1 CRUD Operations (Existing Implementation)

| Entity | PHP Endpoint | Technical Notes |
|:---|:---|:---|
| **Board** | `add_board.php` / `add_member.php` / `get_board_members.php` / `remove_member.php` / `update_member_role.php` | Manage Board and Permission Board |
| **List** | `index.php` | Lists of Board in main file php |
| **Card** | `add_card.php` / `delete_card.php` / `update_card_details.php` / `get_card_details.php` | Complete CRUD for title and description implemented. |
| **Card Move** | `update_card_position.php` | Handles the modification of `list_id` and `position` in a single operation. |

### 3.2 Consistency Requirements

  * **Positioning:** Cards use a `position` field (integer/float) for ordering. The `update_card_position.php` endpoint manages saving the new position and list.
  * **Soft Delete:** Archiving is not implemented. The `delete_card.php` endpoint performs a **physical deletion** (`DELETE FROM cards`).

-----

## 4\. 🖥️ Frontend Architecture & User Experience

The interface is based on a monolithic structure rendered by PHP, with interaction logic handled by Vanilla JavaScript.

### 4.1 Component Structure

The structure is defined directly within the `index.php` file.

  * **Layout:** Styling is defined in the `<style>` tag in `index.php`.
  * **Kanban Board:** Horizontal scrollable container.
  * **Card Modal:** Managed by `openCardModal` and `closeCardModal` in JavaScript.

### 4.2 Drag and Drop Logic (Core Interaction)

The DnD logic is implemented entirely in Vanilla JavaScript.

  * **Core Functions:** `handleDragStart`, `handleDragOver`, `handleDrop`, `getDragAfterElement` (`index.php`).
  * **Logic:**
    1.  **OnDragStart:** Stores the card ID in a global variable (`draggedCardId`).
    2.  **OnDragOver:** Calculates the insertion point (`getDragAfterElement`) and moves the element in the DOM in real-time.
    3.  **OnDrop:**
          * **DOM Update:** The card has already been moved in the DOM.
          * **API Call:** Sends a `POST` to `update_card_position.php` with `cardId`, `newListId`, and `newPosition`.

### 4.3 State Management

  * **Server State:** No centralized state manager. Data is reloaded or modified locally in the DOM after each AJAX operation.
  * **UI State:** Direct management of the DOM and CSS classes (`.hidden`, `.dragging`, `.drag-over`). Translations are loaded into a global JS variable (`TRANSLATIONS`).

-----

## 5\. 🚀 Performance, Logistics, and DevOps

### 5.1 Performance (NFR)

  * **API Latency:** Average $<300$ms.
  * **Frontend:** Being SSR/Vanilla, the First Contentful Paint is generally fast, but re-rendering operations and managing very long lists (50+ Cards) might be inefficient without Virtualization.
  * **Database:** Queries must be optimized (index on `position` and `list_id`).

### 5.2 DevOps

  * **CI/CD:** Continued use of Docker for the development/deployment environment. Lack of automated tests (To be developed).
  * **Monitoring:** Necessary to implement a centralized logging system for PHP and JavaScript errors (e.g., sending JS errors to the server).

-----

## 6\. 🛠️ API Specifications (AJAX Endpoints)

  * **Base URL:** Direct PHP endpoints (e.g., `/add_card.php`).
  * **Transfer Method:** Data is transferred via **`FormData`** (e.g., `new FormData()` in JavaScript) as **POST** requests.
  * **CORS:** Not required as the frontend and backend share the same domain.
  * **Error Handling:** Errors are handled via HTTP status codes and JSON messages returned by the PHP scripts.

-----

## 7\. ✅ Metrics and Success Criteria

### 7.1 Key KPIs

  * **Card Creation Success Rate:** Success rate for Card creation $\rightarrow$ 100% (zero critical errors).
  * **Average AJAX Response Time:** $<300$ms.
  * **First Contentful Paint (FCP):** Frontend loads in **$<1.5$s**.
  * **Interaction to Next Paint (INP):** Drag and drop interactions must feel responsive ($<200$ms).

-----

## 8\. 📅 Planning and Deliverables

The plan is adapted to include the development of authentication features in the existing PHP stack.

| Phase | Backend Activity | Frontend Activity | Main Deliverables |
|:---|:---|:---|:---|
| **Phase 1** (2 Weeks) | Architecture Setup, **Authentication (PHP Sessions, User DB Setup)** | Project Init, UI Kit Setup, Login/Register Pages (UI) | Functional Auth Flow (E2E), Repository Setup |
| **Phase 2** (3 Weeks) | Board/List CRUD, RBAC Middleware | Dashboard Layout, Board Creation, Board View (UI) | Board Management, Role Handling UI |
| **Phase 3** (3 Weeks) | Card CRUD, Move Logic, Comments | **Drag & Drop Implementation** (Reliability), Card Modals | **Functional Kanban Board** |
| **Phase 4** (2 Weeks) | Activity Log, Notifications, Metadata | Activity Sidebar, Notifications UI, Attachment UX | Complete User Experience |
| **Phase 5** (1 Week) | Security Hardening, API Freeze | Performance Tuning, Bundle Optimization, E2E Tests | Production Deployment |

-----

## 9\. ⚠️ Risks and Mitigation

| Type | Description | Mitigation |
|:---|:---|:---|
| **UX/Technical** | "Janky" Drag and Drop due to Vanilla JS code across different browsers. | **In-depth Cross-Browser Testing**; Restructure D\&D logic if problems persist. |
| **Data Integrity** | Race conditions when two users move the same card. | Use DB-level locks (PDO transactions) or implement a timestamp strategy for concurrency control. |
| **Security** | XSS attacks via Card Input. | Use the `htmlspecialchars()` function on the display side (frontend) as already done in `index.php` and `add_card.php`. |
| **Scalability (BE)** | Limitations of the stateful paradigm (PHP Sessions) under high load. | Adopt Caching mechanisms (e.g., Redis) and aggressive MySQL optimization. |

-----

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
| Nicole Caravello | Member |