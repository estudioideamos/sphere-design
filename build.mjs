import { renderPage } from "./render.mjs";
import { createHash } from "node:crypto";
import { addJournal } from "./journal.mjs";
import fs from "node:fs";
import path from "node:path";
import { icon, serviceIcons, processIcons } from "./icons.mjs";
const base =
  process.env.SITE_URL || "https://estudioideamos.github.io/sphere-design/";
const featuredProjects = [
  [
    "interior2",
    "Residential",
    "Space, light & living",
    "A refined living space with sculptural seating and a warm material palette.",
  ],
  [
    "exterior2",
    "Exterior",
    "Architecture in nature",
    "A timber house on the water, surrounded by a misty forest.",
  ],
  [
    "interior1",
    "Residential",
    "The art of materiality",
    "Marble, warm wood and carefully composed lighting in a residential kitchen.",
  ],
  [
    "hospitality",
    "Hospitality",
    "A sense of arrival",
    "A hospitality lounge with layered lighting and expansive glazing.",
  ],
];
const portfolio = JSON.parse(fs.readFileSync("portfolio-data.json", "utf8"));
const projects = [...featuredProjects, ...portfolio];
const services = [
  [
    "Architectural films",
    "Reveal architecture through movement, light and experience.",
    "Animations",
  ],
  [
    "Interior renderings",
    "Materials, lighting, furniture and atmosphere, precisely imagined.",
    "Residential",
  ],
  [
    "Exterior renderings",
    "Communicate architecture, context, landscape and character.",
    "Exterior",
  ],
  [
    "VR 360° experiences",
    "Experience a project from within, before it exists.",
    "VR 360°",
  ],
  [
    "3D floor plans",
    "Bring clarity to layouts and spatial relationships.",
    "3D Floor Plans",
  ],
  [
    "Product rendering",
    "Materiality, form and detail. Seen in a new light.",
    "Product Rendering",
  ],
  [
    "CAD & BIM modeling",
    "Precise documentation and digital modeling to support your project.",
    "All",
  ],
];
const faqs = [
  [
    "What do you need to start a project?",
    "We can work with CAD or PDF drawings, Revit files, SketchUp or 3ds Max models, site and landscape plans, material and color palettes, and FF&E selections.",
  ],
  [
    "Do I need to provide a 3D model?",
    "No. We can work with your existing model or build one from your architectural documentation.",
  ],
  [
    "Can you help with design decisions?",
    "Yes. Our architectural and design background allows us to provide design support when requested.",
  ],
  [
    "How long does a project take?",
    "Timing depends on scope and complexity. We provide an estimated timeline after reviewing your project.",
  ],
  [
    "Can I request changes?",
    "Yes. Feedback rounds are integrated into our workflow before final delivery.",
  ],
  [
    "Do you work only with clients in Miami?",
    "No. We are based in Miami, Florida, and work with clients across the United States and worldwide.",
  ],
  [
    "What resolution are the final images?",
    "Final visualizations are delivered in high resolution and optimized for their intended use.",
  ],
];
const steps = [
  [
    "Share your project",
    "Send us drawings, models, materials, FF&E and project documentation.",
  ],
  [
    "Scope & proposal",
    "Together, we define objectives, deliverables and timeline.",
  ],
  [
    "Model & block-out",
    "Confirm the architecture, composition and camera views.",
  ],
  [
    "Visual development",
    "Materials, lighting and atmosphere take shape, with your feedback.",
  ],
  [
    "Final delivery",
    "Approved images and animations, finished in high resolution.",
  ],
];
const socialIcon = (name, url) =>
  `<a class="social-icon" href="${url}" target="_blank" rel="noopener noreferrer" aria-label="${name} — opens in a new tab" title="${name}">${fs.readFileSync(`assets/social-${name.toLowerCase()}.svg`, "utf8")}</a>`;
const arrow = '<span aria-hidden="true">↗</span>';
const link = (href, text, cls = "text-link") =>
  `<a class="${cls}" href="${href}">${text}${arrow}</a>`;
const picture = (id, alt, extra = "") =>
  id.startsWith("portfolio/")
    ? portfolioPicture(id, alt, extra)
    : id === "hero-poster"
      ? `<img src="assets/hero-poster.jpg" alt="${alt}" loading="lazy" ${extra}>`
      : `<img src="assets/${id}.webp" srcset="assets/${id}-sm.webp 720w, assets/${id}.webp 1800w${["exterior1", "exterior2", "interior1", "interior2", "hospitality"].includes(id) ? `, assets/${id}-xl.webp 2880w` : ""}" sizes="(max-width: 700px) 100vw, 70vw" alt="${alt}" loading="lazy" decoding="async" ${extra}>`;
