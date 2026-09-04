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

1. Keep the retired legacy `Thing` workflow out of the active object path.
2. Use `Item.operational_role` for local warehouse roles such as product,
   holding unit, transportation unit, and location/bay.
3. Use `item_containments` for evidence-backed HU contents and nested units.
4. Add local Classes only when an installation needs a vocabulary that Wikidata
   cannot supply. Keep them separate from physical items.

## Later: validate before building

- HU/TU load and unload transactions, bay assignments, and inventory balances.
- Quantity/unit validation, lot/serial tracking, and cycle counts.
- Wikidata search-as-you-type and cached type selection during item claiming.
- A dedicated containment editor and crate “contents” summary on item pages.
- Manual featured-photo selection and timeline tag editing are retained in the
  backend but intentionally hidden from the compact item screen for now.
- OCR/serial-number intake.
- Configurable resources and class-specific fields.
- Federation, graph storage, and external authority namespaces.
- Subscription billing and an email campaign.

Stripe/Cashier is the likely billing path if subscriptions become real work, but
do not add billing infrastructure before a paid tier and its value are defined.
