# Functional Analysis – Backend & Frontend Trello-like System
**Project title:** Core Backend & Frontend for Project Management Platform (Trello-like)  
**Version:** 1.1  
**Authors:** Team Work  
**Date:** 19/11/2025  
**Approved by:** Luca Sacchi Ricciardi  

---

## 🧭 1. Introduction

### 1.1 Purpose of the document

This document describes the **functional analysis of the complete Trello-like System**, including:

- **Backend**: REST APIs, business logic, persistence, security.
- **Frontend**: Web SPA (Single Page Application) that interacts with the backend via REST APIs and, in the future, WebSocket/webhooks.

It aims to:

- Define the main goals of the overall system.  
- Clarify the expected behavior of the backend services and REST APIs.  
- Clarify the expected behavior and flows of the frontend UI.  
- Provide a shared reference for implementation, testing, and maintenance for both frontend and backend.  
- Ensure alignment with **OpenAPI/Swagger** standards for API design and documentation.

### 1.2 Context

**Project type:**  
Full-stack project:

- **Backend service / REST API**
- **Frontend web SPA** consuming those APIs

**Target platforms:**

- Frontend clients:
  - Web app (desktop and mobile web).
  - Future: mobile apps (native or cross-platform) or integrations.
- External systems via API or webhooks (optional, on the backend side).

**Business need / origin of request:**

Provide a system with core Trello-like functionality to manage:

- Users  
- Boards  
- Lists  
- Cards (tasks)  
- Comments  
- Labels, due dates, activity logs  
- Basic notifications  

The system must be:

- Scalable  
- Secure  
- High-performing  

and ready for future UI enhancements and integrations.

**References:**

- Trello basic features (boards, lists, cards, collaboration).  
- OpenAPI 3.x and Swagger UI documentation.  
- JWT-based authentication best practices.  
- Modern SPA best practices (React/Vue/Angular style).

### 1.3 Main objectives

- Provide a **scalable, secure, high-performance backend** for a Trello-like application.
- Provide a **modern, responsive frontend** that exposes all core features to end users.
- Support core Trello-like features:
  - Boards, lists, cards  
  - User management and sharing  
  - Comments, labels, due dates, activity logging  
- Expose a complete **RESTful API** documented with OpenAPI/Swagger, including:
  - Versioning (e.g., `/api/v1`)  
  - JWT-based authentication  
- Enable:
  - User management, authentication, authorization  
  - Collaboration on shared boards  
- Provide:
  - Basic notifications (e.g., card assignment)  
  - Activity tracking  
- Ensure that the **frontend**:
  - Offers a Trello-like UX (drag & drop, board view)  
  - Is responsive (desktop, tablet, mobile)  
  - Implements a minimal accessibility baseline (a11y)

---

## 🧩 2. General system description

### 2.1 Application context & architecture

Type of application:

- **Backend-only, RESTful API (headless architecture)**.  
- **Frontend SPA** that consumes those APIs and implements the user interface.

High-level architecture:

- **Frontend**:
  - Single Page Application (e.g., React/Vue/Angular) running in the browser.
  - Communicates with the backend via HTTPS/JSON.
  - Manages:
    - Client-side authentication state
    - UI for boards/lists/cards
    - Notifications and activity view
- **Backend**:
  - Server-side application running on:
    - Cloud infrastructure (preferred)
    - Containerized with Docker and orchestrated (e.g., Kubernetes)
  - Exposes JSON REST APIs and (optionally) WebSocket or webhooks
  - Handles business logic, security, persistence, logging

External systems (backend):

- Database (PostgreSQL or MongoDB).  
- Email service or similar for password recovery (SMTP or external provider).  
- Optional webhooks / WebSocket gateway for real-time updates.  
- Monitoring/logging stack.

External interfaces:

- **Client-side (frontend):**
  - Web browser (SPA using JS frameworks).
  - Future: mobile apps or other API clients.
- **Server-side / APIs:**
  - REST APIs defined with OpenAPI/Swagger.
  - Optional WebSocket endpoints or webhook endpoints for real-time notifications.

User input:

- API calls from authenticated or anonymous clients using HTTP(S) with JSON payloads.
- User interaction via forms, drag & drop, buttons in the frontend SPA.

### 2.2 Actors

| Actor                 | Description                                | Permissions / Role                                                                 |
|-----------------------|--------------------------------------------|------------------------------------------------------------------------------------|
| Unauthenticated client| Client not logged in                       | Can register a new user, login, and request password recovery flows.              |
| User                  | Registered user with an account            | Can create and manage boards, lists, cards, comments, labels according to role.   |
| Board Owner           | User who created a board or received ownership | Full control over the board, its lists, cards, members, and settings.        |
| Board Editor          | User invited to a board with edit rights   | Can modify content (lists, cards, comments, labels) but not board ownership.      |
| Board Viewer          | User invited to a board with read-only rights | Can view board, lists, cards, comments, but cannot modify content.           |
| System (backend)      | Internal backend components                | Handles authentication, authorization, persistence, activity logging, notifications. |
| Frontend application  | Browser-based SPA                          | Presents the UI, collects user input, calls backend APIs, manages client state.   |