const portfolioPicture = (id, alt, extra = "") => {
  const p = portfolio.find((p) => p[0] === id),
    w = p[4].width,
    h = p[4].height;
  return `<img src="assets/${id}.webp" srcset="assets/${id}-sm.webp ${Math.min(720, w)}w, assets/${id}.webp ${Math.min(1440, w)}w, assets/${id}-xl.webp ${w}w" sizes="(max-width:700px) 100vw, 50vw" width="${w}" height="${h}" alt="${alt}" loading="lazy" decoding="async" ${extra}>`;
};
const card = (p, i) =>
  `<button class="project project-${i} reveal" data-project="${projects.indexOf(p)}" data-asset="${p[0]}" data-category="${p[1]}" aria-label="Open ${p[2]}"><div class="project-image">${picture(p[0], p[3])}<span class="view">${p[1] === "Animations" ? "Play film" : p[1] === "VR 360°" ? "Explore 360°" : "View image"} ${arrow}</span></div><div class="project-meta"><h3>${p[2]}</h3><span>${p[1]} <i>${String(i + 1).padStart(2, "0")}</i></span></div></button>`;
const eyebrow = (n, text) => `<p class="eyebrow"><span>${n}</span>${text}</p>`;
const cta = () =>
  `<section class="closing wrap reveal" id="conversation"><div class="sphere-watermark watermark-closing" aria-hidden="true"><img src="assets/sphere-symbol.png" alt="" loading="lazy"></div>${eyebrow("LET’S COLLABORATE", "MIAMI · WORLDWIDE")}<h2>Your project has a vision.<br><em>Let’s make it visible.</em></h2>${link("contact.html", "Tell us about your project", "circle-link")}</section>`;
const nav = (active) => {
  const items = [
    ["index.html", "Home", "exterior2", "Where the vision begins"],
    ["about.html", "About us", "interior2", "The people. The perspective."],
    [
      "portfolio.html",
      "Portfolio",
      "hospitality",
      "Selected visual experiences",
    ],
    ["insights.html", "Insights", "interior1", "Ideas behind the image"],
    ["contact.html", "Contact", "exterior2", "Every vision starts here"],
  ];
  return `<a class="skip" href="#main">Skip to content</a><header class="site-header"><a href="index.html" class="brand" aria-label="Sphere Design home"><img src="assets/logo.png" alt="Sphere Design" width="150" height="52"></a><div class="header-links" aria-label="Quick navigation">${items
    .slice(1)
    .map(
      ([url, label]) =>
        `<a href="${url}" ${label === active ? 'aria-current="page"' : ""}>${label}</a>`,
    )
    .join(
      "",
    )}</div><div class="header-actions"><button class="menu-toggle" aria-expanded="false" aria-controls="navigation" aria-label="Open navigation"><span class="menu-word">Menu</span><span class="menu-symbol"><i></i><i></i></span></button></div></header><nav aria-label="Main navigation" id="navigation" class="editorial-menu" aria-hidden="true" inert><div class="menu-layout wrap"><div class="menu-main"><p class="menu-kicker"><span>EXPLORE SPHERE</span><span>INDEX / 01—05</span></p><div class="menu-links">${items.map(([url, label, img, caption], i) => `<a href="${url}" data-preview="${img}" data-caption="${caption}" ${label === active ? 'aria-current="page"' : ""}><span class="menu-number">0${i + 1}</span><span class="menu-title">${label}</span><span class="menu-arrow" aria-hidden="true">↗</span></a>`).join("")}</div></div><aside class="menu-art" aria-hidden="true"><div class="menu-art-frame"><img src="assets/exterior2-xl.webp" alt="" width="2880" height="1800" loading="lazy"><span class="menu-art-mark">${icon("vr")}</span><span class="menu-art-label">ARCHITECTURE, IMAGINED.</span></div><div class="menu-art-caption"><span>01 / 05</span><p>Where the vision begins</p></div></aside><div class="menu-bottom"><span class="menu-location">MIAMI, FLORIDA<span>Working worldwide.</span></span><a class="menu-contact" href="mailto:info@studiospheredesign.com"><span>HAVE A PROJECT IN MIND?</span>Let’s make it visible. <i aria-hidden="true">↗</i></a><div class="menu-socials">${socialIcon("Instagram", "https://www.instagram.com/thespheredesign/")}${socialIcon("LinkedIn", "https://www.linkedin.com/company/studiospheredesign/")}${socialIcon("Behance", "https://www.behance.net/spheredesignllc")}</div></div></div></nav>`;
};
const footer = () =>
  `<footer class="wrap"><div class="footer-signature" aria-hidden="true"><span>SPHERE</span><i>DESIGN STUDIO — MIAMI & BEYOND</i></div><div class="footer-top"><a class="brand" href="index.html"><img src="assets/logo.png" alt="Sphere Design" width="150" height="52"></a><p>Architectural visualization.<br>Beyond what is. Into what could be.</p><div><a href="mailto:info@studiospheredesign.com">info@studiospheredesign.com</a><span>Miami, Florida · Working worldwide</span></div></div><div class="footer-bottom"><span>© ${new Date().getFullYear()} Sphere Design</span><div>${socialIcon("Instagram", "https://www.instagram.com/thespheredesign/")}${socialIcon("LinkedIn", "https://www.linkedin.com/company/studiospheredesign/")}${socialIcon("Behance", "https://www.behance.net/spheredesignllc")}</div><a href="#top">Back to top ↑</a></div><div class="studio-credit"><p>A vision, <em>brought online.</em></p><a href="https://ideamos.com.ar" target="_blank" rel="noopener noreferrer" aria-label="Website crafted by Estudio Ideamos, opens in a new tab"><span>Digital craft by</span><img src="assets/ideamos-light.webp" alt="Estudio Ideamos" width="110" height="27" loading="lazy"><span class="credit-arrow" aria-hidden="true">↗</span></a></div></footer>`;
