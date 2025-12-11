# 🗂️ School Project: Full-Stack Kanban Application (Trello-clone)

Status: MVP in development — documentation updated as components are delivered.



## Table of Contents
1. Overview
2. Documentation
3. Project Summary (Hybrid Architecture)
4. Goals
5. Tech Stack
6. Team Management

---

## 1. Overview 🚀

Technical Writing Project - Full-stack development of a Kanban application (Trello-like) with a focus on producing technical documentation.

**Current Version:** 1.2

**Date:** December 3, 2025

**Team:** Fiorio Matteo, Samuele Piazzi, Samuele Gonnella, Tommaso Villa, Cervini Alessandro, Candela Gloria, Pero Marialia, Granata Filippo, Buzzi Corinna

**Approved by:** Luca Sacchi Ricciardi

This repository contains the **Hybrid Full-Stack Implementation (PHP SSR + JavaScript AJAX)** for a performant and scalable Kanban platform that emulates Trello's core functionalities. The project is a school assignment focused on clear documentation, API design, and collaborative development workflows.

---

## 2. Documentation 📑

* **Product Requirements Document (PRD):** Overall product scope and requirements (**V. 1.2**).
* **Technical Analysis / Architecture:** Details on the **PHP/MySQL** technology stack, architectural principles (**Hybrid SSR/AJAX**), security, and development plan (**V. 1.2**).
* **Functional Analysis:** Detailed description of use cases and business rules.

---

## 3. Project Summary (Hybrid Architecture) 🏗️

The application is designed as a **Hybrid System** that utilizes **Server-Side Rendering (SSR) in PHP** for initial loading and **Vanilla JavaScript** and **AJAX** calls for all dynamic interactions, such as Drag & Drop and opening modals.

The MVP (Minimum Viable Product) includes the following core functionalities:

* **Boards, Lists, and Cards:** Full CRUD (Create, Read, Update, Delete) and Reordering.
* **Card Movement:** Smooth Drag & Drop interaction with position updates via `update_card_position.php`.
* **Authentication and Authorization (To be implemented):** Based on **PHP Sessions** and Role-Based Access Control (**RBAC**).
* **Member Management (To be implemented):** `Owner`, `Editor`, and `Viewer` roles at the board level.
* **Card Details (Expanding - To be implemented):** Support for assignees, comments, due dates, and labels.
* **Activity Log (To be implemented):** Tracking of changes on Boards, Lists, and Cards.

---

## 4. Goals 🎯

* Develop a **robust and secure** backend based on PHP/MySQL, with protection ensured by **PDO (Prepared Statements)**.
* Ensure high performance: **Average AJAX Response Time $<300$ms**.
* Develop an intuitive Frontend that implements the Kanban paradigm with a fluid **Drag & Drop** experience, managed entirely in **Vanilla JS**.
* Implement a basic **Internationalization (i18n)** system.

---

## 5. Tech Stack 💻

The architecture is based on a modified LAMP stack for dynamic interactions:

| Component | Technology | Role |
|:---|:---|:---|
| **Backend Language** | **PHP** | Business logic, SSR rendering, and management of AJAX endpoints. |
| **Database** | **MySQL** | Persistent data storage. |
| **DB Driver** | **PDO** | Secure connections and SQL Injection prevention. |
| **Frontend** | **Vanilla JavaScript** | Dynamic logic (Drag & Drop, Modals) and Fetch/AJAX calls. |
| **Authentication** | **PHP Sessions** | Server-side state and authentication management (To be implemented). |
| **Containerization** | **Docker** | Consistent development and production environments. |
| **API Docs** | Manual | Documentation of AJAX endpoints (lack of auto-generation). |


---

## 6. Team Task 👥

| Name | Task |
|:---|:---|
| **Matteo Fiorio** | **Senior Developer** |
| Corinna Buzzi | ReadMe Document |
| Nicole Caravello | ReadMe Document |
| Filippo Granata | PRD Document |
| Samuele Gonnella | PRD Document |
| Gloria Candela | PRD Document |
| Marialia Pero | Functional Analysis Document |
| Alessandro Cervini | Functional Analysis Document |
| Tommaso Villa | Technical Analysis Document |
| Samuele Piazzi | Technical Analysis Document |

---

## 7. Team Management 👥

| Name | Role |
|:---|:---|
| Luca Sacchi Ricciardi | CEO |
| **Matteo Fiorio** | **Team Leader** |
| Tommaso Villa | Member |
| Samuele Piazzi | Member |
| Filippo Granata | Member |
| Samuele Gonnella | Member |
| Corinna Buzzi | Member |
| Marialia Pero | Member |
| Alessandro Cervini | Member |
| Gloria Candela | Member |
| Nicole Caravello | Member |