### 2.3 High-level flow (end-to-end)

1. **Auth & Session**
   - Client (frontend) calls the API (e.g., `/api/v1/auth/register`) to register or login.
   - The backend validates the request, processes business logic, and issues a JWT upon successful authentication.
   - The frontend stores the token securely (or receives an httpOnly cookie) and updates the session state.

2. **Usage**
   - The authenticated client includes the JWT as a Bearer token in subsequent requests.
   - The user, via the frontend:
     - Creates boards, invites members, sets roles.
     - Creates lists within boards.
     - Creates and manages cards within lists (move, reorder, assign users, due dates, labels).
   - The frontend shows a Trello-like interface (board view, drag & drop of lists and cards).

3. **Backend responsibilities**
   - Persist all operations in the database.
   - Log activity (board/list/card CRUD, comments, assignments).
   - Trigger basic notifications (e.g., card assigned).
   - Optionally:
     - Push events to webhooks or WebSocket channels for real-time updates.

4. **Error handling**
   - In case of errors (validation, auth, permissions, system error), the API returns appropriate HTTP status codes and error payloads.
   - The frontend shows user-friendly error messages and handles loading & empty states.

### 2.4 Frontend application context

Type of frontend:

- Responsive Single Page Application (SPA) targeting **web desktop + mobile web** initially.

Main responsibilities (frontend):

- Manage the user experience for:
  - Boards, lists, cards, comments, labels, due dates, activity, notifications.
- Interact with the backend via:
  - REST APIs (HTTP/JSON)
  - Optional WebSocket/Webhooks in the future.
- Manage client-side authentication:
  - Secure storage of JWTs / session.
  - Redirect between public and protected pages.
- Provide:
  - Drag & drop interface for lists and cards (Kanban board).
  - Loading, error, and empty states; user feedback (toasts/snackbars).
  - Responsive layout (desktop, tablet, mobile).
  - A basic accessibility level (focus states, ARIA attributes, keyboard navigation for frequent actions).

Constraints & assumptions:

- Supported browsers (indicative):
  - Latest stable versions of Chrome, Firefox, Safari, Edge.
- UI design system:
  - To be defined (e.g., component library or internal design system).

---

## 🧠 3. Detailed functional requirements

> Note: All externally exposed backend operations must be aligned with the OpenAPI/Swagger specification.  
> The frontend maps user actions to the corresponding API calls.

### 3.1 Backend functional requirements table (FR)

