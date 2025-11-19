# Functional Analysis – Backend Trello-like System

Project title: **Core Backend For Project Management Platform (trello-like)**  
Version: **1.0**  
Authors: **Team Work**  
Date: **19/11/2025**  
Approved by: **Luca Sacchi Ricciardi**

---

## 🧭 1. Introduction

### 1.1 Purpose of the document

This document describes the **functional analysis** of the *Backend Trello-like System*, specifying objectives, functionalities, technical requirements, and main flows.

It aims to:

- Define the main goals of the system.
- Clarify the expected behavior of the backend and its REST APIs.
- Provide a shared reference for **implementation**, **testing**, and **maintenance**.
- Ensure alignment with **OpenAPI/Swagger** standards for API design and documentation.

### 1.2 Context

- **Project type:** Backend service / REST API
- **Target platforms:**
  - Frontend clients (to be implemented later): web app, mobile apps, or integrations.
  - External systems via API or webhooks (optional).
- **Business need / origin of request:**
  - Provide a **backend-only system** with core Trello-like functionality to manage:
    - Users
    - Boards
    - Lists
    - Cards (tasks)
    - Comments
    - Labels, due dates, and activity logs
  - System must be **scalable, secure, and high-performing**, ready for future UI and integrations.

- **References:**
  - Trello basic features (boards, lists, cards, collaboration).
  - OpenAPI 3.x and Swagger UI documentation.
  - JWT-based authentication best practices.

### 1.3 Main objectives

- Provide a **scalable, secure, and high-performance backend** for a Trello-like application.
- Support all **core features** of Trello’s basic version:
  - Boards, lists, cards
  - User management and sharing
  - Comments, labels, due dates, activity logging.
- Expose a **complete RESTful API** documented with **OpenAPI/Swagger**, including:
  - Versioning (e.g., `/api/v1`)
  - JWT-based authentication
- Enable **user management, authentication, authorization, and collaboration** on shared boards.
- Provide **basic notifications** (e.g., card assignment) and **activity tracking**.

---

## 🧩 2. General system description

### 2.1 Application context

- **Type of application:**
  - Backend-only, RESTful API (headless architecture).
- **Execution environment:**
  - Server-side application running on:
    - Cloud infrastructure (preferred),
    - Containerized with Docker and orchestrated (e.g., Kubernetes).
- **External systems:**
  - Database (PostgreSQL or MongoDB).
  - Email service or similar for password recovery (SMTP or external provider).
  - Optional webhooks / WebSocket gateway for real-time updates.
- **External interfaces:**
  - **Client-side (future):**
    - Web browser (SPA using JS frameworks).
    - Mobile apps or other API clients.
  - **Server-side / APIs:**
    - REST APIs defined with **OpenAPI/Swagger**.
    - Optional WebSocket endpoints or webhook endpoints for real-time notifications.
- **User input:**
  - API calls from authenticated or anonymous clients using HTTP(S) with JSON payloads.

### 2.2 Actors

| Actor        | Description                                              | Permissions / Role                                                                 |
|--------------|----------------------------------------------------------|------------------------------------------------------------------------------------|
| Unauthenticated client | Client not logged in                          | Can register a new user, login, and request password recovery flows.              |
| **User**     | Registered user with an account                          | Can create and manage boards, lists, cards, comments, labels according to role.   |
| **Board Owner** | User who created a board or received ownership       | Full control over the board, its lists, cards, members, and settings.             |
| **Board Editor** | User invited to a board with edit rights            | Can modify content (lists, cards, comments, labels) but not board ownership.      |
| **Board Viewer** | User invited to a board with read-only rights       | Can view board, lists, cards, comments, but cannot modify content.                |
| **System**   | Internal backend components                              | Handles authentication, authorization, persistence, activity logging, notifications.|

### 2.3 High-level flow

1. Client (frontend or integration) calls the **API** (e.g., `/api/v1/auth/register`) to register or login.
2. System validates request, processes business logic, and issues a **JWT** upon successful authentication.
3. Authenticated client includes the JWT as **Bearer token** in subsequent requests.
4. User:
   - Creates boards, invites members, sets roles.
   - Creates lists within boards.
   - Creates and manages cards within lists (move, reorder, assign users, due dates, labels).
