"""
Adapter that keeps Vanna-specific logic out of the FastAPI route file.

This file intentionally uses the "classic" Vanna workflow because it is the
easiest path for a custom Laravel widget:
1. connect_to_mysql(...)
2. train(...)
3. generate_sql(...)
4. run_sql(...)

That flow is still documented by Vanna and maps neatly to GroceMate's
admin-side analytics use case.
"""

from __future__ import annotations

import os
from typing import Any

from dotenv import load_dotenv

try:
    import pandas as pd
except Exception:  # pragma: no cover - optional until dependencies are installed
    pd = None

try:
    from vanna.remote import VannaDefault
except Exception:  # pragma: no cover - optional until dependencies are installed
    VannaDefault = None

load_dotenv()


class VannaAdapter:
    """
    Thin wrapper around Vanna's official Python APIs.
    """

    def __init__(self) -> None:
        self.max_result_rows = int(os.getenv("MAX_RESULT_ROWS", "20"))

    def is_configured(self) -> bool:
        """
        Check whether the environment variables needed for a real Vanna session
        are available.
        """

        required_values = [
            os.getenv("VANNA_API_KEY"),
            os.getenv("VANNA_MODEL_NAME"),
            os.getenv("DB_HOST"),
            os.getenv("DB_NAME"),
            os.getenv("DB_USER"),
        ]

        return all(bool(value) for value in required_values)

    def answer(self, message: str, user: dict[str, Any] | None = None) -> dict[str, Any]:
        """
        Build a safe JSON response for Laravel.

        If Vanna is not ready yet, return a very explicit setup hint instead of
        crashing. This makes the admin widget easier to test incrementally.
        """

        message = (message or "").strip()
        user = user or {}

        if message == "":
            return {
                "answer": "Please send a non-empty message.",
                "meta": {"configured": self.is_configured()},
            }

        if not self.is_configured():
            return {
                "answer": (
                    "The Python chatbot service is running, but Vanna is not fully configured yet. "
                    "Set the VANNA_* and DB_* variables in python_services/vanna_admin_chatbot/.env, "
                    "then train the model with train_vanna.py."
                ),
                "meta": {
                    "configured": False,
                    "user": user,
                },
            }

        if VannaDefault is None or pd is None:
            return {
                "answer": (
                    "The service is configured, but the required Python packages are missing. "
                    "Install them with `pip install -r requirements.txt` before using Vanna."
                ),
                "meta": {
                    "configured": True,
                    "packages_loaded": False,
                },
            }

        try:
            client = self._build_client()
            sql = client.generate_sql(question=message)
            safe_sql = self._ensure_read_only_sql(sql)
            safe_sql = self._enforce_limit(safe_sql)
            dataframe = client.run_sql(safe_sql)

            return {
                "answer": self._format_answer(message=message, sql=safe_sql, dataframe=dataframe),
                "meta": {
                    "configured": True,
                    "sql": safe_sql,
                    "row_count": int(len(dataframe.index)) if hasattr(dataframe, "index") else 0,
                },
            }
        except Exception as exc:  # pragma: no cover - runtime integration path
            return {
                "answer": (
                    "Vanna received the question but could not finish the request. "
                    f"Check the Python console for more detail. Error: {exc}"
                ),
                "meta": {
                    "configured": True,
                    "error": str(exc),
                },
            }

    def _build_client(self):
        """
        Build a Vanna client using the official hosted-model helper.

        This follows the legacy Vanna examples:
        - instantiate VannaDefault
        - connect it to MySQL
        - use generate_sql / run_sql / train
        """

        client = VannaDefault(
            api_key=os.getenv("VANNA_API_KEY"),
            model=os.getenv("VANNA_MODEL_NAME"),
        )

        client.connect_to_mysql(
            host=os.getenv("DB_HOST"),
            dbname=os.getenv("DB_NAME"),
            user=os.getenv("DB_USER"),
            password=os.getenv("DB_PASSWORD"),
            port=int(os.getenv("DB_PORT", "3306")),
        )

        return client

    def _ensure_read_only_sql(self, sql: str) -> str:
        """
        Reject anything that is not obviously safe and read-only.
        """

        normalized = (sql or "").strip().rstrip(";").lower()

        if normalized == "":
            raise ValueError("Vanna returned an empty SQL statement.")

        allowed_starts = ("select", "with", "show", "describe", "explain")

        if not normalized.startswith(allowed_starts):
            raise ValueError("Only read-only SQL is allowed for the admin chatbot.")

        blocked_keywords = [
            " insert ",
            " update ",
            " delete ",
            " drop ",
            " alter ",
            " truncate ",
            " create ",
            " replace ",
            " grant ",
            " revoke ",
        ]

        padded = f" {normalized} "

        if any(keyword in padded for keyword in blocked_keywords):
            raise ValueError("A blocked SQL keyword was detected in the generated SQL.")

        return sql.strip().rstrip(";")

    def _enforce_limit(self, sql: str) -> str:
        """
        Add a LIMIT clause to plain SELECT statements when one is missing.
        """

        normalized = sql.lower()

        if normalized.startswith("select") and " limit " not in normalized:
            return f"{sql} LIMIT {self.max_result_rows}"

        return sql

    def _format_answer(self, message: str, sql: str, dataframe) -> str:
        """
        Convert the generated SQL result into a chat-friendly text answer.
        """

        if dataframe is None:
            return (
                "I generated a read-only SQL query, but no result data was returned.\n"
                f"SQL used:\n{sql}"
            )

        if hasattr(dataframe, "empty") and dataframe.empty:
            return (
                "I ran a read-only SQL query, but it returned no rows.\n"
                f"SQL used:\n{sql}"
            )

        preview = dataframe.head(self.max_result_rows)

        table_text = preview.to_markdown(index=False)

        return (
            f"Question: {message}\n"
            f"SQL used:\n{sql}\n\n"
            f"Top {len(preview.index)} row(s):\n{table_text}"
        )
