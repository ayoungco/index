[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F039056a7-ecc8-412a-944b-111f2b17052e&style=plastic)](https://forge.laravel.com/servers/855626/sites/2635631)

# ![index.ayoung.co](public/index-h.svg)

# index

- Provide a dashboard for everything, at a meaningful URL
- Useful core information for each as well as hyperlinks to other commonly used services. A table of contents.

- track your stuff
- attach notes to things
- comment on things
- a homepage for everything you own, manage, or care about

- catalog your things
- tag and relate them
- find them quickly
- share them securely

# Features

- reference for chemical subtances, ingredients, and materials
- product and manufacturer database
- a location tracking asset scanner

## Installation

See docs/INSTALL.md for installation instructions.

## Laravel Forge storage

Uploaded images are processed into JPEGs and written to:

```text
storage/app/public/items/{uuid}/{image-uuid}.jpg
```

The public `/storage/...` URL requires Laravel's storage link. Run this from the
Forge site directory after each deployment:

```bash
php artisan storage:link
```

A typical Forge deployment script should also include:

```bash
chmod -R ug+rwX storage bootstrap/cache
php artisan storage:link
php artisan config:clear
```

Verify the link, saved file, and web-server access with:

```bash
ls -ld storage/app/public public/storage
readlink -f public/storage
find storage/app/public/items -type f | tail -n 5
curl -I https://index.ayoung.co/storage/items/{uuid}/{image-uuid}.jpg
```

`public/storage` must resolve to the current site's `storage/app/public`
directory. A missing or stale link causes uploaded files to return HTTP 404 even
when image processing and storage succeeded.

### Upload security

All uploads are decoded and re-encoded as application-generated JPEG files before
they are written outside the public web root. They are available only from the
authenticated `/media/...` application route; original uploaded bytes are never
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
