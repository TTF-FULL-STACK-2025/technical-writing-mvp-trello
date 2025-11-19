# TeamTrello Backend (FastAPI)

This directory contains a FastAPI backend implementing a Trello-like system as defined in the PRD, Technical Analysis, and Functional Analysis.

- Runs on port `3000` (backend)
- REST API under `/api/v1/...`
- JWT authentication and Board-level RBAC
- PostgreSQL via SQLAlchemy
- Auto-generated OpenAPI docs available at `/docs`

## Quickstart (Dev)

- Ensure Docker is installed
- Copy `.env.example` to `.env` and adjust `DATABASE_URL`, `JWT_SECRET`
- Start services:

```
cd backend
docker compose up --build
```

- Open `http://localhost:3000/docs` for API exploration

## Core Endpoints

- `POST /api/v1/auth/register` – Register user
- `POST /api/v1/auth/login` – Login and receive JWT
- `GET /api/v1/boards` – List boards for current user
- `POST /api/v1/boards` – Create board (current user becomes owner)
- `POST /api/v1/boards/{boardId}/members` – Add member with role
- `POST /api/v1/boards/{boardId}/lists` – Create list in board
- `POST /api/v1/lists/{listId}/cards` – Create card in list
- `PUT /api/v1/cards/{cardId}/move` – Move card between lists
- `POST /api/v1/cards/{cardId}/comments` – Add comment to card
- `GET /api/v1/boards/{boardId}/activity` – View activity log

## Notes

- For production, use Alembic for migrations and secure secrets management.
- CORS is configured to allow `teamtrello.lab.home.lucasacchi.net:8181` and localhost clients.