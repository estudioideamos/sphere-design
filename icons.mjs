const drawings = {
  film: '<rect x="6" y="10" width="36" height="28" rx="2"/><path d="M6 17h36M6 31h36M14 10v7m10-7v7m10-7v7M14 31v7m10-7v7m10-7v7"/><path class="icon-accent" d="m20 21 8 4-8 4Z"/>',
  interior:
    '<path d="M5 39V10l19-5 19 5v29M5 10l19 9 19-9M24 5v14M24 19v10M5 39l12-10m26 10L31 29"/><path class="icon-accent" d="M14 29h20v9H14zM17 29v-5h14v5M18 38v4m12-4v4"/>',
  exterior:
    '<path d="M5 41h38M9 41V19L26 8l13 7v26M9 19l17 5 13-9M26 24v17M26 8v16M14 24v10m6-8v10M31 27v8m4-11v9"/><path class="icon-accent" d="M4 13 26 2l18 10"/>',
  vr: '<circle cx="24" cy="24" r="18"/><ellipse cx="24" cy="24" rx="8" ry="18"/><path d="M6 24h36M9 14h30M9 34h30"/><path class="icon-accent" d="m38 4 5 5-6 3M43 9A24 24 0 0 0 14 3"/>',
  plan: '<path d="M6 6h36v36H6zM6 21h16V6M22 21v8m0 7v6M22 27h20M31 6v13h11"/><path class="icon-accent" d="M12 13h4m-2-2v4M28 34h8m-4-4v8M8 29v7h7"/>',
  product:
    '<path d="m24 5 17 10v19L24 44 7 34V15L24 5Zm0 20L7 15m17 10 17-10M24 25v19"/><path class="icon-accent" d="m15 11 18 10v9M31 39l5-3"/>',
  bim: '<path d="m24 4 20 11-20 11L4 15 24 4Zm-20 20 20 11 20-11M4 33l20 11 20-11"/><path class="icon-accent" d="M24 4v22M14 9l20 11M34 9 14 20"/>',
  documents:
    '<path d="M12 5h20l7 7v26H12V5Zm20 0v8h7M7 12v31h25"/><path class="icon-accent" d="M18 20h15M18 26h15M18 32h8"/>',
  scope:
    '<circle cx="24" cy="24" r="17"/><path d="M24 3v7m0 28v7M3 24h7m28 0h7"/><path class="icon-accent" d="m31 17-4 10-10 4 4-10 10-4Z"/>',
  light:
    '<circle cx="24" cy="24" r="9"/><path class="icon-accent" d="M24 3v7m0 28v7M3 24h7m28 0h7M9 9l5 5m20 20 5 5M9 39l5-5m20-20 5-5"/><path d="M24 15v18M15 24h18"/>',
  delivery:
    '<path d="M8 10h20M8 10v30h32V23M14 18h9M14 24h6M14 30h16"/><path class="icon-accent" d="M25 8h15v15M40 8 23 25"/>',
};
export const icon = (name) =>
  `<svg class="editorial-icon icon-${name}" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false" stroke="currentColor" stroke-width="1.15" stroke-linecap="round" stroke-linejoin="round">${drawings[name] || drawings.product}</svg>`;
export const serviceIcons = [
  "film",
  "interior",
  "exterior",
  "vr",
  "plan",
  "product",
  "bim",
];
export const processIcons = [
  "documents",
  "scope",
  "product",
  "light",
  "delivery",
];
