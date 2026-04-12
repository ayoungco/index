# Feasibility: Integrating `hessiron@main` into `index@main` (with company-neutral branding)

## Executive assessment

**Short answer:** integration is **feasible**, but only as a staged merge/migration program, not a single cutover.

Because the `hessiron@main` source was not available in this workspace, this evaluation is constrained to:
1. what `index` currently supports,
2. where `index` is structurally ready to absorb additional modules and models, and
3. what due-diligence artifacts are required before an exact field-by-field migration estimate can be produced.

Given the current architecture, there are no obvious blockers to incorporating a second app's functionality and data models if we normalize naming, isolate authentication boundaries, and execute migration adapters per bounded context.

## What `index@main` already provides that helps integration

`index` is already organized around reusable app primitives that can host imported functionality:

- **Configurable installation + branding layer** via installer flow and persisted app settings (`site_name`, scanner/label metadata, logo). This is a strong base for removing hard-coded company identity and replacing it with tenant/site-neutral naming. See installer controller + settings model/support utilities and install UI.  
- **Core item lifecycle domain** with UUID identity, creator ownership, event history, QR-assisted workflows, and media upload/processing. This gives an extensible backbone for integrating asset/record workflows from another codebase.  
- **Extensible graph-like entities** (`Thing`, `Property`, `Relation`) with timeline support, useful as an abstraction target when mapping unknown external models from `hessiron`.  
- **Auth/middleware boundaries** already split between regular auth and Auth0-oriented checks, indicating integration points for identity harmonization.

## Company-reference removal feasibility

Removing references to a specific company is straightforward and low-risk if handled as a formal compatibility layer:

1. **Configuration-first naming**
   - Keep all user-facing product strings behind settings/config keys (already partially present via installer settings).
2. **UI content pass**
   - Replace company-specific copy with neutral language ("organization", "workspace", "inventory", "deployment") in views/docs/tests.
3. **Data backfill migration**
   - Where persisted records contain branded defaults, provide one-time migration to neutral values.
4. **API contract aliasing (if applicable)**
   - If `hessiron` exposes branded field names, preserve temporary aliases and deprecate in phases.

Estimated complexity for de-branding alone: **Low to Medium** (primarily search/replace + migration of seeded/default values).

## Integration risks and constraints

### Known constraints (current workspace)

- No local checkout or remote reference for `hessiron@main` was available, so exact model/endpoint parity could not be measured.
- `index` contains multiple evolving domains (`Thing/*` and `Item/*`), so introducing another full model set requires explicit domain boundaries to avoid duplicate concepts and route collisions.

### Main technical risk categories

1. **Model overlap risk**
   - Potential duplicate entities (e.g., "asset", "item", "object") with incompatible IDs or ownership semantics.
2. **Auth/session coupling risk**
   - Different assumptions about login, verification, and API guards may require adapter middleware.
3. **Migration risk**
   - If `hessiron` schema has tighter non-null constraints or enum differences, direct import may fail without staging tables.
4. **UX drift risk**
   - Feature parity can be achieved while still producing inconsistent user journeys unless views/routes are rationalized.

## Recommended integration approach

### Phase 0 — Discovery (required before build)

Produce a parity matrix against `hessiron@main`:
- Models/tables/fields/indexes/constraints
- Services/jobs/events
- HTTP routes/controllers/policies
- External dependencies (queues, storage, OCR/QR, third-party auth)

### Phase 1 — Canonical domain mapping

Define canonical entities in `index` and map `hessiron` entities to one of:
- **Adopt as-is** (fits existing model)
- **Wrap/adapter** (kept separate with translation layer)
- **Merge/refactor** (new canonical model replacing both)

### Phase 2 — Schema + data migration adapters

- Introduce additive migrations only.
- Build import scripts with idempotent upserts keyed by durable IDs.
- Preserve original source IDs in shadow columns for traceability.

### Phase 3 — Functional module integration

- Move features in vertical slices (data + service + endpoints + UI + tests).
- Keep compatibility routes until clients are updated.

### Phase 4 — Company-neutral hardening

- Final pass on labels/content/config/env keys.
- Remove deprecated branded aliases.
- Freeze migration contracts and publish upgrade notes.

## Feasibility verdict

**Overall feasibility: High, with medium implementation effort and high discovery dependence.**

- If `hessiron` is Laravel-adjacent and conceptually similar, this is mostly a disciplined consolidation exercise.
- If `hessiron` is a different stack or has deeply coupled business rules, feasibility remains good but timeline and adapter complexity increase substantially.

## What is needed next for a precise estimate

To convert this from directional to exact, provide either:
1. a checkout of `hessiron@main` in the workspace, or
2. exported artifacts (schema dump, route list, model list, and key service classes).

With those, a concrete migration backlog can be generated with per-entity acceptance criteria and level-of-effort scoring.
