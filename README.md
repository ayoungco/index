[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F039056a7-ecc8-412a-944b-111f2b17052e&style=plastic)](https://forge.laravel.com/servers/855626/sites/2635631)

# ![index.ayoung.co](public/index-h.svg)

# index

> A public showcase (and eventual multi-tenant platform) for a **universal index of immutable things**.  
> Built on Laravel 12, Livewire v3, Alpine.js, and Tailwind CSS.

---

## Project Vision
`index` aims to provide a human‑readable landing page for **every immutable Thing**—elements, compounds, standards, etc.—and their relationships, at a fixed, semantically meaningful URL.

---

## Domain Model

_For detailed fields and indexes, see [`docs/index-one-model-manifest.md`](docs/index-one-model-manifest.md)._

| Model | Purpose | Key Relationships |
|-------|---------|-------------------|
| **Thing** | Canonical record of an immutable concept | `hasMany Property`, `hasMany outgoing Relation`, `hasMany incoming Relation` |
| **Property** | Typed key‑value pairs describing a Thing | `belongsTo Thing` |
| **Relation** | Edge connecting two Things with semantics & weight | `belongsTo from Thing`, `belongsTo to Thing` |
| **User** | Auth & attribution (Breeze defaults) | `hasMany Message` |
| **Message** | Polymorphic comments / audit notes | `morphTo author`, `morphTo subject` |

_Planned but **deferred**_: Tagging, Revision history, Fine‑grained ACL.

## Design




## Elevator Pitch

- Provide a dashboard for everything, at a meaningful URL
- Useful core information for each as well as hyperlinks to other commonly used services. A table of contents.

## UX

- minimalistic and utilitarian
- inspiration: GUIs from Deus Ex: Mankind Divided and Cyberpunk 2077
- triangles and hexagons

## Domains

Substances, ingredients, materials, products

# Features

- an index of all chemical elements and molecules
- a GPS-aware asset scanner based on QR. Assets are scanned to GPS coordinates, and then to addresses and sub-buildings, rooms, containers. blockchain based tracking?
- deployable as a self-hosted application - name your universe, name your scope. integrate all of your company systems into a single interface
- URL design: `https://index.ayoung.co/element/sodium`, `https://index.ayoung.co/organization/Dell`

## Links

- [Ownerly, similar use case]([https://www.instagram.com/useownerly?igsh=N2hpZ2VxaTNpZmIw](https://www.instagram.com/useownerly?igsh=N2hpZ2VxaTNpZmIw)

## Local Development Setup

### 1. Prerequisites
* PHP 8.3+ with BCMath & PDO extensions  
* Node 18+ & PNPM/Yarn  
* MySQL 8 / MariaDB 10.6+  
* Composer 2.7+  
*(or simply `docker compose up -d` with Laravel Sail)*

### 2. Clone & Install
```bash
git clone https://github.com/your-org/index.git
cd index

# PHP dependencies
composer install

# JS & CSS
pnpm install && pnpm run dev   # or yarn / npm

# Copy env and generate key
cp .env.example .env
php artisan key:generate
```

### 3. Configure Environment
Edit `.env` for database credentials:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=index_one
DB_USERNAME=root
DB_PASSWORD=secret
```

Optional: Set an `index` alias for the `php artisan` command to simplify usage:

```bash
# ~/.bashrc  or  ~/.zshrc
index () {
    php /var/www/index/artisan "$@"
}
```

### 4. Migrate & Seed
```bash
php artisan migrate
php artisan db:seed --class=RelationTypeSeeder   # seeds is_a, part_of, etc.
```

---

## Running & Testing
```bash
# Start dev server
php artisan serve

# Livewire hot‑reloading
pnpm run dev   # Vite

# Run test suite
php artisan test          # PHPUnit
# or
pest                      # if Pest installed
```
Visit **http://localhost:8000**.

---

## Future Roadmap

| Milestone | Description |
|-----------|-------------|
| **Graph Sync** | Repository pattern to mirror Eloquent writes to Neo4j |
| **Tagging & Facets** | Many‑to‑many Tag model; UI filters |
| **Revision History** | ThingRevision & RelationRevision tables with diff viewer |
| **Public API** | Read‑only REST & GraphQL endpoints |
| **Multi‑Tenant** | Tenant‑aware models using Laravel 12 “Pennant” features |
| **OAuth 2.0** | Social / enterprise SSO via Laravel Passport |

---

## Contributing
1. Fork the repo & create a branch (`git checkout -b feature/my-awesome-thing`)  
2. Commit with **semantic‑release** style commits  
3. Push & open a PR describing **what** and **why**  
4. Ensure tests pass (`php artisan test`) and add new tests when reasonable

---

## License
Code released under the **MIT License** unless noted otherwise.  
© 2025 index contributors
