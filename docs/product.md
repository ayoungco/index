# Product model

## Purpose

index is a private registry for physical objects. A QR label is the durable
entry point: it identifies one real-world instance and leads an authenticated
operator to its local record.

The product combines two layers without confusing them:

| Layer | Example | Role |
| --- | --- | --- |
| Physical anchor | UUID on a QR label | Permanent identity for one object. |
| Local record | Name, notes, photos, scans, location | Operational context owned by the installation. |
| Shared concept | Wikidata QID and derived namespace | Optional reference for what the object is. |

An item is never replaced by its Wikidata concept. A particular oxygen tank,
for example, has a UUID and local scan history; it may also point to the public
concept for oxygen or a relevant equipment class.

## Primary workflows

### Register an object

1. Print a QR label with a fresh UUID.
2. Attach it to the physical object.
3. Scan the label and authenticate with Auth0.
4. Claim the item with a local name, photo, notes, and optional Wikidata QID.
5. Return to the UUID whenever the object is handled.

Operators can also create an item from a photo and print its label afterward.

### Maintain provenance

Each item has a timeline of photos, notes, accesses, and location captures.
The newest photo is the default featured image until an operator explicitly
selects another one. Scan counts are displayed beside authenticated actors as
lightweight feedback, not a social ranking system.

### Find an object

The UUID route is canonical. Semantic routes and snake_case aliases are
conveniences, not replacements for the QR anchor:

```text
/{uuid}                         canonical physical anchor
/{namespace}/{slug}             semantic Wikidata-derived route
/{snake_case_identifier}        human-friendly local alias
```

## Product boundaries

- index is not a public object directory: records and media require Auth0.
- index is not a generic CMS: object pages are operational records.
- index does not replicate Wikidata: it links to and caches focused concept data.
- index does not yet promise configurable schemas, subscriptions, federation, or
  graph traversal; those are roadmap bets that must earn their complexity.
