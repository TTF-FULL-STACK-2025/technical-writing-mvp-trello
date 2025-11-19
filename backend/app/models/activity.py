from sqlalchemy import Integer, DateTime, ForeignKey, JSON, Enum
from sqlalchemy.orm import Mapped, mapped_column
from datetime import datetime
from app.core.database import Base
from app.models.enums import TargetType


class ActivityLog(Base):
    __tablename__ = "activity_logs"

    id: Mapped[int] = mapped_column(Integer, primary_key=True)
    actor_id: Mapped[int] = mapped_column(ForeignKey("users.id", ondelete="SET NULL"))
    board_id: Mapped[int] = mapped_column(ForeignKey("boards.id", ondelete="CASCADE"))
    target_type: Mapped[TargetType] = mapped_column(Enum(TargetType))
    target_id: Mapped[int | None] = mapped_column(Integer, nullable=True)
    action: Mapped[str] = mapped_column(JSON)  # store structured action details
    created_at: Mapped[datetime] = mapped_column(DateTime, default=datetime.utcnow)