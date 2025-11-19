from enum import Enum


class Role(str, Enum):
    OWNER = "owner"
    EDITOR = "editor"
    VIEWER = "viewer"


class TargetType(str, Enum):
    BOARD = "board"
    LIST = "list"
    CARD = "card"
    COMMENT = "comment"
    MEMBER = "member"
    LABEL = "label"
    ATTACHMENT = "attachment"