5. System:
   - Persists all operations in the database.
   - Logs activity (board/list/card CRUD, comments, assignments).
   - Triggers basic notifications (e.g., card assigned).
6. Optional:
   - System pushes events to **webhooks** or **WebSocket** channels for real-time updates.
7. In case of errors (validation, auth, permissions, system error), the API returns appropriate HTTP status codes and error payloads consistent with OpenAPI definitions.

---

## 🧠 3. Detailed functional requirements

> Note: All externally exposed operations must be aligned with the OpenAPI/Swagger specification.  
> Each functional requirement (FR) should map to one or more `operationId` entries.

### 3.1 Functional requirements table

| ID  | Function name                         | Description                                                                 | Input (API request)                                                                | Output (Response / state change)                                                                                           | Priority | Dependencies          |
|-----|---------------------------------------|-----------------------------------------------------------------------------|------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------|----------|-----------------------|
| FR1 | User registration                     | Create a new user account with email and password.                         | `POST /api/v1/auth/register` with email, password, optional name.                  | 201 Created; user resource + JWT or login link; validation errors on invalid data.                                         | Must     | –                     |
| FR2 | User login                            | Authenticate user and return JWT.                                          | `POST /api/v1/auth/login` with email and password.                                 | 200 OK; JWT access token (and optional refresh token).                                                                     | Must     | FR1                   |
| FR3 | User logout                           | Invalidate active session/token (if token blacklist or similar).           | `POST /api/v1/auth/logout` with token in Authorization header.                     | 204 No Content or 200 OK; token invalidation effect server-side if implemented.                                            | Should   | FR2                   |
| FR4 | Password recovery initiation          | Start password reset flow.                                                 | `POST /api/v1/auth/password/forgot` with email.                                    | 202 Accepted; email sent or generic response; audit log entry.                                                             | Must     | FR1, Email service    |
| FR5 | Password reset                        | Reset password using a provided token.                                     | `POST /api/v1/auth/password/reset` with token + new password.                      | 200 OK; password updated; JWT optionally issued.                                                                           | Must     | FR4                   |
| FR6 | Profile editing                       | Edit user profile (name, avatar).                                          | `PUT/PATCH /api/v1/users/me` with name, avatar URL, etc.                           | 200 OK; updated user profile.                                                                                              | Should   | FR2                   |
| FR7 | Create board                          | Create a new board owned by the user.                                      | `POST /api/v1/boards` with name, optional description.                             | 201 Created; board resource with owner = current user.                                                                     | Must     | FR2                   |
| FR8 | Edit board                            | Update board name and description.                                         | `PUT/PATCH /api/v1/boards/{boardId}`                                               | 200 OK; updated board resource.                                                                                            | Must     | FR7                   |
| FR9 | Archive/delete board                  | Archive or soft-delete a board.                                            | `DELETE /api/v1/boards/{boardId}` or `POST /api/v1/boards/{id}/archive`           | 200/204 OK; board marked archived or deleted; activity log entry.                                                         | Must     | FR7, FR16             |
| FR10| Manage board members                  | Add, remove, or update member roles (owner/editor/viewer).                 | `POST/PUT/DELETE /api/v1/boards/{boardId}/members`                                 | 200/201/204; updated board membership; notifications and activity log triggered.                                           | Must     | FR7, FR17             |
| FR11| Create list                           | Create a list within a board.                                              | `POST /api/v1/boards/{boardId}/lists` with name and optional position.             | 201 Created; list resource associated with board.                                                                          | Must     | FR7                   |
| FR12| Edit list                             | Edit list name.                                                            | `PUT/PATCH /api/v1/lists/{listId}`                                                 | 200 OK; updated list resource.                                                                                             | Must     | FR11                  |
| FR13| Reorder lists                         | Change the order of lists within a board.                                  | `PUT /api/v1/boards/{boardId}/lists/reorder` with new order/positions.             | 200 OK; list positions updated.                                                                                            | Should   | FR11                  |
| FR14| Archive/delete list                   | Archive or delete a list.                                                  | `DELETE /api/v1/lists/{listId}` or archive endpoint.                               | 200/204; list archived/deleted; cards affected according to rules; activity logged.                                        | Should   | FR11, FR16            |
| FR15| Create card                           | Create a card within a list.                                               | `POST /api/v1/lists/{listId}/cards` with title, optional description, labels, etc. | 201 Created; card resource with default position in list.                                                                  | Must     | FR11                  |
| FR16| Edit card                             | Edit card title and description.                                           | `PUT/PATCH /api/v1/cards/{cardId}`                                                 | 200 OK; updated card resource.                                                                                             | Must     | FR15                  |
| FR17| Assign users to card                  | Assign/unassign users to/from a card.                                      | `POST/DELETE /api/v1/cards/{cardId}/assignees`                                     | 200/201/204; updated card assignee list; notification triggered to assignees.                                              | Must     | FR2, FR15, FR20       |
| FR18| Manage due date                       | Add/update/remove due date of a card.                                      | `PUT/PATCH /api/v1/cards/{cardId}/due-date`                                        | 200 OK; card due date updated; activity log updated.                                                                       | Should   | FR15                  |
| FR19| Manage labels                         | Add/remove labels on a card (and optionally define label set per board).   | `POST/DELETE /api/v1/cards/{cardId}/labels` and `POST /boards/{id}/labels`         | 200/201/204; updated label assignments; label metadata persisted.                                                         | Should   | FR7, FR15             |
| FR20| Move/reorder cards                    | Reorder cards within a list or move between lists.                         | `PUT /api/v1/cards/{cardId}/move` with new listId and position.                    | 200 OK; card moved; list/card positions updated; activity logged.                                                          | Must     | FR11, FR15            |
| FR21| Archive/delete card                   | Archive or delete a card.                                                  | `DELETE /api/v1/cards/{cardId}` or archive endpoint.                               | 200/204; card archived/deleted; activity logged.                                                                           | Must     | FR15, FR24            |
| FR22| Add comment                           | Add a comment to a card.                                                   | `POST /api/v1/cards/{cardId}/comments` with text.                                  | 201 Created; comment resource; activity log updated.                                                                       | Must     | FR15                  |
| FR23| Delete comment                        | Delete an existing comment.                                                | `DELETE /api/v1/comments/{commentId}`                                              | 204 No Content; activity logged.                                                                                           | Should   | FR22                  |
| FR24| Attachments metadata                  | Add attachment metadata to card (no physical storage in scope).            | `POST /api/v1/cards/{cardId}/attachments` with name, URL, size, mime type.         | 201 Created; attachment metadata persisted; activity logged.                                                               | Could    | FR15                  |
| FR25| Activity logging                      | Track CRUD operations on boards/lists/cards/comments/attachments.          | Internal on each related API call.                                                 | Activity entries stored (e.g., `action`, `actor`, `target`, timestamp).                                                    | Must     | Many FRs (7–24)       |
| FR26| Activity retrieval                    | Retrieve activity log for a board/card/user.                               | `GET /api/v1/boards/{boardId}/activity` etc.                                       | 200 OK; list of activity events with pagination.                                                                           | Should   | FR25                  |
| FR27| Basic notifications                   | Trigger basic notifications on significant events (e.g., card assignment). | Internal or via `/notifications` endpoints; possibly webhooks or push channels.    | Notification records created; optional push via WebSocket/webhook/email.                                                  | Should   | FR17, FR18, FR25      |
| FR28| API documentation                     | Expose interactive Swagger UI for all endpoints.                           | `GET /api-docs` or similar.                                                        | OpenAPI UI served, always synchronized with code annotations or spec file.                                                 | Must     | All FRs               |
| FR29| Health check & status                 | Provide basic health endpoints for monitoring.                             | `GET /health`, `GET /status`                                                       | 200 OK with health information; used by orchestrator/load balancer.                                                        | Must     | –                     |

