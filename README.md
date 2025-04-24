# Kane – Personal WordPress Theme & Portfolio

A private, modern WordPress site combining a custom theme, portfolio management, and a small WooCommerce shop. Designed for personal use.

---

## Overview

Kane is a bespoke WordPress project featuring:

- **Custom Theme**: A modular, performance-focused theme with WooCommerce integration and advanced template customization.
- **Portfolio System**: A cutom built robust "Work" plugin for managing and displaying portfolio items, with galleries, categories, outputting schema, and project details.
- **WooCommerce Shop**: Lightweight shop functionality for select products.
- **Personal Branding**: Tailored for a personal site, not intended for public distribution.

---

## Key Features

- **Custom Post Types**: "Work" portfolio items with categories, galleries, and project metadata.
- **WooCommerce Integration**: Custom templates and styles for product and shop pages.
- **Advanced Template System**: Template overrides, hooks, and filters for flexible customization.
- **Gallery Support**: Responsive image galleries with Swiper.js and WebP optimization.
- **Schema & SEO**: Structured data output for portfolio and shop content.
- **Accessibility & Performance**: ARIA support, keyboard navigation, and optimized asset loading.

---

## Technologies & Architecture

- **WordPress** (5.3+)
- **WooCommerce** (4.2+)
- **PHP** (7.4+)
- **SCSS** (modular, with Bourbon & Susy)
- **JavaScript** (ES5/ES6, Swiper.js)
- **WebP** custom built image support and conversion, with thumbnail regeneration.
- **Modular PHP**: Separate files for post types, taxonomies, meta boxes, template functions, and data APIs.
- **Custom Plugin**: `/wp-content/plugins/work` for portfolio management.
- **Custom SEO**: custom built functions for meta and page titles for solid SEO.
- **Custom SEO**: custom built schema output for artworks within the Portfolio
- **Perf**: Focus on performance with custom function for extracting css from inline file, and injecting into head, managed by transient. Worflow upgrade.

---

## Project Structure

- `/wp-content/themes/kaneism/` – Main theme (templates, SCSS, WooCommerce overrides)
- `/wp-content/plugins/work/` – Portfolio plugin (custom post type, API, admin, docs)
- `/docs/` – Developer and integration documentation
- `/assets/` – Theme and plugin assets (CSS, JS, images)
- `/woocommerce/` – Custom WooCommerce templates

---

## Customization & Extensibility

- **Hooks & Filters**: Extensive actions and filters for theme and plugin customization.
- **Template Overrides**: Place `single-work.php`, `archive-work.php`, etc. in your theme to override plugin templates.
- **API Functions**: Use provided PHP functions for safe data access (see `/wp-content/plugins/work/docs/data-api.md`).

---

## Documentation

- **Theme & Plugin Docs**: See `/wp-content/plugins/work/docs/` for API, migration, and customization guides.
- **Code Reference**: Inline documentation throughout PHP and SCSS files.

---

## Installation

1. Clone or copy the repository to your WordPress installation.
2. Activate the Kaneism theme under Appearance > Themes.
3. Activate the "Work" plugin under Plugins.
4. Configure portfolio items and shop products via the WordPress admin.
5. See `/docs/` for advanced setup and customization.

---

## License & Credits

- **License**: MIT for code, GPL for WordPress components.
- **Credits**: Developed by Dylan Pitchford for personal use.
- **Note**: Not intended for public distribution or resale.

---
