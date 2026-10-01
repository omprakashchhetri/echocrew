# EchoCrew

Marketing website, enquiry intake and blog for [EchoCrew](https://echocrew.in), a software studio in Siliguri, West Bengal. Built on **CodeIgniter 4** with **CodeIgniter Shield** for authentication.

## Features

- Home page and service pages (content driven by `app/Config/Seo.php`)
- Enquiry form with honeypot, validation and DB storage
- Blog with categories, drafts/published/archived states, view counting and logged-in comments
- Admin blog editor under `/admin/blog`
- `sitemap.xml`, canonical URLs, breadcrumbs and JSON-LD schema partials

## Requirements

- PHP 8.1+ with `intl`, `mbstring`, `json`, `mysqlnd` (or another supported DB driver)
- Composer
- MySQL/MariaDB

## Quick start

```bash
composer install
cp env .env            # then edit baseURL, database.* and CI_ENVIRONMENT
php spark migrate --all   # --all includes the Shield tables
php spark shield:user create   # create your first admin login
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

## Security notes

- Any authenticated user can currently reach `/admin/*` (the `session` filter only checks login). Before opening registration to the public, restrict admin routes to a group, or disable registration. See [docs/DEVELOPER.md](docs/DEVELOPER.md#security-checklist).
- Blog post bodies are rendered as raw HTML. Only trusted staff should be able to publish.

## License

MIT, see [LICENSE](LICENSE).
