from pydantic import BaseModel


class CommentCreate(BaseModel):
    text: str


class CommentRead(BaseModel):
    id: int
    card_id: int
    user_id: int
    text: str

    class Config:
        from_attributes = True