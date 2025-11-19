from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.core.database import get_db
from app.api.deps import require_board_role
from app.models.card import Card
from app.models.list import List
from app.models.enums import Role
from app.schemas.card import CardCreate, CardUpdate, CardMove, CardRead, DueDateUpdate
from app.services.activity import record_activity
from app.models.enums import TargetType
from app.models.card_relations import CardAssignee, CardLabel
from app.schemas.assignment import AssigneeAdd, LabelAssign

router = APIRouter()


@router.post("/lists/{list_id}/cards", response_model=CardRead, dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def create_card(list_id: int, payload: CardCreate, db: Session = Depends(get_db)):
    lst = db.get(List, list_id)
    if not lst:
        raise HTTPException(status_code=404, detail="List not found")
    position = payload.position if payload.position is not None else 0
    card = Card(board_id=lst.board_id, list_id=list_id, title=payload.title, description=payload.description, position=position)
    db.add(card)
    db.commit()
    db.refresh(card)
    record_activity(db, actor_id=0, board_id=card.board_id, target_type=TargetType.CARD, target_id=card.id, action={"type": "create_card", "title": card.title})
    return card


@router.patch("/cards/{card_id}", response_model=CardRead, dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def update_card(card_id: int, payload: CardUpdate, db: Session = Depends(get_db)):
    card = db.get(Card, card_id)
    if not card:
        raise HTTPException(status_code=404, detail="Card not found")
    if payload.title is not None:
        card.title = payload.title
    if payload.description is not None:
        card.description = payload.description
    db.commit()
    db.refresh(card)
    return card


@router.put("/cards/{card_id}/move", response_model=CardRead, dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def move_card(card_id: int, payload: CardMove, db: Session = Depends(get_db)):
    card = db.get(Card, card_id)
    if not card:
        raise HTTPException(status_code=404, detail="Card not found")
    new_list = db.get(List, payload.list_id)
    if not new_list:
        raise HTTPException(status_code=404, detail="List not found")
    card.list_id = new_list.id
    card.board_id = new_list.board_id
    card.position = payload.position
    db.commit()
    db.refresh(card)
    record_activity(db, actor_id=0, board_id=card.board_id, target_type=TargetType.CARD, target_id=card.id, action={"type": "move_card", "to_list": payload.list_id, "position": payload.position})
    return card


@router.delete("/cards/{card_id}", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def delete_card(card_id: int, db: Session = Depends(get_db)):
    card = db.get(Card, card_id)
    if not card:
        raise HTTPException(status_code=404, detail="Card not found")
    db.delete(card)
    db.commit()
    return {"message": "Card deleted"}


@router.patch("/cards/{card_id}/due-date", response_model=CardRead, dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def update_due_date(card_id: int, payload: DueDateUpdate, db: Session = Depends(get_db)):
    card = db.get(Card, card_id)
    if not card:
        raise HTTPException(status_code=404, detail="Card not found")
    card.due_date = payload.due_date
    db.commit()
    db.refresh(card)
    return card


# Assignees
@router.post("/cards/{card_id}/assignees", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def add_assignee(card_id: int, payload: AssigneeAdd, db: Session = Depends(get_db)):
    card = db.get(Card, card_id)
    if not card:
        raise HTTPException(status_code=404, detail="Card not found")
    db.add(CardAssignee(card_id=card_id, user_id=payload.user_id))
    db.commit()
    record_activity(db, actor_id=payload.user_id, board_id=card.board_id, target_type=TargetType.CARD, target_id=card.id, action={"type": "assign_user", "user_id": payload.user_id})
    return {"message": "User assigned"}


@router.delete("/cards/{card_id}/assignees/{user_id}", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def remove_assignee(card_id: int, user_id: int, db: Session = Depends(get_db)):
    row = db.query(CardAssignee).filter(CardAssignee.card_id == card_id, CardAssignee.user_id == user_id).first()
    if not row:
        raise HTTPException(status_code=404, detail="Assignment not found")
    db.delete(row)
    db.commit()
    return {"message": "User unassigned"}


# Labels on cards
@router.post("/cards/{card_id}/labels", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def add_label(card_id: int, payload: LabelAssign, db: Session = Depends(get_db)):
    card = db.get(Card, card_id)
    if not card:
        raise HTTPException(status_code=404, detail="Card not found")
    db.add(CardLabel(card_id=card_id, label_id=payload.label_id))
    db.commit()
    record_activity(db, actor_id=0, board_id=card.board_id, target_type=TargetType.CARD, target_id=card.id, action={"type": "add_label", "label_id": payload.label_id})
    return {"message": "Label added to card"}


@router.delete("/cards/{card_id}/labels/{label_id}", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def remove_label(card_id: int, label_id: int, db: Session = Depends(get_db)):
    row = db.query(CardLabel).filter(CardLabel.card_id == card_id, CardLabel.label_id == label_id).first()
    if not row:
        raise HTTPException(status_code=404, detail="Card label not found")
    db.delete(row)
    db.commit()
    return {"message": "Label removed from card"}