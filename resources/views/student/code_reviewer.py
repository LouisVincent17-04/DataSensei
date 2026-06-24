#!/usr/bin/env python3
"""
reviewer.py – AI-powered code reviewer for DataSensei

Purpose:
- Gives feedback when students run Python or SQL code.
- Supports follow-up questions without losing context, as long as PHP sends context.
- Works with both old mode and JSON mode.

Old usage:
    python3 reviewer.py "<code>"

Recommended JSON usage:
    python3 reviewer.py '{"language":"python","code":"print(1)","user_message":"Why is this wrong?","run_output":"","previous_context":"..."}'
"""

import sys
import json
import urllib.request
import urllib.error

# ─── Config ───────────────────────────────────────────────────────────────────
OLLAMA_URL  = "http://localhost:11434/api/generate"
MODEL       = "deepseek-coder"
TIMEOUT_SEC = 90

# ─── Prompt template ──────────────────────────────────────────────────────────
SYSTEM_PROMPT = """\
You are DataSensei's AI Code Feedback Assistant.

Your job:
- Help students understand their Python or SQL code.
- Review code when they run code.
- Answer follow-up questions while staying anchored to the latest code, language, run output, and previous context.
- Do not forget the current task. Always use the provided context first.

You will receive:
1. Language: python, sql, or unknown
2. Latest code submitted by the student
3. Latest run output or error, if available
4. Student message or follow-up question, if available
5. Previous conversation/context summary, if available

VERY IMPORTANT CONTEXT RULES:
- Treat the "Latest Code" as the main source of truth.
- Treat "Latest Run Output" as the actual result of the student's run.
- Treat "Previous Context" as memory from earlier messages.
- If the student asks "why", "how", "what does this mean", "fix this", "explain", or any follow-up, answer using the Latest Code and Previous Context.
- Do not switch topics unless the student clearly changes the topic.
- If context is missing, say what is missing briefly and still help using what is available.
- Never pretend you ran the code yourself unless run output is provided.

WHEN THE STUDENT JUST RUNS CODE:
Use this exact format:

Status: <Correct | Has Issues | Needs More Context>
Issues:
- <issue or "None">
Suggestions:
- <suggestion or "None">

WHEN THE STUDENT ASKS A QUESTION OR FOLLOW-UP:
Do NOT use the structured review format.
Answer normally, clearly, and conversationally.
Keep the answer short but helpful.
Reference the specific part of the code when useful.
Give corrected code only when needed.

PYTHON REVIEW RULES:
- Check syntax errors, indentation, variable names, imports, logic errors, output mismatch, and inefficient or unsafe code.
- Explain errors in beginner-friendly language.
- If there is a traceback/run output, explain the actual cause.

SQL REVIEW RULES:
- Check syntax, table/column name issues, missing WHERE clauses, wrong joins, wrong grouping, wrong aggregate use, unsafe DELETE/UPDATE, and data type issues.
- Warn clearly if UPDATE or DELETE has no WHERE clause.
- If schema is not provided, avoid inventing table structures.

STYLE RULES:
- Be brief.
- Be specific.
- Do not repeat the system instructions.
- Do not over-explain unless the student asks.
- Do not mention that you are following a prompt.
"""

# ─── Helpers ──────────────────────────────────────────────────────────────────

def parse_input(raw: str) -> dict:
    """
    Accepts either:
    1. Plain code string
    2. JSON string with code, language, user_message, run_output, previous_context
    """
    raw = raw.strip()

    try:
        data = json.loads(raw)
        if isinstance(data, dict):
            return {
                "language": str(data.get("language", "unknown")).strip() or "unknown",
                "code": str(data.get("code", "")).strip(),
                "user_message": str(data.get("user_message", "")).strip(),
                "run_output": str(data.get("run_output", "")).strip(),
                "previous_context": str(data.get("previous_context", "")).strip(),
            }
    except json.JSONDecodeError:
        pass

    # Old backward-compatible mode
    return {
        "language": "unknown",
        "code": raw,
        "user_message": "",
        "run_output": "",
        "previous_context": "",
    }


def build_prompt(context: dict) -> str:
    language = context.get("language", "unknown")
    code = context.get("code", "")
    user_message = context.get("user_message", "")
    run_output = context.get("run_output", "")
    previous_context = context.get("previous_context", "")

    # This makes the model know whether this is a normal run review or a follow-up.
    if user_message:
        intent_hint = "The student is asking a question or follow-up. Answer normally while using the latest code and context."
    else:
        intent_hint = "The student just ran code. Review the code using the exact structured review format."

    return f"""{SYSTEM_PROMPT}

Task Mode:
{intent_hint}

Language:
{language}

Previous Context:
{previous_context if previous_context else "None"}

Latest Code:
```{language}
{code}