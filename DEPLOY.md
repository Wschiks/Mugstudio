# Putting the MugStudio site online

There are two ways. **Option A is free** and is the one we use.

| | A. Plain HTML on Cloudflare Pages (free) | B. WordPress hosting |
|---|---|---|
| Cost | €0 | about €3–5 a month |
| Support form | Sent by Web3Forms to your inbox | Saved in wp-admin + emailed |
| Changing the site | Change it in Local, run the build, push | Change it in wp-admin |

How the domain is set up now:

- **mugstudio.nl** is registered at **Vimexx** (domain only, no hosting) and its DNS runs on **Cloudflare**.
- **support@mugstudio.nl** already forwards to your own inbox through **Cloudflare Email Routing**. Receiving mail is solved;
  the website only needs to *send* the form to you.

---

## A. Plain HTML on Cloudflare Pages (free)

The `docs/` folder is a copy of the site as plain HTML, made from the Local site by
`static-site/build.py`. It looks exactly like WordPress (checked pixel by pixel).

### One time: set it up

1. **Web3Forms key (for the support form).** Go to https://web3forms.com and sign up with **your own inbox**
   (not support@mugstudio.nl: if the forwarding ever breaks, form mail would get lost too).
   The access key arrives by email. Paste it in `static-site/form.js` at `ACCESS_KEY`, then build and push (see below).
   Without a key the form only pretends to send when you test it on your own computer, and online it asks people to email you.
2. **Make the Pages project.** In the Cloudflare dashboard: *Workers & Pages → Create → Pages → Connect to Git* →
   choose the GitHub repo **Wschiks/Mugstudio**. Settings:

   | Setting | Value |
   |---|---|
   | Production branch | `main` |
   | Framework preset | None |
   | Build command | *(leave empty)* |
   | Build output directory | `docs` |

   *Save and Deploy.* You get a test address like `mugstudio.pages.dev`; check the site there.
3. **Connect your domain.** In the Pages project: *Custom domains → Set up a custom domain* → `mugstudio.nl` → *Activate*.
   Do the same for `www.mugstudio.nl`. Because the domain is already in your Cloudflare account, Cloudflare makes the
   DNS records and the HTTPS certificate itself. Nothing to change at Vimexx.
   Leave the **MX/email records** for Email Routing alone: they are separate from the website.
4. **Test the form for real.** Send a request on https://mugstudio.nl/support/ and check that it arrives.
   **Look in your spam folder too**: forwarded form mail sometimes lands there. Mark it "not spam" once.

### Every time you change something

1. Change the site in Local (theme files or wp-admin). Local must be running.
2. Build the copy:

   ```bash
   python3 static-site/build.py
   ```

3. Commit and push. Cloudflare Pages sees the push and updates the site within a minute or two.

### Test the copy on your own computer

```bash
cd docs && python3 -m http.server 8765
```

Open http://localhost:8765. The form runs in test mode there: it shows the confirmation but sends nothing.

### App Store links

- Support URL: `https://mugstudio.nl/support/`
- Privacy policy URL: `https://mugstudio.nl/privacy/`

---

## B. WordPress hosting

1. **Get hosting with WordPress.** Almost every Dutch host (TransIP, Hostinger, Vimexx…) can install WordPress with one click. Use PHP 8.1 or newer and turn on HTTPS (free Let's Encrypt certificate).
2. **Make a zip of the theme** on your Mac:

   ```bash
   cd "app/public/wp-content/themes" && zip -r ~/Desktop/dobbel.zip dobbel -x '*.DS_Store'
   ```

3. **Upload and activate it** in the new site: *Appearance → Themes → Add new → Upload theme* → choose `dobbel.zip` → *Activate*.
4. **Open the homepage once.** The theme now creates Home, News, Support, Privacy Policy, Terms of Service and the first post, sets the homepage and turns on pretty links.
5. **Point the domain at the host** in Cloudflare DNS (the host tells you which records), keeping the Email Routing records.

Before you share the link:

- [ ] **Strong admin password** that you do not use anywhere else (*Users → Profile*).
- [ ] **Admin email** set to your real address (*Settings → General*). Locally it is `dev-email@wpengine.local`.
- [ ] **Email that really arrives.** Install the plugin **WP Mail SMTP** and connect it to a mailbox that can send, then send a test request.
- [ ] **Search engines allowed** (*Settings → Reading → leave "Discourage search engines" unticked*).
- [ ] **Store links** filled in for games that are live: `dobbel_game_links()` in `functions.php`.

---

## What is not in git (on purpose)

Only the theme, the plain-HTML copy (`docs/`) and its build script (`static-site/`) are in git (see `.gitignore`). Not in it:

- **WordPress itself** (`wp-admin`, `wp-includes`…): the host installs it, and updates replace it anyway.
- **`wp-config.php`, the database dump (`app/sql/`), Local's `conf/` and `logs/`**: these hold passwords, secret keys and
  personal data (like the names and emails of test persons), and the GitHub repo is public.

All of these are still on the Mac, so the local site keeps working.
