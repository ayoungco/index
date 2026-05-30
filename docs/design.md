# index — Design Document

A living specification. High-level system overview, architecture rationale, and active design decisions.

---

## What index is

index is a QR-anchored physical object registry. A QR code printed on any real-world object is the primary entry point to that object's metadata. The QR encodes a UUID URL. Scanning it lands on a page that combines:

- **Local operational data** — who registered it, where it's been, who's accessed it, photos
- **Wikidata-derived conceptual data** — what kind of thing it *is*, pulled from the public knowledge graph

The output is a structured, human-readable object page at a semantically meaningful URL, backed by Wikidata for categorization.

---

## Primary user flow

```
Physical object
    ↓
QR label (UUID URL printed on it)
    ↓ scan
/{uuid} — item landing page
    ↓ initialized?
No  → claim flow (name + optional Wikidata type link)
Yes → item page showing local record + Wikidata concept enrichment
```

On first scan of an unclaimed UUID, the page prompts for a name and optionally links to a Wikidata concept. That concept determines the item's namespace and enriches the page with structured knowledge-graph data.

---

## Identity layers

Three distinct identity types coexist without collision:

| Layer | Identifier | Example URL | Purpose |
|---|---|---|---|
| Physical instance | UUID | `/550e8400-…` | Immutable QR anchor; always resolves |
| Local semantic | slug | `/thing/oxygen-tank-shelf-3` | Human-readable local reference |
| Wikidata concept | QID | `/wd/Q629` | Shared public knowledge about a concept |

A single item may have all three: a UUID, a user-given slug, and a linked Wikidata QID. They are not interchangeable.

The UUID is permanent and lives on the physical label. The slug is optional and mutable. The QID is optional and links to the external concept that categorizes the object.

---

## Wikidata as namespace source

When an item is linked to a Wikidata QID, the system derives a `namespace` from that entity's `instance of (P31)` chain. The namespace becomes the first path segment in the semantic URL:

```
item linked to Q629 (oxygen)  →  instance of: chemical element (Q11344)
→ namespace: element
→ semantic URL: /element/oxygen-tank-shelf-3
```

Other examples:

```
/material/stainless-steel-rod
/device/oscilloscope-bench-1
/compound/water-sample-a
/standard/ISO-9001-cert
/organism/ficus-desk-plant
```

The semantic URL is optional and supplemental. The UUID route is always the canonical fallback.

---

## Object model (target state)

`Item` is the central model. It should be extended with:

```
uuid            — immutable QR anchor (primary key for physical identity)
name            — user-given name
description     — freeform notes
slug            — optional human-readable URL segment
wikidata_qid    — optional Wikidata concept link (e.g., Q629)
type_namespace  — derived from Wikidata P31 chain (e.g., "element")
user_id         — creator/owner
```

`Thing` (the older model) is a predecessor to `Item` and should eventually be merged or retired. It currently handles slug-based routing and session scan tracking. These capabilities belong on `Item`.

---

## Route structure

```
/{uuid}                      → ItemController::show       — always resolves; canonical
/{uuid}/print                → ItemController::print      — printable QR label
/{uuid}/initialize           → ItemController::initialize — claim flow
/{uuid}/events               → ItemController::addPhoto   — photo upload

/{namespace}/{slug}          → ItemController::showBySemantic — enriched semantic page
/{slug}                      → ThingController::showBySlug    — legacy; migrate to Item

/wd/item/{qid}               → WikidataThingController::show  — concept reference page
/wd/type/{type}              → WikidataTypeController::index  — concept type browser

/dashboard                   → search + item list
```

---

## Data flow: Wikidata enrichment

When an item page loads with a linked `wikidata_qid`:

1. Resolve QID → entity payload (cached, 12h TTL)
2. Extract label, description, `instance of (P31)`, and selected claims
3. Derive `type_namespace` from the P31 chain if not already set
4. Render item page with both local record and Wikidata facts merged

The Wikidata layer never replaces the local record — it enriches it. Local fields (name, owner, scan history, photos) are always primary.

---

## Wikidata backend strategy

**Short-term (current):** Direct Wikidata API + Laravel cache layer. The `Wikidata` service is already built and operational.

**Medium-term:** Add a local `wikidata_entities` cache table (qid, label, description, claims_json, fetched_at) to reduce dependency on the public endpoint and speed up repeat loads.

**Long-term (if traffic or latency demands it):** Dump-backed Neo4j graph for multi-hop traversal and deep concept exploration. The Neo4j importer already exists in `database/wikidata-neo4j-importer/`.

Trigger for migration to dump backend: sustained public endpoint rate limit hits, P95 latency too high for interactive use, or need for multi-hop relationship queries.

---

## Visual and UX

- **Mobile-first** as a scanner tool. The landing page after a QR scan must be instantly readable on a phone.
- **Desktop** is a search and reference tool — large command-bar search, fast AJAX results.
- **Aesthetic:** minimal, utilitarian, bold terminal feel. Influenced by Atomic Heart, Deus Ex: Mankind Divided, and Cyberpunk 2077 UI.
- **QR label:** brutally simple — index logo, QR code, inverted bold title block. Shared Blade partial; print and item page use the same partial identically.
- **Breadcrumbs:** each item page shows its hierarchical context, e.g., `index > element > oxygen > oxygen-tank-shelf-3`.

---

## What is not index (scope boundaries)

- index is not a social network. Access tracking is operational/provenance data, not social feed.
- index is not a CMS. Object pages are structured records, not free-form articles.
- index is not an inventory spreadsheet. The QR scan flow is the primary input method.
- index does not replace Wikidata. It references Wikidata for concept metadata; it does not replicate or curate it.

---

## Near-term implementation priorities

1. Add Wikidata concept search to the item initialization/claim flow (search-as-you-type → select QID).
2. Add `wikidata_entities` local cache table.
3. Migrate or retire `Thing` model once `Item` covers slug + scan session tracking.
