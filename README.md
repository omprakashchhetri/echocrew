# EchoCrew

Marketing website, enquiry intake and blog for [EchoCrew](https://echocrew.in), a software studio in Siliguri, West Bengal. Built on **CodeIgniter 4** with **CodeIgniter Shield** for authentication.

## Features

- Home page and service pages (content driven by `app/Config/Seo.php`)
- Enquiry form with honeypot, validation and DB storage
- Blog with categories, drafts/published/archived states, view counting and logged-in comments
- Staff admin at `/admin`: dashboard, posts (search, filter, publish/unpublish), categories, tags, comment moderation, enquiry inbox and user/role management
- Public blog extras: category and tag pages, excerpts, RSS at `/blog/feed.xml`
- `sitemap.xml`, canonical URLs, breadcrumbs and JSON-LD schema partials

## Requirements

- PHP 8.1+ with `intl`, `mbstring`, `json`, `mysqlnd` (or another supported DB driver)
- Composer
- MySQL/MariaDB

## Quick start

```bash
composer install
cp env.example .env    # then edit baseURL, database.* and CI_ENVIRONMENT
php spark migrate --all   # --all includes the Shield tables
php spark shield:user create -n yourname -e you@example.com -g superadmin
php spark serve        # http://localhost:8080
```

The web root is `public/`. Point your virtual host there, never at the project root.

## Documentation

| Doc | Contents |
| --- | --- |
| [docs/DEVELOPER.md](docs/DEVELOPER.md) | Architecture, routes, config, database, conventions, testing, deployment |
| [docs/BLOG.md](docs/BLOG.md) | Blog data model, admin workflow, authoring and SEO guidelines, roadmap |

## Project layout

```
app/Config/Seo.php        Brand, services, FAQ and SEO data
app/Controllers/          Home, Services, Enquiry, Blog, Sitemap, Admin/BlogController
app/Models/               Blog, Category, Comment, Enquiry, Tag, User models
app/Views/                layouts, pages, sections, partials, blog, admin
app/Database/Migrations/  Blog and enquiry tables
app/Database/Seeds/       BlogSeeder (categories and starter drafts)
public/assets/            CSS, JS, fonts, images
```

## Common commands

```bash
php spark migrate --all
php spark db:seed BlogSeeder
php spark routes
composer test             # or vendor/bin/phpunit
```

## Admin and roles

Public registration is disabled. Staff accounts are created by a super admin at `/admin/users`, or from the CLI with `php spark shield:user create ... -g <group>`.

| Group | Can do |
| --- | --- |
| `superadmin` | Everything, including creating/editing/deleting admin-level accounts |
| `admin`, `developer` | Use `/admin` (posts, comments, enquiries, users in non-privileged groups) |
| `user`, `beta` | No admin access; may comment on posts |

Security measures: admin routes require the `admin.access` permission, CSRF is enabled globally, every state change is a POST, post bodies are sanitised with an allow-list on save, and only whitelisted fields are saved. Details in [docs/DEVELOPER.md](docs/DEVELOPER.md#security).

## License

MIT, see [LICENSE](LICENSE).
