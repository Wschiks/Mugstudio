# MugStudio website

The website of MugStudio, a small indie game studio (Surf Tycoon, Dobbelen).
It is a WordPress theme called **dobbel**: [`app/public/wp-content/themes/dobbel`](app/public/wp-content/themes/dobbel).

This repo holds the theme, plus a plain-HTML copy of the whole site in [`docs/`](docs) for free hosting on Cloudflare Pages
(made by [`static-site/build.py`](static-site/build.py)). WordPress itself, the database and the local settings are not in it.

## What is in the theme

| File | What it does |
|---|---|
| `front-page.php` | Home page: the games and a button to the support form |
| `page-support.php` | Support page: FAQ per game and the support request form |
| `inc/support-form.php` | The form logic: checks the input, saves requests (wp-admin → Support requests), emails support, deletes requests after 12 months |
| `template-parts/support-form.php` | The form, its error messages and the confirmation screen |
| `assets/js/support-form.js` | Character counter, "Sending…" state, jump to the first mistake |
| `inc/seed.php` | Creates the pages (Home, News, Support, Privacy, Terms) the first time the theme runs |
| `inc/legal.php` | Privacy policy and terms text |
| `index.php`, `single.php`, `archive.php`, `404.php` | News, a post, archives and the "page not found" page |
| `style.css` | All styling |

## Running it

Locally with [Local](https://localwp.com): make a WordPress site, put the `dobbel` folder in `wp-content/themes/` and activate it.
Putting it online (free, as plain HTML on Cloudflare Pages at mugstudio.nl): see [DEPLOY.md](DEPLOY.md).
