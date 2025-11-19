from fastapi import APIRouter, Depends, HTTPException, status, Request
from sqlalchemy.orm import Session
from slowapi import Limiter

from app.core.database import get_db
from app.core.security import hash_password, verify_password, create_access_token
from app.models.user import User
from app.schemas.user import UserCreate, UserRead, TokenResponse

router = APIRouter()


@router.post("/auth/register", response_model=UserRead)
def register(payload: UserCreate, db: Session = Depends(get_db)):
    if db.query(User).filter(User.email == payload.email).first():
        raise HTTPException(status_code=400, detail="Email already registered")
    user = User(email=payload.email, password_hash=hash_password(payload.password), name=payload.name)
    db.add(user)
    db.commit()
    db.refresh(user)
    # Owner will be set when creating boards
    return user


@router.post("/auth/login", response_model=TokenResponse)
def login(payload: UserCreate, request: Request, db: Session = Depends(get_db)):
    # rate limit on login
    limiter: Limiter = request.app.state.limiter
    limiter.hit(request)

    user = db.query(User).filter(User.email == payload.email).first()
    if not user or not verify_password(payload.password, user.password_hash):
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Invalid credentials")
    token = create_access_token(subject=str(user.id))
    return TokenResponse(access_token=token)


# Password recovery stubs (MVP): In production, generate a single-use token and email.
@router.post("/auth/password/forgot")
def password_forgot(email: str):
    # Always return 202 to avoid email enumeration
    return {"status": "accepted"}


@router.post("/auth/password/reset")
def password_reset(token: str, new_password: str):
    # Validate token and set new password (out of scope MVP)
    return {"status": "ok"}