You can further split FRs into subsections (User, Boards, Lists, Cards, etc.) in the OpenAPI spec using tags.

---

## ⚙️ 4. Non-functional requirements

| Type          | Description                                                | Target / Value                                          | Notes                                                                 |
|---------------|------------------------------------------------------------|---------------------------------------------------------|-----------------------------------------------------------------------|
| Performance   | API latency                                                | ≤ 300 ms for 95% of requests under normal load         | Includes DB operations; excludes network latency outside platform.    |
| Scalability   | Horizontal scalability                                     | Support horizontal scaling of API instances             | Stateless services with shared DB/cache; suitable for containerization.|
| Security      | Authentication and authorization                           | JWT Bearer, password hashing (e.g. bcrypt/argon2)       | Enforce HTTPS; role-based access control (Owner/Editor/Viewer).       |
| Security      | Rate limiting                                              | Rate limit per IP/token                                 | Protect from brute-force attacks and abuse.                           |
| Reliability   | Uptime target                                              | ≥ 99.5%                                                 | Excludes scheduled maintenance; supported by redundancy.              |
| Reliability   | Backups                                                    | Regular automated backups                               | Configurable schedule; tested recovery process.                       |
| Observability | Logging and monitoring                                     | Centralized logs, metrics, alerts                       | Include API latency, error rates, DB health; structured logs.         |
| Compatibility | Client compatibility                                       | REST over HTTPS, JSON format                            | Standard HTTP verbs and status codes.                                |
| API Docs      | OpenAPI/Swagger                                            | OpenAPI 3.x compliant                                   | Documented endpoints, schemas, and errors; Swagger UI available.      |
| Maintainability| Code structure & documentation                            | Modular architecture, clear layering                    | Separation of domain, persistence, and transport layers.              |
| Data privacy  | Protection of user data                                    | Encrypted at rest and in transit                        | Passwords always hashed; secrets stored securely.                     |

