from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.core.database import get_db
from app.api.deps import get_current_user, require_board_role
from app.models.board import Board
from app.models.membership import BoardMember
from app.models.enums import Role
from app.schemas.board import BoardCreate, BoardRead, MemberAdd, MemberUpdate
from app.services.activity import record_activity
from app.models.enums import TargetType

router = APIRouter()


@router.get("/boards", response_model=list[BoardRead])
def list_boards(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    boards = (
        db.query(Board)
        .join(BoardMember, BoardMember.board_id == Board.id)
        .filter(BoardMember.user_id == current_user.id)
        .all()
    )
    return boards


@router.post("/boards", response_model=BoardRead)
def create_board(payload: BoardCreate, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    board = Board(name=payload.name, description=payload.description, owner_id=current_user.id)
    db.add(board)
    db.commit()
    db.refresh(board)
    # Add owner membership
    db.add(BoardMember(board_id=board.id, user_id=current_user.id, role=Role.OWNER))
    db.commit()
    record_activity(db, actor_id=current_user.id, board_id=board.id, target_type=TargetType.BOARD, target_id=board.id, action={"type": "create_board", "name": board.name})
    return board


@router.get("/boards/{board_id}", response_model=BoardRead)
def get_board(board_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    member = db.query(BoardMember).filter(BoardMember.board_id == board_id, BoardMember.user_id == current_user.id).first()
    if not member:
        raise HTTPException(status_code=403, detail="Not a board member")
    board = db.get(Board, board_id)
    if not board:
        raise HTTPException(status_code=404, detail="Board not found")
    return board


@router.patch("/boards/{board_id}", response_model=BoardRead)
def update_board(board_id: int, payload: BoardCreate, db: Session = Depends(get_db), current_user=Depends(require_board_role([Role.OWNER, Role.EDITOR]))):
    board = db.get(Board, board_id)
    if not board:
        raise HTTPException(status_code=404, detail="Board not found")
    board.name = payload.name or board.name
    board.description = payload.description if payload.description is not None else board.description
    db.commit()
    db.refresh(board)
    record_activity(db, actor_id=current_user.id, board_id=board.id, target_type=TargetType.BOARD, target_id=board.id, action={"type": "update_board"})
    return board


@router.delete("/boards/{board_id}")
def delete_board(board_id: int, db: Session = Depends(get_db), current_user=Depends(require_board_role([Role.OWNER]))):
    board = db.get(Board, board_id)
    if not board:
        raise HTTPException(status_code=404, detail="Board not found")
    db.delete(board)
    db.commit()
    record_activity(db, actor_id=current_user.id, board_id=board.id, target_type=TargetType.BOARD, target_id=board.id, action={"type": "delete_board"})
    return {"message": "Board deleted"}


# Member management
@router.post("/boards/{board_id}/members", dependencies=[Depends(require_board_role([Role.OWNER]))])
def add_member(board_id: int, payload: MemberAdd, db: Session = Depends(get_db)):
    db.add(BoardMember(board_id=board_id, user_id=payload.user_id, role=payload.role))
    db.commit()
    record_activity(db, actor_id=payload.user_id, board_id=board_id, target_type=TargetType.MEMBER, target_id=payload.user_id, action={"type": "add_member", "role": payload.role})
    return {"message": "Member added"}


@router.put("/boards/{board_id}/members/{user_id}", dependencies=[Depends(require_board_role([Role.OWNER]))])
def update_member(board_id: int, user_id: int, payload: MemberUpdate, db: Session = Depends(get_db)):
    member = (
        db.query(BoardMember)
        .filter(BoardMember.board_id == board_id, BoardMember.user_id == user_id)
        .first()
    )
    if not member:
        raise HTTPException(status_code=404, detail="Member not found")
    member.role = payload.role
    db.commit()
    return {"message": "Member updated"}


@router.delete("/boards/{board_id}/members/{user_id}", dependencies=[Depends(require_board_role([Role.OWNER]))])
def remove_member(board_id: int, user_id: int, db: Session = Depends(get_db)):
    member = (
        db.query(BoardMember)
        .filter(BoardMember.board_id == board_id, BoardMember.user_id == user_id)
        .first()
    )
    if not member:
        raise HTTPException(status_code=404, detail="Member not found")
    db.delete(member)
    db.commit()
    return {"message": "Member removed"}