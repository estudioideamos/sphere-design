# Hostinger migration — 2026-09-29

Sphere is deployed at https://studiospheredesign.com/ on Hostinger. The original https://ideamos.ar/sphere/ remains the separate staging copy.

- Migrated the current database and theme, media, custom content modules and manual: 79 portfolio projects, 3 articles and 5 pages.
- Preserved destination database credentials; source WordPress users and passwords remain valid.
- Replaced staging URLs using serialized-data-aware WP-CLI search-replace. Destination uses the wpud_ table prefix; original destination tables are retained for rollback.
- Enabled indexing with the user's explicit approval. Canonical URLs and the public WordPress sitemap use the final domain.
- Replaced WP Super Cache with LiteSpeed Cache; excluded contact, REST and authenticated/private cache. The performance module now purges LiteSpeed after editorial/contact changes.
- Did not copy old migration-plugin archives or migration plugins. Kept Twenty Twenty-Five as a recovery theme.
- Corrected the host's week-long default HTML browser cache rule. Public contact responds no-store/private.
- Repaired long filenames truncated by WP-CLI's core archive extraction using GNU tar and the official WordPress 7.1.2 package. Full core checksums pass.

Validation: desktop/mobile public routes, 79-item portfolio, YouTube modal, full-resolution image modal, three Insights, existing administrator login, manual and contact editor passed. Contact submission passed with wp_mail intercepted (no email sent); real inbox delivery remains to be tested. DNS and email records were not changed.

Private backups of both installations and the final database are retained outside public_html on Hostinger. A source backup is also stored outside the repository on the operator's computer. Never commit credentials or backup archives.