---

## 🧾 5. Detailed flows / Use cases

### 5.1 Use Case UC1 – User registration and authentication

- **Actors:** Unauthenticated client, User, System
- **Goal:** Allow a new user to register and then login to obtain a JWT.

**Main flow:**

1. Client calls `POST /api/v1/auth/register` with email, password, and optional name.
2. System validates input (format, uniqueness of email).
3. System hashes password and creates user record.
4. System returns 201 Created and may return a JWT or require separate login.
5. Client calls `POST /api/v1/auth/login` with email and password.
6. System verifies credentials and issues JWT (and optional refresh token).
7. Client stores JWT and uses it in `Authorization: Bearer <token>` for subsequent calls.

**Alternative / exception flows:**

- **Alt A1:** Email already in use.
  - System returns 409 Conflict with appropriate error code.
- **Error E1:** Invalid email/password format.
  - System returns 400 Bad Request with validation details.
- **Error E2:** Wrong credentials.
  - System returns 401 Unauthorized with generic error message.

**Related requirements:** FR1, FR2, FR4, FR5, FR6

---

### 5.2 Use Case UC2 – Board creation → Member addition → List creation → Card creation

- **Actors:** Board Owner (User), Board Editor (User), System
- **Goal:** Set up a new board and start managing tasks.

**Main flow:**

1. **Create board**
   - User calls `POST /api/v1/boards` with board name/description.
   - System creates board with `owner = current user` and returns the board.

2. **Add members and roles**
   - Owner calls `POST /api/v1/boards/{boardId}/members` with user IDs and role (editor/viewer).
   - System validates that caller is board owner.
   - System adds or updates membership and logs activity.
   - System optionally sends notifications to invited members.

3. **Create lists**
   - Owner or Editor calls `POST /api/v1/boards/{boardId}/lists` with list name and position.
   - System creates lists within the board and returns them.

4. **Create cards**
   - Owner or Editor calls `POST /api/v1/lists/{listId}/cards` with title, optional description.
   - System creates card with default position and logs activity.
   - System returns card details.

**Alternative / exception flows:**

- **Alt A1:** Viewer tries to create lists or cards.
  - System returns 403 Forbidden (insufficient permissions).
- **Error E1:** Board does not exist or user not a member.
  - System returns 404 Not Found or 403 Forbidden.
- **Error E2:** Input validation error (missing title, too long strings, etc.).
  - System returns 400 Bad Request with validation error details.

**Related requirements:** FR7, FR8, FR10, FR11, FR12, FR15, FR25

---

### 5.3 Use Case UC3 – Card assignment and notifications

- **Actors:** Board Owner/Editor, Assignee (User), System
- **Goal:** Assign a user to a card and notify them.

**Main flow:**

1. Owner/Editor calls `POST /api/v1/cards/{cardId}/assignees` with user ID.
2. System validates:
   - Caller has at least Editor role on the board.
   - Assignee is a board member (Owner/Editor/Viewer).
3. System adds user to the card’s assignee list.
4. System logs an activity event: “User X assigned user Y to card Z”.
5. System triggers a notification:
   - Create notification record or message.
   - Optionally send push (webhook/WebSocket/email).

