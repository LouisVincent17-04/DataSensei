#!/usr/bin/env python3
"""
reviewer.py – AI-powered code reviewer (MVP)

Usage (called by PHP via shell_exec):
    python3 reviewer.py "<escaped code string>"

Sends code to a local Ollama instance and prints the structured review to stdout.
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
You are a code reviewer. Review the code below and respond using exactly this format:

Status: <Correct | Has Issues>
Issues:
- <issue or "None">
Suggestions:
- <suggestion or "None">

Be brief. One line per point. Do not repeat instructions."""

# ─── Helpers ──────────────────────────────────────────────────────────────────

def build_payload(code: str) -> bytes:
    """Build the JSON payload for Ollama's /api/generate endpoint."""
    data = {
        "model":  MODEL,
        "prompt": f"{SYSTEM_PROMPT}\n\nCode to review:\n```\n{code}\n```",
        "stream": False,
        "options": {
            "temperature": 0.2,
            "num_predict": 512,
        },
    }
    return json.dumps(data).encode("utf-8")


def call_ollama(code: str) -> str:
    """Send code to Ollama and return the model's text response."""
    payload = build_payload(code)

    req = urllib.request.Request(
        OLLAMA_URL,
        data=payload,
        headers={"Content-Type": "application/json"},
        method="POST",
    )

    try:
        with urllib.request.urlopen(req, timeout=TIMEOUT_SEC) as resp:
            body = resp.read().decode("utf-8")
    except urllib.error.URLError as exc:
        return (
            f"[ERROR] Could not reach Ollama at {OLLAMA_URL}.\n"
            f"Make sure Ollama is running (`ollama serve`) and the model is pulled.\n"
            f"Detail: {exc}"
        )

    try:
        parsed = json.loads(body)
        return parsed.get("response", "[ERROR] Empty response field in Ollama reply.")
    except json.JSONDecodeError:
        return f"[ERROR] Could not parse Ollama JSON response:\n{body[:500]}"


# ─── Entry point ──────────────────────────────────────────────────────────────

def main() -> None:
    if len(sys.argv) < 2:
        print("[ERROR] No code supplied. Usage: python3 reviewer.py '<code>'")
        sys.exit(1)

    code = sys.argv[1].strip()

    if not code:
        print("[ERROR] Empty code string received.")
        sys.exit(1)

    result = call_ollama(code)
    print(result)


if __name__ == "__main__":
    main()