| ID   | Function name              | Description | Input (API request) | Output (Response / state change) | Priority | Dependencies |
|------|----------------------------|-------------|---------------------|----------------------------------|----------|--------------|
| FR1  | User registration          | Create a new user account with email and password. | POST `/api/v1/auth/register` with email, password, optional name. | 201 Created; user resource + JWT or login link; validation errors on invalid data. | Must | – |
| FR2  | User login                 | Authenticate user and return JWT. | POST `/api/v1/auth/login` with email and password. | 200 OK; JWT access token (and optional refresh token). | Must | FR1 |
| FR3  | User logout                | Invalidate active session/token (if token blacklist or similar). | POST `/api/v1/auth/logout` with token in Authorization header. | 204 No Content or 200 OK; token invalidation effect server-side if implemented. | Should | FR2 |
| FR4  | Password recovery initiation | Start password reset flow. | POST `/api/v1/auth/password/forgot` with email. | 202 Accepted; email sent or generic response; audit log entry. | Must | FR1, Email service |
| FR5  | Password reset             | Reset password using a provided token. | POST `/api/v1/auth/password/reset` with token + new password. | 200 OK; password updated; JWT optionally issued. | Must | FR4 |
| FR6  | Profile editing            | Edit user profile (name, avatar). | PUT/PATCH `/api/v1/users/me` with name, avatar URL, etc. | 200 OK; updated user profile. | Should | FR2 |
| FR7  | Create board               | Create a new board owned by the user. | POST `/api/v1/boards` with name, optional description. | 201 Created; board resource with owner = current user. | Must | FR2 |
| FR8  | Edit board                 | Update board name and description. | PUT/PATCH `/api/v1/boards/{boardId}` | 200 OK; updated board resource. | Must | FR7 |
| FR9  | Archive/delete board       | Archive or soft-delete a board. | DELETE `/api/v1/boards/{boardId}` or POST `/api/v1/boards/{id}/archive` | 200/204 OK; board marked archived or deleted; activity log entry. | Must | FR7, FR16 |
| FR10 | Manage board members       | Add, remove, or update member roles (owner/editor/viewer). | POST/PUT/DELETE `/api/v1/boards/{boardId}/members` | 200/201/204; updated board membership; notifications and activity log triggered. | Must | FR7, FR17 |
| FR11 | Create list                | Create a list within a board. | POST `/api/v1/boards/{boardId}/lists` with name and optional position. | 201 Created; list resource associated with board. | Must | FR7 |
| FR12 | Edit list                  | Edit list name. | PUT/PATCH `/api/v1/lists/{listId}` | 200 OK; updated list resource. | Must | FR11 |
| FR13 | Reorder lists              | Change the order of lists within a board. | PUT `/api/v1/boards/{boardId}/lists/reorder` with new order/positions. | 200 OK; list positions updated. | Should | FR11 |
| FR14 | Archive/delete list        | Archive or delete a list. | DELETE `/api/v1/lists/{listId}` or archive endpoint. | 200/204; list archived/deleted; cards affected according to rules; activity logged. | Should | FR11, FR16 |
| FR15 | Create card                | Create a card within a list. | POST `/api/v1/lists/{listId}/cards` with title, optional description, labels, etc. | 201 Created; card resource with default position in list. | Must | FR11 |
| FR16 | Edit card                  | Edit card title and description. | PUT/PATCH `/api/v1/cards/{cardId}` | 200 OK; updated card resource. | Must | FR15 |
| FR17 | Assign users to card       | Assign/unassign users to/from a card. | POST/DELETE `/api/v1/cards/{cardId}/assignees` | 200/201/204; updated card assignee list; notification triggered to assignees. | Must | FR2, FR15, FR20 |
| FR18 | Manage due date            | Add/update/remove due date of a card. | PUT/PATCH `/api/v1/cards/{cardId}/due-date` | 200 OK; card due date updated; activity log updated. | Should | FR15 |
| FR19 | Manage labels              | Add/remove labels on a card (and optionally define label set per board). | POST/DELETE `/api/v1/cards/{cardId}/labels` and POST `/boards/{id}/labels` | 200/201/204; updated label assignments; label metadata persisted. | Should | FR7, FR15 |
| FR20 | Move/reorder cards         | Reorder cards within a list or move between lists. | PUT `/api/v1/cards/{cardId}/move` with new listId and position. | 200 OK; card moved; list/card positions updated; activity logged. | Must | FR11, FR15 |
| FR21 | Archive/delete card        | Archive or delete a card. | DELETE `/api/v1/cards/{cardId}` or archive endpoint. | 200/204; card archived/deleted; activity logged. | Must | FR15, FR24 |
| FR22 | Add comment                | Add a comment to a card. | POST `/api/v1/cards/{cardId}/comments` with text. | 201 Created; comment resource; activity log updated. | Must | FR15 |
| FR23 | Delete comment             | Delete an existing comment. | DELETE `/api/v1/comments/{commentId}` | 204 No Content; activity logged. | Should | FR22 |
| FR24 | Attachments metadata       | Add attachment metadata to card (no physical storage in scope). | POST `/api/v1/cards/{cardId}/attachments` with name, URL, size, mime type. | 201 Created; attachment metadata persisted; activity logged. | Could | FR15 |
| FR25 | Activity logging           | Track CRUD operations on boards/lists/cards/comments/attachments. | Internal on each related API call. | Activity entries stored (e.g., action, actor, target, timestamp). | Must | Many FRs (7–24) |
| FR26 | Activity retrieval         | Retrieve activity log for a board/card/user. | GET `/api/v1/boards/{boardId}/activity` etc. | 200 OK; list of activity events with pagination. | Should | FR25 |
| FR27 | Basic notifications        | Trigger basic notifications on significant events (e.g., card assignment). | Internal or via `/notifications` endpoints; possibly webhooks or push channels. | Notification records created; optional push via WebSocket/webhook/email. | Should | FR17, FR18, FR25 |
| FR28 | API documentation          | Expose interactive Swagger UI for all endpoints. | GET `/api-docs` or similar. | OpenAPI UI served, always synchronized with code annotations or spec file. | Must | All FRs |
| FR29 | Health check & status      | Provide basic health endpoints for monitoring. | GET `/health`, GET `/status` | 200 OK with health information; used by orchestrator/load balancer. | Must | – |

### 3.2 Frontend functional requirements (FFR)

