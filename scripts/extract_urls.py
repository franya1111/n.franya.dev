#!/usr/bin/env python3
"""Extract image URLs from z-ai CLI output that contains progress messages."""
import json
import re
import sys
from pathlib import Path

def extract_json(text: str) -> dict | None:
    """Find the first JSON object in text."""
    # Find first { and last }
    start = text.find('{')
    if start == -1:
        return None
    # Find matching closing brace
    depth = 0
    for i in range(start, len(text)):
        if text[i] == '{':
            depth += 1
        elif text[i] == '}':
            depth -= 1
            if depth == 0:
                try:
                    return json.loads(text[start:i+1])
                except json.JSONDecodeError:
                    return None
    return None

def main():
    files = sys.argv[1:]
    for f in files:
        text = Path(f).read_text()
        data = extract_json(text)
        if data and 'results' in data:
            urls = [r['original_url'] for r in data['results']]
            print(f"{f}:")
            for u in urls:
                print(f"  {u}")

if __name__ == '__main__':
    main()
