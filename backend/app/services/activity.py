"""Simple activity logging service."""
from sqlalchemy.orm import Session
from app.models.activity import ActivityLog
from app.models.enums import TargetType


def record_activity(db: Session, actor_id: int, board_id: int, target_type: TargetType, target_id: int | None, action: dict):
    entry = ActivityLog(actor_id=actor_id, board_id=board_id, target_type=target_type, target_id=target_id, action=action)
    db.add(entry)
    db.commit()