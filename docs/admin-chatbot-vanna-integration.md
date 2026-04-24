# GroceMate Admin Chatbot Integration Guide

## Purpose

This document explains, step by step, how the new sticky admin chatbot works inside GroceMate, how Laravel and Python talk to each other, how Vanna should be configured, and how the training flow is designed.

## Real Vanna References Used For This Plan

- `https://vanna.ai/docs`
- `https://vanna.ai/docs/migration`
- `https://try.vanna.ai/docs/mysql-openai-vanna-vannadb/`
- `https://github.com/vanna-ai/vanna`
- `https://github.com/vanna-ai/vanna-flask`

## What Was Added In GroceMate

- `config/chatbot.php`
- `app/Http/Controllers/AdminChatbotController.php`
- `app/Services/AdminChatbotService.php`
- `resources/views/inventory/partials/admin-chatbot.blade.php`
- `python_services/vanna_admin_chatbot/.env.example`
- `python_services/vanna_admin_chatbot/requirements.txt`
- `python_services/vanna_admin_chatbot/app.py`
- `python_services/vanna_admin_chatbot/vanna_adapter.py`
- `python_services/vanna_admin_chatbot/train_vanna.py`
- `python_services/vanna_admin_chatbot/README.md`

## High-Level Architecture

1. The admin opens any page that extends the inventory layout.
2. The sticky chatbot button appears in the bottom-right corner.
3. Clicking the button opens the slide-over chatbot panel.
4. The panel sends the question to Laravel at `inventory/chatbot/message`.
5. Laravel checks the prompt first.
6. If the prompt is a local safe question, Laravel answers it directly.
7. If the prompt needs broader analytics, Laravel forwards it to the Python Vanna service.
8. The Python service generates read-only SQL, runs it, formats the result, and sends the answer back.
9. Laravel returns that answer to the widget.
10. The widget shows the reply inside the panel and stores the conversation in browser local storage.

## Line-By-Line Style Explanation Of The Laravel Side

### `config/chatbot.php`

- The `ui` section stores frontend defaults for the widget.
- `storage_key` decides where the browser saves local chat history.
- `starter_prompts` defines the quick action buttons shown when the panel opens.
- The `vanna` section contains the Python sidecar settings.
- `enabled` decides whether Laravel should forward questions to Python.
- `base_url` is the root URL of the Python service.
- `timeout_seconds` protects the admin panel from waiting too long.
- `chat_endpoint` is the JSON route Laravel calls inside the Python service.
- The `limits` section controls how many low-stock or expiry rows are shown at once.

### `app/Http/Controllers/AdminChatbotController.php`

- This file is intentionally small.
- It validates the incoming message.
- It sends the clean message to `AdminChatbotService`.
- It returns a JSON response that the sticky widget can render.
- Keeping the controller small makes the real chatbot logic easier to maintain and test.

### `app/Services/AdminChatbotService.php`

- This is the main Laravel brain for the admin chatbot.
- `answer()` is the entry point.
- It normalizes the admin message.
- It first tries greeting handling.
- It then checks whether the prompt is a calculator question.
- It then checks for low-stock questions.
- It then checks for supplier-count questions.
- It then checks for expiry questions.
- If none of those match, it tries the Vanna sidecar.
- If Vanna is disabled or unreachable, it returns a safe fallback message.

#### Local Answers Inside `AdminChatbotService`

- `tryGreeting()` handles small human prompts like `hello`.
- `tryCalculator()` avoids asking the LLM to guess math.
- `evaluateCalculationPrompt()` supports arithmetic and VAT-style `%` additions.
- `tokenizeExpression()`, `parseExpression()`, `parseTerm()`, and `parseFactor()` form a small safe parser.
- `tryLowStockAnswer()` uses the existing GroceMate stock logic instead of inventing new rules.
- `trySupplierCountAnswer()` gives a simple local answer.
- `tryExpiryAnswer()` reads `purchase_items.expiry_date` and builds a preview.
- `buildExpiryResponse()` formats those rows into chat-friendly text.

#### Why Low Stock Is Handled Locally

- Your existing low-stock business rule is already defined in `app/Models/Stock.php`.
- Reusing that logic keeps the chatbot aligned with your dashboard.
- This avoids having two different definitions of "low stock" in the project.

### `resources/views/inventory/partials/admin-chatbot.blade.php`

- This file renders the sticky launcher button.
- It also renders the slide-over panel.
- The panel uses Alpine because Alpine is already loaded in the project.
- `x-data` creates a small frontend component state.
- `init()` restores saved messages from local storage.
- `togglePanel()` opens and closes the panel.
- `sendPrompt()` sends one of the quick starter prompts.
- `submitMessage()` sends whatever the admin typed.
- `sendMessage()` posts JSON to Laravel with CSRF protection.
- `pushMessage()` stores each message in memory and local storage.
- `scrollMessagesToBottom()` keeps the newest reply visible.

### `routes/web.php`

- The chatbot route is inside the existing `auth` + `admin` middleware group.
- That means normal customers cannot use the admin chatbot route.
- The route path is `inventory/chatbot/message`.
- The route name is `inventory.chatbot.message`.

## Line-By-Line Style Explanation Of The Python Side

