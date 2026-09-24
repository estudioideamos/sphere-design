import fs from "node:fs";
import path from "node:path";
import assert from "node:assert/strict";
const root = path.resolve("dist");
const files = fs.readdirSync(root).filter((f) => f.endsWith(".html"));
const portfolio = JSON.parse(fs.readFileSync("portfolio-data.json", "utf8"));
assert.equal(portfolio.length, 79);
assert.equal(new Set(portfolio.map((p) => p[4].sourceId)).size, 79);
assert.equal(portfolio.filter((p) => p[1] === "Animations").length, 3);
assert.equal(portfolio.filter((p) => p[1] === "VR 360°").length, 3);
for (const file of files) {
  const html = fs.readFileSync(path.join(root, file), "utf8");
  assert.equal(
    (html.match(/<h1[ >]/g) || []).length,
    1,
    file + " needs exactly one H1",
  );
  assert.match(html, /rel="canonical"/);
  assert.match(html, /Content-Security-Policy/);
  assert.match(html, /og:image/);
  assert.match(html, /name="description"/);
  for (const m of html.matchAll(
    /<script type="application\/ld\+json">([\s\S]*?)<\/script>/g,
  ))
    JSON.parse(m[1]);
  for (const m of html.matchAll(/(?:src|href)="([^"#]+)"/g)) {
    const ref = m[1];
    if (/^(https?:|mailto:|tel:|data:)/.test(ref)) continue;
    const local = ref.split(/[?#]/)[0];
    assert.ok(
      fs.existsSync(path.resolve(root, local)),
      `${file}: missing ${local}`,
    );
  }
  for (const m of html.matchAll(/srcset="([^"]+)"/g))
    for (const candidate of m[1].split(",")) {
      const file = candidate.trim().split(/\s+/)[0];
      assert.ok(
        fs.existsSync(path.resolve(root, file)),
        "Missing source " + file,
      );
    }
}
for (const p of portfolio) {
  for (const suffix of [
    ".webp",
    "-sm.webp",
    "-xl.webp",
    ...(p[1] === "Animations" ? [".mp4"] : []),
  ])
    assert.ok(fs.existsSync(path.join(root, "assets", p[0] + suffix)));
  if (p[1] === "VR 360°")
    assert.equal(
      p[4].width / p[4].height,
      2,
      "Panorama must be equirectangular",
    );
}
for (const css of ["style.css", "motion.css"]) {
  const s = fs.readFileSync(path.join(root, css), "utf8");
  assert.ok(!s.includes("@import"), "No render-blocking external CSS imports");
  for (const m of s.matchAll(/url\(["']?([^)'" ]+)/g)) {
    if (/^(data:|#|%23)/.test(m[1])) continue;
    assert.ok(
      fs.existsSync(path.join(root, m[1])),
      "Missing CSS asset " + m[1],
    );
  }
}
assert.ok(fs.existsSync(root + "/llms.txt"));
assert.ok(fs.existsSync(root + "/sitemap.xml"));
console.log(
  `Verified ${files.length} pages, 79 portfolio items, local references, metadata, schemas and media variants.`,
);