const modal = `<dialog class="lightbox" aria-label="Portfolio viewer"><button class="modal-close" aria-label="Close viewer">✕</button><div class="modal-media"></div><div class="modal-caption"><button class="previous" aria-label="Previous image">←</button><p></p><button class="next" aria-label="Next image">→</button></div></dialog>`;

const marquee = (large = false) => {
  const words = large
    ? '<span class="marquee-phrase">Every detail <em>speaks.</em></span><span class="marquee-medallion"><img src="assets/sphere-symbol.png" alt="" width="32" height="32"></span><span class="marquee-outline">SPHERE DESIGN</span><span class="marquee-image"><img src="assets/interior1-sm.webp" alt="" width="150" height="96" loading="lazy"></span>'
    : '<span>Architecture</span><b><img src="assets/sphere-symbol.png" alt="" width="24" height="24"></b><span><em>Atmosphere</em></span><b><img src="assets/sphere-symbol.png" alt="" width="24" height="24"></b><span>Materiality</span><b><img src="assets/sphere-symbol.png" alt="" width="24" height="24"></b><span><em>Light & emotion</em></span><b><img src="assets/sphere-symbol.png" alt="" width="24" height="24"></b>';
  const secondary =
    '<span>Still images</span><i><img src="assets/sphere-symbol.png" alt="" width="18" height="18"></i><span><em>Cinematic films</em></span><i><img src="assets/sphere-symbol.png" alt="" width="18" height="18"></i><span>Immersive experiences</span><i><img src="assets/sphere-symbol.png" alt="" width="18" height="18"></i>';
  return `<section class="marquee-section ${large ? "marquee-statement" : "marquee-ribbon"}" aria-label="${large ? "Every detail speaks. Sphere Design." : "Architecture, atmosphere, materiality, light and emotion."}"><div class="marquee-topline wrap"><span>${large ? "THE SPHERE SIGNATURE" : "DESIGN / VISUALIZATION / EXPERIENCE"}</span><button class="marquee-toggle" type="button" aria-pressed="false" aria-label="Pause ${large ? "signature" : "expertise"} marquee"><svg class="playback-icon" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><rect x="4" y="3" width="2.5" height="10" rx=".5"/><rect x="9.5" y="3" width="2.5" height="10" rx=".5"/></svg> <span>Pause motion</span></button></div><div class="marquee-window" aria-hidden="true"><div class="marquee-track"><div class="marquee-group">${words}</div><div class="marquee-group">${words}</div></div></div>${large ? `<div class="marquee-window marquee-secondary" aria-hidden="true"><div class="marquee-track"><div class="marquee-group">${secondary}${secondary}</div><div class="marquee-group">${secondary}${secondary}</div></div></div>` : ""}</section>`;
};