| ID    | Function name                      | Description                                                                                       | Output (UX / state)                                                                 | Priority | Related backend FRs        |
|-------|------------------------------------|---------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------|----------|----------------------------|
| FFR1  | Auth screens (login & register)    | Screens for registration, login, logout, password recovery/reset.                                 | Validated forms, clear error messages, redirect after success.                      | Must     | FR1, FR2, FR3, FR4, FR5    |
| FFR2  | Session handling                   | Management of authentication state (logged-in / logged-out).                                      | Automatic redirect to/from protected pages; Authorization headers handled.          | Must     | FR2                        |
| FFR3  | Boards list (dashboard)            | Page showing the user’s boards (owner + shared).                                                 | Grid/list view of boards; “Create board” button; basic filters/search.              | Must     | FR7, FR8, FR9, FR10        |
| FFR4  | Board detail (Kanban)              | Main Trello-like view: lists as columns, cards as drag & drop elements.                          | Drag & drop lists/cards; quick creation of lists/cards; buttons for basic actions.  | Must     | FR11, FR12, FR13, FR14, FR15, FR16, FR20, FR21 |
| FFR5  | Card quick view                    | Inline card preview (title, labels, assignee, due date) shown on the board.                      | Main info always visible; indicators for status (overdue, assigned, etc.).          | Must     | FR16, FR17, FR18, FR19     |
| FFR6  | Card detail modal / side panel     | Card detail opened on click (modal or side panel).                                               | Edit title/description, assignees, due date, labels, attachments, comments.         | Must     | FR16–FR24                  |
| FFR7  | Comments UI                        | Interface to add and view comments.                                                              | Simple text editor, comment list, timestamp, author, delete where allowed.          | Must     | FR22, FR23                 |
| FFR8  | Board members & roles UI           | UI to view and manage board members and roles.                                                   | Member list with roles; ability to add/remove members (Owner only).                 | Should   | FR10, BR5                  |
| FFR9  | Labels management UI               | Interface to define/manage board labels and assign them to cards.                                | Label palette, colors and names; quick application from card detail/quick actions.  | Should   | FR19                       |
| FFR10 | Due date & status visualization    | Clear UI for managing and displaying card due dates.                                             | Visual indicators for overdue, due soon; usable and accessible date pickers.        | Should   | FR18                       |
| FFR11 | Activity / History view            | Activity view for board/card integrated into the interface.                                      | Readable timeline, filterable, with pagination/lazy loading.                        | Should   | FR25, FR26                 |
| FFR12 | Notifications center               | UI for showing notifications to the user (e.g., assigned cards).                                 | Icon/badge with count, clickable list, quick actions (mark as read, open card).     | Should   | FR27                       |
| FFR13 | Profile & settings screen          | “My profile” section to change name, avatar, personal settings.                                  | Name/avatar form, basic options (e.g., language, timezone if implemented).          | Should   | FR6                        |
| FFR14 | Error, empty & loading states      | Dedicated UI for errors, loading and empty lists.                                                | Skeleton loaders/spinners, user-friendly error messages, CTAs to retry.            | Must     | All FRs (generic)          |
| FFR15 | Responsive layout                  | Layout adapts to different resolutions (mobile, tablet, desktop).                                | Defined breakpoints; board usable on touch devices; mobile-compatible drag & drop.  | Must     | –                          |
| FFR16 | Accessibility baseline (a11y)      | Basic accessibility support: focus management, ARIA, keyboard shortcuts for frequent actions.    | Keyboard navigation; screen readers correctly interpret key UI elements.            | Should   | –                          |

---

## ⚙️ 4. Non-functional requirements

### 4.1 Backend non-functional requirements

| Type            | Description                      | Target / Value                                | Notes |
|-----------------|----------------------------------|-----------------------------------------------|-------|
| Performance     | API latency                      | ≤ 300 ms for 95% of requests under normal load | Includes DB operations; excludes external network latency. |
| Scalability     | Horizontal scalability           | Support horizontal scaling of API instances   | Stateless services with shared DB/cache; container-ready. |
| Security        | Authentication and authorization | JWT Bearer, password hashing (bcrypt/argon2)  | Enforce HTTPS; role-based access control (Owner/Editor/Viewer). |
| Security        | Rate limiting                    | Rate limit per IP/token                       | Protect from brute-force attacks and abuse. |
| Reliability     | Uptime target                    | ≥ 99.5%                                       | Excludes scheduled maintenance; supported by redundancy. |
| Reliability     | Backups                          | Regular automated backups                     | Configurable schedule; tested recovery process. |
| Observability   | Logging and monitoring           | Centralized logs, metrics, alerts             | Include API latency, error rates, DB health; structured logs. |
| Compatibility   | Client compatibility             | REST over HTTPS, JSON format                  | Standard HTTP verbs and status codes. |
| API Docs        | OpenAPI/Swagger                  | OpenAPI 3.x compliant                         | Documented endpoints, schemas, errors; Swagger UI available. |
| Maintainability | Code structure & documentation   | Modular architecture, clear layering          | Separation of domain, persistence, transport layers. |
| Data privacy    | Protection of user data          | Encrypted at rest and in transit              | Passwords always hashed; secrets stored securely. |

### 4.2 Frontend non-functional requirements

| Type           | Description                | Target / Value                                  | Notes |
|----------------|----------------------------|-------------------------------------------------|-------|
| Performance    | Initial load time          | ≤ 2 seconds on a normal connection for first paint | Bundle splitting, lazy loading, caching. |
| Performance    | UI responsiveness          | Smooth animations and drag & drop interactions | Target ~60 FPS on modern hardware. |
| Compatibility  | Browser support            | Latest 2 versions of major browsers            | Chrome, Firefox, Safari, Edge. |
| Usability      | Consistency & UX           | Consistent layout and patterns across screens  | Use of design system/component library. |
| Accessibility  | WCAG baseline              | “Lite” compliance with WCAG 2.1 AA for key elements | Focus states, color contrast, ARIA, alt text. |
| Security       | Client-side token storage  | Secure handling of JWTs                        | Minimize XSS risk; proper logout/expiry handling. |

---

## 🧾 5. Detailed flows / Use cases

> These flows describe both the backend logic and the front-end interaction (UI screens).

### 5.1 Use Case UC1 – User registration and authentication

**Actors:** Unauthenticated client, User, System  
**Goal:** Allow a new user to register and then log in to obtain a JWT.

**Main flow (via frontend):**