**Alternative / exception flows:**

- **Error E1:** Assignee is not a board member.
  - System returns 400 or 409 with appropriate message.
- **Error E2:** Caller is Viewer.
  - System returns 403 Forbidden.

**Related requirements:** FR17, FR25, FR27

---

### 5.4 Use Case UC4 – Activity log retrieval

- **Actors:** Board members, System
- **Goal:** View the activity history of a board.

**Main flow:**

1. User (Owner/Editor/Viewer) calls `GET /api/v1/boards/{boardId}/activity` with pagination parameters.
2. System verifies that user is a member of the board.
3. System retrieves activity entries (filterable by time range, actor, or resource).
4. System returns a paginated list of activity records.

**Related requirements:** FR25, FR26

---

## 🧩 6. User interface (if applicable)

> UI is **out of scope** for this project. Only backend APIs are implemented.  
> However, the system will provide **Swagger UI** for API exploration.

### 6.1 Screen overview

| ID  | Screen name     | Description                                  | Main elements                               |
|-----|-----------------|----------------------------------------------|---------------------------------------------|
| UI1 | Swagger UI      | Auto-generated API documentation and console| API endpoints list, schemas, try-out panel. |

### 6.2 Mockups / Wireframes

- UI for end-users (boards, lists, cards UI) is **not part of this backend project**.
- Only Swagger UI (and possibly minimal admin/debug pages) are available.

---

## 🔗 7. Integrations & API specification (OpenAPI/Swagger)

### 7.1 External services and modules

| Service / Module          | Type of integration | Protocol / API         | Notes                                                         |
|---------------------------|---------------------|------------------------|---------------------------------------------------------------|
| Database (PostgreSQL/MongoDB) | Sync DB access   | Native drivers         | Main persistence layer for users, boards, lists, cards, etc. |
| Email service (password recovery) | Async or sync | SMTP / Provider API    | Used for password reset emails.                              |
| Webhooks (optional)       | Async               | HTTP callbacks         | Push events (activity, notifications) to external systems.    |
| WebSocket gateway (optional) | Async            | WebSocket              | Real-time updates to connected clients.                       |
| Monitoring/Logging stack  | Async               | Log collector / metrics| For observability (logs, metrics, traces).                    |

### 7.2 OpenAPI/Swagger specification

- **OpenAPI version:** 3.0.3 (or higher, e.g., 3.1.0)
- **Specification location:**
  - File path: `openapi/spec.yaml` (or `.json`)
  - Repository: `[Git repository URL or path]`
- **API base URLs:**
  - Production: `https://api.example.com/api/v1`
  - Staging: `https://staging-api.example.com/api/v1`

**Conventions:**

- All endpoints must be defined in the **OpenAPI document** under appropriate tags (`Auth`, `Users`, `Boards`, `Lists`, `Cards`, `Comments`, `Attachments`, `Activity`, `Notifications`, `System`).
- Each `operationId` should reference related functional requirement IDs, e.g.:
  - `operationId: registerUser_FR1`
  - `operationId: createBoard_FR7`
- Request/response schemas must be defined under `components/schemas`, e.g.:
  - `User`, `Board`, `List`, `Card`, `Comment`, `Attachment`, `ActivityEvent`, `Notification`, `ErrorResponse`.
- Authentication:
  - `components/securitySchemes` must define `bearerAuth` with type `http`, scheme `bearer`, bearerFormat `JWT`.
  - Security requirement: most endpoints use `security: [ { bearerAuth: [] } ]`.
- Error handling:
  - Common error responses defined under `components/responses` (e.g., `UnauthorizedError`, `ForbiddenError`, `NotFoundError`, `ValidationError`).
  - Each operation documents possible status codes and error payloads.

**Example main endpoints list (non-exhaustive):**

