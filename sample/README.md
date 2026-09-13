# Sample data

Enough to exercise the page before any real orders exist.

- `card.png` — a blank 900×1260 gradient, the proportions of a 5×7 card.
- `orders.xlsx` — a workbook shaped like a real Cheddar Up fundraiser export:
  four sheets, including a long `All Purchases` tab that mixes items behind
  generic `Question 1` / `Answer 1` columns. The page should ignore that one
  and pick `1-BirthdayGramGramosdeCumplea`.
- `orders.csv` — the same rows as that sheet, for the CSV path.

Every row is there to prove something:

| Student | What it tests |
| --- | --- |
| Avery Nakamura | an ordinary row |
| Jordan Lee | age typed as `Turning 8`, and a quantity of 2 |
| Priya Raman | a message long enough that it has to shrink to fit |
| Mary-Katherine Okafor | a hyphenated first name, a teacher with a trailing space |
| Miles O'Brien | an age answered `not sure`, and an apostrophe in the file name |
| Sam Whitfield-Ross | a hyphenated surname and a trailing space in the message |

Six rows become **seven cards**, because Jordan Lee's order was for two.

Expect the mapping panel to fill in all nine roles by itself, and the hint to
read *"7 grams, 1 whose age cannot be read, so that line is left off."*