1. The frontend shows screen **UI2 – Auth Register** (FFR1).  
2. The client fills out the form and sends `POST /api/v1/auth/register` with email, password, optional name.  
3. The backend:
   - Validates the input (format, email uniqueness).
   - Hashes the password and creates the user record.  
4. The backend returns `201 Created` and optionally a JWT.  
5. The frontend:
   - Updates session state or redirects to login screen **UI1**.  
6. For login:
   - The frontend shows **UI1 – Auth Login**.  
   - The user sends `POST /api/v1/auth/login` with email and password.  
   - The backend verifies credentials and issues a JWT (and optional refresh token).  
   - The frontend stores the JWT and updates auth state; then redirects to **UI4 – Boards Dashboard**.

**Alternative / exception flows:**

- **Alt A1: Email already in use.**  
  - Backend: `409 Conflict`.  
  - Frontend: shows form error message.  

- **Error E1: Invalid email/password format.**  
  - Backend: `400 Bad Request` with details.  
  - Frontend: highlights invalid fields and shows validation messages.  

- **Error E2: Wrong credentials.**  
  - Backend: `401 Unauthorized`.  
  - Frontend: shows generic error (“Invalid credentials”).

**Related requirements:** FR1, FR2, FR4, FR5, FR6, FFR1, FFR2

### 5.2 Use Case UC2 – Board creation → Member addition → List creation → Card creation

**Actors:** Board Owner (User), Board Editor (User), System  
**Goal:** Set up a new board and start managing tasks.

**Main flow (via UI):**

1. **Create board**
   - Logged-in user is on **UI4 – Boards Dashboard**.
   - Clicks “Create Board” → inline or modal form.  
   - Frontend sends `POST /api/v1/boards`.  
   - Backend creates the board with owner = current user and logs activity.  
   - Frontend updates the board list and redirects to **UI5 – Board View (Kanban)**.

2. **Add members and roles**
   - Owner opens the members panel (**UI7 – Members & Roles Management**).  
   - Enters user IDs/emails, selects role (Editor/Viewer).  
   - Frontend sends `POST /api/v1/boards/{boardId}/members`.  
   - Backend validates that the caller is the board owner, updates membership, logs activity, and may send notifications (FR27).  
   - Frontend refreshes the member list.

3. **Create lists**
   - Owner or Editor, from **UI5**, clicks “Add list”.  
   - Frontend sends `POST /api/v1/boards/{boardId}/lists`.  
   - Backend creates the lists, assigns their positions, logs activity.  
   - Frontend displays the new columns in the board view.

4. **Create cards**
   - Owner or Editor, in a list column, clicks “Add card”.  
   - Frontend sends `POST /api/v1/lists/{listId}/cards`.  
   - Backend creates the card with default position and logs activity.  
   - Frontend shows the new card in the list on **UI5**, with card quick view (FFR5).

**Alternative / exception flows:**

- **Alt A1: Viewer tries to create lists or cards.**  
  - Backend: `403 Forbidden`.  
  - Frontend: shows message like “Insufficient permissions” and keeps the board unchanged.

- **Error E1: Board does not exist or user not a member.**  
  - Backend: `404 Not Found` or `403 Forbidden`.  
  - Frontend: redirects to an error page or back to the dashboard.

- **Error E2: Input validation error.**  
  - Backend: `400 Bad Request`.  
  - Frontend: highlights invalid fields and shows validation messages.

**Related requirements:** FR7–FR12, FR15, FR25, FFR3–FFR5

### 5.3 Use Case UC3 – Card assignment and notifications

**Actors:** Board Owner/Editor, Assignee (User), System  
**Goal:** Assign a user to a card and notify them.

**Main flow (via UI):**

1. Owner/Editor opens a card in **UI6 – Card Detail Modal/Panel**.  
2. Selects a user from the board members list as assignee.  
3. Frontend calls `POST /api/v1/cards/{cardId}/assignees`.  
4. Backend validates:
   - Caller has at least Editor role.
   - Assignee is a board member.  
5. Backend:
   - Adds the user to the card’s assignee list.
   - Logs an activity event: “User X assigned user Y to card Z”.
   - Creates a notification record (FR27).  
6. Frontend:
   - Updates the card UI (quick view + detail).
   - Shows a success toast/snackbar.  
7. The assignee sees the notification in **UI10 – Notifications Center**.

**Alternative / exception flows:**

- **Error E1: Assignee is not a board member.**  
  - Backend: `400` or `409` with appropriate message.  
  - Frontend: suggests adding the user as board member first.

- **Error E2: Caller is Viewer.**  
  - Backend: `403 Forbidden`.  
  - Frontend: disables the action and/or shows a permission error message.

**Related requirements:** FR17, FR25, FR27, FFR5, FFR6, FFR12

### 5.4 Use Case UC4 – Activity log retrieval

**Actors:** Board members, System  
**Goal:** View the activity history of a board.

**Main flow (via UI):**

1. User (Owner/Editor/Viewer) opens **UI9 – Activity View** from the board.  
2. Frontend calls `GET /api/v1/boards/{boardId}/activity` with optional filter/pagination parameters.  
3. Backend:
   - Verifies that the user is a member of the board.
   - Retrieves filtered activity entries.  
