# GroceMate Vanna Admin Chatbot Sidecar

This folder contains the Python service that works beside Laravel for the admin chatbot.

## What It Does

- Receives admin chat messages from the Laravel sticky widget
- Uses Vanna to turn broader business questions into read-only SQL
- Executes the SQL against GroceMate's MySQL database
- Returns a formatted text answer back to Laravel

## Main Files

- `app.py`: FastAPI entry point used by Laravel
- `vanna_adapter.py`: Vanna connection, SQL safety, and answer formatting
- `train_vanna.py`: bootstrap training script for GroceMate-specific knowledge
- `.env.example`: required environment variables
- `requirements.txt`: Python dependencies

## Quick Start

1. Create and activate a Python virtual environment.
2. Install dependencies:

```bash
pip install -r requirements.txt
```

3. Copy `.env.example` to `.env` and fill in the real values.
4. Train the model:

```bash
python train_vanna.py
```

5. Start the sidecar:

```bash
uvicorn app:app --reload --host 127.0.0.1 --port 8001
```

6. In Laravel, enable the sidecar with:

```env
ADMIN_CHATBOT_VANNA_ENABLED=true
ADMIN_CHATBOT_VANNA_URL=http://127.0.0.1:8001
ADMIN_CHATBOT_VANNA_CHAT_ENDPOINT=/api/chat
```

## Important Safety Note

Create a read-only MySQL user for the chatbot. Do not give the AI service write permissions to the production database.
