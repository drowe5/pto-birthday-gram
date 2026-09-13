# Sample data

Enough to exercise the page before any real orders exist.

- `card.png` — a blank 900×1260 gradient, the proportions of a 5×7 card.
- `orders.csv` — a stand-in export carrying the cases that actually break
  things:

| Row | What it is there for |
| --- | --- |
| Avery Nakamura | an ordinary row, ISO date |
| Jordan Lee | quantity 2, so one order must become two cards |
| Spirit T-Shirt | not a gram at all, to be filtered out |
| Priya Raman | a message long enough to have to shrink, and a written-out date |
| Miles O'Brien | a birthdate question answered "not sure" |

Load both, then set *Only rows where* → `Item Name` contains `Birthday Gram`.
Six rows become five cards: the T-shirt drops out, Jordan Lee appears twice,
and Miles keeps his name and message but loses the birthdate and age lines.
