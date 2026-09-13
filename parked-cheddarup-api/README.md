# Reading orders from the Cheddar Up API (parked)

Not wired up. `index.html` reads exports and refers to nothing in here.

## Why it is parked

Cheddar Up publishes no API reference. The endpoint paths, and even which
header the key belongs in, were never confirmed — a probe run found nothing to
authenticate against. Rather than ship a page whose Cheddar Up tab might
simply never work, the integration was taken out of the interface.

## What is here, and what was actually proven

`cheddar.php` is a read-only proxy. The browser cannot call the API itself:
the response carries no CORS header for your page's origin, and a key written
into page JavaScript is readable by anyone who opens View Source. The proxy
holds the key server-side, forwards GET only, and only to the host named in
`config.php`.

It was tested end to end against a stand-in server that mimics the expected
shape. Confirmed working: the key is attached in any of five auth styles, the
access password is enforced, extra query parameters are forwarded, upstream
status codes pass through, and both path traversal and absolute-URL smuggling
are refused. What was never tested is Cheddar Up itself.

`tools/probe.sh` finds which URLs are real and which auth style a key wants.
`tools/shape.py` prints the structure of a JSON response with the values
replaced by descriptions of themselves, so a response can be shared without
the people in it. Both are documented in `tools/README.md`.

## Picking it back up

1. Find the real endpoints — `tools/README.md` explains both the probe and the
   more reliable route of watching my.cheddarup.com's own network traffic.
2. Put the base URL and auth style in `config.php`, copied from
   `config.sample.php`. Set `access_token`, or anyone who finds the URL of
   `cheddar.php` can read the account through it.
3. Restore the Cheddar Up panel in `index.html`. It was removed in the commit
   that added `.xlsx` reading, so `git log -- parked-cheddarup-api` will find
   the markup and the fetch code as they were.

Upload `.htaccess` alongside any of this: it is what stops `config.php` from
being served.
