#!/usr/bin/env python3
"""
Make a plain-HTML copy of the MugStudio site.

It asks the running Local site (http://test.local) for every page, so the copy
looks exactly like WordPress (same HTML, same CSS, same fonts and images), and
writes it to ../docs. Only these things change:

  * WordPress-only links (feeds, REST API, emoji script…) are left out;
  * links become /support/ instead of http://test.local/support/;
  * the support form sends through Web3Forms (form.js) instead of WordPress.

Change the site in WordPress, then run this again:

    python3 static-site/build.py

Then commit and push the docs/ folder.
"""

import html
import os
import re
import shutil
import sys
import urllib.error
import urllib.parse
import urllib.request
from datetime import date

# ---------------------------------------------------------------- settings --

SOURCE = "http://test.local"          # the Local WordPress site
SITE_URL = "https://mugstudio.nl"     # the live domain (used for link previews, canonical links and the sitemap)
HERE = os.path.dirname(os.path.abspath(__file__))
OUT = os.path.join(os.path.dirname(HERE), "docs")

# Pages to start from; links found on them are followed.
START = ["/", "/support/", "/news/", "/privacy/", "/terms/"]

# Never copy these (WordPress machinery, not pages).
SKIP = re.compile(r"^/(wp-admin|wp-login\.php|wp-json|feed|comments/feed|xmlrpc\.php|wp-cron\.php)|/feed/?$|[?&](s|p|page_id|replytocom)=")

# ------------------------------------------------------------------ helpers --


def fetch(path):
    """Return (status, bytes, content-type) for a path on the Local site."""
    req = urllib.request.Request(SOURCE + path, headers={"User-Agent": "mugstudio-static-build"})
    try:
        with urllib.request.urlopen(req, timeout=30) as r:
            return r.status, r.read(), r.headers.get("Content-Type", "")
    except urllib.error.HTTPError as e:
        return e.code, e.read(), e.headers.get("Content-Type", "")


def write(path, data):
    full = os.path.join(OUT, path.lstrip("/"))
    os.makedirs(os.path.dirname(full), exist_ok=True)
    with open(full, "wb") as f:
        f.write(data if isinstance(data, bytes) else data.encode("utf-8"))


def page_file(path):
    """/support/ -> support/index.html, / -> index.html"""
    path = path.split("#")[0].split("?")[0]
    return (path.rstrip("/") + "/index.html").lstrip("/")


def local_path(url):
    """http://test.local/x/?a#b -> /x/?a#b, or None for other sites."""
    url = html.unescape(url)
    if url.startswith(SOURCE):
        return url[len(SOURCE):] or "/"
    if url.startswith("/") and not url.startswith("//"):
        return url
    return None


# Lines WordPress adds to <head> that a plain site does not need.
HEAD_JUNK = [
    r'<link rel="alternate" type="application/rss\+xml"[^>]*>\n?',
    r'<link rel="alternate" title="oEmbed[^>]*>\n?',
    r'<link rel="https://api\.w\.org/"[^>]*>',
    r'<link rel="alternate" title="JSON"[^>]*>',
    r'<link rel="EditURI"[^>]*>\n?',
    r'<meta name="generator"[^>]*>\n?',
    r"<link rel='shortlink'[^>]*>\n?",
    r'<script type="speculationrules">.*?</script>\n?',
    r'<script id="wp-emoji-settings"[^>]*>.*?</script>\n?',
    r'<script type="module">.*?</script>\n?',
]


def clean_html(text, path):
    for pattern in HEAD_JUNK:
        text = re.sub(pattern, "", text, flags=re.S)

    # Absolute URLs in link previews and canonical: use the real domain when we know it.
    def absolute(m):
        p = local_path(m.group(2))
        return m.group(1) + (SITE_URL + p if SITE_URL else p) + m.group(3)
    text = re.sub(r'(<meta property="og:(?:url|image)" content=")([^"]*)(")', absolute, text)
    text = re.sub(r'(<link rel="canonical" href=")([^"]*)(")', absolute, text)
    if not SITE_URL:
        text = re.sub(r'<link rel="canonical"[^>]*>\n?', "", text)

    # Every other link to the Local site becomes a link from the root of the domain.
    text = text.replace(SOURCE + "/", "/").replace(SOURCE, "/")
    return text


# Privacy: on the plain site the form goes through Web3Forms, not the WordPress database.
PRIVACY_OLD = re.compile(r"(<h2>This website and the support form</h2>\s*<p>).*?(</p>)", re.S)
PRIVACY_NEW = (
    "This website has no accounts, no analytics and no tracking cookies. When you send a request through the "
    "support form, we receive the game, topic and device you chose, your message, your email address and, if you "
    "fill it in, your name. The form service Web3Forms passes it on to our support mailbox. We use it only to answer "
    "your request, we never sell or share it, and we delete requests within 12 months. Want to see what we have, or "
    "want it deleted sooner? Email us and we will do it."
)