4. Backend returns a paginated list.  
5. Frontend:
   - Shows a readable timeline.
   - Allows filtering/scrolling/pagination.

**Related requirements:** FR25, FR26, FFR11

---

## 🧩 6. User interface

The end-user UI (boards, lists, cards) is a core part of the project, together with Swagger UI for developers.

### 6.1 Screen overview

| ID   | Screen name                 | Description                                                                                   | Main elements |
|------|----------------------------|-----------------------------------------------------------------------------------------------|--------------|
| UI1  | Auth – Login               | Login screen for registered users.                                                           | Email, password, optional “remember me”, “forgot password” link, Login button. |
| UI2  | Auth – Register            | Registration screen for new users.                                                           | Email, password, confirm password, optional name, Register button, link to login. |
| UI3  | Auth – Forgot/Reset        | Screens for initiating and completing password reset.                                        | Email input, success/error messages, new password + confirm form. |
| UI4  | Boards Dashboard           | View showing the user’s boards.                                                              | Board list/grid, “Create board” button, search, basic filters. |
| UI5  | Board View (Kanban)        | Board view with lists as columns and cards as drag & drop elements.                          | Board header (name, description, members), list columns, card items, quick add, drag & drop. |
| UI6  | Card Detail Modal/Panel    | Card detail as overlay or side panel.                                                        | Title, description, assignees, labels, due date, attachments, comments, activity snippet. |
| UI7  | Members & Roles Management | Section/panel to manage board members.                                                       | Member list, roles (Owner/Editor/Viewer), invite form, buttons to change role/remove. |
| UI8  | Labels Management          | Dialog/panel to create/edit board labels.                                                    | Label list, name+color form, save/delete buttons. |
| UI9  | Activity View              | Activity timeline for the board (or card).                                                   | Event list, filters by user/type/date, pagination or lazy loading. |
| UI10 | Notifications Center       | Global notifications panel (assigned cards, board invites, etc.).                            | Icon with badge, notifications list, links to cards/boards, mark-as-read. |
| UI11 | Profile & Settings         | User profile and personal settings page.                                                     | Name/avatar form, basic preferences (language, date format). |
| UI12 | System / Swagger UI        | Auto-generated UI for backend API documentation.                                             | Endpoints, request/response models, “try out” panel, auth header, etc. |

### 6.2 Mockups / Wireframes

Detailed mockups and wireframes are not included in this document.  
The UI must:

- Follow the Trello-like mental model (board → lists → cards).  
- Support drag & drop interactions for lists and cards.  
- Provide immediate feedback after critical actions (creation, deletion, assignment).  
- Expose notifications and activity in a non-intrusive but easily accessible way.  
- Follow the agreed design system guidelines (colors, typography, components).

Mockups will be provided in a separate document (e.g., Figma, PDF) referenced by this section.

---

## 🔗 7. Integrations & API specification (OpenAPI/Swagger)

### 7.1 External services and modules (backend)

| Service / Module              | Type of integration | Protocol / API      | Notes |
|------------------------------|---------------------|---------------------|-------|
| Database (PostgreSQL/MongoDB) | Sync DB access      | Native drivers      | Main persistence layer for users, boards, lists, cards, etc. |
| Email service (password recovery) | Async or sync    | SMTP / Provider API | Used for password reset emails. |
| Webhooks (optional)          | Async               | HTTP callbacks      | Push events (activity, notifications) to external systems. |
| WebSocket gateway (optional) | Async               | WebSocket           | Real-time updates to connected clients. |
| Monitoring/Logging stack     | Async               | Log collector / metrics | For observability (logs, metrics, traces). |

The frontend integrates with the backend exclusively via:

- REST APIs (OpenAPI/Swagger)
- Optional WebSocket (for real-time notifications and board/card updates).

### 7.2 OpenAPI/Swagger specification

- **OpenAPI version:** 3.0.3 (or higher, e.g., 3.1.0)  
- **Specification location:**
  - File path: `openapi/spec.yaml` (or `.json`)
  - Repository: [Git repository URL or path]

**API base URLs:**

- Production: `https://api.example.com/api/v1`  
- Staging: `https://staging-api.example.com/api/v1`

**Conventions:**

- All endpoints must be defined in the OpenAPI document under appropriate tags:
  - Auth, Users, Boards, Lists, Cards, Comments, Attachments, Activity, Notifications, System.
- Each `operationId` should reference related functional requirement IDs, e.g.:
  - `operationId: registerUser_FR1`
  - `operationId: createBoard_FR7`
- Request/response schemas defined under `components/schemas`, e.g.:
  - `User`, `Board`, `List`, `Card`, `Comment`, `Attachment`, `ActivityEvent`, `Notification`, `ErrorResponse`.

**Authentication:**

- `components/securitySchemes` defines `bearerAuth` with:
  - type: `http`
  - scheme: `bearer`
  - bearerFormat: `JWT`
- Security requirement:
  - Most endpoints use `security: [ { bearerAuth: [] } ]`.

**Error handling:**

