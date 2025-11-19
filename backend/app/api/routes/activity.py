from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.core.database import get_db
from app.api.deps import require_board_role
from app.models.activity import ActivityLog
from app.models.enums import Role

router = APIRouter()


@router.get("/boards/{board_id}/activity", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR, Role.VIEWER]))])
def get_activity(board_id: int, db: Session = Depends(get_db)):
    entries = db.query(ActivityLog).filter(ActivityLog.board_id == board_id).order_by(ActivityLog.created_at.desc()).all()
    return entries