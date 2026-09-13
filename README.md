# Birthday Gram Maker

Drop in the card artwork, load the birthday gram orders, drag each piece of
text where it belongs, and save a PNG for every gram. The arrangement you set
once is used for all of them.

This is a plain page you upload to your own web server, not a Claude Artifact.
It has to be, for two reasons: an Artifact runs under a content policy that
blocks any call to `api.cheddarup.com`, and a framed Artifact cannot hand the
viewer a file to save.

## What goes on the server

| File | Needed for |
| --- | --- |
| `index.html` | always |
| `cheddar.php` | only for reading orders straight from Cheddar Up |
| `config.php` | only for the same, copied from `config.sample.php` |
| `.htaccess` | only for the same, and it must go up with them |

Everything is drawn in the browser. Once the page has loaded, no artwork,
name, or message is sent anywhere.

## Trying it before you have real orders

`sample/` holds a blank card and a stand-in export built around the awkward
cases: a quantity of two, three date formats, an unreadable date, a message
long enough to have to shrink, and a row that is not a gram. See
`sample/README.md` for what each row is there to prove.

## Spreadsheet only

Upload `index.html` anywhere and open it. In Cheddar Up, open the collection,
choose **Reports → Export**, and take the item-level report so each gram is its
own row. Drop that CSV on the page.

This needs no key, no PHP, and no configuration. It also works with the file
opened straight off a desktop, with no web server at all.

Only CSV and TSV are read. An `.xlsx` has to be opened and re-saved as CSV
first; the page says so rather than failing quietly.

## Reading orders straight from Cheddar Up

The browser cannot call Cheddar Up itself. The API answers without a CORS
header for your domain, so the browser refuses to hand the response to the
page; and a key written into page JavaScript is readable by anyone who opens
View Source. `cheddar.php` solves both: the page calls your own domain, and
the key stays in a file the browser never sees.

1. Upload `index.html`, `cheddar.php`, `.htaccess` and `config.sample.php` into
   the same folder.
2. Copy `config.sample.php` to `config.php` and put your key in `api_key`.
3. Set `access_token` to any long random string. Leave it empty and anyone who
   guesses the URL of `cheddar.php` can read your Cheddar Up account through it.
4. Open the page, switch the Orders panel to **Cheddar Up**, type that same
   string into *Access password*, and press **Check setup**. It reports the PHP
   version, the last four characters of the key, and anything still missing.
5. Press **Get** on `/v1/collections` to find the birthday gram collection,
   then follow its id into the endpoint that lists its payments.
6. Press **Use these rows**, then match each column to what it holds.

### If the key comes back refused

Cheddar Up may want the key presented a different way. Change `auth_style` in
`config.php` and press **Check setup** again. The five it understands are
`bearer`, `token`, `x-api-key`, `basic` and `query`. A 401 usually means the
wrong style; a 404 means the wrong path.

`cheddar.php` forwards GET requests only, and only to the host in `base_url`,
so the worst a wrong path can do is read nothing.

### Not verified against the live API

The endpoint paths above are a starting point, not documentation. This was
built without ever calling Cheddar Up: the network this was written on blocks
`cheddarup.com`, so the proxy was tested against a stand-in that mimics their
shape, and the page was tested end to end against a real CSV export layout.

The page is written to survive that. It does not assume any particular field
names: it takes whatever the endpoint returns, finds the list of records in it,
folds any `{name, value}` answer list into named columns, and asks you which
column is which. Question text like *"Student's Name"* becomes a column the
same way it would from a spreadsheet.

## Notes on the page

- **Positions are fractions, not pixels.** A saved layout fits a small proof
  and the 300&nbsp;dpi file it is printed from without being redrawn. Work at
  the size you will print: 300&nbsp;dpi on a 5×7 card is 1500×2100.
- **Ages** are worked out against the *Grams handed out on* date, not today,
  so a batch printed on Friday for Monday still says the right number.
- **A date question that was not answered with a date** — "not sure", "ask
  mom" — leaves the birthdate and age lines off that card rather than printing
  the words. The mapping panel says how many rows that affects before you
  print. Setting the date format to *exactly as typed* overrides it.
- **An order that skipped a question** drops that whole line, so no card
  carries a stranded "Turning" or "From".
- **Quantity** is honoured: one order for three grams becomes three cards, and
  the file names are de-duplicated.
- **Download all** writes one zip, assembled so that only one card is held in
  memory at a time rather than the whole batch. Turn the zip off and the
  browser saves each PNG separately after asking once.
- **The layout is remembered in the browser** as you work, along with the
  artwork when it is small enough to be worth storing. The orders are not kept:
  on a shared school computer they should not outlive the session. *Save
  layout* writes a file you can keep with the artwork or hand to someone else.
- **The access password is deliberately not remembered**, for the same reason.

## Keeping it private

The page has a `noindex` tag, but that is a request, not a lock. If the orders
matter, put the whole folder behind a password — in cPanel that is *Directory
Privacy*. It costs nothing and covers `cheddar.php` at the same time.
