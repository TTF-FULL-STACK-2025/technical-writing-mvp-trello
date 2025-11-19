from pydantic import BaseModel


class ListCreate(BaseModel):
    name: str
    position: int | None = None


class ListRead(BaseModel):
    id: int
    board_id: int
    name: str
    position: int

    class Config:
        from_attributes = True