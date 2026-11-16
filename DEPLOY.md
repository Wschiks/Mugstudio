# Putting the MugStudio site online

There are two ways. **Option A is free** and is the one we use.

| | A. Plain HTML (free) | B. WordPress hosting |
|---|---|---|
| Cost | €0 | about €3–5 a month |
| Where | GitHub Pages (or Cloudflare Pages / Netlify) | Any host with WordPress |
| Support form | Sent by Web3Forms to your mailbox | Saved in wp-admin + emailed |
| Changing the site | Change it in Local, run the build, push | Change it in wp-admin |

---

## A. Plain HTML on GitHub Pages (free)

The `docs/` folder is a copy of the site as plain HTML, made from the Local site by
`static-site/build.py`. It looks exactly like WordPress (checked pixel by pixel).

### One time: set it up

1. **Web3Forms key (for the support form).** Go to https://web3forms.com, fill in `support@mugstudio.nl`
   and you get an access key by email. Paste it in `static-site/form.js` at `ACCESS_KEY`.
   (Without a key the form only pretends to send when you test it on your own computer, and online it asks people to email you.)
2. **Your domain.** Put it in `static-site/build.py` at `SITE_URL`, for example `"https://mugstudio.nl"`.
   That makes link previews and the sitemap use your domain.
3. **Build and push** (Local must be running, because the build reads the pages from `test.local`):

   ```bash
   python3 static-site/build.py
   ```

   Then commit and push `docs/`.
4. **Turn on GitHub Pages.** On github.com: the repo → *Settings → Pages* →
   *Source: Deploy from a branch* → branch `main`, folder `/docs` → *Save*.
5. **Connect your domain.** Same page, *Custom domain*: fill in your domain and *Save*
   (GitHub then adds a `CNAME` file to `docs/`; pull it before you build again, the build keeps it).
   At the company where you bought the domain, set these DNS records:

   | Type | Name | Value |
   |---|---|---|
   | A | @ | 185.199.108.153 |
   | A | @ | 185.199.109.153 |
   | A | @ | 185.199.110.153 |
   | A | @ | 185.199.111.153 |
   | CNAME | www | wschiks.github.io |

   DNS can take up to a day. Then tick *Enforce HTTPS*.

### Every time you change something

1. Change the site in Local (theme files or wp-admin).
2. Run `python3 static-site/build.py`.
3. Commit and push. GitHub Pages updates within a minute or two.

### Test the copy on your own computer

```bash
cd docs && python3 -m http.server 8765
```

Open http://localhost:8765. The form runs in test mode there: it shows the confirmation but sends nothing.

### App Store links

- Support URL: `https://<your domain>/support/`
- Privacy policy URL: `https://<your domain>/privacy/`

---

## B. WordPress hosting

1. **Get hosting with WordPress.** Almost every Dutch host (TransIP, Hostinger, Vimexx…) can install WordPress with one click. Use PHP 8.1 or newer and turn on HTTPS (free Let's Encrypt certificate).
2. **Make a zip of the theme** on your Mac:

   ```bash
   cd "app/public/wp-content/themes" && zip -r ~/Desktop/dobbel.zip dobbel -x '*.DS_Store'
   ```

3. **Upload and activate it** in the new site: *Appearance → Themes → Add new → Upload theme* → choose `dobbel.zip` → *Activate*.
4. **Open the homepage once.** The theme now creates Home, News, Support, Privacy Policy, Terms of Service and the first post, sets the homepage and turns on pretty links.

Before you share the link:

- [ ] **Strong admin password** that you do not use anywhere else (*Users → Profile*).
- [ ] **Admin email** set to your real address (*Settings → General*). Locally it is `dev-email@wpengine.local`.
- [ ] **Email that really arrives.** Install the plugin **WP Mail SMTP** and connect it to the mailbox of `support@mugstudio.nl`, then send a test request.
- [ ] **Search engines allowed** (*Settings → Reading → leave "Discourage search engines" unticked*).
- [ ] **Store links** filled in for games that are live: `dobbel_game_links()` in `functions.php`.

---

## What is not in git (on purpose)

Only the theme, the plain-HTML copy (`docs/`) and its build script (`static-site/`) are in git (see `.gitignore`). Not in it:

- **WordPress itself** (`wp-admin`, `wp-includes`…): the host installs it, and updates replace it anyway.
- **`wp-config.php`, the database dump (`app/sql/`), Local's `conf/` and `logs/`**: these hold passwords, secret keys and
  personal data (like the names and emails of test persons), and the GitHub repo is public.

All of these are still on the Mac, so the local site keeps working.
