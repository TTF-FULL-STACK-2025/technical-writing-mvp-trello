"""
FastAPI entrypoint for TeamTrello backend (Trello-like system).

Aligns with PRD/Technical/Functional analyses:
- API-first, JWT auth, RBAC, PostgreSQL
- Port 3000, CORS for teamtrello.lab.home.lucasacchi.net:8181
- Auto-generated OpenAPI docs at /docs
"""
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from slowapi import Limiter, _rate_limit_exceeded_handler
from slowapi.util import get_remote_address
from slowapi.errors import RateLimitExceeded
from slowapi.middleware import SlowAPIMiddleware

from app.core.config import settings
from app.core.database import Base, engine
from app.api.routes import auth, boards, lists, cards, comments, activity, health, labels

# Create DB schema (for MVP; replace with Alembic in production)
Base.metadata.create_all(bind=engine)

app = FastAPI(title=settings.APP_NAME, version="1.0")

# CORS configuration for web/mobile clients
app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.CORS_ORIGINS,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Basic rate limiting (login/register primarily)
limiter = Limiter(key_func=get_remote_address, default_limits=["100/minute"])  # global sane default
app.state.limiter = limiter
app.add_exception_handler(RateLimitExceeded, _rate_limit_exceeded_handler)
app.add_middleware(SlowAPIMiddleware)

# Routers (versioned base path)
app.include_router(health.router, prefix="/api/v1", tags=["health"])
app.include_router(auth.router, prefix="/api/v1", tags=["auth"])
app.include_router(boards.router, prefix="/api/v1", tags=["boards"])
app.include_router(lists.router, prefix="/api/v1", tags=["lists"])
app.include_router(cards.router, prefix="/api/v1", tags=["cards"])
app.include_router(comments.router, prefix="/api/v1", tags=["comments"])
app.include_router(activity.router, prefix="/api/v1", tags=["activity"])
app.include_router(labels.router, prefix="/api/v1", tags=["labels"])

@app.get("/", tags=["root"])  # simple landing for sanity checks
def root():
    return {"service": settings.APP_NAME, "docs": "/docs", "status": "ok"}