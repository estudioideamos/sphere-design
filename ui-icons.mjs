const paths = {
  ne: "M6 18 18 6M6 6h12v12",
  right: "M4 12h16m-7-7 7 7-7 7",
  left: "M20 12H4m7-7-7 7 7 7",
  up: "M12 20V4m-7 7 7-7 7 7",
  down: "M12 4v16m-7-7 7 7 7-7",
};
const directions = {
  "↗": "ne",
  "→": "right",
  "←": "left",
  "↑": "up",
  "↓": "down",
};
export function vectorArrows(html) {
  return html.replace(
    />([^<]*)</g,
    (_, text) =>
      ">" +
      text.replace(
        /[↗→←↑↓]/g,
        (g) =>
          `<svg class="ui-arrow arrow-${directions[g]}" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="${paths[directions[g]]}"/></svg>`,
      ) +
      "<",
  );
}
