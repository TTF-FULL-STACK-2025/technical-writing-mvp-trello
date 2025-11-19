from pydantic import BaseModel
from app.models.enums import Role


class BoardCreate(BaseModel):
    name: str
    description: str | None = None


class BoardRead(BaseModel):
    id: int
    name: str
    description: str | None
    owner_id: int

    class Config:
        from_attributes = True


class MemberAdd(BaseModel):
    user_id: int
    role: Role


class MemberUpdate(BaseModel):
    role: Role