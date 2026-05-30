# Wikidata Integration — MVP

Status of Wikidata integration and what's left to build.

---

## Current state

| Component | Status |
|---|---|
| `app/Services/Wikidata.php` — API client with caching + retry | ✅ built |
| `app/Http/Controllers/WikidataThingController.php` — entity show page | ✅ built |
| `app/Http/Controllers/WikidataTypeController.php` — type browser | ✅ built |
| `config/services.php` — service config | ✅ built |
| Routes `/wd/item/{qid}` and `/wd/type/{type}` | ✅ wired |
| `database/wikidata-neo4j-importer/` — dump import pipeline | ✅ exists |
| `wikidata_qid` on `Item` model | ✅ built |
| `wikidata_entities` local cache table | ❌ not yet |
| Wikidata enrichment on item show page | ✅ built |
| QID search in item initialization flow | ❌ not yet |

---

## What's left for MVP

1. Add `wikidata_entities` cache table (`qid`, `label`, `description`, `claims_json`, `fetched_at`)
2. Add a resolver service: search → rank → fetch → cache (wraps existing `Wikidata` service)
3. Add Wikidata concept search to the item initialization/claim flow — search-as-you-type, returns QID + label + description, user picks one to categorize the item

---

## Backend strategy

**Short-term:** Direct Wikidata API + Laravel cache (12h TTL). The `Wikidata` service already handles this with retry and backoff. Add the `wikidata_entities` table as a materialized cache for frequently accessed concepts.

**Long-term (if needed):** Wikidata dump → Neo4j. The importer pipeline exists. Move here when:
- Public endpoint rate limits are routinely hit
- P95 latency is too high for interactive use
- Product needs multi-hop relationship traversal at interactive speed

Until any of those are true, direct API + cache is the right backend.

**Dump storage reference:** compressed dump ≈ 90 GB → decompressed JSON ≈ 500 GB → Neo4j after import ≈ 85–110 GB. The import drops redundant fields (sitelinks, revision metadata) and tokenizes repeated keys.

---

## Identity model

UUIDs and Wikidata QIDs solve different problems and must not be conflated.

- `uuid` — identifies one specific physical object instance (immutable, lives on the QR label)
- `wikidata_qid` — identifies a public concept that the object *is an example of*

One QID can apply to many items. Many items may have no QID at all. The QID is enrichment, not identity.

Route families that coexist cleanly:

```
/{uuid}                   → ItemController — always resolves; canonical fallback
/{namespace}/{slug}       → ItemController — semantic URL derived from Wikidata P31 type
/wd/item/{qid}            → WikidataThingController — concept reference page
/wd/type/{type}           → WikidataTypeController — concept type browser
```

Future external authority namespaces (do not block on these):

```
/@gtin/{code}
/@isbn/{number}
/@cas/{number}
```

---

## Query patterns

```
entity lookup by QID    → Special:EntityData/{QID}.json
type from QID           → P31 (instance of) + P279 (subclass of) chain
concept search by text  → wbsearchentities
list by type (SPARQL)   → ?item wdt:P31 wd:{QID}
```
