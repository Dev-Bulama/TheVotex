# THEvotex — WordPress Theme

> Premium nightclub & event venue theme. ThemeForest quality.  
> Requires WordPress 6.3 · PHP 8.1 · Optional: Elementor, WooCommerce, ACF Pro

---

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [First-Time Setup](#first-time-setup)
4. [Customizer Reference](#customizer-reference)
5. [Elementor Usage](#elementor-usage)
6. [WooCommerce Setup](#woocommerce-setup)
7. [Custom Post Types](#custom-post-types)
8. [Custom Widgets](#custom-widgets)
9. [Plugin Compatibility](#plugin-compatibility)
10. [HTML → WordPress Template Mapping](#html--wordpress-template-mapping)
11. [Performance](#performance)
12. [Security](#security)
13. [Accessibility](#accessibility)
14. [Demo Import](#demo-import)
15. [File Structure](#file-structure)
16. [Changelog](#changelog)

---

## Requirements

| Requirement | Minimum |
|-------------|---------|
| WordPress | 6.3 |
| PHP | 8.1 |
| MySQL | 5.7 / MariaDB 10.4 |
| Elementor | 3.x (optional) |
| Elementor Pro | 3.x (optional, for Theme Builder) |
| WooCommerce | 7.x (optional) |
| ACF Pro | 6.x (optional) |

---

## Installation

### Via WordPress Admin (recommended)

1. Download `thevotex.zip` from ThemeForest.
2. In WordPress admin → **Appearance → Themes → Add New → Upload Theme**.
3. Upload the ZIP file and click **Install Now**.
4. Click **Activate**.

### Via FTP

1. Unzip `thevotex.zip`.
2. Upload the `thevotex/` folder to `/wp-content/themes/`.
3. In WordPress admin → **Appearance → Themes**, activate **THEvotex**.

---

## First-Time Setup

### 1. Logo

**Appearance → Customize → Site Identity → Logo**

Upload an SVG or high-resolution PNG. Recommended dimensions: **240 × 80 px** (flexible). The theme supports transparent backgrounds.

### 2. Navigation Menus

**Appearance → Menus**

Create menus and assign them to the four registered locations:

| Location | Used For |
|----------|----------|
| **Primary** | Desktop header navigation |
| **Mobile** | Mobile drawer (falls back to Primary if not assigned) |
| **Footer Navigate** | Footer links column |
| **Footer Legal** | Bottom bar — privacy policy, terms, etc. |

Add a menu item with CSS class `nav-cta` to style it as the gold CTA button in the header.

### 3. Widgets

**Appearance → Widgets**

| Widget Area | Suggested Widget |
|-------------|-----------------|
| **Footer: Navigate** | Navigation Menu widget |
| **Footer: Hours** | THEvotex Hours widget |
| **Footer: Contact** | THEvotex Contact widget |
| **Blog Sidebar** | Any — recent posts, categories, search |
| **Off-Canvas / Popup** | Newsletter plugin widget, GDPR notice |

### 4. Reading Settings

If the front page is a static page (recommended):

**Settings → Reading → Your homepage displays → A static page**

Assign your homepage to **"Front Page"** and optionally assign a posts page to **"Posts Page"**.

### 5. Permalinks

**Settings → Permalinks → Post name** (`/%postname%/`)

Click **Save Changes** after activating the theme to flush rewrite rules.

---

## Customizer Reference

Navigate to **Appearance → Customize → THEvotex Theme Options**.

### Colors

| Setting | CSS Token | Default |
|---------|-----------|---------|
| Primary — Gold | `--thevotex-gold` | `#c9a84c` |
| Dark Background | `--thevotex-black` | `#020204` |
| Card / Surface | `--thevotex-card` | `#0b0b14` |
| Body Text | `--thevotex-white` | `#f0eee8` |
| Muted Text | `--thevotex-muted` | `#7a7a8a` |

All color pickers use **postMessage live preview** — changes appear instantly without page reload.

### Typography

| Setting | Default |
|---------|---------|
| Heading Font | Bebas Neue |
| Body Font | DM Sans |
| Base Font Size | 16px (range: 14–20) |

### Header Layout

| Setting | Default |
|---------|---------|
| Sticky header | Enabled |
| Transparent on front page | Enabled |
| Header height | 80px (range: 60–140) |

### Footer

| Setting | Notes |
|---------|-------|
| Footer Tagline | Displayed in the brand column |
| Footer Background Color | Overrides the default dark background |
| Custom Copyright Text | Auto-generated if left blank |

### Other Sections

- **Contact & Location** — address, phone, email (used by the Contact widget)
- **Operating Hours** — per-day open/close times (used by the Hours widget)
- **Social Media** — Instagram, Facebook, TikTok, Twitter/X URLs
- **Hero Slider** — 3 background images + slide interval (ms)
- **Chatbot Widget** — enable/disable + chatbot ID/API URL

---

## Elementor Usage

### Page Templates

Select under **Page Attributes → Template** when editing a page:

| Template | Effect |
|----------|--------|
| **Default Template** | Standard header + content + footer |
| **Full Width** | Header + full-width content area (no container) + footer |
| **Canvas (Elementor)** | Completely blank — Elementor renders everything |
| **Reservation** | Standard header + minimal strip footer |

### Elementor Pro Theme Builder

The theme declares `add_theme_support('elementor')` and `add_theme_support('header-footer-elementor')`. To use Elementor Pro Theme Builder:

1. In Elementor Pro → **Theme Builder → Header**, create a new template.
2. Set the display condition to **Entire Site**.
3. The Elementor header/footer will replace the theme's `template-parts/header/site-header.php` and `template-parts/footer/site-footer.php`.

### Recommended Elementor Workflow

- Use **Full Width** template for landing pages
- Use **Canvas** template for popup/modal pages
- Keep the **Default Template** for standard content pages (About, Blog, Contact)

### CSS Conflicts

The theme uses CSS custom properties (`--thevotex-*`) as design tokens. Elementor widgets inherit these automatically. If a conflict occurs, prefix your Elementor custom CSS with `.elementor-widget` to increase specificity.

---

## WooCommerce Setup

1. Install and activate the **WooCommerce** plugin.
2. Run the WooCommerce setup wizard.
3. The theme's `woocommerce.php` automatically wraps all shop pages.

### Shop Grid

Defaults can be overridden in `inc/woocommerce.php`:

```php
// Change via Customizer settings: thevotex_woo_columns, thevotex_woo_per_page
```

| Device | Columns |
|--------|---------|
| Desktop | 3 |
| Tablet | 2 |
| Mobile | 1 |

### WooCommerce Pages to Create

After installing WooCommerce, create these pages and assign them in **WooCommerce → Settings → Advanced**:

- Cart
- Checkout
- My Account
- Shop

### Product Images

Recommended image sizes (configured via `add_theme_support('woocommerce')`):

| Size | Dimensions |
|------|-----------|
| Thumbnail | 400 × 400 px |
| Single product | 800 × 800 px |

---

## Custom Post Types

| CPT | Slug | Admin Label | Purpose |
|-----|------|-------------|---------|
| Event | `thevotex_event` | Events | Upcoming events with date, DJ, genre |
| DJ | `thevotex_dj` | DJs | Resident and guest DJ profiles |
| Package | `thevotex_package` | Packages | VIP / bottle service packages |
| Food Item | `thevotex_food_item` | Food Menu | Menu items (food & cocktails) |
| Reservation | `thevotex_reservation` | Reservations | Internal reservation records (private) |

### Custom Taxonomies

| Taxonomy | Slug | Attached To |
|----------|------|-------------|
| Event Genre | `event_genre` | Events |
| Food Category | `food_category` | Food Items |

### ACF Field Groups (with ACF Pro)

Load ACF field groups by adding them to `inc/acf-fields.php`. The theme includes an `acf_add_local_field_group()` loader in `functions.php`.

---

## Custom Widgets

### THEvotex Contact Widget

Displays venue address, phone, and email. Falls back to Customizer values when widget fields are left blank.

**Fields:** Title, Street address, City/Province, Phone, Email, Map URL, Show map checkbox

### THEvotex Hours Widget

Weekly operating hours table. Per-day fields: label, hours text, closed checkbox.

**Falls back to:** `thevotex_hours_{day}_open` Customizer settings.

**Output:** `<dl>` with `<dt>` day names and `<dd>` hours.

### THEvotex Social Widget

Social media icon links with gold gradient SVG icons.

**Platforms:** Instagram, Facebook, TikTok, Twitter/X  
**Falls back to:** `thevotex_social_*` Customizer settings.

---

## Plugin Compatibility

| Plugin | Status | Notes |
|--------|--------|-------|
| Elementor Free | ✅ Full | Canvas + Full-Width templates |
| Elementor Pro | ✅ Full | Theme Builder replaces header/footer |
| WooCommerce 7.x+ | ✅ Full | Custom wrapper, breadcrumbs, cart fragment |
| ACF / ACF Pro | ✅ Full | Conditional loader in functions.php |
| Contact Form 7 | ✅ Full | Assets load only on pages with CF7 shortcode |
| WPForms | ✅ Full | Global asset loading disabled |
| Gravity Forms | ✅ Full | Default CSS disabled, theme provides base styles |
| Yoast SEO | ✅ Full | Reservation CPT excluded from sitemap |
| Rank Math SEO | ✅ Full | Reservation CPT excluded from analysis |
| Wordfence | ✅ Compatible | No conflicts |
| iThemes Security | ✅ Compatible | No conflicts |
| W3 Total Cache | ✅ Compatible | Asset versioning via filemtime |
| WP Rocket | ✅ Compatible | Scripts use defer; fonts preconnected |
| LiteSpeed Cache | ✅ Compatible | No conflicts |

---

## HTML → WordPress Template Mapping

| Original HTML | WordPress Template | Notes |
|---------------|--------------------|-------|
| `index.html` | `front-page.php` | Section-based layout via template-parts |
| `about.html` | `page.php` | Default page template |
| `events.html` | `archive.php` (CPT: `thevotex_event`) | Auto-renders on `/events/` |
| `menu.html` | `archive.php` (CPT: `thevotex_food_item`) | Auto-renders on `/food-menu/` |
| `reservation.html` | `page-templates/template-reservation.php` | Strip footer auto-activated |
| `contact.html` | `page.php` | Assign the Reservation template or use CF7/WPForms |
| `<nav id="mainNav">` | `template-parts/header/site-header.php` | Dynamic logo, Walker nav |
| Full footer | `template-parts/footer/site-footer.php` | 3 widget column areas |
| Strip footer | `template-parts/footer/site-footer-strip.php` | Auto on reservation template |
| Mobile menu overlay | `template-parts/header/site-header.php` | `role="dialog"` + `aria-modal` |

### Reusable Sections → Template Parts

| HTML Section | Template Part Path |
|--------------|--------------------|
| Hero slider | `template-parts/sections/hero.php` |
| Upcoming events | `template-parts/sections/events.php` |
| Resident DJs | `template-parts/sections/djs.php` |
| About / story | `template-parts/sections/about.php` |
| Packages / table service | `template-parts/sections/packages.php` |
| Food menu teaser | `template-parts/sections/food.php` |
| Reservation CTA | `template-parts/sections/reservation-cta.php` |

---

## Performance

### Core Web Vitals Targets

| Metric | Target | Implementation |
|--------|--------|----------------|
| LCP | < 2.5s | Hero image: `fetchpriority="high"` + `loading="eager"` |
| CLS | < 0.1 | Images have explicit width/height; fonts preconnected |
| FID/INP | < 100ms | All scripts use `strategy: 'defer'` |
| TTFB | < 600ms | Heartbeat disabled on front-end; wp-embed removed |

### Asset Strategy

- **Main CSS** — single compiled `assets/css/theme.min.css`, filemtime-versioned
- **Google Fonts** — `preconnect` + `display=swap` to prevent render-blocking
- **Scripts** — `strategy: 'defer'` (WP 6.3 API) — non-blocking
- **Block library CSS** — dequeued on pages with no Gutenberg blocks
- **Dashicons** — dequeued for non-logged-in visitors
- **XML-RPC** — disabled
- **wp-embed** — deregistered on front-end
- **Heartbeat** — deregistered on front-end (only needed in admin)

---

## Security

| Measure | Implementation |
|---------|---------------|
| Output escaping | `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()` everywhere |
| Input sanitization | `sanitize_text_field()`, `sanitize_email()`, `absint()`, `sanitize_hex_color()` |
| Nonce validation | `thevotex_verify_nonce()` called at top of every AJAX handler |
| Direct file access | `if (!defined('ABSPATH')) exit;` in every PHP file |
| WP version hidden | `the_generator` filter returns empty string |
| Asset versions | `?ver=` stripped from all asset URLs |
| Security headers | `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, `Content-Security-Policy` |
| Login errors | Single ambiguous message — prevents username enumeration |
| REST API users | `/wp/v2/users` blocked for unauthenticated requests |
| File editor | `DISALLOW_FILE_EDIT` enabled (override with `THEVOTEX_ALLOW_FILE_EDIT` in wp-config.php) |
| Comment URLs | Author URL field removed from comment forms |

---

## Accessibility

| Feature | Implementation |
|---------|---------------|
| Skip link | `<a class="skip-link" href="#main-content">` in `header.php` |
| Landmarks | `role="banner"`, `role="main"`, `role="contentinfo"`, `role="navigation"` |
| Dialog | Mobile menu: `role="dialog"`, `aria-modal="true"` |
| Button states | Hamburger: `aria-expanded`, `aria-controls` |
| Focus management | WebKit skip-link fix in `inc/accessibility.php` |
| Image alt text | Fallback to attachment title when alt is blank |
| Heading hierarchy | H1 per page (archive title / page title), H2 in loops |
| Screen reader text | `.screen-reader-text` utility class in `style.css` |
| WCAG colour contrast | Dark background (#020204) + gold (#c9a84c) meets AA on large text |
| Keyboard navigation | Focus-visible outline handled by CSS; tab order follows DOM order |
| Search form | Accessible label, `role="search"`, `aria-label` on input |
| Pagination | `<nav>` landmark with `aria-label="Posts navigation"` |
| Widgets | Widget titles get `id` for `aria-labelledby` on sidebar regions |

---

## Demo Import

For one-click demo content import, install the **One Click Demo Import** plugin (or equivalent).

### Demo Files (place in `/demo/`)

| File | Contents |
|------|----------|
| `demo/demo-content.xml` | Pages, events, DJs, food items, packages, menus |
| `demo/widgets.wie` | Pre-configured widget layout (Widget Importer & Exporter format) |
| `demo/customizer.dat` | Customizer settings (Customizer Export/Import format) |
| `demo/images/` | Placeholder images for all demo content |

### Manual Demo Setup Steps

1. Import `demo-content.xml` via **Tools → Import → WordPress**.
2. Import widgets via the Widget Importer plugin or **Appearance → Widgets** (`.wie` file).
3. Import Customizer settings via Customizer Export/Import plugin (`.dat` file).
4. Go to **Appearance → Menus** and assign imported menus to all four locations.
5. Go to **Settings → Reading** and set the Front Page to the imported "Home" page.
6. Go to **Appearance → Customize → Site Identity** and upload your logo.

---

## File Structure

```
thevotex/
├── style.css                          # Theme header + CSS tokens
├── functions.php                      # Constants + modular inc/ loader
├── header.php                         # DOCTYPE → wp_head() → site-header
├── footer.php                         # site-footer → wp_footer()
├── index.php                          # Fallback loop template
├── front-page.php                     # Section-based homepage
├── page.php                           # Default page template
├── single.php                         # Single post template
├── archive.php                        # All archive types
├── 404.php                            # Error page
├── woocommerce.php                    # WooCommerce page wrapper
│
├── inc/
│   ├── helpers.php                    # Utility functions (thevotex_option etc.)
│   ├── setup.php                      # add_theme_support, image sizes
│   ├── post-types.php                 # CPT + taxonomy registration
│   ├── menus.php                      # Nav locations + Walker classes
│   ├── widgets.php                    # Sidebar areas + custom WP_Widget classes
│   ├── enqueue.php                    # Scripts, styles, Customizer inline CSS
│   ├── security.php                   # Headers, nonces, lockdown
│   ├── performance.php                # LCP, heartbeat, embed, dashicons
│   ├── accessibility.php              # ARIA hooks, search form, focus fix
│   ├── customizer.php                 # Customizer panels/sections/controls
│   ├── woocommerce.php                # WooCommerce hooks
│   └── compatibility.php             # ACF, CF7, Yoast, Rank Math shims
│
├── page-templates/
│   ├── template-canvas.php            # Elementor blank canvas
│   ├── template-fullwidth.php         # Full-width, no sidebar
│   └── template-reservation.php      # Reservation page (strip footer)
│
├── template-parts/
│   ├── header/
│   │   └── site-header.php           # Logo, nav, mobile overlay
│   ├── footer/
│   │   ├── site-footer.php           # Full 4-column footer
│   │   └── site-footer-strip.php     # Minimal strip footer
│   ├── content/
│   │   ├── content-page.php          # Page content
│   │   ├── content-single.php        # Single post content
│   │   ├── content-archive.php       # Post card for archives
│   │   └── content-none.php          # Empty state
│   └── sections/                     # Homepage sections (front-page.php)
│       ├── hero.php
│       ├── events.php
│       ├── djs.php
│       ├── about.php
│       ├── packages.php
│       ├── food.php
│       └── reservation-cta.php
│
├── assets/
│   ├── css/
│   │   ├── theme.min.css             # Compiled global styles
│   │   ├── reservation.min.css       # Reservation wizard styles
│   │   ├── woocommerce.min.css       # WooCommerce overrides
│   │   └── editor.min.css            # Block editor styles
│   ├── js/
│   │   ├── theme.min.js              # Cursor, nav, slider, scroll-reveal
│   │   ├── reservation.min.js        # Reservation form wizard
│   │   └── customizer-preview.js     # Customizer live-preview bindings
│   └── fonts/                        # Self-hosted fonts (optional)
│
├── languages/                         # .pot / .po / .mo translation files
└── demo/                              # One-click demo import files
    ├── demo-content.xml
    ├── widgets.wie
    └── customizer.dat
```

---

## Changelog

### 1.0.0 — Initial Release

- Full nightclub/event venue theme
- 5 Custom Post Types: Events, DJs, Packages, Food Items, Reservations
- 4 navigation menu locations with custom Walker classes
- 3 custom WP_Widget classes (Contact, Hours, Social)
- Full Elementor compatibility (Canvas + Full Width + Theme Builder)
- Full WooCommerce compatibility
- Customizer: Colors, Typography, Header/Footer Layout, Social, Hours, Hero Slider
- Live Customizer preview via postMessage (no page reload)
- Security: CSP, Permissions-Policy, login hardening, nonce validation
- Performance: defer scripts, fetchpriority LCP, heartbeat off, wp-embed removed
- Accessibility: WCAG 2.1 AA baseline, ARIA landmarks, skip link, accessible search
- Plugin compatibility: ACF, CF7, WPForms, Yoast, Rank Math, Elementor, WooCommerce
