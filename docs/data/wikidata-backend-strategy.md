# Wikidata Backend Strategy for Generic / Abstract Concept Data

This document captures how to use Wikidata as a backend for requests about generic concepts (for example: materials, categories, standards, entities, and other abstract things).

## Current State in This Repo

- Live Wikidata service client exists: `app/Services/Wikidata.php`
- Wikidata controllers exist: `app/Http/Controllers/WikidataThingController.php`, `app/Http/Controllers/WikidataTypeController.php`
- Service config is present: `config/services.php`
- Neo4j dump importer exists: `database/wikidata-neo4j-importer/`
- The Wikidata routes are not currently wired in `routes/web.php`

## Two Viable Backend Modes

## 1) Direct Wikidata (API + SPARQL endpoint)

Use Wikidata directly as a read-through external backend:

- `wbsearchentities` for query-to-entity matching
- `Special:EntityData/QID.json` for canonical entity payloads
- SPARQL endpoint for typed lists and graph-style lookups

Pros:

- Fastest path to production
- No ingest pipeline
- Fresh data

Cons:

- Public endpoint limits and availability constraints
- Variable latency
- Less control over heavy graph workloads

Best fit:

- MVP and moderate traffic
- Immediate support for broad concept types

## 2) Wikidata Dump (Local Graph Backend)

Import the dump into local infra (Neo4j in this repo) and query locally.

Pros:

- Deterministic local performance
- Better for deep traversal and neighborhood queries
- No dependency on public runtime APIs

Cons:

- Operational overhead (import pipeline, storage, refresh cadence)
- Snapshot staleness between refreshes

Best fit:

- Higher scale / lower-latency requirements
- Advanced relationship-heavy concept exploration

## Recommendation: Hybrid

Use a staged hybrid approach:

1. Start with direct Wikidata + cache.
2. Materialize frequently requested entities locally.
3. Add dump-backed graph queries only when traffic or query complexity requires it.

This keeps implementation fast now while preserving a path to serious graph capabilities later.

## Query Pattern for Abstract Concept Requests

Normalize user requests into Wikidata graph primitives:

- "What is X?" -> entity lookup by QID
- "What kind of thing is X?" -> `instance of (P31)` + `subclass of (P279)`
- "What concepts relate to X?" -> selected relationship expansion (`P279`, `P361`, etc.)
- "Show examples of concept X" -> `?item wdt:P31/wdt:P279* wd:Q...`

## Suggested Data Flow in This App

1. Resolve query text to candidate QIDs (`search`) using language + ranking.
2. Fetch canonical entity payload (`entity`) and cache.
3. Enrich with relationship query (`sparql`) for immediate context.
4. Persist top entities locally for fast repeat requests and app resilience.

## Minimal Local Persistence Layer

Add a local cache table for reused concepts, for example:

- `qid` (primary key)
- `label`
- `description`
- `claims_json`
- `fetched_at`
- optional provenance/version fields

This enables:

- Faster repeated lookups
- Graceful degradation when upstream throttles/fails
- Consistent app behavior across sessions

## Near-Term Implementation Plan

1. Wire the existing Wikidata routes/controllers in `routes/web.php`.
2. Add `wikidata_entities` table for materialized cache.
3. Add a resolver service for search -> rank -> fetch -> cache.
4. Link local `Thing` records to external QIDs where appropriate.
5. Reassess if Neo4j dump import is needed based on real latency/traffic.

## Decision Trigger for Dump Migration

Move to dump-backed graph when one or more are true:

- Public endpoint limits are routinely hit.
- P95/P99 latency is too high for concept pages.
- Product requires multi-hop concept traversal at interactive speed.
- Availability requirements exceed dependence on public endpoints.

Until then, direct + cached Wikidata is the highest-leverage backend strategy.
