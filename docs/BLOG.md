# Blog Guide

## How it works

- Public list: `/blog` (published posts, 10 per page, newest first).
- Public post: `/blog/view/{slug}`. Drafts and archived posts return 404.
- Views increment once per IP per 10 minutes (cache-based).
- Comments need a logged-in user and are shown under the post.
- Posts appear in `sitemap.xml` and get Article metadata, canonical URL and breadcrumbs.

## Admin workflow

1. Log in at `/login`.
2. Open `/admin/blog` and choose **Create**.
3. Enter title, body (HTML via the editor), category and status.
4. Keep status `draft` until reviewed; switch to `published` to go live.
5. Slug is generated from the title (`url_title`). Editing the title changes the slug and therefore the URL, so avoid retitling published posts.

Seed starter categories and draft posts:

```bash
php spark db:seed BlogSeeder
```

The seeder is idempotent for categories and skips posts whose slug already exists. It needs at least one user (create one with `php spark shield:user create`) and publishes nothing: all seeded posts are `draft`.

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

1. Lock admin to an `admin` group and make delete a POST (see the security checklist in DEVELOPER.md).
2. Category pages (`/blog/category/{slug}`) and tag UI using the existing `tags` and `post_tags` tables.
3. Excerpt and cover image fields on `posts`.
4. RSS feed (`/blog/feed.xml`).
5. Comment moderation and spam protection.
6. Replace CKEditor 4 with a maintained editor.
