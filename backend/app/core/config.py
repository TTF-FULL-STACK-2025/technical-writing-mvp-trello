"""Application configuration using pydantic-settings."""
from pydantic_settings import BaseSettings
from typing import List


class Settings(BaseSettings):
    APP_NAME: str = "teamtrello-backend"
    ENV: str = "development"
    PORT: int = 3000

    # JWT
    JWT_SECRET: str = "change_me"
    JWT_ALGORITHM: str = "HS256"
    ACCESS_TOKEN_EXPIRE_MINUTES: int = 60

    # DB
    # Default to SQLite for local dev; override via .env for Postgres
    DATABASE_URL: str = "sqlite:///./teamtrello.db"

    # CORS
    CORS_ORIGINS: List[str] = [
        "http://teamtrello.lab.home.lucasacchi.net:8181",
        "http://localhost:8181",
        "http://localhost:3000",
    ]

    class Config:
        env_file = ".env"


settings = Settings()