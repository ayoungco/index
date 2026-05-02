# Brainstorm

Sorted by an arbitrary-but-useful metric: implementation leverage. Ideas closest to the core product loop and most likely to clarify the system come first; broad speculative directions come later.

## Highest leverage

- [ ] Large, omnipresent search/command bar that emulates the responsiveness of a command line interface. It should return AJAX results for character-by-character input, and allow for fuzzy matching.
- [ ] Each registered Thing should have a human-readable landing page at a fixed, semantically meaningful URL, with their most likely type as the first segment (e.g., `/element/oxygen`, `/compound/water`, `/standard/ISO-9001`).
- [ ] QR codes will be generated for each Thing, linking to its landing page. This will allow for easy sharing and referencing in physical documents or presentations.
- [ ] AWS style "breadcrumbs" at the top of each Thing's page, showing its hierarchical context (e.g., `Home > Element > Oxygen`).


## Data model and graph shape

- [ ] implement graph-like relationships using our Laravel data model and write some sample useful queries via GraphQL or Eloquent.
- [ ] feasibility of using a graph database as the backend or keep relational with a graph-like structure and just use graph transformation.
- [ ] Things/UUIDs could be "anchors" that can forward to other ones or be containers that can hold other things.
- [ ] find a way to genericize to allow lookup of chemicals and compounds via thoughtful namespacing.
- [ ] All index instances use the same central database?

## Product directions


- [ ] index page specification, minimal formatting and predictable structure like Wikidata but more useful.
- [ ] all chemical elements and molecules.
- [ ] index - specializing in custom intranets - integrate all of your company systems into a single interface.
- [ ] deployable index.
- [ ] INDEX - INtelligence Dictionary Exchange.

## Architecture and implementation questions

- [ ] feasibility + is it easier to implement this idea with Python/Django as "indexpy" instead of PHP/Laravel.
- [ ] add a way to add another index installation as an upstream source but default to index.ayoung.co. Federation?
- [ ] "automated laravel model creator" whose database connections can gracefully degrade. index dashboard is a list of those first class citizens created by the user on the left.

## Marketing and demos

- [ ] demo scanning behavior via vertical video shortform content on Instagram or TikTok advert.
- [ ] Zebra Label maker web app: https://zsbportal.zebra.com/designer?sid=1763402111392&template=20115423&lastUrl=%2Fmy-designs

## Research threads

- Exogen (index) - phenotypic behavioral strata across generations of human beings raised in differing chemical environments: lead, asbestos, microplastics, open air nuclear weapons testing fallout, heavy metals. This is called the exposome in epidemiology.
- Reference: Stomper Kreeg - NPC - Ascension Database.

## Someday

- Blockchain tracking of real items.

## Graph database resources

- https://neo4j.com/labs/neosemantics/how-to-guide/
- https://github.com/findie/wikidata-neo4j-importer

## Use cases

- Object provenance and custody tracking for supply chains, museums, libraries, and archives - Hudson Valley House Parts.

- [ ] Stripe monetization: users can press a prominent "upgrade" button to get a subscription, which will give them access to the "pro" features. This will be a one-time payment, not a recurring subscription. Weight cost benefit of adding Laravel Cashier or just implementing Stripe's API directly.1