const pages = {};
pages["index.html"] = {
  title: "Architectural Visualization Studio in Miami",
  active: "Home",
  description:
    "Sphere Design creates architectural renderings, cinematic animations and immersive VR experiences. Based in Miami. Working worldwide.",
  body: `<section class="hero"><div class="hero-media"><video id="hero-video" muted playsinline loop preload="none" poster="assets/hero-poster.webp" aria-label="Sphere Design architectural film"><source data-src="assets/hero.mp4" type="video/mp4"></video></div><div class="hero-shade"></div><div class="hero-content wrap"><div class="hero-topline"><span class="eyebrow">ARCHITECTURAL VISUALIZATION STUDIO</span><span class="location"><i></i> MIAMI, FL · WORLDWIDE</span></div><h1>Before it’s built.<br><em>Make it felt.</em></h1><div class="hero-bottom"><p>Architectural visualization for decisions<br>made before construction.</p>${link("portfolio.html", "Explore our work", "hero-link")}<button class="film-control" aria-label="Play background film"><svg class="playback-icon" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M5 3.5 12 8l-7 4.5Z"/></svg> <span>Play film</span></button></div></div><a href="#intro" class="scroll-cue">SCROLL TO DISCOVER <span>↓</span></a></section>
${marquee()}<section class="intro wrap" id="intro">${eyebrow("01", "THE SPHERE PERSPECTIVE")}<div><h2>More than an image.<br><em>A vision, made tangible.</em></h2><div class="intro-bottom"><p>Helping developers market and sell, architects communicate with clarity, interior designers validate design with precision, and realtors showcase property and potential.</p>${link("about.html", "Meet the studio")}</div></div></section>
<section class="selected wrap" id="selected-work"><div class="section-heading">${eyebrow("02", "SELECTED WORK")}<h2>Unbuilt.<br><em>Unforgettable.</em></h2>${link("portfolio.html", "View full portfolio")}</div><div class="selected-grid">${[projects[0], projects[1], projects[2], projects[3]].map(card).join("")}</div></section>
${marquee(true)}<section class="cinema" aria-label="The art of atmosphere"><div class="cinema-frame">${picture("interior2", "Sculptural furniture, warm light and considered materials in a Sphere Design residential interior").replace('sizes="(max-width: 700px) 100vw, 70vw"', 'sizes="100vw"')}<div class="cinema-shade"></div><div class="cinema-content wrap"><p class="eyebrow"><span>THE ART OF ATMOSPHERE</span>STILL IMAGE. LASTING IMPRESSION.</p><h2>A feeling.<br>A place.<br><em>A possibility.</em></h2><div class="cinema-bottom"><span>LIGHT / MATERIAL / EMOTION</span>${link("portfolio.html?category=Residential", "Enter the experience")}</div></div><span class="frame-corner corner-tl" aria-hidden="true"></span><span class="frame-corner corner-br" aria-hidden="true"></span></div></section><section class="services wrap" id="expertise"><div class="section-heading">${eyebrow("03", "OUR EXPERTISE")}<h2>Visualization beyond<br><em>the still image.</em></h2></div><div class="service-layout"><div class="service-visual">${picture("interior1", "Detailed marble and timber material palette in a Sphere Design interior")}<span>PRECISION IN EVERY DETAIL.</span></div><div class="service-list">${services.map((s, i) => `<a class="service-row" href="portfolio.html?category=${encodeURIComponent(s[2])}"><span class="service-num"><small>0${i + 1}</small>${icon(serviceIcons[i])}</span><div><h3>${s[0]}</h3><p>${s[1]}</p></div>${arrow}</a>`).join("")}</div></div></section>
<section class="industries wrap">${eyebrow("04", "INDUSTRIES WE SERVE")}<div class="industry-list">${["Luxury residential", "Real estate developments", "Multifamily"].map((s, i) => `<a href="portfolio.html?category=${i === 1 ? "Exterior" : "Residential"}"><span>0${i + 1}</span><h2>${s}</h2>${arrow}</a>`).join("")}<p>Residential · Hospitality · Food & Beverage · Sports · Corporate · Retail · Healthcare · Entertainment · Institutional · Aviation & Yachting</p></div></section>
<section class="process wrap" id="process"><div class="section-heading">${eyebrow("05", "THE PROCESS")}<h2>From possibility<br><em>to visual certainty.</em></h2><p>A clear, collaborative process.<br>A shared vision, at every step.</p></div><div class="steps">${steps.map((s, i) => `<article class="reveal"><div class="step-visual"><span>0${i + 1}</span>${icon(processIcons[i])}</div><h3>${s[0]}</h3><p>${s[1]}</p></article>`).join("")}</div></section>
<section class="why wrap"><div class="sphere-watermark watermark-why" aria-hidden="true"><img src="assets/sphere-symbol.png" alt="" loading="lazy"></div>${eyebrow("06", "WHY SPHERE")}<h2>Design expertise.<br><em>A partner you can rely on.</em></h2><div class="why-grid">${[
    [
      "Precision & consistency",
      "From architecture and materials to lighting and atmosphere, every detail is considered.",
    ],
    [
      "Personalized collaboration",
      "We listen, adapt and build the right visual response around your objectives.",
    ],
    [
      "Reliable execution",
      "Clear communication, structured processes and dependable delivery.",
    ],
  ]
    .map(([a, b]) => `<article><h3>${a}</h3><p>${b}</p></article>`)
    .join("")}</div></section>
<section class="faq wrap"><div>${eyebrow("07", "A LITTLE MORE CLARITY")}<h2>Good questions.<br><em>Clear answers.</em></h2></div><div>${faqs.map(([q, a], i) => `<details><summary><span class="faq-number" aria-hidden="true">${String(i + 1).padStart(2, "0")}</span><span class="faq-question">${q}</span><span class="faq-toggle" aria-hidden="true"><i></i><i></i></span></summary><div class="faq-answer"><p>${a}</p></div></details>`).join("")}</div></section>${cta()}`,
};
pages["portfolio.html"] = {
  title: "Portfolio — Architectural Renderings & Films",
  active: "Portfolio",
  description:
    "Explore Sphere Design architectural visualization across residential, hospitality, exterior, retail and more.",
  body: `<section class="page-title wrap">${eyebrow("THE PORTFOLIO", "A STUDY IN POSSIBILITY")}<h1>A world of visual<br><em>experiences.</em></h1><div class="title-bottom"><p>Architecture, atmosphere and detail, crafted to communicate<br>the value of what has yet to be built.</p><span>STILLS / MOTION / IMMERSION</span></div></section><section class="portfolio wrap"><div class="filters" aria-label="Filter portfolio">${["All", ...new Set(portfolio.map((p) => p[1]))].map((s, i) => `<button data-filter="${s}" aria-pressed="${!i}">${s}</button>`).join("")}</div><p class="result-count" aria-live="polite">${portfolio.length} visual experiences</p><div class="portfolio-grid">${portfolio.map(card).join("")}</div><button class="portfolio-more" type="button" hidden>Explore more <span aria-hidden="true">＋</span></button></section>${cta()}`,
};
pages["about.html"] = {
  title: "About the Studio",
  active: "About us",
  description:
    "Meet the team behind Sphere Design. A multidisciplinary architectural visualization studio based in Miami.",
  body: `<section class="page-title wrap">${eyebrow("ABOUT SPHERE", "DESIGN EXPERTISE × VISUAL STRATEGY")}<h1>Creative minds.<br><em>Shared vision.</em></h1></section><section class="about-image wrap">${picture("interior2", "Luxury residential interior visualized by Sphere Design")}</section><section class="intro wrap">${eyebrow("01", "THE STUDIO")}<div><h2>Where design expertise<br><em>meets visual strategy.</em></h2><div class="prose"><p>Sphere Design is a creative architectural visualization studio built around a multidisciplinary approach to design, visualization and business.</p><p>We combine architectural and design expertise with a specialized team of creative artists to produce visual experiences that communicate with precision, consistency and purpose.</p><p>From early design decisions to marketing and sales, we approach every project with one goal: to understand what our clients need to achieve and create the visual response to support it.</p></div></div></section><section class="team wrap"><div class="section-heading">${eyebrow("02", "THE PEOPLE BEHIND SPHERE")}<h2>Personal commitment.<br><em>Collective expertise.</em></h2></div><div class="team-grid"><article>${picture("vanesa", "Vanesa Vila, Founder and CEO of Sphere Design")}<h3>Vanesa Vila</h3><span>FOUNDER & CEO</span><p>Architect and Interior Designer with experience in luxury architecture, interior design and architectural visualization. With an MBA and a background in Project Management, she brings together design expertise, visual thinking and strategic project understanding.</p></article><article>${picture("ivanna", "Ivanna Poblet, Business Operations Manager at Sphere Design")}<h3>Ivanna Poblet</h3><span>BUSINESS OPERATIONS MANAGER</span><p>Accountant and MBA with experience in finance and business operations across multinational companies and diverse industries. Her background brings a strong business and operational perspective to Sphere Design.</p></article></div><div class="team-note"><h3>A specialized creative team.</h3><p>3D modeling, high-end rendering, image post-production, animation, video editing and project management. The right creative team around the needs of each project.</p></div></section>${cta()}`,
};
pages["contact.html"] = {
  title: "Contact — Tell Us About Your Project",
  active: "Contact",
  description:
    "Start a conversation with Sphere Design about architectural renderings, films and immersive experiences for your project.",
  body: `<section class="page-title wrap">${eyebrow("LET’S TALK", "EVERY VISION STARTS WITH A CONVERSATION")}<h1>What do you<br><em>have in mind?</em></h1></section><section class="contact-layout wrap"><div class="contact-info"><p>Tell us what you’re working on. We’ll help you determine the right visualization approach for your project.</p><div><span class="eyebrow">EMAIL US</span><a href="mailto:info@studiospheredesign.com">info@studiospheredesign.com ↗</a></div><div><span class="eyebrow">BASED IN MIAMI</span><p>Working across the United States<br>and worldwide.</p></div><div class="socials">${socialIcon("LinkedIn", "https://www.linkedin.com/company/studiospheredesign/")}${socialIcon("Instagram", "https://www.instagram.com/thespheredesign/")}</div></div><form id="contact-form"><div class="form-grid"><label>Your name <span>*</span><input name="name" autocomplete="name" required placeholder="Full name"></label><label>Email address <span>*</span><input name="email" type="email" autocomplete="email" required placeholder="you@company.com"></label></div><label>Company<input name="company" autocomplete="organization" placeholder="Company / studio"></label><label>How can we help? <span>*</span><select name="service" required><option value="" disabled selected>Select a service</option>${[...services.map((s) => s[0]), "Multiple services / full project", "Not sure — help me choose"].map((name) => `<option value="${name}">${name}</option>`).join("")}</select></label><label>A little about your project <span>*</span><textarea name="message" required rows="4" placeholder="Your vision, project location, timeline…"></textarea></label><p class="form-note">This demo opens your email app with your project details. Nothing is sent automatically.</p><button class="submit" type="submit">Prepare project inquiry ${arrow}</button><p id="form-status" aria-live="polite"></p></form></section>`,
};
addJournal(pages, { picture, eyebrow, link, cta });
const output = path.resolve("dist");
if (output !== path.join(fs.realpathSync("."), "dist"))
  throw Error("Invalid build output path");
