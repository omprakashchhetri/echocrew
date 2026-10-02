# Blog Guide

## Design

Styles live in `public/assets/css/blog.css` (prefix `eb-`), behaviour in `public/assets/js/blog.js`. The index has a featured latest post, category chips, search and a card grid; posts have a reading-progress bar, sticky share buttons, an auto-generated table of contents from `<h2>` headings, author box, call to action, related posts and BlogPosting structured data.

## How it works

- Public list: `/blog` (published posts, 10 per page, newest first), plus `/blog/category/{slug}` and `/blog/tag/{slug}`.
- Public post: `/blog/view/{slug}`. Drafts and archived posts return 404.
- RSS: `/blog/feed.xml`. Published posts are in `sitemap.xml`; drafts are not.
- Views increment once per IP per 10 minutes (cache-based).
- Comments need a logged-in user. Admins can hide or delete them at `/admin/comments`; hidden comments are not shown publicly.

## Admin workflow

1. Log in at `/login` with a staff account (see README, "Admin and roles").
2. **Posts** (`/admin/blog`): search by title, filter by status or category, publish/unpublish in one click, edit or delete.
3. **New post**: title, optional slug and excerpt, body, category, tags (tick existing or type new ones), status.
4. Keep status `draft` until reviewed; `published` sets the publish date the first time.
5. **Slugs** are generated from the title once. Editing the title later does not change the URL; change the slug field deliberately if you must.
6. **Categories** and **Tags** have their own pages. A category with posts cannot be deleted.
7. **Images:** upload a cover (shown on cards, the post hero and social previews; 16:9 or wider works best, add alt text) and use the image button in the editor for inline pictures. Posts without a cover get a generated colour placeholder.
8. Bodies are sanitised on save: headings, paragraphs, lists, links, images, quotes, code and tables only.

Seed starter categories and draft posts:

```bash
php spark db:seed BlogSeeder
```

The seeder is idempotent for categories and skips posts whose slug already exists. It needs at least one user and publishes nothing: all seeded posts are `draft`.

## Authoring guidelines

- Audience: owners and managers of small and mid-sized businesses, mainly in Siliguri and North Bengal.
- Lead with the problem, then the approach, then a concrete example. Plain language over jargon.
- 800-1,500 words; one `<h2>` per major point (the page title is the `<h1>`).
- Title under 60 characters; the first 155 characters of the body become the meta description, so make them count.
- Link to the matching service page (`/services/{slug}`) and end with a clear next step to the enquiry form.
- Use `<h2>`, `<h3>`, `<p>`, `<ul>`, `<ol>`, `<blockquote>`, `<a>`, `<img alt="...">` only; the stylesheet class `ec-prose` styles these.

## Starter content plan

| Post | Category | Related service |
| --- | --- | --- |
| Custom software or off-the-shelf: how to decide | Custom Software | `custom-software-development` |
| What a CRM should do for a small business | CRM & Automation | `crm-development` |
| Signs your school needs management software | Education | `school-management-software` |
| Five processes worth automating first | CRM & Automation | `business-automation` |
| Modernising a legacy system without stopping the business | Engineering | `legacy-system-modernization` |
| Website basics for local search in Siliguri | Web & SEO | `digital-marketing-seo` |

The seeder creates these as drafts with an outline for each, ready to be written up.

## Roadmap

1. Scheduled publishing and post revisions.
2. Comment replies and spam protection.
3. Feature tests for the admin controllers.
