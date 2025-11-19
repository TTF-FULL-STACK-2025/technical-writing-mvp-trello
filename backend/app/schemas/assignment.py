from pydantic import BaseModel


class AssigneeAdd(BaseModel):
    user_id: int


class LabelAssign(BaseModel):
    label_id: int