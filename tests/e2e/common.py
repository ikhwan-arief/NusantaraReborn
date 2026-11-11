# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""Shared helpers for NusantaraReborn OJS 3.3 browser tests (local synthetic stack only)."""
import os
HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(os.path.dirname(HERE))
TRACK = os.environ.get("NR_TRACK", "33")
RUNTIME = os.path.join(ROOT, "tests", "runtime" + TRACK)
PORTS = {"33": "8099", "34": "8102", "35": "8100"}
BASE = os.environ.get("NR_BASE", "http://localhost:" + PORTS[TRACK] + "/index.php")
# OJS 3.5 URLs carry the locale after the journal path.
JOURNAL = "nusantara/en" if TRACK == "35" else "nusantara"
SHOTS = os.path.join(ROOT, "tests", "reports", "ojs" + TRACK, "screenshots")
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
