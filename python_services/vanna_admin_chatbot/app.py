"""
FastAPI entry point for the GroceMate admin chatbot sidecar.

Why keep this as a separate Python service?
1. Vanna's native integration story is Python-first.
2. Laravel can stay focused on auth, UI, and business workflows.
3. The AI layer can be started, restarted, trained, and debugged separately.
"""

from __future__ import annotations

import os
from typing import Any

from dotenv import load_dotenv
from fastapi import FastAPI
from pydantic import BaseModel, Field

from vanna_adapter import VannaAdapter

load_dotenv()

app = FastAPI(
    title="GroceMate Vanna Admin Chatbot",
    version="1.0.0",
    description="Python sidecar that receives admin chat requests from Laravel and answers them through Vanna.",
)

# Create one adapter instance when the service boots so every request can reuse
# the same configuration logic.
adapter = VannaAdapter()


class ChatUser(BaseModel):
    """
    Small user payload sent by Laravel.

    We keep it lightweight for now, but this makes it easy to add user-aware
    permissions or logging later if you expand the assistant.
    """

    id: int | None = None
    email: str | None = None
    name: str | None = None
    groups: list[str] = Field(default_factory=list)


class ChatRequest(BaseModel):
    """
    Request body for a single chat message.
    """

    message: str = Field(..., min_length=1, max_length=2000)
    user: ChatUser | None = None


@app.get("/health")
def health() -> dict[str, Any]:
    """
    Small health endpoint for quick connectivity checks.
    """

    return {
        "status": "ok",
        "mode": os.getenv("VANNA_MODE", "legacy_vanna"),
        "configured": adapter.is_configured(),
    }


@app.post("/api/chat")
def chat(request: ChatRequest) -> dict[str, Any]:
    """
    Main JSON endpoint used by the Laravel admin widget.
    """

    return adapter.answer(
        message=request.message,
        user=request.user.model_dump() if request.user else {},
    )
