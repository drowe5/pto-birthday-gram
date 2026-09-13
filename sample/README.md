# Sample data

Enough to exercise the page before any real orders exist.

- `card.png` — a blank 900×1260 gradient, the proportions of a 5×7 card, with
  a dark band along the bottom. The band is there because the default layout
  puts the teacher and grade in near-white along that edge, as the real
  artwork does; on a card that is pale all the way down they would be
  invisible and the sample would not show what the defaults actually do.
- `orders.xlsx` — a workbook shaped like a real Cheddar Up fundraiser export:
  four sheets, including a long `All Purchases` tab that mixes items behind
  generic `Question 1` / `Answer 1` columns. The page should ignore that one
  and pick `1-BirthdayGramGramosdeCumplea`.
- `orders.csv` — the same rows as that sheet, for the CSV path.

Every row is there to prove something:

| Student | Birthday | What it tests |
| --- | --- | --- |
| Jordan Lee | Sep 18 | age typed as `Turning 8`, and a quantity of 2 |
| Avery Nakamura | Oct 5 | an ordinary row; Oct 5 2026 is a Monday, for the weekday formats |
| Mary-Katherine Okafor | Nov 20 | a hyphenated first name, a teacher with a trailing space |
| Priya Raman | Jan 20 | a message long enough that it has to shrink; a date in the next calendar year |
| Miles O'Brien | Apr 12 | an age answered `not sure`, and an apostrophe in the file name |
| Sam Whitfield-Ross | — | no delivery date at all, so it sorts to the end |

Six rows become **seven cards**, because Jordan Lee's order was for two.

Sorted by birthday they should come out in the order above: September through
April across the school year, with the dateless row last. That the year rolls
from 2026 to 2027 is the point — sorting on month and day alone would put
January and April first.

Expect the mapping panel to fill in all nine roles by itself, *Birthday* to
land on `Delivery Date`, and the hint to read *"7 grams, 1 whose age cannot be
read, so that line is left off and 1 with no readable birthday, sorted to the
end."*
