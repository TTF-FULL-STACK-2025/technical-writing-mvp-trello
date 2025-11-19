Upgrade the following README file based on the attached files

# School Project: Backend for a Trello-like Kanban Application
Status: MVP in development — documentation updated as components are delivered.
\[Foto del progetto quando disponibile]
## Table of contents:
 1. Overview
 2. Documentation
 3. Project Summary
 4. Goals
 5. Team Management
## Overview
Technical Writing Project - Trello-like app fullstack dev with technical documentation

**Current Version:** 1.0  

**Date:** November 19, 2025  

**Team:** Fiorio Matteo, Samuele Piazzi, Samuele Gonnella, Tommaso Villa, Cervini Alessandro, Candela Gloria, Pero Marialia, Granata Filippo, Buzzi Corinna

**Approved by:** Luca Sacchi Ricciardi

This repository contains the full-stack implementation for a scalable, secure, and performant web-based Kanban application, emulating Trello's core features.

This project is part of a school assignment focused on the production of clear and maintainable technical documentation, API design, and collaborative development workflows.  

## Documentation
Full project documentation is available in the `/docs` folder:
 - **Product Requirements Document (PRD)** – overall product scope and requirements
 - **Functional Analysis**: Detailed description of backend REST APIs (FR) and frontend UI flows (FFR), use cases, and business rules.
 - **Technical Analysis / Architecture**: Deep dive into the chosen technology stack, architectural principles (Decoupled, Stateless Backend, Optimistic UI), and DevOps strategy.
Links are to be added when available.
## Project Summary
The application is designed as a Full-Stack System with a completely Decoupled Architecture, communicating exclusively via RESTful APIs.
The MVP includes user accounts, boards, lists, cards, role management, and activity logging.

The MVP includes the following must-have functionalities:
- Boards, Lists, and Cards (Full CRUD and Reordering).
- Authentication (JWT) and Authorization (Role-Based Access Control - RBAC) at the board level.
- Board Membership management (Owner, Editor, Viewer roles).
- Card Details: Assignments, Comments, Due Dates, and Labels.
- Activity Logging (tracking CRUD operations).
- Frontend UX: Intuitive Kanban board with fluid Drag & Drop for cards and lists.

For the full feature list, refer to the PRD in `/docs`.
## Project Goals
 - Provide a scalable, secure, high-performance backend (API call **<300ms**).
- Develop an intuitive Frontend that implements the Kanban paradigm with a smooth **Drag & Drop** experience.
- Expose a complete **RESTful API** well-documented with OpenAPI 3.x / Swagger UI.
## Tech Stack
 - Language: Python
 - Framework: FastAPI
 - Database: PostgreSQL expected
 - Auth: OAuth2/JWT
 - Containerization: Docker
 - API Docs: OpenAPI/Swagger (auto-generated)
## Team Management

| Name                  | Role        |
| --------------------- | ----------- |
| Luca Sacchi Ricciardi | CEO         |
| Matteo Fiorio         | Team Leader |
| Tommaso Villa         | Member      |
| Samuele Piazzi        | Member      |
| Filippo Granata       | Member      |
| Samuele Gonnella      | Member      |
| Corinna Buzzi         | Member      |
| Marialia Pero         | Member      |
| Alessandro Cervini    | Member      |
| Gloria Candela        | Member      |
