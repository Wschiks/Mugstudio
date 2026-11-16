# Putting the MugStudio site online

The site is a normal WordPress site with one theme: `app/public/wp-content/themes/dobbel`.
There are no plugins. The theme sets up the pages by itself the first time it runs.

## Easiest way: fresh WordPress + the theme

1. **Get hosting with WordPress.** Almost every Dutch host (TransIP, Hostinger, Vimexx…) can install WordPress with one click. Use PHP 8.1 or newer and turn on HTTPS (free Let's Encrypt certificate).
2. **Make a zip of the theme** on your Mac:

   ```bash
   cd "app/public/wp-content/themes" && zip -r ~/Desktop/dobbel.zip dobbel -x '*.DS_Store'
   ```

3. **Upload and activate it** in the new site: *Appearance → Themes → Add new → Upload theme* → choose `dobbel.zip` → *Activate*.
4. **Open the homepage once.** The theme now creates Home, News, Support, Privacy Policy, Terms of Service and the first post, sets the homepage and turns on pretty links.

That's it: the site looks like it does on Local.

## Before you share the link

- [ ] **Strong admin password** that you do not use anywhere else (*Users → Profile*).
- [ ] **Admin email** set to your real address (*Settings → General*). Locally it is `dev-email@wpengine.local`.
- [ ] **Email that really arrives.** Locally all mail is caught by Mailpit. Online, install the plugin **WP Mail SMTP** and connect it to the mailbox of `support@mugstudio.nl`. Then send a test request through the Support form and check that it arrives.
- [ ] **Search engines allowed** (*Settings → Reading → leave "Discourage search engines" unticked*).
- [ ] **Store links** filled in for games that are live: `dobbel_game_links()` in `functions.php`.
- [ ] Send one test request, check it under *Support requests* in wp-admin, then delete it.

## What is not in git (on purpose)

Only the theme is in git (see `.gitignore`). Not in it:

- **WordPress itself** (`wp-admin`, `wp-includes`…): the host installs it, and updates replace it anyway.
- **`wp-config.php`, the database dump (`app/sql/`), Local's `conf/` and `logs/`**: these hold passwords, secret keys and
  personal data (like the names and emails of test persons), and the GitHub repo is public. The host makes its own `wp-config.php`.

All of these are still on the Mac, so the local site keeps working.