| Endpoint                                   | Method | Short description                       | Related FRs          | OpenAPI section (operationId)      |
|-------------------------------------------|--------|-----------------------------------------|----------------------|------------------------------------|
| `/auth/register`                          | POST   | Register a new user                     | FR1                  | `registerUser_FR1`                 |
| `/auth/login`                             | POST   | Authenticate and obtain JWT             | FR2                  | `loginUser_FR2`                    |
| `/users/me`                               | GET    | Get current user profile                | FR6                  | `getCurrentUser_FR6`               |
| `/users/me`                               | PATCH  | Update current user profile             | FR6                  | `updateCurrentUser_FR6`            |
| `/boards`                                 | POST   | Create a board                          | FR7                  | `createBoard_FR7`                  |
| `/boards`                                 | GET    | List boards for current user            | FR7, FR10            | `listBoards_FR7`                   |
| `/boards/{boardId}`                       | GET    | Get board details                       | FR7, FR10            | `getBoard_FR7`                     |
| `/boards/{boardId}`                       | PATCH  | Update board                            | FR8                  | `updateBoard_FR8`                  |
| `/boards/{boardId}`                       | DELETE | Archive/delete board                    | FR9                  | `archiveBoard_FR9`                 |
| `/boards/{boardId}/members`               | POST   | Add board members                       | FR10                 | `addBoardMember_FR10`              |
| `/boards/{boardId}/lists`                 | POST   | Create list in board                    | FR11                 | `createList_FR11`                  |
| `/lists/{listId}`                         | PATCH  | Update list                             | FR12                 | `updateList_FR12`                  |
| `/boards/{boardId}/lists/reorder`         | PUT    | Reorder lists                           | FR13                 | `reorderLists_FR13`                |
| `/lists/{listId}`                         | DELETE | Archive/delete list                     | FR14                 | `archiveList_FR14`                 |
| `/lists/{listId}/cards`                   | POST   | Create card                             | FR15                 | `createCard_FR15`                  |
| `/cards/{cardId}`                         | PATCH  | Update card                             | FR16                 | `updateCard_FR16`                  |
| `/cards/{cardId}/move`                    | PUT    | Move card / change position             | FR20                 | `moveCard_FR20`                    |
| `/cards/{cardId}/assignees`               | POST   | Assign user to card                     | FR17                 | `assignUser_FR17`                  |
| `/cards/{cardId}/comments`                | POST   | Add comment to card                     | FR22                 | `addComment_FR22`                  |
| `/comments/{commentId}`                   | DELETE | Delete comment                          | FR23                 | `deleteComment_FR23`               |
| `/cards/{cardId}/attachments`             | POST   | Add attachment metadata                 | FR24                 | `addAttachment_FR24`               |
| `/boards/{boardId}/activity`              | GET    | Get board activity                      | FR26                 | `getBoardActivity_FR26`            |
| `/notifications`                          | GET    | Get notifications for current user      | FR27                 | `listNotifications_FR27`           |
| `/health`                                 | GET    | Basic health check                      | FR29                 | `healthCheck_FR29`                 |

---

## 🧰 8. Business rules

| ID   | Name                                 | Description                                                                 |
|------|--------------------------------------|-----------------------------------------------------------------------------|
| BR1  | Unique email                         | Each user must have a unique email address; duplicates are not allowed.    |
| BR2  | Password strength                    | Password must meet minimal complexity (length, characters, etc.).          |
| BR3  | Board ownership                      | The creator of a board is its initial Owner and cannot be removed by others.|
| BR4  | Board visibility                     | Only board members (Owner/Editor/Viewer) can see board content and activity.|
| BR5  | Role-based permissions               | Owner: full control; Editor: modify content; Viewer: read-only.            |
| BR6  | Card assignment visibility           | Only board members can be assigned to cards on that board.                 |
| BR7  | Archiving rules                      | Archiving a board implicitly archives its lists and cards (logically).     |
| BR8  | Deletion vs archiving                | Hard delete may be restricted to Owner or admins; default is soft archive. |
| BR9  | Activity log immutability            | Activity records cannot be edited or deleted by regular users.             |
| BR10 | Due date optionality                 | Card due dates are optional; missing due date must be handled gracefully.  |
| BR11 | Rate limiting                        | Login attempts per IP/user are limited to prevent brute-force attacks.     |
| BR12 | API versioning                       | Breaking changes require a new API version (e.g., `/api/v2`).              |

---

## 🧪 9. Functional test cases

