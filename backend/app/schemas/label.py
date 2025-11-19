from pydantic import BaseModel


class LabelCreate(BaseModel):
    name: str
    color: str


class LabelRead(BaseModel):
    id: int
    board_id: int
    name: str
    color: str

    class Config:
        from_attributes = True