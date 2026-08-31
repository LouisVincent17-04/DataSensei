#!/usr/bin/env python3
"""Retired compatibility entry point.

DataSensei code review is implemented by the authenticated Laravel
CodeReviewController and its configured Ollama service. Keeping a second Python
review client here previously duplicated configuration and the file was
syntactically incomplete. This small valid entry point prevents accidental use
of that abandoned implementation without creating a second review pipeline.
"""

import sys


def main() -> int:
    sys.stderr.write("Use the DataSensei web Code Review feature.\n")
    return 2


if __name__ == "__main__":
    raise SystemExit(main())