- Common error responses defined under `components/responses`:
  - `UnauthorizedError`, `ForbiddenError`, `NotFoundError`, `ValidationError`, etc.
- Each operation documents possible status codes and error payloads.

**Example main endpoints list (non-exhaustive):**

| Endpoint                         | Method | Short description               | Related FRs                          | operationId                |
|----------------------------------|--------|---------------------------------|--------------------------------------|----------------------------|
| `/auth/register`                | POST   | Register a new user             | FR1                                  | `registerUser_FR1`         |
| `/auth/login`                   | POST   | Authenticate and obtain JWT     | FR2                                  | `loginUser_FR2`            |
| `/users/me`                     | GET    | Get current user profile        | FR6                                  | `getCurrentUser_FR6`       |
| `/users/me`                     | PATCH  | Update current user profile     | FR6                                  | `updateCurrentUser_FR6`    |
| `/boards`                       | POST   | Create a board                  | FR7                                  | `createBoard_FR7`          |
| `/boards`                       | GET    | List boards for current user    | FR7, FR10                            | `listBoards_FR7`           |
| `/boards/{boardId}`             | GET    | Get board details               | FR7, FR10                            | `getBoard_FR7`             |
| `/boards/{boardId}`             | PATCH  | Update board                    | FR8                                  | `updateBoard_FR8`          |
| `/boards/{boardId}`             | DELETE | Archive/delete board            | FR9                                  | `archiveBoard_FR9`         |
| `/boards/{boardId}/members`     | POST   | Add board members               | FR10                                 | `addBoardMember_FR10`      |
| `/boards/{boardId}/lists`       | POST   | Create list in board            | FR11                                 | `createList_FR11`          |
| `/lists/{listId}`               | PATCH  | Update list                     | FR12                                 | `updateList_FR12`          |
| `/boards/{boardId}/lists/reorder` | PUT  | Reorder lists                   | FR13                                 | `reorderLists_FR13`        |
| `/lists/{listId}`               | DELETE | Archive/delete list             | FR14                                 | `archiveList_FR14`         |
| `/lists/{listId}/cards`         | POST   | Create card                     | FR15                                 | `createCard_FR15`          |
| `/cards/{cardId}`               | PATCH  | Update card                     | FR16                                 | `updateCard_FR16`          |
| `/cards/{cardId}/move`          | PUT    | Move card / change position     | FR20                                 | `moveCard_FR20`            |
| `/cards/{cardId}/assignees`     | POST   | Assign user to card             | FR17                                 | `assignUser_FR17`          |
| `/cards/{cardId}/comments`      | POST   | Add comment to card             | FR22                                 | `addComment_FR22`          |
| `/comments/{commentId}`         | DELETE | Delete comment                  | FR23                                 | `deleteComment_FR23`       |
| `/cards/{cardId}/attachments`   | POST   | Add attachment metadata         | FR24                                 | `addAttachment_FR24`       |
| `/boards/{boardId}/activity`    | GET    | Get board activity              | FR26                                 | `getBoardActivity_FR26`    |
| `/notifications`                | GET    | Get notifications for current user | FR27                              | `listNotifications_FR27`   |
| `/health`                       | GET    | Basic health check              | FR29                                 | `healthCheck_FR29`         |

---

## 🧰 8. Business rules

| ID   | Name                       | Description |
|------|----------------------------|-------------|
| BR1  | Unique email               | Each user must have a unique email address; duplicates are not allowed. |
| BR2  | Password strength          | Password must meet minimum complexity requirements (length, characters, etc.). |
| BR3  | Board ownership            | The creator of a board is its initial Owner and cannot be removed by others. |
| BR4  | Board visibility           | Only board members (Owner/Editor/Viewer) can see board content and activity. |
| BR5  | Role-based permissions     | Owner: full control; Editor: modify content; Viewer: read-only. |
| BR6  | Card assignment visibility | Only board members can be assigned to cards on that board. |
| BR7  | Archiving rules            | Archiving a board implicitly archives its lists and cards (logical archive). |
| BR8  | Deletion vs archiving      | Hard delete may be restricted to Owner or admins; default is soft archive. |
| BR9  | Activity log immutability  | Activity records cannot be edited or deleted by regular users. |
| BR10 | Due date optionality       | Card due dates are optional; missing due dates must be handled gracefully. |
| BR11 | Rate limiting              | Login attempts per IP/user are limited to prevent brute-force attacks. |
| BR12 | API versioning             | Breaking changes require a new API version (e.g., `/api/v2`). |

---

## 🧪 9. Functional test cases

(Mostly API-level, but also usable for end-to-end testing via the frontend.)

