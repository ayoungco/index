# Decision log

## QR UUIDs are physical anchors

UUIDs identify physical instances and remain stable on labels. Slugs, namespaces,
and QIDs are secondary references.

## Auth0 protects records and media

The product is private by default. There is no anonymous item page or public
upload URL.

## Wikidata enriches; it does not own local identity

Use QIDs as optional concept references. Cache direct API responses first; defer
Neo4j/dump infrastructure until measurable latency, rate-limit, or graph-query
needs justify it.

## Keep the runtime model stable

Do not generate Laravel code or arbitrary database schemas at runtime. If
configurable resources become necessary, represent definitions and records with
a small set of stable models.

## Preserve submitted URLs when URL records are introduced

Store a user-entered URL verbatim (apart from required transport-safe trimming).
Any future normalized or hashed lookup key is secondary and must not overwrite
the displayed value.
