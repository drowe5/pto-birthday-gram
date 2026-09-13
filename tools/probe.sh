#!/usr/bin/env bash
# Find out how to talk to the Cheddar Up API, without printing the key.
#
#   chmod +x probe.sh && ./probe.sh
#
# Stage 1 asks a spread of likely URLs with no credentials at all. The status
# code is the tell: 401/403 means a real endpoint that wants auth -- that is
# the one worth attacking. 404 means the host is right and the path is wrong.
# A resolve failure means the host is wrong.
#
# Stage 2 takes every URL that asked for auth and tries each way of presenting
# the key until one returns 200.
#
# The key is read without echoing and never appears in the output, so the whole
# transcript is safe to paste.

set -u
CURL=(curl -sS -m 12 -o /dev/null)

if [ -z "${KEY:-}" ]; then
  printf 'Cheddar Up API key (nothing will echo): '
  read -rs KEY
  printf '\n\n'
fi
[ -z "$KEY" ] && { echo "No key given; stage 2 will be skipped."; }

URLS=(
  "https://api.cheddarup.com/"
  "https://api.cheddarup.com/v1/"
  "https://api.cheddarup.com/v1/users/me"
  "https://api.cheddarup.com/v1/me"
  "https://api.cheddarup.com/v1/collections"
  "https://api.cheddarup.com/api/v1/collections"
  "https://my.cheddarup.com/api/v1/users/me"
  "https://my.cheddarup.com/api/v1/collections"
  "https://www.cheddarup.com/api/v1/collections"
)

echo "=== stage 1: which URLs are real (no credentials sent) ==="
printf '%-6s %-10s %s\n' "code" "type" "url"
NEEDS_AUTH=()
for u in "${URLS[@]}"; do
  out=$("${CURL[@]}" -w '%{http_code} %{content_type}' "$u" 2>/dev/null)
  code=${out%% *}
  ctype=${out#* }
  case "$ctype" in *json*) ctype="json" ;; *html*) ctype="html" ;; *) ctype="${ctype:0:10}" ;; esac
  case "$code" in
    401|403) note="WANTS AUTH"; NEEDS_AUTH+=("$u") ;;
    200)     note="open" ;;
    404)     note="no path" ;;
    ""|000)  code="---"; ctype="---"; note="unreachable (bad host or no route)" ;;
    *)       note="" ;;
  esac
  printf '%-6s %-10s %-52s %s\n' "$code" "$ctype" "$u" "$note"
done

[ -z "${KEY:-}" ] && exit 0
if [ ${#NEEDS_AUTH[@]} -eq 0 ]; then
  echo
  echo "Nothing asked for credentials, so there is no endpoint here to"
  echo "authenticate against. See the DevTools route instead."
  exit 0
fi

echo
echo "=== stage 2: how the key should be presented ==="
for u in "${NEEDS_AUTH[@]}"; do
  echo "--- $u"
  printf '  %-12s %s\n' "bearer"    "$("${CURL[@]}" -w '%{http_code}' -H "Authorization: Bearer $KEY" "$u")"
  printf '  %-12s %s\n' "token"     "$("${CURL[@]}" -w '%{http_code}' -H "Authorization: Token $KEY" "$u")"
  printf '  %-12s %s\n' "x-api-key" "$("${CURL[@]}" -w '%{http_code}' -H "X-Api-Key: $KEY" "$u")"
  printf '  %-12s %s\n' "apikey"    "$("${CURL[@]}" -w '%{http_code}' -H "apikey: $KEY" "$u")"
  printf '  %-12s %s\n' "basic"     "$("${CURL[@]}" -w '%{http_code}' -u "$KEY:" "$u")"
  printf '  %-12s %s\n' "query"     "$("${CURL[@]}" -w '%{http_code}' "$u?api_key=$KEY")"
done

echo
echo "A 200 above is the answer: that URL with that style. Put the style in"
echo "config.php as auth_style, and fetch the body with:"
echo '  curl -sS -H "Authorization: Bearer $KEY" <url> | python3 shape.py'
