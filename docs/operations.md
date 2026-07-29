# Operations and implementation principles

## Privacy and media

All item pages and media require Auth0 authentication. Uploads are decoded and
re-encoded as JPEGs, then stored outside the public web root at
`storage/app/private/uploads`. Media is delivered by the authenticated
`/media/...` route. Block direct `/storage` requests at the web server.

New deployments must run migrations and preserve write access to `storage` and
`bootstrap/cache`:

```sh
php artisan migrate --force
chmod -R ug+rwX storage bootstrap/cache
php artisan config:clear
```

## Camera and location

Use the native file input for photo capture:

```html
<input type="file" accept="image/*" capture="environment">
```

It is the most reliable approach across iOS and Android. Browser camera and GPS
permission must be triggered by a user action and require HTTPS; the server
cannot bypass either permission.

Location capture stores the device coordinates and optional room/container.
Reverse geocoding enriches the scan with a surrounding address and municipality.
Set `GEOCODING_USER_AGENT` to an identifiable contact string in production and
treat the external result as approximate context, never as authoritative room
or custody information.

## Delivery practice

Keep the application server-rendered and simple: Blade templates, normal links,
and ordinary form posts. Use page-local JavaScript only where browser APIs or a
measurable scanning improvement requires it.

Rapid iteration means testing the smallest load-bearing assumption first. Every
change should be small enough to review, covered by a relevant test, and safe to
roll back with Git. Avoid adding an SPA runtime, generated runtime models, or a
new service dependency before a specific workflow demands it.
