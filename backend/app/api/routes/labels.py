from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.core.database import get_db
from app.api.deps import require_board_role
from app.models.label import Label
from app.models.enums import Role
from app.schemas.label import LabelCreate, LabelRead

router = APIRouter()


@router.get("/boards/{board_id}/labels", response_model=list[LabelRead], dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR, Role.VIEWER]))])
def list_labels(board_id: int, db: Session = Depends(get_db)):
    return db.query(Label).filter(Label.board_id == board_id).all()


@router.post("/boards/{board_id}/labels", response_model=LabelRead, dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def create_label(board_id: int, payload: LabelCreate, db: Session = Depends(get_db)):
    label = Label(board_id=board_id, name=payload.name, color=payload.color)
    db.add(label)
    db.commit()
    db.refresh(label)
    return label


@router.delete("/labels/{label_id}", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def delete_label(label_id: int, db: Session = Depends(get_db)):
    label = db.get(Label, label_id)
    if not label:
        raise HTTPException(status_code=404, detail="Label not found")
    db.delete(label)
    db.commit()
    return {"message": "Label deleted"}