# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""Screenshot key pages at 375/768/1440 px and report horizontal overflow, external requests,
console errors and failed requests.  Usage: shoot.py <label> [path ...]"""
import json, os, sys
from urllib.parse import urlparse
from playwright.sync_api import sync_playwright
from common import BASE, JOURNAL, LAUNCH, SHOTS

label = sys.argv[1] if len(sys.argv) > 1 else "default"
paths = sys.argv[2:] or ["index", "article/view/1", "issue/archive", "about/editorialTeam", "policies", "issue/view/1"]
widths = [int(w) for w in os.environ.get("NR_WIDTHS", "375,768,1440").split(",")]
os.makedirs(os.path.join(SHOTS, label), exist_ok=True)
report = []
with sync_playwright() as p:
    b = p.chromium.launch(**LAUNCH)
    for w in widths:
        ctx = b.new_context(viewport={"width": w, "height": 900}, locale=os.environ.get("NR_LOCALE", "en-US"))
        page = ctx.new_page()
        external, errors, failed = set(), [], []
        page.on("request", lambda r: external.add(urlparse(r.url).netloc) if urlparse(r.url).hostname not in ("localhost", None) and not r.url.startswith("data:") else None)
        page.on("console", lambda m: errors.append(m.text) if m.type == "error" else None)
        page.on("requestfailed", lambda r: failed.append(r.url))
        page.on("response", lambda r: failed.append(f"{r.status} {r.url}") if r.status >= 400 else None)
        for path in paths:
            url = path if path.startswith("http") else f"{BASE}/{JOURNAL}/{path}"
            page.goto(url, wait_until="networkidle")
            overflow = page.evaluate("() => document.documentElement.scrollWidth - document.documentElement.clientWidth")
            name = f"{path.replace('/', '_')}-{w}.png"
            page.screenshot(path=os.path.join(SHOTS, label, name), full_page=True)
            report.append({"width": w, "path": path, "overflowPx": overflow})
        report.append({"width": w, "external": sorted(external), "consoleErrors": errors, "failed": failed})
        ctx.close()
    b.close()
print(json.dumps(report, indent=1))
