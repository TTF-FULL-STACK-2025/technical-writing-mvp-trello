from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.core.database import get_db
from app.api.deps import require_board_role
from app.models.list import List
from app.models.enums import Role
from app.schemas.list import ListCreate, ListRead
from app.services.activity import record_activity
from app.models.enums import TargetType

router = APIRouter()


@router.post("/boards/{board_id}/lists", response_model=ListRead, dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def create_list(board_id: int, payload: ListCreate, db: Session = Depends(get_db)):
    position = payload.position if payload.position is not None else 0
    lst = List(board_id=board_id, name=payload.name, position=position)
    db.add(lst)
    db.commit()
    db.refresh(lst)
    record_activity(db, actor_id=0, board_id=board_id, target_type=TargetType.LIST, target_id=lst.id, action={"type": "create_list", "name": lst.name})
    return lst


@router.patch("/lists/{list_id}", response_model=ListRead, dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def update_list(list_id: int, payload: ListCreate, db: Session = Depends(get_db)):
    lst = db.get(List, list_id)
    if not lst:
        raise HTTPException(status_code=404, detail="List not found")
    lst.name = payload.name or lst.name
    if payload.position is not None:
        lst.position = payload.position
    db.commit()
    db.refresh(lst)
    return lst


@router.delete("/lists/{list_id}", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def delete_list(list_id: int, db: Session = Depends(get_db)):
    lst = db.get(List, list_id)
    if not lst:
        raise HTTPException(status_code=404, detail="List not found")
    db.delete(lst)
    db.commit()
    return {"message": "List deleted"}


@router.put("/boards/{board_id}/lists/reorder", dependencies=[Depends(require_board_role([Role.OWNER, Role.EDITOR]))])
def reorder_lists(board_id: int, order: list[int], db: Session = Depends(get_db)):
    # order = [list_id in desired order]
    for pos, lid in enumerate(order):
        lst = db.get(List, lid)
        if lst and lst.board_id == board_id:
            lst.position = pos
    db.commit()
    return {"message": "Lists reordered"}