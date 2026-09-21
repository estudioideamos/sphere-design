import { vectorArrows } from "./ui-icons.mjs";
import crypto from "node:crypto";
const esc = (value) =>
  String(value)
    .replaceAll("&", "&amp;")
    .replaceAll('"', "&quot;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;");
export function renderPage({ file, page, base, nav, footer, modal, versions }) {
  const url = new URL(file === "index.html" ? "" : file, base).href;
  const article = [
    "the-first-image.html",
    "light-and-material.html",
    "beyond-the-frame.html",
  ].includes(file);
  const cover = new URL("assets/og-sphere.jpg", base).href;
  const schema = {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": base + "#organization",
        name: "Sphere Design",
        url: base,
        logo: new URL("assets/logo.png", base).href,
        email: "info@studiospheredesign.com",
        sameAs: [
          "https://www.instagram.com/thespheredesign/",
          "https://www.linkedin.com/company/studiospheredesign/",
          "https://www.behance.net/spheredesignllc",
        ],
      },
      {
        "@type": "WebSite",
        "@id": base + "#website",
        url: base,
        name: "Sphere Design",
        publisher: { "@id": base + "#organization" },
        inLanguage: "en",
      },
      {
        "@type": article
          ? "BlogPosting"
          : file === "portfolio.html"
            ? "CollectionPage"
            : "WebPage",
        "@id": url + "#page",
        url,
        name: page.title,
        headline: page.title,
        description: page.description,
        inLanguage: "en",
        isPartOf: { "@id": base + "#website" },
        image: cover,
      },
      ...(file === "index.html"
        ? []
        : [
            {
              "@type": "BreadcrumbList",
              itemListElement: [
                { "@type": "ListItem", position: 1, name: "Home", item: base },
                {
                  "@type": "ListItem",
                  position: 2,
                  name: article ? "Insights" : page.active,
                  item: article ? new URL("insights.html", base).href : url,
                },
                ...(article
                  ? [
                      {
                        "@type": "ListItem",
                        position: 3,
                        name: page.title,
                        item: url,
                      },
                    ]
                  : []),
              ],
            },
          ]),
    ],
  };
  const json = JSON.stringify(schema).replaceAll("<", "\\u003c");
  const hash = crypto.createHash("sha256").update(json).digest("base64");
  const csp = `default-src 'self'; script-src 'self' 'sha256-${hash}'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; font-src 'self'; media-src 'self' blob:; connect-src 'self'; frame-src https://www.youtube-nocookie.com; object-src 'none'; base-uri 'self'; form-action 'self' mailto:; upgrade-insecure-requests`;
  const indexable = process.env.INDEXABLE !== "false";
  return vectorArrows(
    `<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta http-equiv="Content-Security-Policy" content="${esc(csp)}"><meta name="referrer" content="strict-origin-when-cross-origin"><meta name="theme-color" content="#141513"><meta name="robots" content="${indexable ? "index,follow,max-image-preview:large" : "noindex,follow"}"><title>${esc(page.title)} | Sphere Design</title><meta name="description" content="${esc(page.description)}">${file === "index.html" ? '<link rel="preload" as="image" href="assets/hero-poster.webp?v=20260921" fetchpriority="high">' : ""}<link rel="canonical" href="${esc(url)}"><meta property="og:site_name" content="Sphere Design"><meta property="og:title" content="${esc(page.title)}"><meta property="og:description" content="${esc(page.description)}"><meta property="og:type" content="${article ? "article" : "website"}"><meta property="og:url" content="${esc(url)}"><meta property="og:image" content="${cover}"><meta property="og:image:alt" content="Architectural visualization by Sphere Design"><meta name="twitter:card" content="summary_large_image"><link rel="preload" href="assets/fonts/dm-sans-latin.woff2" as="font" type="font/woff2" crossorigin><link rel="preload" href="assets/fonts/italiana-latin.woff2" as="font" type="font/woff2" crossorigin><link rel="icon" href="assets/favicon.png" type="image/png"><link rel="stylesheet" href="style.css?v=${versions.style}"><link rel="stylesheet" href="motion.css?v=${versions.motion}"><script src="app.js?v=${versions.app}" defer></script><script src="motion.js?v=${versions.motionjs}" defer></script></head><body id="top" class="${page.active === "Home" ? "home" : "inner"}">${nav(page.active)}<main id="main">${page.body}</main>${footer()}${modal}<script type="application/ld+json">${json}</script></body></html>`,
  );
}