| ID   | Test case name                                   | Steps (summary)                                                                                                 | Expected result                                                                                     | Related FRs / UCs             |
|------|--------------------------------------------------|------------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------|--------------------------------|
| TC1  | User registration with valid data                | 1. Call `POST /auth/register` with valid email/password.                                                        2. Check response. | 201 Created; user stored; optional JWT returned; no duplicate email allowed.                       | FR1, UC1                      |
| TC2  | User login with correct credentials             | 1. Register user. 2. Call `POST /auth/login` with same credentials.                                             | 200 OK; JWT returned; token usable on protected endpoints.                                          | FR1, FR2, UC1                 |
| TC3  | Create board and verify ownership               | 1. Login as user. 2. Call `POST /boards`. 3. Retrieve board.                                                    | Board created with owner = current user; visible in user’s boards list.                            | FR7, FR8, UC2                 |
| TC4  | Add member as editor to board                   | 1. Owner creates board. 2. Owner calls `POST /boards/{id}/members` with another user as Editor.                 | Member added with Editor role; can create lists and cards.                                          | FR10, BR5, UC2                |
| TC5  | Viewer cannot modify board content              | 1. Owner adds user as Viewer. 2. Viewer attempts `POST /boards/{id}/lists`.                                    | 403 Forbidden; no list created; board unchanged.                                                    | FR10, FR11, BR5               |
| TC6  | Create list and card flow                       | 1. Editor creates list. 2. Editor creates card in that list.                                                    | List and card created successfully; card associated with the right list/board.                     | FR11, FR15, UC2               |
| TC7  | Move card to another list                       | 1. Create two lists. 2. Create card in list1. 3. Call `PUT /cards/{id}/move` to move to list2.                   | Card appears in list2 with updated position; activity logged.                                      | FR20, FR25                    |
| TC8  | Assign user to card and verify notification     | 1. Create card. 2. Call `POST /cards/{id}/assignees` with user ID. 3. Retrieve notifications.                   | User is added as assignee; notification created and available; activity logged.                     | FR17, FR25, FR27, UC3         |
| TC9  | Activity log retrieval for board                | 1. Perform various board/list/card operations. 2. Call `GET /boards/{id}/activity`.                             | Activity list includes entries for all performed actions, in correct order and structure.           | FR25, FR26, UC4               |
| TC10 | Password recovery flow                          | 1. Call `POST /auth/password/forgot`. 2. Simulate receiving reset token. 3. Call `POST /auth/password/reset`.   | Password reset successfully; user can login with new password; old password no longer valid.       | FR4, FR5, UC1                 |

---

## 📅 10. Planning / Release plan

> Based on the development roadmap in the PRD.

| Milestone                             | Description                                          | Target date     | Owner / Role          |
|--------------------------------------|------------------------------------------------------|-----------------|-----------------------|
| M1 – Functional analysis completed   | This document approved                               | [DD/MM/YYYY]    | Product Owner / Architect |
| M2 – Phase 1                         | Architecture setup, authentication                   | +2 weeks        | Backend Team Lead     |
| M3 – Phase 2                         | Board and list management                            | +3 weeks after M2 | Backend Devs       |
| M4 – Phase 3                         | Card and comment management                          | +3 weeks after M3 | Backend Devs       |
| M5 – Phase 4                         | Activity log and notifications                       | +2 weeks after M4 | Backend Devs       |
| M6 – Phase 5                         | Hardening, testing, and deployment                   | +1 week after M5 | QA & DevOps           |

(Exact dates to be filled based on project start date.)

---

## ⚠️ 11. Risks and operational notes

| Type          | Description                                                   | Impact / Probability      | Mitigation strategy                                                   |
|---------------|---------------------------------------------------------------|---------------------------|------------------------------------------------------------------------|
| Technical     | Insufficient initial scalability under heavy load             | High / Medium             | Design stateless services; enable horizontal scaling and performance testing. |
| Technical     | Security issues with shared board access and permissions      | High / Medium             | Implement strict RBAC (Owner/Editor/Viewer); unit/integration security tests; code reviews. |
| Technical     | Data inconsistency between boards, lists, and cards          | Medium / Medium           | Use transactions where possible; enforce foreign keys or references; validate inputs. |
| UX / Product  | Limited features compared to full Trello (no power-ups, automations) | Medium / High       | Clearly communicate scope; plan future enhancements in roadmap.       |
| Organizational| Delays in requirements clarification                          | Medium / Medium           | Regular stakeholder reviews; maintain living documentation.           |
| Operational   | Backup/restore procedures not tested                          | High / Low                | Schedule regular DR tests; document restoration procedure.            |

---

## ✅ 12. Approvals

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

---
