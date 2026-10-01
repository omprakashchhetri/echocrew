# Developer Guide

## Stack

- PHP 8.1+, CodeIgniter 4 (`codeigniter4/framework ^4`)
- Auth: `codeigniter4/shield ^1.2` (session auth; routes registered via `auth()->routes($routes)`)
- Frontend: server-rendered PHP views, Bootstrap, Swiper, Slick, Atropos, Isotope (all vendored in `public/assets`)
- Tests: PHPUnit 10

## Setup

1. `composer install`
2. `cp env .env` and set at least:
   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'
   database.default.hostname = localhost
   database.default.database = echocrew
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```
3. `php spark migrate --all`
4. `php spark shield:user create` for an admin login
5. `php spark serve`

`.env` is git-ignored. Never commit credentials.

## Request flow

| Route | Controller | Notes |
| --- | --- | --- |
| `GET /` | `Home::index` | Passes title, description, FAQ schema |
| `GET /services`, `/services/{slug}` | `Services` | Slugs are keys of `Seo::$services`; unknown slug = 404 |
| `POST /enquiry` | `Enquiry::store` | Honeypot field `ec_website`, validated, saved to `enquiries` |
| `GET /sitemap.xml` | `Sitemap::index` | Static pages + services + all blog posts |
| `GET /blog`, `/blog/view/{slug}` | `Blog` | Published posts only |
| `POST /blog/comment/{id}` | `Blog::comment` | Login required, 1-2000 chars |
| `/admin/blog/*` | `Admin\BlogController` | Behind `session` filter |
| Shield routes | `auth()->routes()` | `/login`, `/register`, `/logout` etc. |

Run `php spark routes` for the live table.

## SEO data

`app/Config/Seo.php` holds brand name, contact details, opening hours, the `services` map and `homeFaq`. Views read it through `config('Seo')`. To add a service, add an entry to `$services`; the listing page, detail page and sitemap pick it up automatically. Controllers pass `title`, `description`, `canonical`, optional `ogType` and `breadcrumbs` to `layouts/main.php` and `partials/schema.php`.

## Database

Migrations live in `app/Database/Migrations/`.

| Table | Purpose |
| --- | --- |
| `posts` | title, unique slug, content (HTML), status enum, view_count, user_id, category_id |
| `categories`, `tags`, `post_tags` | Taxonomy (tags are modelled but not wired into the UI yet) |
| `comments` | post_id, user_id, comment |
| `enquiries` | Contact form submissions |
| Shield tables | `users`, `auth_identities`, `auth_groups_users`, etc. |

Rollback: `php spark migrate:rollback`. Seed: `php spark db:seed BlogSeeder`.

## Conventions

- Controllers stay thin; shared queries belong in models (`BlogModel::withCategory()`).
- Always escape output with `esc()`. The only intentional raw output is blog `content`.
- Use `base_url()` / `site_url()` for links so the app works in a subfolder.
- Validation rules mirror column sizes. Keep them in sync when changing a migration.
- Put new page fragments in `app/Views/sections` or `partials`, not inline in pages.
- Follow PSR-12; 4-space indent.

## Testing

```bash
composer test
```

Tests live in `tests/` (`unit`, `database`, `session`, `_support`). Add a feature test for any new route; use a separate test database configured under `database.tests.*` in `.env`.

## Deployment

1. Set `CI_ENVIRONMENT = production` in `.env` and the real `app.baseURL`.
2. `composer install --no-dev --optimize-autoloader`
3. `php spark migrate --all`
4. Ensure `writable/` is writable by the web user.
5. Web root is `public/`. The root `.htaccess` rewrites to `public/` as a fallback for shared hosting, but a vhost pointing at `public/` is preferred.
6. Submit `/sitemap.xml` to Search Console after publishing new posts.

## Security checklist

- [ ] Restrict `/admin/*` to an `admin` group (e.g. `['filter' => 'group:admin']`) or set `Auth::$allowRegistration = false`. Today any logged-in user can edit posts.
- [ ] Use POST + CSRF for deletes. `admin/blog/delete/{id}` is currently a GET route.
- [ ] Sanitise or restrict HTML in post content, since it is rendered unescaped.
- [ ] Whitelist fields in `BlogController::store/update` instead of passing the full POST array to `save()`.
- [ ] Keep CSRF enabled for forms (`csrf_field()` is used in admin views).
- [ ] Rate-limit `/enquiry` at the web server if spam appears beyond the honeypot.

## Known gaps / TODO

- Tags are not editable or displayed.
- No category pages or post search.
- Admin views are unstyled and use CKEditor 4 from a CDN (end-of-life); plan a move to a maintained editor.
- No tests for blog or enquiry flows yet.
