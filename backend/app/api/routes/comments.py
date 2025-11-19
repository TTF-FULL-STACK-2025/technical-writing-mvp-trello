from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.core.database import get_db
from app.api.deps import require_board_role
from app.models.comment import Comment
from app.models.card import Card
from app.models.enums import Role
from app.schemas.comment import CommentCreate, CommentRead
from app.api.deps import get_current_user
from app.services.activity import record_activity
from app.models.enums import TargetType

router = APIRouter()


@router.post("/cards/{card_id}/comments", response_model=CommentRead, dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def add_comment(card_id: int, payload: CommentCreate, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    card = db.get(Card, card_id)
    if not card:
        raise HTTPException(status_code=404, detail="Card not found")
    comment = Comment(card_id=card_id, user_id=current_user.id, text=payload.text)
    db.add(comment)
    db.commit()
    db.refresh(comment)
    record_activity(db, actor_id=current_user.id, board_id=card.board_id, target_type=TargetType.COMMENT, target_id=comment.id, action={"type": "add_comment"})
    return comment


@router.delete("/comments/{comment_id}", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def delete_comment(comment_id: int, db: Session = Depends(get_db)):
    comment = db.get(Comment, comment_id)
    if not comment:
        raise HTTPException(status_code=404, detail="Comment not found")
    db.delete(comment)
    db.commit()
    return {"message": "Comment deleted"}