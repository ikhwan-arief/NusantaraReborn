# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""Install the built tarball through Settings > Website > Plugins > Upload, enable it and select it
as the theme of journal "nusantara" through the Appearance form.

  NR_TRACK=33 python install_theme.py          (OJS 3.3, default)
  NR_TRACK=34 python install_theme.py          (OJS 3.4)
  NR_TRACK=35 python install_theme.py          (OJS 3.5)
  NR_SKIP_UPLOAD=1 ...                          (plugin already uploaded: only enable + select)
"""
import os
from playwright.sync_api import sync_playwright
from common import BASE, JOURNAL, LAUNCH, ROOT, SHOTS, TRACK, login

VERSION = {"33": "1.0.2.0", "34": "3.0.0.0", "35": "2.0.0.0"}[TRACK]
TARBALL = os.path.join(ROOT, "dist", "ojs" + TRACK, f"nusantaraReborn-{VERSION}.tar.gz")
os.makedirs(SHOTS, exist_ok=True)


def open_tab(page, name, label):
    page.goto(f"{BASE}/{JOURNAL}/management/settings/website")
    page.wait_for_load_state("networkidle")
    if TRACK in ("34", "35"):
        page.get_by_role("tab", name=label).first.click()
    else:
        page.locator(f"a[href='#{name}'], button#{name}-button, [aria-controls='{name}']").first.click()
    page.wait_for_timeout(2500)


with sync_playwright() as p:
    b = p.chromium.launch(**LAUNCH)
    page = b.new_page(viewport={"width": 1440, "height": 1000})
    login(page)
    if not os.environ.get("NR_SKIP_UPLOAD"):
        open_tab(page, "plugins", "Plugins")
        link = page.get_by_text("Upload A New Plugin").first
        link.wait_for(state="visible", timeout=30000)
        link.click()
        # The modal form loads over AJAX; a fresh stack with cold caches can be slow.
        # OJS 3.3 opens a .pkp_modal; OJS 3.5 opens a side panel with role="dialog".
        modal_input = ".pkp_modal input[type=file], [role=dialog] input[type=file]"
        page.wait_for_selector(modal_input, state="attached", timeout=90000)
        page.locator(modal_input).first.set_input_files(TARBALL)
        page.wait_for_timeout(5000)
        page.locator(".pkp_modal button.submitFormButton, .pkp_modal button:has-text('Save'), [role=dialog] button:has-text('Save')").first.click()
        page.wait_for_timeout(5000)
        page.screenshot(path=os.path.join(SHOTS, "install-01-uploaded.png"))
    # Enable the plugin in the installed plugins grid.
    open_tab(page, "plugins", "Plugins")
    row = page.locator("tr", has_text="Indonesian character").first
    row.wait_for(timeout=15000)
    box = row.locator("input[type=checkbox]").first
    if not box.is_checked():
        box.check()
        page.wait_for_timeout(3000)
    print("enabled:", box.is_checked())
    # Select the theme.
    open_tab(page, "appearance", "Appearance")
    page.select_option("select[name=themePluginPath]", "nusantaraReborn")
    page.wait_for_timeout(2000)
    page.locator("#theme button:has-text('Save'), .pkpFormPage button:has-text('Save'), form button:has-text('Save')").first.click()
    page.wait_for_timeout(3500)
    page.screenshot(path=os.path.join(SHOTS, "install-02-theme-options.png"), full_page=True)
    b.close()
