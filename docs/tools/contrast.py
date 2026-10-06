"""WCAG 2.x contrast check for the Body Balance palette (see docs/07-design-findings.md).

Usage: python docs/tools/contrast.py
"""


def luminance(hex_color: str) -> float:
    h = hex_color.lstrip("#")
    r, g, b = (int(h[i:i + 2], 16) / 255 for i in (0, 2, 4))

    def channel(c: float) -> float:
        return c / 12.92 if c <= 0.04045 else ((c + 0.055) / 1.055) ** 2.4

    return 0.2126 * channel(r) + 0.7152 * channel(g) + 0.0722 * channel(b)


def contrast(a: str, b: str) -> float:
    hi, lo = sorted((luminance(a), luminance(b)), reverse=True)
    return (hi + 0.05) / (lo + 0.05)


BACKGROUNDS = {"ivory #FAF8F3": "#FAF8F3", "beige #F1ECDF": "#F1ECDF", "beige2 #EFE8D8": "#EFE8D8"}
TEXT = ["#2B2B26", "#55534A", "#8A876F", "#A79F87", "#4A5D4E", "#C97B5C"]
PAIRS = [
    ("white on sage", "#FFFFFF", "#4A5D4E"),
    ("white on terracotta", "#FFFFFF", "#C97B5C"),
    ("ivory on terracotta", "#FAF8F3", "#C97B5C"),
    ("border #E7E1D4 on ivory", "#E7E1D4", "#FAF8F3"),
]

if __name__ == "__main__":
    for fg in TEXT:
        print(fg, "  ".join(f"{name}: {contrast(fg, bg):.2f}" for name, bg in BACKGROUNDS.items()))
    for name, fg, bg in PAIRS:
        print(f"{name}: {contrast(fg, bg):.2f}")
