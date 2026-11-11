# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""Install the built 3.3 tarball through Settings > Website > Plugins > Upload, enable it and
select it as the theme of journal "nusantara" through the Appearance form."""
import os, sys
from playwright.sync_api import sync_playwright
from common import BASE, JOURNAL, LAUNCH, ROOT, SHOTS, login

TARBALL = os.path.join(ROOT, "dist", "ojs33", "nusantaraReborn-1.0.2.0.tar.gz")
os.makedirs(SHOTS, exist_ok=True)


def open_tab(page, name):
    page.goto(f"{BASE}/{JOURNAL}/management/settings/website")
    page.wait_for_load_state("networkidle")
    page.locator(f"a[href='#{name}'], button#{name}-button, [aria-controls='{name}']").first.click()
    page.wait_for_timeout(2500)

with sync_playwright() as p:
    b = p.chromium.launch(**LAUNCH)
    page = b.new_page(viewport={"width": 1440, "height": 1000})
    login(page)
    if not os.environ.get("NR_SKIP_UPLOAD"):
        open_tab(page, "plugins")
        page.get_by_text("Upload A New Plugin").click()
        page.wait_for_selector(".pkp_modal input[type=file]", state="attached")
        page.set_input_files(".pkp_modal input[type=file]", TARBALL)
        page.wait_for_timeout(5000)
        page.locator(".pkp_modal button.submitFormButton, .pkp_modal button:has-text('Save')").first.click()
        page.wait_for_timeout(4000)
        page.screenshot(path=os.path.join(SHOTS, "install-01-uploaded.png"))
    # Enable the plugin in the installed plugins grid.
    open_tab(page, "plugins")
    row = page.locator("tr", has_text="Indonesian character").first
    row.wait_for(timeout=15000)
    box = row.locator("input[type=checkbox]").first
    if not box.is_checked():
        box.check()
        page.wait_for_timeout(2500)
    print("enabled:", box.is_checked())
    # Select the theme.
    open_tab(page, "appearance")
    page.select_option("select#theme-themePluginPath-control, select[name=themePluginPath]", "nusantaraReborn")
    page.wait_for_timeout(1500)
    page.locator("#theme button:has-text('Save'), .pkpFormPage button:has-text('Save')").first.click()
    page.wait_for_timeout(3000)
    page.screenshot(path=os.path.join(SHOTS, "install-02-theme-options.png"), full_page=True)
    b.close()