if (fs.existsSync(output) && fs.lstatSync(output).isSymbolicLink())
  throw Error("Build output cannot be a symlink");
fs.rmSync(output, { recursive: true, force: true });
fs.mkdirSync(output, { recursive: true });
fs.cpSync("assets", "dist/assets", { recursive: true });
for (const f of ["style.css", "app.js", "motion.css", "motion.js"])
  fs.copyFileSync(f, "dist/" + f);
fs.writeFileSync("dist/assets/projects.json", JSON.stringify(projects));
const digest = (f) =>
  createHash("sha256").update(fs.readFileSync(f)).digest("hex").slice(0, 10);
const versions = {
  style: digest("style.css"),
  motion: digest("motion.css"),
  app: digest("app.js"),
  motionjs: digest("motion.js"),
};
for (const [file, page] of Object.entries(pages))
  fs.writeFileSync(
    "dist/" + file,
    renderPage({ file, page, base, nav, footer, modal, versions }),
  );
fs.writeFileSync(
  "dist/robots.txt",
  "User-agent: *\nAllow: /\nSitemap: " + base + "sitemap.xml\n",
);
fs.writeFileSync(
  "dist/sitemap.xml",
  `<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">${Object.keys(
    pages,
  )
    .map((f) => `<url><loc>${base}${f === "index.html" ? "" : f}</loc></url>`)
    .join("")}</urlset>`,
);
fs.copyFileSync("llms.txt", "dist/llms.txt");
fs.writeFileSync("dist/.nojekyll", "");
console.log(`Built ${Object.keys(pages).length} pages.`);
