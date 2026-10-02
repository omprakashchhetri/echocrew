# Developer Guide

## Stack

- PHP 8.1+, CodeIgniter 4 (`codeigniter4/framework ^4`)
- Auth: `codeigniter4/shield ^1.2` (session auth; routes registered via `auth()->routes($routes)`)
- Frontend: server-rendered PHP views, Bootstrap, Swiper, Slick, Atropos, Isotope (all vendored in `public/assets`)
- Tests: PHPUnit 10

## Setup

1. `composer install`
2. `cp env.example .env` and set at least:
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
4. `php spark shield:user create -n yourname -e you@example.com -g superadmin`
5. `php spark serve`

`.env` is git-ignored. Never commit credentials.

## Request flow

| Route | Controller | Notes |
| --- | --- | --- |
| `GET /` | `Home::index` | Passes title, description, FAQ schema |
| `GET /services`, `/services/{slug}` | `Services` | Slugs are keys of `Seo::$services`; unknown slug = 404 |
| `POST /enquiry` | `Enquiry::store` | Honeypot field `ec_website`, validated, saved to `enquiries` |
| `GET /sitemap.xml` | `Sitemap::index` | Static pages, services, published posts only |
| `GET /blog`, `/blog/view/{slug}` | `Blog` | Published posts only |
| `GET /blog/category/{slug}`, `/blog/tag/{slug}` | `Blog` | Filtered lists |
| `GET /blog/feed.xml` | `Blog::feed` | RSS, latest 20 |
| `POST /blog/comment/{id}` | `Blog::comment` | Login required, 1-2000 chars; hidden comments are not shown |
| `/admin` | `Admin\Dashboard` | Needs permission `admin.access` |
| `/admin/blog/*` | `Admin\BlogController` | List/filter, create, edit, status, delete |
| `/admin/categories`, `/admin/tags` | `CategoryController`, `TagController` | Categories in use cannot be deleted |
| `POST /admin/media/upload` | `Admin\MediaController` | Inline editor image upload, JSON response |
| `/admin/comments` | `Admin\CommentController` | Hide/show/delete |
| `/admin/enquiries` | `Admin\EnquiryController` | Inbox with new/contacted/closed status |
| `/admin/users/*` | `Admin\UserController` | Needs `users.edit`; admin-level accounts need `users.manage-admins` |
| Shield routes | `auth()->routes()` | `/login`, `/logout` etc. Registration is disabled |

Run `php spark routes` for the live table.

## SEO data

`app/Config/Seo.php` holds brand name, contact details, opening hours, the `services` map and `homeFaq`. Views read it through `config('Seo')`. To add a service, add an entry to `$services`; the listing page, detail page and sitemap pick it up automatically. Controllers pass `title`, `description`, `canonical`, optional `ogType` and `breadcrumbs` to `layouts/main.php` and `partials/schema.php`.

## Database

Migrations live in `app/Database/Migrations/`.

| Table | Purpose |
| --- | --- |
| `posts` | title, unique slug, excerpt, content (sanitised HTML), status, published_at, view_count, user_id, category_id |
| `categories`, `tags`, `post_tags` | Taxonomy (managed in admin, shown on posts, filterable publicly) |
| `comments` | post_id, user_id, comment, status (`approved`/`hidden`) |
| `enquiries` | Contact form submissions with status (`new`/`contacted`/`closed`) |
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

## Security

Implemented:

- Admin routes use Shield's `permission:admin.access` filter; user management additionally needs `users.edit`.
- Only `superadmin` (`users.manage-admins`) can create, edit, ban or delete `superadmin`/`admin`/`developer` accounts or assign those roles. Nobody can ban, delete or re-role themselves.
- Registration is off (`Auth::$allowRegistration = false`); the login redirect sends staff to `/admin`.
- CSRF is enabled globally. All deletes and status changes are POST. Any new form must include `csrf_field()`.
- Controllers save a fixed whitelist of fields, never the raw POST array.
- Post bodies pass through `App\Libraries\HtmlSanitizer` (allow-list of tags and attributes, `javascript:` and similar URLs removed, `<h1>` demoted to `<h2>`) before being stored, and are rendered as stored.
- The sitemap and feed expose published posts only.

### Abuse protection for public forms

- **Rate limiting:** `App\Filters\Throttle` (alias `throttle`) is a per-IP token bucket. Applied to `POST /enquiry` (5/hour), `POST /blog/comment/{id}` (8/10 min), `POST /login` (10/5 min) and admin image uploads. Over the limit returns HTTP 429 with `Retry-After`. Behind a proxy or CDN set `Config\App::$proxyIPs` so the real client IP is used.
- **Captcha:** `App\Libraries\Captcha` protects the enquiry and comment forms. Set `turnstile.siteKey` and `turnstile.secretKey` in `.env` to use Cloudflare Turnstile (recommended). Without keys it falls back to a one-time maths question plus a 3 second minimum fill time, tracked in the session.
- **Honeypot** field on the enquiry form, CSRF everywhere, and `secureheaders` enabled globally.
- For a real DDoS (volume attacks) put the site behind a CDN/WAF such as Cloudflare; application code cannot absorb that.

Still recommended: serve over HTTPS and add a Content-Security-Policy that permits the editor CDN (`cdn.jsdelivr.net`) on admin pages only.

### Image uploads

`App\Libraries\ImageUploader` accepts JPG, PNG, WebP or GIF up to 5 MB, verifies the real image type, decodes and re-encodes it with GD (dropping metadata and hidden payloads), scales to 1600px wide and stores it as a randomly named WebP in `public/uploads/blog/YYYY/MM/`. `public/uploads/.htaccess` blocks script execution there. Uploaded files are git-ignored. Back up `public/uploads` with the database.

## Admin editor

The post form uses Quill 2 from jsdelivr. If the CDN is unreachable, the form falls back to a plain HTML textarea. The sanitiser is the real safety net, not the editor.

## Known gaps / TODO

- No media library page (images are uploaded per post or inline in the editor).
- No post revisions, scheduling or comment replies.
- Feature tests for the admin controllers (the HTML sanitiser has unit tests in `tests/unit`).
