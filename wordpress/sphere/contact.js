const contactForm = document.querySelector("#contact-form");
if (contactForm) {
  const note = contactForm.querySelector(".form-note");
  if (note)
    note.textContent = "Tell us about your project. We will reply by email.";
  const button = contactForm.querySelector("button[type=submit]");
  button.firstChild.textContent = "Send project inquiry ";
  contactForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!contactForm.reportValidity()) return;
    const status = document.querySelector("#form-status");
    button.disabled = true;
    status.textContent = "Sending…";
    try {
      const payload = Object.fromEntries(new FormData(contactForm));
      payload.nonce = SPHERE.nonce;
      const res = await fetch(SPHERE.contact, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (!res.ok) throw Error(data.message || "Please try again.");
      status.textContent = data.message;
      contactForm.reset();
    } catch (err) {
      status.textContent = err.message;
    } finally {
      button.disabled = false;
    }
  });
}
const toc = document.querySelector("#article-toc");
if (toc)
  document.querySelectorAll(".article-copy h2").forEach((h, i) => {
    h.id = h.id || "story-" + i;
    const li = document.createElement("li");
    const a = document.createElement("a");
    a.href = "#" + h.id;
    a.textContent = h.textContent;
    li.append(a);
    toc.append(li);
  });
