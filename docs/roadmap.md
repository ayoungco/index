# Roadmap

This is an ordered set of bets, not a promise to build every idea in the archive.
Work from top to bottom unless direct operator feedback changes the order.

## Next: strengthen the object loop

1. Wikidata concept search during claim/creation, with a local entity cache.
2. UUID-aware dashboard search and a single “Add object” decision: photo-first
   or label-first.
3. Bare item claiming for cases where documentation comes later.
4. Simple label calibration and a default print action with alternate layouts
   behind a disclosure.
5. Breadcrumbs that explain local name, namespace, and concept without making
   the route hierarchy misleading.

## Then: operational structure

1. Audit and retire the legacy `Thing` workflow once all useful behavior lives
   on `Item`.
2. Add local Classes only when an installation needs a vocabulary that Wikidata
   cannot supply. Keep them separate from physical items.
3. Add explicit containment, ownership, and provenance relationships after the
   item and class workflows are proven.
4. Use scan location data to test whether Locations, rooms, and containers need
   first-class records rather than more free text.

## Later: validate before building

- OCR/serial-number intake.
- Configurable resources and class-specific fields.
- Federation, graph storage, and external authority namespaces.
- Subscription billing and an email campaign.

Stripe/Cashier is the likely billing path if subscriptions become real work, but
do not add billing infrastructure before a paid tier and its value are defined.
