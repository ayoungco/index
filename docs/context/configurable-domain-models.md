# Configurable Domain Models

## Goal

Make business concepts visible and configurable for organizations that install index.

By default, index catalogs two concepts:

- **Item**: a physical instance identified by a QR code.
- **Class**: what that item is within the organization.

An installation should later be able to introduce additional business objects such as Locations, Vendors, Work Orders, Employees, Inspections, or Certifications without presenting placeholder application concepts that have no meaning to that organization.

## Product Vocabulary

### Item

An Item is a specific physical object. It has an immutable UUID, a QR label, a name, history, photos, and operational metadata.

Examples:

- Makita XDT16 #4
- Break Room Refrigerator
- Ford Transit 12

### Class

A Class describes what an Item is within the business. An Item has one primary Class.

Examples:

- Impact Driver
- Refrigerator
- Vehicle

Classes may form a hierarchy:

```text
Tool
Power Tool
Impact Driver

Appliance
Kitchen Appliance
Refrigerator
```

The distinction is instance versus category:

```text
Item:  Makita XDT16 #4
Class: Impact Driver
```

## Item Classes

Add a stable Laravel model named `ItemClass`, presented as **Classes** in the interface.

Suggested schema:

```text
item_classes
- id
- name
- slug
- description
- parent_id
- wikidata_qid
- is_active
- timestamps

items
- item_class_id
```

`items.item_class_id` should initially be nullable so existing records and low-friction item creation continue to work.

Class behavior:

- Classes are created by each installation; do not seed generic examples.
- A Class may have an optional parent.
- A Class may link to an optional Wikidata concept.
- Classes can be archived without breaking existing Items.
- A Class page lists all Items assigned to it.
- Class names and hierarchy are searchable.

## Label and Item Presentation

The QR label should display the Item name first and its Class second:

```text
MAKITA XDT16 #4
IMPACT DRIVER
```

The Class should also appear on:

- Item detail pages
- Item creation and editing forms
- Dashboard search results
- Class-filtered Item lists
- Breadcrumbs and semantic URLs where appropriate

The Item name remains the primary identity. The Class gives immediate context without replacing the local name.

## Navigation

Remove the current placeholder navigation entries:

- Things
- Properties
- Relations
- Messages

Initially expose only meaningful product areas:

- Items
- Classes
- Settings

`Thing` is a predecessor to `Item` and should be retired after confirming that its tables and routes contain no production data that needs migration.

The placeholder models, controllers, routes, views, factories, and migrations for Things, Properties, Relations, and Messages should not remain visible as if they were supported product concepts.

## User-Defined Business Objects

Classes solve Item categorization, but organizations will need other first-class concepts. These should be configurable records backed by stable Eloquent models, not PHP classes generated at runtime.

Suggested schema:

```text
resource_types
- id
- singular_name
- plural_name
- slug
- navigation_visible
- schema
- timestamps

resource_records
- id
- resource_type_id
- name
- data
- timestamps
```

Example Resource Types:

- Locations
- Vendors
- Work Orders
- Customers
- Employees
- Inspections
- Certifications

Enabled Resource Types can generate their own navigation entries. Native application resources such as Items and Classes can participate in the same resource registry while remaining backed by dedicated Laravel models and tables.

This makes business objects visible in the product without exposing framework internals or requiring runtime code generation.

## Class-Specific Fields

A later phase can allow Classes to define fields that apply to their Items.

Examples:

```text
Tool
- manufacturer
- model
- serial_number
- calibration_due

Vehicle
- VIN
- license_plate
- mileage
```

Suggested schema:

```text
class_fields
- id
- item_class_id
- key
- label
- type
- required
- configuration
- timestamps

item_field_values
- id
- item_id
- class_field_id
- value
- timestamps
```

Prefer first-class field and value records over storing everything in a single opaque JSON column. Separate records are easier to validate, search, index, report on, and migrate. Type-specific value columns or a carefully designed typed value representation may eventually be needed for efficient querying.

## Relationships

Once Items, Classes, and user-defined resources are stable, add explicit relationships between records.

Examples:

- Item **located in** Location
- Item **owned by** Customer
- Item **supplied by** Vendor
- Item **serviced through** Work Order
- Item **certified by** Inspection
- Location **contained in** Location

Relationships should use configured relationship definitions rather than reviving the current empty `Relation` placeholder as a user-facing feature.

## Architectural Constraint

Do not generate arbitrary Laravel models, migrations, or PHP classes at runtime.

Runtime code generation creates deployment, validation, security, upgrade, and schema-migration problems. Use a small number of stable Eloquent models to represent configurable schemas, records, fields, and relationships. Generate navigation, forms, validation, and record pages from those persisted definitions.

Dedicated Laravel models remain appropriate when a concept gains substantial behavior that cannot be represented cleanly by the generic resource system.

## Implementation Phases

### Phase 1: Classes

1. Remove placeholder navigation links.
2. Add the `ItemClass` model and `item_classes` table.
3. Add nullable `items.item_class_id`.
4. Build minimal Classes CRUD.
5. Add Class selection to Item creation and editing.
6. Display the Class beneath the Item name on labels and Item pages.
7. Add Class pages, filtering, and search.

### Phase 2: Retire Legacy Scaffolding

1. Audit existing Thing, Property, Relation, and Message records.
2. Migrate any meaningful Thing data to Items.
3. Remove obsolete routes, controllers, views, models, factories, and tables.
4. Remove legacy slug routing after equivalent Item routing exists.

### Phase 3: Configurable Resources

1. Add Resource Types and Resource Records.
2. Generate simple list, create, show, and edit interfaces from definitions.
3. Generate navigation from enabled Resource Types.
4. Add permissions and archival behavior.
5. Add search across configured resources.

### Phase 4: Class Fields and Relationships

1. Add typed Class field definitions and Item values.
2. Add validation and field-aware search.
3. Add configured relationship definitions.
4. Connect Items to Locations, Vendors, Work Orders, and other records.
5. Add relationship history to the Item timeline where operationally useful.

## Open Questions

- Should the user-facing term be **Class**, **Type**, or an installation-configurable label?
- Can an Item have only one Class, or should secondary classifications be supported later?
- Should Class hierarchy be strict single-parent taxonomy or a broader graph?
- How should local Classes and Wikidata concepts interact when they disagree?
- Which Resource Type features are required before one should appear in navigation?
- Should custom resource data begin in JSON, normalized field values, or a hybrid representation?
- What permissions govern creating schemas versus editing records?
- How should schema changes affect existing records and historical values?

## Initial Product Position

The first coherent version of this direction is intentionally narrow:

> index catalogs Items and Classes. Each installation defines the vocabulary that describes its physical world.

That foundation can later expand into installation-defined operational software while keeping the QR-linked Item as the center of the product.
