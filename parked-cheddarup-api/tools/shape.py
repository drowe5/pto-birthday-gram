#!/usr/bin/env python3
"""Print the shape of a JSON response without printing what is in it.

Keys, types, and array lengths come through; every scalar value is replaced
by a description of itself. That is enough to write code against an API and
not enough to leak a child's name, a parent's email, or a birthday message.

    curl ... | python3 shape.py
    python3 shape.py < saved.json
"""
import json, sys, re

MAX_KEYS = 60
MAX_DEPTH = 8

def describe(v):
    if v is None:
        return "null"
    if v is True or v is False:
        return "bool"
    if isinstance(v, int):
        return "int(%d digits)" % len(str(abs(v)))
    if isinstance(v, float):
        return "float"
    s = str(v)
    # Shapes worth naming outright: they say what a field IS without saying
    # what it holds, and they are what you match an endpoint on.
    if re.fullmatch(r"\d{4}-\d{2}-\d{2}", s):
        return "date YYYY-MM-DD"
    if re.fullmatch(r"\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}.*", s):
        return "timestamp ISO-8601"
    if re.fullmatch(r"[^@\s]+@[^@\s]+\.[A-Za-z]{2,}", s):
        return "email"
    if re.fullmatch(r"https?://\S+", s):
        return "url"
    if re.fullmatch(r"[0-9a-fA-F-]{32,36}", s):
        return "uuid-ish"
    if s == "":
        return "str(empty)"
    return "str(%d chars)" % len(s)

NAME_KEYS = ("name", "label", "question", "title", "key", "field", "prompt")
VALUE_KEYS = ("value", "answer", "response", "text", "val", "content")

def pair_labels(items):
    """The question text of a {name, value} answer list, or None."""
    labels = []
    for o in items:
        if not isinstance(o, dict):
            return None
        nk = next((k for k in NAME_KEYS if isinstance(o.get(k), str)), None)
        vk = next((k for k in VALUE_KEYS
                   if o.get(k) is not None and not isinstance(o.get(k), dict)), None)
        if not nk or not vk:
            return None
        labels.append(str(o[nk]))
    return labels or None

def walk(node, depth=0):
    pad = "  " * depth
    if depth > MAX_DEPTH:
        return [pad + "..."]
    if isinstance(node, dict):
        out = []
        for i, (k, v) in enumerate(node.items()):
            if i >= MAX_KEYS:
                out.append(pad + "... %d more keys" % (len(node) - MAX_KEYS))
                break
            if isinstance(v, (dict, list)):
                out.append("%s%s:" % (pad, k))
                out.extend(walk(v, depth + 1))
            else:
                out.append("%s%s: %s" % (pad, k, describe(v)))
        return out
    if isinstance(node, list):
        if not node:
            return [pad + "[] empty"]
        labels = pair_labels(node)
        if labels:
            # A checkout question list. The labels are the questions the form
            # asked, which is public-facing text and is exactly what has to be
            # mapped to a role; only the answers beside them are withheld.
            out = ["%s[%d question/answer pairs:]" % (pad, len(node))]
            out.extend(["%s  %s = <answer withheld>" % (pad, l) for l in labels])
            return out
        out = ["%s[%d items, first one:]" % (pad, len(node))]
        out.extend(walk(node[0], depth + 1))
        same = all(isinstance(x, type(node[0])) for x in node)
        if not same:
            out.append(pad + "(! items are not all the same type)")
        return out
    return [pad + describe(node)]

def main():
    raw = sys.stdin.read()
    try:
        data = json.loads(raw)
    except ValueError:
        head = raw.strip()[:400]
        print("NOT JSON. First 400 characters:\n" + head)
        return 1
    print("\n".join(walk(data)))
    return 0

if __name__ == "__main__":
    sys.exit(main())
