from pydantic import BaseModel, field_validator


class ORMBase(BaseModel):
    class Config:
        from_attributes = True


class Message(BaseModel):
    message: str


class IDResponse(BaseModel):
    id: int