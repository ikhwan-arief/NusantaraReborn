# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""Shared helpers for NusantaraReborn OJS 3.3 browser tests (local synthetic stack only)."""
import os
HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(os.path.dirname(HERE))
RUNTIME = os.path.join(ROOT, "tests", "runtime33")
BASE = os.environ.get("NR_BASE", "http://localhost:8099/index.php")
JOURNAL = "nusantara"
SHOTS = os.path.join(ROOT, "tests", "reports", "ojs33", "screenshots")
LAUNCH = {"channel": "chrome", "headless": True}


def admin_password():
    with open(os.path.join(RUNTIME, "credentials.txt")) as f:
        for line in f:
            if line.startswith("admin "):
                return line.split()[1]
    raise SystemExit("no admin credentials")


def login(page, journal=JOURNAL):
    page.goto(f"{BASE}/{journal}/login")
    page.fill("input[name=username]", "admin")
    page.fill("input[name=password]", admin_password())
    page.click("button.submit, button[type=submit]")
    page.wait_for_load_state("networkidle")