### `python_services/vanna_admin_chatbot/app.py`

- This is the FastAPI entry point.
- It loads the `.env` variables.
- It creates a `FastAPI` app.
- It creates one `VannaAdapter`.
- `ChatUser` defines the optional user info payload sent by Laravel.
- `ChatRequest` defines the expected JSON body.
- `/health` confirms whether the sidecar is alive.
- `/api/chat` accepts a message from Laravel and returns a JSON answer.

### `python_services/vanna_admin_chatbot/vanna_adapter.py`

- This file isolates all Vanna-specific logic.
- `is_configured()` checks whether the required credentials exist.
- `answer()` decides whether it can answer with a real Vanna session or should return a setup message.
- `_build_client()` follows the official Vanna legacy flow:
  - create `VannaDefault`
  - connect to MySQL
  - use `generate_sql()`
  - use `run_sql()`
- `_ensure_read_only_sql()` rejects risky SQL such as `UPDATE`, `DELETE`, `DROP`, and `ALTER`.
- `_enforce_limit()` adds a `LIMIT` clause when a plain `SELECT` forgot one.
- `_format_answer()` converts the result dataframe into markdown text so Laravel can display it directly.

### `python_services/vanna_admin_chatbot/train_vanna.py`

- This is your starter training script.
- `documentation_entries()` teaches Vanna GroceMate-specific business meaning.
- `sql_examples()` teaches good query patterns.
- `ddl_examples()` reinforces the most important schema structure.
- `main()` builds a Vanna client and trains each type of entry one by one.
- This file is intended to be expanded whenever your schema or business rules change.

## How To Integrate Everything Step By Step

1. Keep the current Laravel changes in place.
2. Create a Python virtual environment in `python_services/vanna_admin_chatbot`.
3. Install Python dependencies from `requirements.txt`.
4. Copy `.env.example` to `.env`.
5. Fill in your real Vanna API key and Vanna model name.
6. Create a dedicated read-only MySQL user for the chatbot.
7. Put the read-only DB credentials into the Python `.env`.
8. Run `python train_vanna.py`.
9. Start the service with `uvicorn app:app --reload --host 127.0.0.1 --port 8001`.
10. Add the Laravel environment values:
    - `ADMIN_CHATBOT_VANNA_ENABLED=true`
    - `ADMIN_CHATBOT_VANNA_URL=http://127.0.0.1:8001`
    - `ADMIN_CHATBOT_VANNA_CHAT_ENDPOINT=/api/chat`
11. Clear Laravel config cache if needed:
    - `php artisan config:clear`
12. Open the admin panel and click the sticky button.

## How The Chatbot Works Right Now

- The sticky button already appears inside the shared admin layout.
- The slide-over UI already posts messages to Laravel.
- Local question types already work without Python:
  - low stock
  - expiry alerts
  - supplier count
  - calculator
- Broader database questions depend on the Python sidecar being configured and running.

## How Training Works

Vanna gets better answers when you train it with three things:

1. Documentation
- Business rules in plain English
- Example: what "low stock" means in GroceMate
- Example: which tables matter for expiry calculations

2. DDL
- Table structures and important columns
- Example: `stock.product_id`, `stock.quantity`, `stock.reorder_level`
- Example: `purchase_items.expiry_date`

3. Example SQL
- Good read-only SQL examples for your real business questions
- Example: low-stock list
- Example: expiring products
- Example: supplier count
- Example: monthly purchase totals

## Recommended Training Questions For GroceMate

Add training examples for questions like:

- Which products are low stock?
- Which products are out of stock?
- Show items expiring within 7 days.
- Show items expiring within 30 days.
- Which supplier has the most purchases this month?
- What is the total purchase amount this week?
- What is the total purchase amount this month?
- Which business has the highest purchase amount?
- Show top 10 products by remaining stock.
- Show top 10 most sold products from POS invoices.

## Important Safety Rules

1. Use a dedicated read-only database user.
2. Do not allow the Python chatbot account to write to the database.
3. Keep SQL safety checks in the adapter.
4. Keep local business-critical helpers in Laravel when possible.
5. Log errors, but avoid logging secrets.
6. Retrain Vanna after major schema changes.

## Current Limitations

- The Python sidecar still needs your real credentials and Python dependency install.
- The current Vanna sidecar format returns text, not charts.
- The current Laravel widget is synchronous, not streaming.
- The current calculator is intentionally simple and safe.
- The current local rules cover only a few admin questions.

## Best Next Improvements

1. Add more local helpers for totals, sales summaries, and top suppliers.
2. Store chat history in the database if you want shared admin conversations.
3. Add a small admin settings page to show Vanna health status.
4. Add source badges such as `Laravel Rule`, `Calculator`, or `Vanna SQL`.
5. Add tests for more chatbot prompt types.
6. Expand the training SQL library as your schema evolves.

## Quick Test Checklist

- Open any admin page.
- Confirm the sticky chatbot button appears in the bottom-right corner.
- Ask `Which products are low stock?`
- Ask `Show products expiring in 30 days`
- Ask `How many suppliers do we have?`
- Ask `Calculate 2450 + 18%`
- Enable the Python sidecar and then ask a broader analytics question.
