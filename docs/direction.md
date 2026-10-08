# Direction: an object terminal and a card worth passing on

Status: exploration documented from the product discussion on 2026-09-27.
This describes intended experiments, not implemented features or a commitment
to a frontend framework.

## Product direction

The inspiration is practical warehousing learned through Caterpillar's SAP
implementation, made accessible from mobile devices and available to a broader
audience. The interaction inspiration is the responsiveness of a TUI and the
simple, predictable controls of a classic American Megatrends BIOS.

Borrow consistent screens, visible selection, aligned fields, immediate
feedback, and a small set of actions. Provide touch-sized controls on mobile
and optional keyboard shortcuts on desktop. Preserve browser light/dark mode.
Avoid making terminal decoration or keyboard knowledge a prerequisite.

The core loop is scan → identify or register → update → return to work.
Preserve search and scroll context. A future SPA refactor should serve this
loop and be evaluated against cold mobile QR-entry performance.

Intended object capabilities:

- Type: optional Wikidata concept, selected by label and description.
- Tags: loose object-level groupings.
- Attributes: simple key/value facts, with typed values and units only as needed.
- Relationships: containment, location, custody, and membership.
- History: photos, notes, movements, and changes.

At inspection, the backend accepts a Wikidata QID during creation and renders
enrichment, but the registration UI has no type picker. Existing tags belong
to photo events; general object tags and key/value editing remain proposed.

“Open” remains a product question: publicly available software/service,
self-hosting, public records, or some combination. Existing private records
must not become public as a side effect of this exploration.

## Business-card QR experiment

The owner's printed business cards share one QR destination. A curious scanner
should encounter something immediately understandable and worth passing on,
without an account wall. Treat this as a deliberately public experience with
its own content, separate from authenticated inventory.

The exact printed destination has not yet been supplied. Preserve that URL;
inspect its current route and behavior before implementing anything.

### Leading concept: “You found a physical hyperlink”

Make the card a tiny demonstration of the product: a physical object that opens
a living record. A spare terminal-like screen introduces the owner, explains
the experiment in one sentence, and offers a few clear actions:

1. “I found this card” — optionally contribute a broad place or short story.
2. “Pass it on” — invite someone to hand the card to another person; provide
   ordinary link sharing as an alternative.
3. “Meet Adam” — a straightforward profile/contact path, always easy to find.

An illustrative opening, subject to copy review:

> YOU FOUND A PHYSICAL HYPERLINK.
> This card connects a real object to a living record.
> Where will it go next?

Show a small, curated collective travel log or aggregate milestones. The payoff
should be visible before asking for participation. Do not invent activity or
suggest that a view is a unique person, a physical scan, or a verified handoff.

All printed cards share a URL: the system cannot reliably identify individual
cards or reconstruct their individual journeys. Present a collective card
experiment. Future unique labels could support separate card identities;
browser identifiers cannot establish physical-card identity.

### Other concepts to test

| Concept | Hook | Tradeoff |
| --- | --- | --- |
| Collective passport | Leave an optional broad place; watch the cards spread. | Needs enough participation and moderation to feel alive. |
| Tiny mission | A short rotating invitation: pass the card to a maker, name an object worth keeping, or repair something. | Easy to launch; needs thoughtful prompts rather than pressure. |
| Object story | Show one interesting object and its history, with an invitation to start a record. | Best product demonstration; requires explicitly public example content. |
| Secret BIOS page | A compact playful menu with a small discovery behind one action. | Memorable presentation, but must still explain who Adam is and why the page exists. |

Combine the passport and BIOS presentation first; keep the mission as optional
copy. Virality is a hypothesis, not a promised outcome. The reason to share
should be curiosity and participation rather than a forced referral gate.

## Smallest useful experiment

Build one fast public mobile page at the existing printed URL, with concise
owner identity, the physical-hyperlink premise, a pass-it-on invitation, and a
contact/profile path. Start with a curated story if participation is sparse.
Add one optional contribution form only when moderation is ready.

Use explicit submission for contributions; GET requests and link previews
must not create public entries. Avoid mandatory location permission, exact
coordinates, public IP/device data, and persistent visitor tracking. If broad
place input is offered, let visitors type it. Rate-limit submissions and review
free text before publication. Do not collect photos in the first experiment.

Measure whether visitors understand the premise, choose to participate, use
the sharing control, or follow the owner profile. A share-button click is not
proof of a completed share or a physical handoff. Prefer minimal aggregate
measurement; account for bots, reloads, and previews in any displayed counts.

Before implementation, resolve:

- Exact printed URL and its current purpose.
- Desired owner profile/contact destination and public copy.
- Whether the first version accepts contributions or starts curated.
- Who reviews submissions and which aggregate activity is public.

## Suggested sequence

1. Prototype the business-card page independently of private inventory.
2. Test the core object terminal interaction on mobile and desktop.
3. Add the Wikidata picker, object tags, and simple attributes incrementally.
4. Choose SPA scope based on demonstrated interaction and performance needs.
