# Working out the Cheddar Up API

Cheddar Up publishes no API reference, so the endpoints have to be found
rather than looked up. These two scripts do that without putting the key or
anybody's data where it does not belong.

## probe.sh

    ./tools/probe.sh

Asks a spread of likely URLs with no credentials at all, then takes whichever
ones demand auth and tries each way of presenting the key.

Read stage 1 by status code:

| Code | Means |
| --- | --- |
| 401 / 403 | a real endpoint that wants credentials — the one worth pursuing |
| 200 | real and open |
| 404 | host is right, path is wrong |
| `---` | host is wrong, or nothing is listening |

A 200 in stage 2 names both the endpoint and the `auth_style` for `config.php`.

The key is read without echoing and is never printed, so the output is safe to
paste anywhere.

## shape.py

    curl -sS -H "Authorization: Bearer $KEY" <url> | python3 tools/shape.py

Prints the structure of a JSON response — keys, types, array lengths — with
every value replaced by a description of itself. `"Avery Nakamura"` becomes
`str(14 chars)`, an address becomes `email`, a birthdate becomes
`date YYYY-MM-DD`.

Checkout questions are the deliberate exception. In a `{name, value}` answer
list the names are the questions the form asked, and those are printed in full
because they become the page's column names and carry nothing private. Only
the answers beside them are withheld.

That combination is what makes a response safe to share while still being
enough to write code against.

## When the guessing runs out

If nothing in stage 1 answers, stop guessing and watch the real thing. Sign in
at my.cheddarup.com, open the browser's developer tools to the Network tab,
filter to Fetch/XHR, and click into the birthday gram collection. The app is
talking to its own API, so every request it makes shows the base URL, the path
shape, and the header the key belongs in. Right-click any of them and choose
*Copy as cURL* — but strip the session cookie before pasting it anywhere.
