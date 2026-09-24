(() => {
  const input = document.querySelector("#sg-search");
  if (!input) return;
  const normalize = (s) =>
    s
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");
  input.addEventListener("input", () => {
    const term = normalize(input.value.trim());
    let count = 0;
    document.querySelectorAll(".sg-section").forEach((section) => {
      const matches = !term || normalize(section.textContent).includes(term);
      section.hidden = !matches;
      if (matches) count++;
      section.querySelectorAll("details").forEach((d) => {
        if (term) d.open = normalize(d.textContent).includes(term);
      });
    });
    document.querySelector("#sg-search-status").textContent = term
      ? `${count} secciones encontradas.`
      : "";
  });
})();
