# Sphere Design

Responsive editorial website built from the client's supplied content. Published on GitHub Pages at https://estudioideamos.github.io/sphere-design/.

## Develop and validate

Use Node.js 24 or newer. No npm packages are required to build or run the website.

- `npm run build` rebuilds the generated `dist/` folder from source.
- `npm run check` validates pages, local references, metadata, structured data and every portfolio asset.
- `npm run dev` serves the result at http://127.0.0.1:4173.

## Source structure

- `build.mjs`: page composition and build output.
- `render.mjs`: shared HTML document, metadata and content security policy.
- `portfolio-data.json`: the complete supplied portfolio, its categories and source provenance.
- `journal.mjs`: three clearly labeled sample editorial articles.
- `icons.mjs`: the custom service/process icon family.
- `style.css`, `motion.css`: base design, responsive layouts and interaction styling.
- `app.js`, `motion.js`: navigation, gallery, panorama, filters and optional motion.
- `verify.mjs`: dependency-free build validation.

## Portfolio and media

The portfolio contains all 79 media files supplied in the client's `3- PORTFOLIO` folder: 73 still images, 3 equirectangular panoramas and 3 complete films, across 12 categories. File IDs preserve the source mapping. Numbered visual labels are not claimed project names. The home page retains four featured images.

Images are served as responsive WebP variants: 720px thumbnails, 1440px previews and up to 2880px gallery images without enlarging originals. Panoramas use 4096px textures. The portfolio reveals 12 items at a time with a keyboard-accessible load-more control. Images load lazily; full-resolution media opens on request. Full films load only inside the viewer, with controls. The hero uses the complete 26.03-second client update supplied on September 18 (6.11 MB desktop, 2.77 MB mobile), with separate MP4 variants, no audio and fast-start metadata. The services preview is a six-second excerpt (0.61 MB), loaded on demand. Selected Work uses paired category transitions driven by native scrolling on larger screens; mobile and reduced-motion users retain the static full-width gallery.

Fonts are self-hosted WOFF2 with swap rendering and bundled OFL licenses. No third-party scripts, trackers, remote fonts or runtime libraries are loaded. Motion respects reduced-motion preferences; marquees keep moving on hover and have explicit pause controls. Hidden/off-screen animation stops. Touch devices retain native interaction.

## Search and machine-readable content

Pages render complete HTML at build time. Each has a unique title and description, canonical URL, social preview metadata and JSON-LD. The build emits `sitemap.xml`, `robots.txt` and `llms.txt`. The latter is a reading aid, not a promise of AI inclusion or ranking. Pages are indexable by default; set `INDEXABLE=false` for a private-review deployment. Set `SITE_URL` when deploying to the final domain so canonical URLs and the sitemap use that domain.

The three Insights posts are original demonstration content, visibly identified as samples. They were not provided by the client. Contact information comes from supplied materials. No client results, ratings or project locations are invented.

## Security and delivery

No backend, database, authentication, secrets or production npm dependencies are present. The contact form prepares a mailto message; it does not submit or store inquiries. A real backend form and an appropriate privacy policy are separate production work.

The HTML sets a restrictive content security policy, including self-hosted scripts, hashed structured-data blocks, blocked plugin objects and restricted form destinations. Inline styles remain allowed because motion updates CSS properties. External links use noopener/noreferrer. The local preview server validates paths, handles invalid requests and supports video byte ranges.

GitHub Actions uses current actions pinned to immutable commit hashes, Node 24, least-privilege workflow permissions and validation before deployment. Dependabot checks Actions weekly. Build output, scratch files and package caches are excluded from git.

GitHub Pages controls HTTP response headers; this repository cannot set headers such as Permissions-Policy or CSP frame-ancestors through HTML. These require configuration on a production host/CDN. No claim of absolute security or guaranteed Core Web Vitals is made. Future changes still need review and testing.

## Ownership and credits

Only authorized public-facing media is included. Client briefs, original source documents and full-resolution working files are not published. Sphere branding, photography and renders remain the property of their owners. Social brand icons come from Simple Icons v11 (CC0). Fonts include their licenses. Site credits link to https://ideamos.com.ar and use the studio's official logo.