def make_form_static(text):
    """Support page: swap the WordPress form plumbing for the Web3Forms one."""
    text = re.sub(r'\s*<input type="hidden" name="dobbel_support" value="1">', "", text)
    text = re.sub(r'\s*<input type="hidden" id="_dobbel_nonce"[^>]*>', "", text)
    text = re.sub(r'<form class="card support-form" id="support-form" method="post" action="[^"]*"',
                  '<form class="card support-form" id="support-form" method="post" action="https://api.web3forms.com/submit"',
                  text)
    text = re.sub(r'<script[^>]*id="dobbel-support-form-js"[^>]*></script>',
                  '<script defer src="/assets/form.js"></script>', text)
    return text


# -------------------------------------------------------------------- build --


def main():
    status, _, _ = fetch("/")
    if status != 200:
        sys.exit(f"Could not reach {SOURCE}. Is the site started in Local?")

    # Start from an empty docs/ (keep a CNAME file for GitHub Pages if there is one).
    cname = None
    if os.path.exists(os.path.join(OUT, "CNAME")):
        cname = open(os.path.join(OUT, "CNAME")).read()
    shutil.rmtree(OUT, ignore_errors=True)
    os.makedirs(OUT)
    if cname:
        write("CNAME", cname)

    pages, assets, todo = [], set(), list(START)
    seen = set()
    while todo:
        path = todo.pop(0).split("#")[0]
        if path in seen or SKIP.search(path):
            continue
        seen.add(path)
        status, body, ctype = fetch(path)
        if status != 200 or "text/html" not in ctype:
            print(f"  skipped {path} ({status})")
            continue
        text = body.decode("utf-8")

        for url in re.findall(r'(?:href|src)=["\']([^"\']+)["\']', text):
            p = local_path(url)
            if not p or p.startswith("#"):
                continue
            clean = p.split("#")[0]
            if re.search(r"\.(css|js|png|jpe?g|webp|gif|svg|ico|woff2?|ttf|txt)(\?|$)", clean):
                assets.add(clean.split("?")[0])
            elif not SKIP.search(clean):
                todo.append(clean)

        text = clean_html(text, path)
        if path == "/support/":
            text = make_form_static(text)
        if path == "/privacy/":
            text = PRIVACY_OLD.sub(lambda m: m.group(1) + PRIVACY_NEW + m.group(2), text)
        write(page_file(path), text)
        pages.append(path)
        print(f"  page  {path}")

    # 404 page (static hosts show 404.html for unknown addresses)
    status, body, _ = fetch("/this-page-does-not-exist-404/")
    write("404.html", clean_html(body.decode("utf-8"), "/404"))
    print("  page  404.html")

    # Files the pages use, plus the fonts that style.css asks for
    done = set()
    while assets:
        a = assets.pop()
        if a in done or a.endswith("support-form.js"):
            continue
        done.add(a)
        status, body, ctype = fetch(a)
        if status != 200:
            print(f"  MISSING {a} ({status})")
            continue
        write(a, body)
        if a.endswith(".css"):
            base = a.rsplit("/", 1)[0] + "/"
            for u in re.findall(r"url\(['\"]?([^'\")]+)['\"]?\)", body.decode("utf-8")):
                if not u.startswith(("data:", "http", "/")):
                    assets.add(urllib.parse.urljoin(base, u))

    # The form script, a favicon.ico and robots.txt
    os.makedirs(os.path.join(OUT, "assets"), exist_ok=True)
    shutil.copy(os.path.join(HERE, "form.js"), os.path.join(OUT, "assets", "form.js"))
    fav = os.path.join(OUT, "wp-content/themes/dobbel/assets/favicon.png")
    if os.path.exists(fav):
        shutil.copy(fav, os.path.join(OUT, "favicon.ico"))
    robots = "User-agent: *\nAllow: /\n"
    if SITE_URL:
        today = date.today().isoformat()
        urls = "".join(f"  <url><loc>{SITE_URL}{p}</loc><lastmod>{today}</lastmod></url>\n" for p in pages)
        write("sitemap.xml", f'<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n{urls}</urlset>\n')
        robots += f"Sitemap: {SITE_URL}/sitemap.xml\n"
    write("robots.txt", robots)
    write(".nojekyll", "")  # GitHub Pages: serve the files as they are

    print(f"\nDone: {len(pages)} pages and {len(done)} files in {OUT}")


if __name__ == "__main__":
    main()
