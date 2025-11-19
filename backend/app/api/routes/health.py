from fastapi import APIRouter
from app.core.config import settings

router = APIRouter()


@router.get("/health")
def health():
    return {"status": "ok", "service": settings.APP_NAME}


@router.get("/status")
def status():
    return {"status": "ok"}