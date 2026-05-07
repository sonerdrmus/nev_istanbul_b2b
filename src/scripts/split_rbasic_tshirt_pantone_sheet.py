#!/usr/bin/env python3
"""
Tek seferlik: R BASIC T-SHIRT renk tablosunu hücrelere böler.
Çıktı: storage/app/public/interface_color_variations/
"""
from __future__ import annotations

import sys
from pathlib import Path

from PIL import Image

# Varsayılan: repoda scripts/assets/tshirt-pantone-sheet.png
_SCRIPT_DIR = Path(__file__).resolve().parent
_DEFAULT_SRC = _SCRIPT_DIR / "assets" / "tshirt-pantone-sheet.png"

ROWS: list[tuple[int, list[str]]] = [
    (
        9,
        [
            "WHITE",
            "OFF WHITE",
            "CREAM",
            "SAND",
            "BROWN",
            "YELLOW",
            "BABY PINK",
            "PINK",
            "BURGUNDY",
        ],
    ),
    (
        7,
        [
            "RED",
            "ORANGE",
            "PURPLE",
            "FOREST GREEN",
            "TEAL",
            "DARK GREEN",
            "OLIVE GREEN",
        ],
    ),
    (
        7,
        [
            "SKY BLUE",
            "ROYAL BLUE",
            "BLUE",
            "NAVY",
            "DEEP NAVY",
            "NATURAL",
            "HEATHER GRAY",
        ],
    ),
    (3, ["GREY", "CHARCOAL", "BLACK"]),
]


def slug(name: str) -> str:
    return name.lower().replace(" ", "-").replace("/", "-")


def main() -> int:
    src = Path(sys.argv[1]).expanduser() if len(sys.argv) > 1 else _DEFAULT_SRC
    if not src.is_file():
        print(f"Kaynak bulunamadı: {src}", file=sys.stderr)
        return 1

    base = Path(__file__).resolve().parent.parent
    out_dir = base / "storage/app/public/interface_color_variations"
    out_dir.mkdir(parents=True, exist_ok=True)

    # Başlık / alt yazı için kenarlar (1024x683 ölçülerine göre ayarlı)
    margin_x = 28
    margin_top = 68
    margin_bottom = 36

    img = Image.open(src).convert("RGBA")
    w, h = img.size
    inner = img.crop((margin_x, margin_top, w - margin_x, h - margin_bottom))
    iw, ih = inner.size
    row_h_base = ih // 4

    order = 0
    for row_idx, (ncols, names) in enumerate(ROWS):
        y0 = row_idx * row_h_base
        y1 = (row_idx + 1) * row_h_base if row_idx < 3 else ih
        row_img = inner.crop((0, y0, iw, y1))
        rw, rh = row_img.size
        col_w = rw // ncols

        for col_idx, name in enumerate(names):
            order += 1
            x0 = col_idx * col_w
            x1 = rw if col_idx == ncols - 1 else (col_idx + 1) * col_w
            cell = row_img.crop((x0, 0, x1, rh))
            cw, ch = cell.size
            px = max(2, cw // 28)
            py = max(2, ch // 28)
            cell_trim = cell.crop((px, py, cw - px, ch - py))
            ctw, cth = cell_trim.size
            # Alt etiket / radyo çemberini kırp; üstte tişört ikonu kalsın
            bot_frac = 0.76
            swatch = cell_trim.crop((0, 0, ctw, int(cth * bot_frac)))

            fname = f"{order:02d}-{slug(name)}.png"
            out_path = out_dir / fname
            swatch.save(out_path, "PNG", optimize=True)
            print(f"Yazıldı: {out_path.relative_to(base)} ({swatch.size[0]}x{swatch.size[1]})")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
