"""Reusable FastAPI dependencies: auth and RBAC."""
from typing import Annotated
from fastapi import Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer
from sqlalchemy.orm import Session
from jose import JWTError

from app.core.database import get_db
from app.core.security import decode_token
from app.models.user import User
from app.models.board import Board
from app.models.list import List
from app.models.card import Card
from app.models.membership import BoardMember
from app.models.enums import Role

oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/v1/auth/login")


def get_current_user(
    token: Annotated[str, Depends(oauth2_scheme)], db: Annotated[Session, Depends(get_db)]
) -> User:
    try:
        payload = decode_token(token)
        user_id = int(payload.get("sub"))
    except (JWTError, ValueError):
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Invalid token")
    user = db.get(User, user_id)
    if not user:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="User not found")
    return user


def _board_id_from_context(db: Session, board_id: int | None, list_id: int | None, card_id: int | None) -> int:
    if board_id:
        return board_id
    if list_id:
        lst = db.get(List, list_id)
        if not lst:
            raise HTTPException(status_code=404, detail="List not found")
        return lst.board_id
    if card_id:
        card = db.get(Card, card_id)
        if not card:
            raise HTTPException(status_code=404, detail="Card not found")
        return card.board_id
    raise HTTPException(status_code=400, detail="Board context is required")


def require_board_role(roles: list[Role]):
    def dependency(
        db: Annotated[Session, Depends(get_db)],
        current_user: Annotated[User, Depends(get_current_user)],
        board_id: int | None = None,
        list_id: int | None = None,
        card_id: int | None = None,
    ) -> User:
        resolved_board_id = _board_id_from_context(db, board_id, list_id, card_id)
        member = (
            db.query(BoardMember)
            .filter(BoardMember.board_id == resolved_board_id, BoardMember.user_id == current_user.id)
            .first()
        )
        if not member or member.role not in roles:
            raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Insufficient role")
        return current_user

    return dependency