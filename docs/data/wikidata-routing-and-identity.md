# Wikidata Routing and Identity Model

This note captures how local scanned objects, semantic URLs, and Wikidata entities can coexist in the application without collapsing different identity domains into one.

## Core Distinction

There are at least two different kinds of identity in the app:

- Local object identity: a specific scanned or managed object in this system
- External concept identity: a shared public concept represented by a Wikidata entity

These should not be treated as the same thing.

Examples:

- A UUID identifies one exact local record or physical instance
- A Wikidata QID identifies a concept, class, person, material, place, or other public entity

That means a local scanned object may optionally map to a Wikidata concept, but the Wikidata concept should not replace the local object's primary identity.

## Recommended Identity Layers

Use separate fields for separate purposes:

- `uuid`: immutable local identity for scanned objects
- `canonical_slug`: human-readable local route
- `wikidata_qid`: optional external concept link
- `entity_type` or `namespace`: optional route-disambiguation metadata

This allows one object to have:

- a stable internal identifier
- a semantic public URL
- an optional public knowledge-graph mapping

## Route Patterns

Several route families can coexist cleanly:

- Local instance: `/item/{uuid}`
- Local semantic route: `/thing/{slug}`
- Wikidata concept route: `/wd/{qid}`
- Concept route alias: `/concept/{qid}`
- Typed semantic route: `/element/oxygen`
- Typed semantic route: `/material/stainless-steel`

These should resolve through a shared lookup layer rather than each route family inventing its own identity semantics.

## Namespaced Resolver Pattern

If the application needs a more DNS-like naming model, use path namespaces first rather than actual DNS:

- `/@wd/Q42`
- `/@local/{uuid}`
- `/@thing/{slug}`
- `/scan/{uuid}`
- `/ext/wd/Q42`

This provides:

- clear boundary between local and external namespaces
- lower collision risk
- flexibility to add more external authorities later

Examples of future authorities:

- `/@gtin/{code}`
- `/@cas/{number}`
- `/@isbn/{number}`

## Mapping Strategy

A scanned object should map to Wikidata through a relation, not by identity replacement.

Example:

- local asset record: "Bottle on shelf 3"
- local fields: owner, location, scan history, photos, notes
- optional linked concept: a Wikidata QID for bottle, water bottle, stainless steel bottle, or a relevant product class

This keeps local operational data separate from external descriptive knowledge.

## Why UUID and QID Are Not Interchangeable

UUIDs and Wikidata QIDs solve different problems:

- UUIDs identify one local object instance
- QIDs identify globally shared concepts

Using a QID as the primary key for scanned objects breaks down when:

- multiple local objects map to the same concept
- the object is internal or private
- the object has no clean Wikidata equivalent
- the object is custom, composite, or temporary

## Canonical URL Strategy

Recommended long-term approach:

1. Keep UUID as the immutable local primary identifier.
2. Add optional semantic slugs for human-facing URLs.
3. Add optional `wikidata_qid` for concept enrichment.
4. Resolve all route forms through a central identity resolver.
5. Prefer semantic URLs as canonical only when confidence is high.
6. Keep UUID routes as the durable fallback.

This allows all of the following to refer to related but distinct layers:

- `/item/550e8400-e29b-41d4-a716-446655440000`
- `/thing/oxygen-cylinder-7`
- `/wd/Q629`
- `/element/oxygen`

## Product Interpretation

These are not fundamentally different applications.

Instead, they represent different layers of the same system:

- the app manages concrete local entities and operational state
- Wikidata supplies shared public knowledge about concepts those entities may correspond to

That separation is a feature, not a problem.

## Practical Recommendation

The clean design for this codebase is:

- keep UUIDs for local object identity
- support semantic slugs for local navigation
- add optional Wikidata links for enrichment
- introduce route namespaces early if collisions are likely
- never make Wikidata QIDs the sole identity for scanned objects

If the application later needs richer ontology behavior, local objects can also link to local type records, with those type records linking onward to Wikidata concepts.
