#!/usr/bin/env bash
# Lists the unicode subsets Google Fonts serves for each family (look for "cyrillic").
# Usage: bash docs/tools/check-fonts.sh [Family+Name ...]
UA="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36"
FONTS=("$@")
if [ ${#FONTS[@]} -eq 0 ]; then
  FONTS=(Fraunces Work+Sans Lora Literata Playfair+Display Cormorant Spectral Manrope Onest Golos+Text Commissioner Inter)
fi
for f in "${FONTS[@]}"; do
  subs=$(curl -s -A "$UA" "https://fonts.googleapis.com/css2?family=$f&display=swap" \
    | grep -oE "^/\* [a-z-]+ \*/" | tr -d '/* ' | sort -u | tr '\n' ' ')
  echo "$f: $subs"
done
