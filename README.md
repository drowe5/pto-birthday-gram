# Birthday Gram Maker

Drop in the card artwork, drop in the Cheddar Up export, drag each piece of
text where it belongs, and save a printable PNG for every gram. The
arrangement you set once is used for all of them.

`index.html` is the whole program. No server, no build step, no install, no
network: **double-click it and it runs.** Upload it to a web host if you want
other people to reach it, but nothing about it requires one.

## Using it

1. Open `index.html`.
2. Drop the card artwork on the top-left panel. Work at print resolution —
   300 dpi on a 5×7 card is 1500×2100.
3. Drop the export straight in. `.xlsx` is read directly; `.csv` and `.tsv`
   work too. There is no need to convert anything.
4. Check the mapping panel, drag the text where you want it, and press
   **Download all**.

`sample/` has a card and a stand-in export if you want to try it before the
real orders exist.

## What it expects in the export

Cheddar Up's fundraiser workbook has a tab per item. The birthday gram tab is
the one whose headers are the checkout questions themselves, and the page
picks it out on its own: it scores every sheet by how many of its headers it
can match to a role, so the long "All Purchases" tab — which mixes every item
together and hides answers behind `Question 1` / `Answer 1` columns — loses to
the shorter, better-shaped one. If it guesses wrong, the Sheet menu overrides it.

These roles are matched by header text and can all be reassigned by hand:

| Role | Matches headers like |
| --- | --- |
| First name / Last name | *Student's First Name*, *Student's Last Name* |
| From | *Please indicate who this gram is from…* |
| Message | *Message for student (will be added to certificate)* |
| Age | *Student's Age* |
| Grade | *Grade of Student* |
| Teacher | *Teacher's Name* |
| Birthday | *Delivery Date* — a gram is handed over on the day, so that column is the birthday. A form collecting a *Date of Birth* lands in the same role. |
| Quantity | *Qty Sold* |

Anything else in the export can still go on a card: **Any column** adds a text
box bound to whichever column you pick.

## Working through the list

The list is ordered **by birthday** — the order you print and deliver in — and
shows each date beside the name. *As in the file* and *by name* are there too.

Grams can be left out of a batch without being deleted:

- **The tick** beside a name drops that one, or puts it back.
- **All** and **None** at the top of the list do the whole batch.
- **Hovering a row** offers two more, which is how a rolling batch works:
  &#10514; excludes everything above that row, and &#10515; includes that row
  and everything below it. Click both on the first gram you have not done yet
  and the batch is exactly the ones still to print.

An exclusion belongs to a *gram*, not to a position, so re-sorting the list or
changing the filter never moves it onto somebody else. A birthday the page
cannot read sorts to the end, where it gets noticed rather than lost.

## Things it handles because the real data needed it

- **Ages are not typed as numbers.** One export carried `8`, `10` and
  `Turning 8` in the same column. The card supplies its own "Turning", so the
  digits are taken out of the answer. *Exactly as typed* turns that off.
- **An age that is not a number at all** — "not sure" — drops the age line
  from that card instead of printing the words. Same for a date question
  answered in prose.
- **A skipped question drops its whole line**, so no card carries a stranded
  "Turning" or "From".
- **Names arrive in two columns** and are joined for the card, while first and
  last stay separately placeable.
- **Cheddar Up ships two decoy columns**, its own unused `From` and `Message`,
  whose names would otherwise beat the real questions to those roles. Columns
  empty in every row are dropped before matching.
- **Quantity is honoured**: one order for two grams becomes two cards, with
  de-duplicated file names.
- **Ages from a birthdate** are worked out against the *Grams handed out on*
  date, not today, so a batch printed Friday for Monday still says the right
  number. This only applies to a form that collects a date of birth; where
  there is an age column, that is what the parent answered and that is what
  is used.
- **The birthday prints in whichever format suits the card** — *March 14*,
  *March 14th*, *Mon, Mar 14*, *3/14*, *Monday* on its own, and more. Because
  the delivery column carries a real year, the weekday is the day the gram is
  actually handed over. Year-bearing formats are there but usually wrong on a
  card: that year is the school year, not the year the child was born.

## Notes on the page

- **Positions are fractions, not pixels.** One saved layout fits a screen
  proof and the 300 dpi file it is printed from without being redrawn.
- **`.xlsx` is read without a library.** An xlsx is a zip of XML, and the
  browser can inflate and parse both on its own. That is what keeps this a
  single file that works offline. It needs Chrome, Edge, or Safari 16.4 and
  up; anything older should use CSV, and the page says so rather than failing
  quietly.
- **The layout is remembered in this browser** as you work, along with the
  artwork when it is small enough to store. The orders are not kept: on a
  shared school computer they should not outlive the session. *Save layout*
  writes a file you can keep with the artwork or hand to someone else.
- **Download all** writes one zip, assembled so only one card is held in
  memory at a time rather than the whole batch.

## parked-cheddarup-api/

An earlier attempt to read orders from the Cheddar Up API live, rather than
from an export. It is parked, not wired up: Cheddar Up publishes no API
reference, and the endpoints could not be confirmed.

The code is sound and tested against a stand-in server, so it is kept for
whenever the API is pinned down. See the folder's own README. Nothing in
`index.html` refers to it, and the page needs none of it.