| ID   | Test case name                        | Steps (summary)                                                                                      | Expected result                                                                                 | Related FRs / UCs |
|------|---------------------------------------|------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------|-------------------|
| TC1  | User registration with valid data     | 1. Call POST `/auth/register` with valid email/password. 2. Check response.                         | 201 Created; user stored; optional JWT returned; no duplicate email allowed.                    | FR1, UC1          |
| TC2  | User login with correct credentials   | 1. Register user. 2. Call POST `/auth/login` with same credentials.                                 | 200 OK; JWT returned; token usable on protected endpoints.                                      | FR1, FR2, UC1     |
| TC3  | Create board and verify ownership     | 1. Login as user. 2. Call POST `/boards`. 3. Retrieve board.                                        | Board created with owner = current user; visible in user’s boards list.                         | FR7, FR8, UC2     |
| TC4  | Add member as editor to board         | 1. Owner creates board. 2. Owner calls POST `/boards/{id}/members` with another user as Editor.     | Member added with Editor role; can create lists and cards.                                      | FR10, BR5, UC2    |
| TC5  | Viewer cannot modify board content    | 1. Owner adds user as Viewer. 2. Viewer attempts POST `/boards/{id}/lists`.                         | 403 Forbidden; no list created; board unchanged.                                                | FR10, FR11, BR5   |
| TC6  | Create list and card flow             | 1. Editor creates list. 2. Editor creates card in that list.                                        | List and card created successfully; card associated with the right list/board.                  | FR11, FR15, UC2   |
| TC7  | Move card to another list             | 1. Create two lists. 2. Create card in list1. 3. Call PUT `/cards/{id}/move` to move to list2.      | Card appears in list2 with updated position; activity logged.                                   | FR20, FR25        |
| TC8  | Assign user to card and verify notification | 1. Create card. 2. Call POST `/cards/{id}/assignees` with user ID. 3. Retrieve notifications. | User is added as assignee; notification created and available; activity logged.                | FR17, FR25, FR27, UC3 |
| TC9  | Activity log retrieval for board      | 1. Perform various board/list/card operations. 2. Call GET `/boards/{id}/activity`.                 | Activity list includes entries for all performed actions, in correct order and structure.       | FR25, FR26, UC4   |
| TC10 | Password recovery flow                | 1. Call POST `/auth/password/forgot`. 2. Simulate receiving reset token. 3. Call POST `/auth/password/reset`. | Password reset successfully; user can log in with new password; old password no longer valid. | FR4, FR5, UC1     |

(Additional end-to-end frontend tests may cover responsive layout, drag & drop behavior, UI error states, and accessibility.)

---

## 📅 10. Planning / Release plan

Based on the development roadmap in the PRD.  
The milestones listed are mainly backend-focused; frontend development can proceed in parallel, coordinated with these phases.

| Milestone                  | Description                                         | Target date       | Owner / Role            |
|----------------------------|-----------------------------------------------------|-------------------|-------------------------|
| M1 – Functional analysis completed | This document approved                      | [DD/MM/YYYY]      | Product Owner / Architect |
| M2 – Phase 1              | Architecture setup, authentication (backend + basic auth UI) | +2 weeks          | Backend Team Lead + Frontend Dev |
| M3 – Phase 2              | Board and list management (API + basic board UI)   | +3 weeks after M2 | Backend Devs + Frontend Devs |
| M4 – Phase 3              | Card and comment management (API + card UI + comments) | +3 weeks after M3 | Backend Devs + Frontend Devs |
| M5 – Phase 4              | Activity log and notifications (API + timeline/notifications UI) | +2 weeks after M4 | Backend Devs + Frontend Devs |
| M6 – Phase 5              | Hardening, testing, and deployment (full stack)    | +1 week after M5  | QA & DevOps             |

(Exact dates to be filled in based on project start date.)

---

## ⚠️ 11. Risks and operational notes

| Type         | Description                                                | Impact / Probability | Mitigation strategy |
|--------------|------------------------------------------------------------|----------------------|---------------------|
| Technical    | Insufficient initial scalability under heavy load         | High / Medium        | Design stateless services; enable horizontal scaling and performance testing. |
| Technical    | Security issues with shared board access and permissions  | High / Medium        | Implement strict RBAC (Owner/Editor/Viewer); security tests; code reviews. |
| Technical    | Data inconsistency between boards, lists, and cards       | Medium / Medium      | Use transactions; enforce foreign keys or references; validate inputs. |
| Technical    | Frontend performance and drag & drop lag                  | Medium / Medium      | Optimize rendering, use list virtualization, profiling. |
| UX / Product | Limited features compared to full Trello                  | Medium / High        | Communicate scope clearly; plan future enhancements in roadmap. |
| UX / Product | Poor UX or confusing UI                                   | Medium / Medium      | UX reviews, user testing, refinement of key flows. |
| Organizational | Delays in requirements clarification                    | Medium / Medium      | Regular stakeholder reviews; keep documentation up to date. |
| Operational  | Backup/restore procedures not tested                      | High / Low           | Schedule regular DR tests; document restoration procedures. |

---

## ✅ 12. Approvals

| Name                   | Role        |
|------------------------|------------|
| Luca Sacchi Ricciardi  | CEO        |
| Matteo Fiorio          | Team Leader|
| Tommaso Villa          | Member     |
| Samuele Piazzi         | Member     |
| Filippo Granata        | Member     |
| Samuele Gonnella      | Member     |
| Corinna Buzzi          | Member     |
| Marialia Pero          | Member     |
| Alessandro Cervini     | Member     |
| Gloria Candela         | Member     |

