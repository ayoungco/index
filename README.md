[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F039056a7-ecc8-412a-944b-111f2b17052e&style=plastic)](https://forge.laravel.com/servers/855626/sites/2635631)

# ![index.ayoung.co](public/index-h.svg)

# index

index is a QR-first registry for real-world objects. A printed QR label
contains a permanent UUID URL. Scanning that label opens the local record for the
specific object—not a generic product page—where anyone can view public records
and authenticated operators can add
photos, notes, scan locations, and custody context.

Wikidata supplies the conceptual layer. An operator can link an item to a QID,
which enriches the local record and derives a useful namespace such as
`/element/oxygen-tank-shelf-3`. The local UUID remains the durable physical
anchor; the semantic URL is a convenient alias.

## Core workflow

1. Print a UUID QR label and attach it to an object.
2. Scan it and authenticate with Auth0.
3. Claim the object, add local metadata and a Wikidata category.
4. Record photos, selected featured media, and optional device location.
5. Return to the same UUID whenever the physical object is handled.

Objects can be public or private. Public records remain visible from their QR and
semantic URLs; private records and operator features require Auth0. The dashboard
is the authenticated homepage for finding records and their featured photos.

## Installation

See docs/INSTALL.md for installation instructions.

## Laravel Forge storage

Uploaded images are processed into JPEGs and written to:

```text
storage/app/private/uploads/items/{uuid}/{image-uuid}.jpg
```

No `storage:link` is needed for uploads. A typical Forge deployment script should
ensure Laravel can write the private storage directory:

```bash
chmod -R ug+rwX storage bootstrap/cache
php artisan config:clear
```

Verify the link, saved file, and web-server access with:

```bash
find storage/app/private/uploads/items -type f | tail -n 5
```

`public/storage` must resolve to the current site's `storage/app/public`
directory. A missing or stale link causes uploaded files to return HTTP 404 even
when image processing and storage succeeded.

### Upload security

All uploads are decoded and re-encoded as application-generated JPEG files before
they are written outside the public web root. They are available only from the
visibility-aware `/media/...` application route; original uploaded bytes are never
served. Legacy public-storage files are also read only through that route. Keep
all direct `/storage` requests blocked at the web-server layer. Apache deployments
receive this protection from `storage/app/public/.htaccess`; for Nginx, add:

```nginx
location ^~ /storage/ { return 404; }
```

## Use cases

- Upcycling and repurposing materials
- Home inventory management
- Personal knowledge management
