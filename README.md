# Sphere Design — editorial demo

Dark, responsive architectural visualization website built with the client's supplied copy and imagery.

## Local development

Node.js 22 or later. No production dependencies.

- `npm run build` generates the five static pages in `dist/`.
- `npm run dev` serves the generated site at http://127.0.0.1:4173.

## Pages and interactions

Home, About, Portfolio, Insights and Contact. Category filters, accessible native image dialog, film playback, an interactive WebGL panorama, responsive menu, FAQs, reduced-motion support and mailto inquiry composition.

## Demo boundaries

This is a static design demo on GitHub Pages, not the final WordPress implementation. The portfolio is a curated selection, not the full archive. Editorial image labels describe the visuals; they are not asserted project names. The hero film is an optimized 22-second excerpt. Insights contains an honest coming-soon state because no articles were supplied. The contact form opens the visitor's email client; it does not submit to a backend or track successful conversions. Phone omitted because the supplied contact document marks it TBC.

The demo uses noindex to avoid competing with the client's current website. Page titles, descriptions, canonical URLs, structured headings, descriptive image alternatives, schema, robots.txt and sitemap.xml are included. Before production, replace the demo domain, remove noindex, configure WordPress content types/templates, a real form endpoint, privacy/consent requirements, GA4 and Search Console. No measured Core Web Vitals claims are made.

## Content and assets

Source: client-authorized Google Drive folder provided for this project. Only selected optimized public-facing media are included here. Original briefs and source documents are not published. All client branding, renders and photography remain the property of their respective owners. No license for reuse is granted.

## Publishing

The GitHub Actions workflow builds and deploys `dist/` to GitHub Pages on pushes to main. `SITE_URL` may be set at build time to change the canonical origin.

## Editorial motion update

Native, dependency-free motion in `motion.js` and `motion.css`: masked heading reveals, image reveal and subtle scroll drift, an immersive editorial interlude, contextual portfolio hover controls, service-image crossfades, animated filters and FAQ expansion, a sticky header, chapter navigation, reading progress, Miami local time, magnetic CTA detail and a large typographic footer. No scroll hijacking or blocking intro loader. Pointer effects apply only to fine pointers. Reduced-motion preferences remove transforms, reveals and autoplay. The motion layer responds to preference changes.

The kinetic typography layer adds two marquee sections with three seamless tracks. Duplicate groups are hidden from assistive technology, each section has a pause/resume button, motion continues on hover and focus, and off-screen/background tracks stop. Reduced-motion users see static typography. The footer glow follows fine pointers only.

## Navigation, identity and layout refinements

The shorter column in paired content sections becomes sticky only on desktop, measured after image/font layout and bounded by its section. The full-screen editorial navigation supports Escape, trapped keyboard focus, background inert state, mobile layout and image previews. Services and process use a custom SVG icon family with short entrance/hover strokes and reduced-motion support. Sphere watermarks use the supplied original symbol. The opening marquee uses an ivory palette and original brand-symbol separators. The full-width CTA glow has a feathered mask to avoid boxed edges.

Footer credits link to https://ideamos.com.ar and use the official logo from https://ideamos.com.ar/logos/ideamos-light.webp, as requested. Mobile footer content is centered.

Compact coordinated ivory marquees, higher-resolution original media and larger supporting typography improve clarity. Numbered FAQ panels use animated circular plus/minus controls. A fine-pointer cursor combines a precise dot, eased ring and contextual image labels; touch and reduced-motion users retain their native cursor.


Insights includes three original sample editorials, each with its own reading page and related-story navigation. These are labeled demo content. Social links use monochrome SVG brand marks from Simple Icons v11 (CC0). The signature marquee retains its large dark editorial treatment; the opening ribbon remains compact.

