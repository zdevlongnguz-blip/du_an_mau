#!/usr/bin/env bash
# Chạy ở thư mục gốc dự án (Git Bash trên Windows cũng được).
# Báo lỗi nếu có file thay đổi nằm ngoài vùng được phép đổi giao diện người dùng.
set -u
git rev-parse --is-inside-work-tree >/dev/null 2>&1 || { echo "Chưa phải repo git. Chạy: git init && git add -A && git commit -m baseline"; exit 2; }

changed=$( (git diff --name-only HEAD; git ls-files --others --exclude-standard) | sort -u )
bad=0

while IFS= read -r f; do
  [ -z "$f" ] && continue
  case "$f" in
    views/web/home-products.php|views/web/home-vue.php) echo "CẤM (file thừa, không đụng): $f"; bad=1 ;;
    views/main.php|views/web/*.php|views/auth/*.php|views/user/*.php) ;;
    assets/css/*|assets/img/*|assets/fonts/*) ;;
    assets/js/user-*.js) ;;
    docs/ui-redesign/*|.github/*|tools/*) ;;
    *) echo "NGOÀI PHẠM VI: $f"; bad=1 ;;
  esac
done <<< "$changed"

# Cảnh báo kỹ thuật trong file thay đổi
for f in $(echo "$changed" | grep -E '^views/(main|web/|auth/|user/).*\.php$|^views/main\.php$'); do
  [ -f "$f" ] || continue
  if grep -q "cdn.tailwindcss.com" "$f"; then echo "KHÔNG DÙNG Tailwind CDN: $f"; bad=1; fi
  if command -v php >/dev/null 2>&1; then
    php -l "$f" >/dev/null 2>&1 || { echo "LỖI CÚ PHÁP PHP: $f"; php -l "$f"; bad=1; }
  fi
done

# Dòng PHP bị xóa/sửa: chỉ cảnh báo để người dùng xem lại bằng mắt
removed=$(git diff -U0 HEAD -- views/main.php views/web views/auth views/user 2>/dev/null | grep -E '^-[^-].*(<\?php|<\?=|\$_SESSION|\$_GET|\$_POST)' | head -40)
if [ -n "$removed" ]; then
  echo ""
  echo "XEM LẠI: các dòng PHP sau bị xóa hoặc sửa (đảm bảo không mất logic):"
  echo "$removed"
fi

# Cảnh báo class hs-* dùng trong view nhưng CHƯA có selector trong user-theme.css
css="assets/css/user-theme.css"
if [ -f "$css" ]; then
  used=$(for f in $(echo "$changed" | grep -E '^views/(main\.php|web/|auth/|user/)'); do [ -f "$f" ] && grep -ohE 'hs-[a-zA-Z0-9_-]+' "$f"; done | sort -u)
  missing=""
  for c in $used; do
    case "$c" in *-) continue ;; esac   # bỏ qua class ghép động như hs-chip--<?= $x ?>
    grep -qE "\.${c}([^a-zA-Z0-9_-]|$)" "$css" || missing="$missing $c"
  done
  if [ -n "$missing" ]; then
    echo ""; echo "THIẾU CSS: các class sau dùng trong view nhưng không có selector trong $css:"
    for c in $missing; do echo "  .$c"; done
    bad=1
  fi
fi

if [ $bad -eq 0 ]; then echo "OK: mọi thay đổi nằm trong vùng được phép."; else echo "THẤT BẠI: xem các dòng trên."; fi
exit $bad