from pydantic import BaseModel
from datetime import datetime


class CardCreate(BaseModel):
    title: str
    description: str | None = None
    position: int | None = None


class CardUpdate(BaseModel):
    title: str | None = None
    description: str | None = None


class CardMove(BaseModel):
    list_id: int
    position: int


class DueDateUpdate(BaseModel):
    due_date: datetime | None


class CardRead(BaseModel):
    id: int
    board_id: int
    list_id: int
    title: str
    description: str | None
    position: int
    due_date: datetime | None

    class Config:
        from_attributes = True