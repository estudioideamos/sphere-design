# Sphere Design on WordPress

This directory contains the custom theme and the persistent content/form module.

- `sphere/`: theme. Copy the repository `assets/` directory into `sphere/assets/` for deployment. Assets remain shared with the static demo rather than duplicated in Git.
- `sphere-content.php`: install in `wp-content/mu-plugins/`. Registers editable portfolio projects and the contact endpoint independently of the active theme.
- `import.php`: run once using WP-CLI after installing the content module. Imports the approved pages, three articles and 79 projects from `sphere/seed.json`. Re-running updates matching slugs, so do not run after client edits without reconciling those edits.

## Editing

Insights use native WordPress Posts: title, excerpt, body and featured image. New published posts appear automatically in Insights. Portfolio projects use the Portfolio admin menu: title, featured image, category, Order and optional YouTube URL. Existing assets are registered in Media with their optimized responsive variants. Set the category Animations for YouTube films and VR 360° for panoramas.

The approved composed pages are fixed, per the final client scope. The admin exposes only Insights posts, Portfolio projects and Datos de contacto. The latter edits shared contact information and social links. Native page editing and the former visual page editor are removed from the client workflow. Install sphere-editor.php and sphere-editor.css in mu-plugins.

## Contact

The public contact form validates fields, uses a WordPress nonce, includes a honeypot and limits submissions per IP. It submits through wp_mail to info@thespheredesign.com, with the visitor email as Reply-To. Transport acceptance is not proof of inbox delivery. Verify actual delivery and configure SMTP for the final domain if required.

## Deployment

The WordPress staging site is https://ideamos.ar/sphere/ and is set to noindex. Before final launch, confirm the final domain, migrate URL references, verify mail delivery and enable indexing. Backups are stored outside the public directory. No credentials, session files or private keys belong in this repository.

The static GitHub Pages demo remains a separate deployment. Future WordPress content edits are not automatically synchronized back to the static generator.

## Administrator experience

Classic Editor is active for all users, with editor switching disabled. The custom Manual del sitio menu documents the classic Visual/Text workflow, featured images, excerpts, categories, ordering, deletion and restoration. Its assets are loaded only on that admin screen. Install sphere-manual.php, sphere-manual.css and sphere-manual.js beside the content module in mu-plugins.

The theme metadata and 1200×900 cover credit Estudio Ideamos. The public footer has no agency attribution, per client instruction. The cover uses actual client renders, not invented project images.

## Cleanup performed

196 redundant or unused asset files (240.9 MiB) were removed from the installed theme after copying the portfolio and article images to the WordPress media library. Source assets remain in the static project because its separate Pages deployment uses them. The three old local portfolio MP4s are not used: animations use the confirmed YouTube embeds. The public theme seed JSON was removed after import; retain the repository copy only for controlled migrations. Inactive sample plugins were removed. Twenty Twenty-Five remains as a recovery theme.


## SEO and caching

The theme seo.php provides unique page titles, editable post metadata, canonical URLs, social sharing metadata, English frontend language, Organization/WebSite/WebPage and Article/Breadcrumb graphs. WordPress core sitemaps activate when indexing is enabled at final launch; staging remains noindex by explicit instruction. User and taxonomy archive sitemaps are excluded. Site icon is set in WordPress with a 512px brand asset.

WP Super Cache is configured in simple mode with logged-in visitors excluded. Contact and the REST API are excluded; sphere-performance.php also prevents contact caching and invalidates public cache after editorial/contact changes. Install this MU module with the content and manual modules. OPCache is not available in the current PHP runtime and needs hosting-provider configuration.

## Production delivery from GitHub

Production is https://studiospheredesign.com/ on Hostinger. `.github/workflows/hostinger.yml` publishes pushes to main that touch `wordpress/`, the WordPress asset builder, or package manifests. Manual dispatch is also available. The static Pages workflow is independent: root static HTML/CSS changes are not implicitly converted into WordPress content.

Run `npm ci` and `node scripts/build-wordpress.mjs` to produce compressed theme assets. Edit theme source under `wordpress/sphere/`; edit custom modules under `wordpress/`. Each production deployment validates PHP, uploads a release, backs up installed code and clears LiteSpeed. It never imports the seed, replaces wp-config.php, modifies the content database or deletes uploads. Removed files must be handled deliberately after checking references. Releases/backups live outside public_html. GitHub holds a dedicated SSH key in encrypted secrets; the host key is pinned.

Sphere Security supplies response headers and disables XML-RPC authentication/pingbacks. Production disables the built-in code editor and script execution in uploads. These are protections, not a guarantee against vulnerabilities. Classic Editor and LiteSpeed Cache auto-update; retain regular hosting backups. The contact form remains excluded from cache.

Sphere Discovery serves /llms.txt from current public posts and contact data. This is an optional directory for AI tools, not a ranking requirement or guarantee of AI citations. Public semantic HTML, canonical metadata, structured data and the XML sitemap remain authoritative.
