#!/usr/bin/env python3
# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
"""Generate the NusantaraReborn decorative SVGs from scripts/presets.json.

Writes, for each preset:
  shared/assets/ornaments/<preset>.svg  repeating tile used by CSS as a decorative background
  shared/assets/covers/<preset>.svg     text-free fallback cover for issues without a cover image

The motifs are original abstract interpretations (leaves, waves, repeating geometry, simple
bands, crossing threads). They carry no text, claims, logos or emblems.
Run: python3 -I scripts/make-visuals.py
"""
import json, os

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(HERE)
PRESETS = {k: v for k, v in json.load(open(os.path.join(HERE, "presets.json"))).items() if not k.startswith("_")}
HEADER = ("<!--\n  Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).\n"
          "  Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.\n"
          "  NusantaraReborn original decorative asset. License: CC BY-NC 4.0.\n-->\n")

# Ornament colour on dark hero/footer backgrounds (decorative only, never text).
ORNAMENT_COLOUR = {
    "zamrudKhatulistiwa": "#C9A253",
    "bahariNusantara": "#4FB7B9",
    "batikSogan": "#D29A6E",
    "merahPutih": "#FFFFFF",
    "tenunSenja": "#D88A6C",
}


def tile(kind, c):
    """Return (width, height, inner SVG) for one seamless tile."""
    s = f'fill="none" stroke="{c}" stroke-width="1.6" stroke-linecap="round"'
    if kind == "leaves":
        return 64, 64, (
            f'<g {s}><path d="M16 50 C10 36 16 22 32 14 C34 30 28 44 16 50 Z"/><path d="M16 50 L30 18"/>'
            f'<path d="M48 26 C58 22 64 30 62 40 C52 42 46 36 48 26 Z" transform="translate(-6 10)"/></g>'
            f'<circle cx="52" cy="10" r="2" fill="{c}"/>')
    if kind == "waves":
        return 80, 28, (
            f'<g {s}><path d="M0 9 C10 2 20 2 30 9 S50 16 60 9 S70 2 80 9"/>'
            f'<path d="M0 21 C10 14 20 14 30 21 S50 28 60 21 S70 14 80 21" opacity=".6"/></g>')
    if kind == "geometri":
        # Repeating original geometry: four rounded lozenges around a dot.
        return 48, 48, (
            f'<g {s}><path d="M24 4 C30 12 30 18 24 22 C18 18 18 12 24 4 Z"/>'
            f'<path d="M24 44 C30 36 30 30 24 26 C18 30 18 36 24 44 Z"/>'
            f'<path d="M4 24 C12 18 18 18 22 24 C18 30 12 30 4 24 Z"/>'
            f'<path d="M44 24 C36 18 30 18 26 24 C30 30 36 30 44 24 Z"/></g>'
            f'<circle cx="24" cy="24" r="1.8" fill="{c}"/><circle cx="0" cy="0" r="1.8" fill="{c}"/>'
            f'<circle cx="48" cy="0" r="1.8" fill="{c}"/><circle cx="0" cy="48" r="1.8" fill="{c}"/><circle cx="48" cy="48" r="1.8" fill="{c}"/>')
    if kind == "lines":
        return 40, 40, (
            f'<g {s}><path d="M0 40 L40 0"/><path d="M-10 10 L10 -10" /><path d="M30 50 L50 30"/></g>')
    if kind == "tenun":
        return 36, 36, (
            f'<g {s}><path d="M0 0 L36 36"/><path d="M36 0 L0 36"/><path d="M18 0 L18 6 M18 30 L18 36 M0 18 L6 18 M30 18 L36 18" opacity=".7"/></g>'
            f'<path d="M18 12 L24 18 L18 24 L12 18 Z" fill="{c}" opacity=".55"/>')
    raise ValueError(kind)


def ornament_svg(name, p):
    w, h, inner = tile(p["ornament"], ORNAMENT_COLOUR[name])
    return (HEADER + f'<svg xmlns="http://www.w3.org/2000/svg" width="{w}" height="{h}" viewBox="0 0 {w} {h}" aria-hidden="true">'
            f'{inner}</svg>\n')


def cover_svg(name, p):
    w, h, inner = tile(p["ornament"], ORNAMENT_COLOUR[name])
    dark, primary, bright, page = p["primaryDark"], p["primary"], p["accentBright"], p["pageBg"]
    return (HEADER +
            '<svg xmlns="http://www.w3.org/2000/svg" width="720" height="980" viewBox="0 0 720 980" aria-hidden="true">\n'
            f'  <defs>\n    <pattern id="nrMotif" width="{w}" height="{h}" patternUnits="userSpaceOnUse">{inner}</pattern>\n'
            f'    <linearGradient id="nrGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{dark}"/><stop offset="1" stop-color="{primary}"/></linearGradient>\n  </defs>\n'
            f'  <rect width="720" height="980" fill="{page}"/>\n'
            f'  <rect width="720" height="560" fill="url(#nrGrad)"/>\n'
            f'  <rect width="720" height="560" fill="url(#nrMotif)" opacity=".32"/>\n'
            f'  <rect x="0" y="560" width="720" height="18" fill="{bright}"/>\n'
            f'  <path d="M0 760 C160 700 300 820 450 750 C560 700 640 720 720 690 L720 980 L0 980 Z" fill="{primary}" opacity=".12"/>\n'
            f'  <path d="M0 840 C170 790 300 880 460 830 C580 792 650 806 720 782 L720 980 L0 980 Z" fill="{bright}" opacity=".18"/>\n'
            '</svg>\n')


def main():
    for folder in ("ornaments", "covers"):
        os.makedirs(os.path.join(ROOT, "shared", "assets", folder), exist_ok=True)
    for name, p in PRESETS.items():
        with open(os.path.join(ROOT, "shared", "assets", "ornaments", name + ".svg"), "w") as f:
            f.write(ornament_svg(name, p))
        with open(os.path.join(ROOT, "shared", "assets", "covers", name + ".svg"), "w") as f:
            f.write(cover_svg(name, p))
        print("wrote", name)


if __name__ == "__main__":
    main()
