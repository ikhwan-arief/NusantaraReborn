#!/usr/bin/env python3
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""WCAG 2.1 contrast check of the NusantaraReborn preset tokens (scripts/presets.json)."""
import json, os, sys

HERE = os.path.dirname(os.path.abspath(__file__))
presets = {k: v for k, v in json.load(open(os.path.join(HERE, "presets.json"))).items() if not k.startswith("_")}

def rgb(h):
    return [int(h[i:i + 2], 16) for i in (1, 3, 5)]

def lum(h):
    c = [x / 255 for x in rgb(h)]
    c = [x / 12.92 if x <= 0.03928 else ((x + 0.055) / 1.055) ** 2.4 for x in c]
    return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]

def ratio(a, b):
    la, lb = sorted((lum(a), lum(b)), reverse=True)
    return (la + 0.05) / (lb + 0.05)

def mix(fg, pct, base):
    a, b = rgb(fg), rgb(base)
    return "#" + "".join(f"{round(x * pct + y * (1 - pct)):02X}" for x, y in zip(a, b))

# (label, foreground, background, minimum). "box" is the tinted panel used by .nr_box.
PAIRS = [
    ("body text / page", "text", "pageBg", 4.5),
    ("body text / box", "text", "box", 4.5),
    ("muted text / page", "muted", "pageBg", 4.5),
    ("muted text / surface", "muted", "surface", 4.5),
    ("muted text / box", "muted", "box", 4.5),
    ("link primary / page", "primary", "pageBg", 4.5),
    ("link primary / box", "primary", "box", 4.5),
    ("link primary / white card", "primary", "#FFFFFF", 4.5),
    ("accent text / page", "accent", "pageBg", 4.5),
    ("accent-dark text / box", "accentDark", "box", 4.5),
    ("white on accent button", "#FFFFFF", "accent", 4.5),
    ("white on accent-dark hover", "#FFFFFF", "accentDark", 4.5),
    ("white on primary-dark nav/footer", "#FFFFFF", "primaryDark", 4.5),
    ("white on primary", "#FFFFFF", "primary", 4.5),
    ("primary-dark ink / page", "primaryDark", "pageBg", 4.5),
    ("nav active text / bg", "navActiveText", "navActiveBg", 4.5),
    ("icons accent-bright / page (non-text)", "accentBright", "pageBg", 3.0),
    ("focus ring accent-bright / page (non-text)", "accentBright", "pageBg", 3.0),
    ("focus ring white / primary-dark (non-text)", "#FFFFFF", "primaryDark", 3.0),
]

def main():
    fails = 0
    for name, p in presets.items():
        p = dict(p)
        p["box"] = mix(p["accentBright"], 0.07, p["pageBg"])
        print(f"\n{name}")
        for label, fg, bg, need in PAIRS:
            a = fg if fg.startswith("#") else p[fg]
            b = bg if bg.startswith("#") else p[bg]
            r = ratio(a, b)
            ok = r >= need
            fails += not ok
            print(f"  {'PASS' if ok else 'FAIL'} {r:5.2f}:1 (min {need}) {label}  {a} on {b}")
    print(f"\n{'ALL PASS' if not fails else f'{fails} FAIL'}")
    return 1 if fails else 0

if __name__ == "__main__":
    sys.exit(main())
