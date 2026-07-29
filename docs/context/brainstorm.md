# Brainstorm

Raw ideas, sorted by implementation leverage — things closest to the core product loop first, speculative directions last.

---

## Core loop (do these next)

- [x] Add `slug`, `wikidata_qid`, `type_namespace` to `items` table
- [x] Wire Wikidata enrichment into item show page when `wikidata_qid` is set
- [ ] Add Wikidata concept search to the initialization/claim flow (search-as-you-type → select a QID to categorize the object)
- [x] `/{namespace}/{slug}` route resolving through ItemController (e.g., `/element/oxygen-tank-1`)
- [ ] Add `wikidata_entities` local cache table to reduce public API calls
- [ ] Large omnipresent search/command bar — AJAX, fuzzy, character-by-character; emulates CLI responsiveness
- [ ] Breadcrumbs on each item page showing hierarchical context: `index > element > oxygen > oxygen-tank-shelf-3`

---

## QR and label

- [x] Print a pre-initialization QR label sheet: generate 1–30 UUID URLs without creating Item records, apply labels, then initialize on first scan
- [ ] Consolidate the dashboard's creation paths under one “Add object” choice: create from photo or print labels first
- [ ] Add a one-label calibration print and optional horizontal/vertical offsets before printing a full sheet
- [ ] Add UUID matching to dashboard search so a label can be looked up directly
- [ ] Simplify item-level printing to a default “Print label” action with alternate sizes behind a disclosure
- [ ] Allow bare item initialization without a required photo, so a label can be claimed when documentation must happen later
- [ ] Zebra label maker integration: https://zsbportal.zebra.com/designer
- [ ] Avery diskette labels from CTI: https://app.print.avery.com/
- [ ] Demo scanning behavior via vertical shortform video (Instagram/TikTok) for product discovery
- [ ] Serial number photo capture: take a photo of a device serial number → auto-fill item details via OCR + lookup

---

## Data model and graph

- [ ] Migrate or retire `Thing` model once `Item` covers slug + scan session tracking
- [ ] Link local `Item` records to Wikidata QIDs via `wikidata_qid` field (relation, not identity replacement)
- [ ] Graph-like relationships between items: ownership chains, containment, provenance
- [ ] Items/UUIDs as "anchors" that can forward to or contain other items
- [ ] Feasibility of graph database as backend vs. relational with graph transformation
- [ ] All index instances share a central database? Or federated instances?

---

## Product directions

- [ ] GPS-aware asset scanner: scan item → record GPS coordinates → resolve to address + room + container
- [ ] Gamify scanning: show total scan count next to username in timeline
- [ ] Object provenance and custody tracking — museums, archives, supply chains, Hudson Valley House Parts
- [ ] Chemical elements and molecules as a first-class namespace demo
- [ ] index as a deployable intranet: integrate company systems into a single interface
- [ ] Blockchain tracking for high-value physical item provenance (long-term)

---

## Architecture questions

- [ ] Python/Django ("indexpy") vs. PHP/Laravel — feasibility comparison
- [ ] Add another index installation as an upstream source (federation)
- [ ] "Automated model creator" whose database connections gracefully degrade

---

## Monetization

- [ ] Stripe subscription: "pro" tier via a prominent upgrade button. Evaluate Laravel Cashier vs. direct Stripe API.
- [ ] Email subscription list / campaign

---

## Research threads

- Exogen (exposome): phenotypic behavioral strata across generations raised in differing chemical environments — lead, asbestos, microplastics, nuclear fallout, heavy metals. Relevant for chemical substance namespace.
- Reference: Stomper Kreeg - NPC - Ascension Database.

---

## Graph database resources

- https://neo4j.com/labs/neosemantics/how-to-guide/
- https://github.com/findie/wikidata-neo4j-importer
