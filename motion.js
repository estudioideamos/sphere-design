(() => {
  "use strict";
  const motionPreference = matchMedia("(prefers-reduced-motion: reduce)");
  const finePointer = matchMedia("(hover: hover) and (pointer: fine)");
  const canMove = () => !motionPreference.matches;
  const header = document.querySelector(".site-header");

  // Split only authored line breaks, preserving each heading's semantic text.
  document
    .querySelectorAll(
      "h1, .intro h2, .section-heading h2, .why>h2, .faq h2, .closing h2, .cinema h2, .insights-feature h2",
    )
    .forEach((heading) => {
      const lines = heading.innerHTML.split(/<br\s*\/?\s*>/i);
      heading.innerHTML = lines
        .map(
          (line, i) =>
            `<span class="motion-line" style="--line:${i}"><span>${line}</span></span>`,
        )
        .join("");
      heading.dataset.title = "";
    });

  const revealTargets =
    ".project, .steps article, .why-grid article, .team-grid article, .intro-bottom, .prose, .team-note, .contact-info, #contact-form, .service-visual, .service-row, .industry-list>a, .faq details, .closing>.circle-link, .footer-signature, .section-heading>.eyebrow, .intro>.eyebrow";
  document
    .querySelectorAll(revealTargets)
    .forEach((el) => (el.dataset.enter = ""));
  document
    .querySelectorAll(".steps, .why-grid, .service-list, .team-grid")
    .forEach((group) => {
      [...group.children].forEach((el, i) =>
        el.style.setProperty("--stagger", `${Math.min(i * 65, 260)}ms`),
      );
    });
  const observed = [...document.querySelectorAll("[data-title], [data-enter]")];
  if ("IntersectionObserver" in window) {
    const revealObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach(({ target, isIntersecting }) => {
          if (isIntersecting) {
            target.classList.add("entered");
            revealObserver.unobserve(target);
          }
        });
      },
      { threshold: 0.06, rootMargin: "0px 0px -20px 0px" },
    );
    observed.forEach((el) => revealObserver.observe(el));
    document.body.classList.add("motion-ready");
  } else observed.forEach((el) => el.classList.add("entered"));

  const progress = document.createElement("div");
  progress.className = "reading-progress";
  progress.setAttribute("aria-hidden", "true");
  document.body.append(progress);

  let chapterLinks = [];
  if (document.body.classList.contains("home")) {
    const chapters = [
      ["intro", "01 — Perspective"],
      ["selected-work", "02 — Selected work"],
      ["expertise", "03 — Expertise"],
      ["process", "04 — Process"],
      ["conversation", "05 — Let’s talk"],
    ];
    const rail = document.createElement("nav");
    rail.className = "chapter-nav";
    rail.setAttribute("aria-label", "Page chapters");
    rail.innerHTML = chapters
      .map(
        ([id, label]) =>
          `<a href="#${id}" aria-label="${label}"><span>${label}</span></a>`,
      )
      .join("");
    document.body.append(rail);
    chapterLinks = [...rail.children].map((a) => ({
      a,
      section: document.querySelector(a.hash),
    }));
  }

  const location = document.querySelector(".location");
  let clockTimer;
  if (location) {
    const time = document.createElement("time");
    time.setAttribute("aria-label", "Current time in Miami");
    location.replaceChildren();
    const dot = document.createElement("i");
    dot.setAttribute("aria-hidden", "true");
    location.append(dot, document.createTextNode("MIAMI, FL · "), time);
    const formatter = new Intl.DateTimeFormat("en-US", {
      timeZone: "America/New_York",
      hour: "2-digit",
      minute: "2-digit",
      hour12: false,
    });
    const tick = () => {
      const now = new Date();
      time.dateTime = now.toISOString();
      time.textContent = formatter.format(now);
    };
    tick();
    clockTimer = setInterval(tick, 60000);
  }

  // Keep scroll work to visible images; schedule only when input changes.
  const activeImages = new Set();
  const imageObserver = new IntersectionObserver(
    (entries) =>
      entries.forEach((e) => {
        if (e.isIntersecting) activeImages.add(e.target);
        else activeImages.delete(e.target);
      }),
    { rootMargin: "80px" },
  );
  document.querySelectorAll(".project-image").forEach((el, i) => {
    imageObserver.observe(el);
    const index = document.createElement("span");
    index.className = "project-index";
    index.setAttribute("aria-hidden", "true");
    index.textContent = `${String(i + 1).padStart(2, "0")} / SPHERE`;
    el.append(index);
    el.addEventListener(
      "pointermove",
      (e) => {
        if (!finePointer.matches || !canMove()) return;
        const rect = el.getBoundingClientRect();
        el.style.setProperty("--pointer-x", `${e.clientX - rect.left}px`);
        el.style.setProperty("--pointer-y", `${e.clientY - rect.top}px`);
      },
      { passive: true },
    );
  });
  const heroMedia = document.querySelector(".hero-media");
  const cinema = document.querySelector(".cinema-frame");
  const orbit = document.querySelector(".orbit");
  const signature = document.querySelector(".footer-signature");
  let frame = 0;
  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
  function updateScroll() {
    frame = 0;
    const y = scrollY,
      vh = innerHeight;
    const total = document.documentElement.scrollHeight - vh;
    header.classList.toggle("scrolled", y > 70);
    progress.style.transform = `scaleX(${total > 0 ? clamp(y / total, 0, 1) : 0})`;
    if (chapterLinks.length) {
      document
        .querySelector(".chapter-nav")
        .classList.toggle("shown", y > vh * 0.65);
      let active = chapterLinks[0];
      chapterLinks.forEach((item) => {
        if (item.section.getBoundingClientRect().top < vh * 0.48) active = item;
      });
      chapterLinks.forEach((item) => {
        if (item === active) item.a.setAttribute("aria-current", "location");
        else item.a.removeAttribute("aria-current");
      });
    }
    if (!canMove()) return;
    if (heroMedia && y < vh * 1.4)
      heroMedia.style.transform = `translateY(${Math.min(y * 0.16, 160)}px)`;
    if (innerWidth > 700)
      activeImages.forEach((el) => {
        const r = el.getBoundingClientRect();
        const offset = clamp(
          ((vh / 2 - r.top - r.height / 2) / vh) * 36,
          -22,
          22,
        );
        el.style.setProperty("--drift", `${offset.toFixed(2)}px`);
      });
    if (cinema) {
      const r = cinema.getBoundingClientRect();
      if (r.bottom > 0 && r.top < vh) {
        const position = clamp((vh - r.top) / (vh + r.height), 0, 1);
        cinema.style.setProperty(
          "--cinema-inset",
          `${(4 * (1 - Math.min(position * 2, 1))).toFixed(2)}%`,
        );
        cinema.style.setProperty(
          "--cinema-scale",
          `${(1.12 - position * 0.12).toFixed(3)}`,
        );
      }
    }
    if (orbit) {
      const r = orbit.parentElement.getBoundingClientRect();
      if (r.top < vh && r.bottom > 0)
        orbit.style.setProperty(
          "--orbit-turn",
          `${((vh - r.top) * 0.035).toFixed(1)}deg`,
        );
    }
    if (signature && innerWidth > 700) {
      const r = signature.getBoundingClientRect();
      if (r.top < vh)
        signature.style.setProperty(
          "--signature-shift",
          `${clamp((vh - r.top) * 0.012 - 10, -10, 8)}px`,
        );
    }
  }
  const scheduleScroll = () => {
    if (!frame) frame = requestAnimationFrame(updateScroll);
  };
  addEventListener("scroll", scheduleScroll, { passive: true });
  addEventListener("resize", scheduleScroll, { passive: true });
  addEventListener("pageshow", scheduleScroll);
  motionPreference.addEventListener("change", () => {
    if (!canMove()) {
      observed.forEach((el) => el.classList.add("entered"));
      document.getAnimations().forEach((a) => {
        if (a.effect?.getComputedTiming().endTime !== Infinity) {
          try {
            a.finish();
          } catch {}
        }
      });
    }
    scheduleScroll();
  });
  scheduleScroll();

  document.querySelectorAll(".circle-link>span").forEach((el) => {
    el.addEventListener("pointermove", (e) => {
      if (!finePointer.matches || !canMove()) return;
      const r = el.getBoundingClientRect();
      el.style.translate = `${clamp((e.clientX - r.left - r.width / 2) * 0.22, -8, 8)}px ${clamp((e.clientY - r.top - r.height / 2) * 0.22, -8, 8)}px`;
    });
    el.addEventListener("pointerleave", () => (el.style.translate = "0px 0px"));
  });

  // Editorial crossfades reuse media already supplied for the portfolio.
  const serviceVisual = document.querySelector(".service-visual");
  if (serviceVisual) {
    const preview = new Image();
    preview.className = "service-preview";
    preview.alt = "";
    preview.setAttribute("aria-hidden", "true");
    serviceVisual.append(preview);
    const serviceImages = [
      "hero-poster.jpg",
      "interior1-xl.webp",
      "exterior2-xl.webp",
      "vr.webp",
      "floorplan.webp",
      "product.webp",
      "exterior1.webp",
    ];
    const serviceLabels = [
      "ARCHITECTURE IN MOTION.",
      "PRECISION IN EVERY DETAIL.",
      "ARCHITECTURE. IN CONTEXT.",
      "A NEW POINT OF VIEW.",
      "CLARITY AT EVERY LEVEL.",
      "MATERIAL. FORM. DETAIL.",
      "THE FOUNDATION OF THE VISION.",
    ];
    let ticket = 0;
    document.querySelectorAll(".service-row").forEach((row, i) => {
      const change = () => {
        if (!finePointer.matches) return;
        const stamp = ++ticket;
        const img = new Image();
        img.src = `assets/${serviceImages[i]}`;
        img
          .decode()
          .then(() => {
            if (stamp !== ticket) return;
            preview.src = img.src;
            preview.classList.add("active");
            if (canMove())
              preview.animate(
                [
                  { opacity: 0.25, transform: "scale(1.035)" },
                  { opacity: 1, transform: "scale(1)" },
                ],
                { duration: 600, easing: "cubic-bezier(.16,1,.3,1)" },
              );
            serviceVisual.querySelector("span").textContent = serviceLabels[i];
          })
          .catch(() => {});
      };
      row.addEventListener("pointerenter", change);
      row.addEventListener("focus", change);
    });
  }

  document.querySelectorAll("[data-filter]").forEach((button) =>
    button.addEventListener("click", () => {
      const visible = [
        ...document.querySelectorAll(".portfolio-grid .project:not([hidden])"),
      ];
      visible.forEach((card, i) => {
        card.classList.add("entered");
        card.getAnimations().forEach((a) => a.cancel());
        if (canMove())
          card.animate(
            [
              { opacity: 0, transform: "translateY(22px)" },
              { opacity: 1, transform: "translateY(0)" },
            ],
            {
              duration: 550,
              delay: Math.min(i * 45, 200),
              easing: "cubic-bezier(.16,1,.3,1)",
              fill: "backwards",
            },
          );
      });
      scheduleScroll();
    }),
  );

  // Animate the native accordion without losing keyboard or no-JS behavior.
  document.querySelectorAll(".faq details").forEach((details) => {
    const summary = details.querySelector("summary");
    let animation,
      expanded = details.open;
    details.addEventListener("toggle", () => {
      if (!animation) expanded = details.open;
    });
    summary.addEventListener("click", (e) => {
      if (!canMove()) return;
      e.preventDefault();
      const from = details.getBoundingClientRect().height;
      expanded = !expanded;
      if (animation) {
        animation.onfinish = null;
        animation.cancel();
      }
      details.style.height = "";
      details.open = true;
      const to = expanded
        ? details.getBoundingClientRect().height
        : summary.getBoundingClientRect().height + 1;
      animation = details.animate(
        { height: [`${from}px`, `${to}px`] },
        { duration: 360, easing: "cubic-bezier(.16,1,.3,1)" },
      );
      animation.onfinish = () => {
        details.open = expanded;
        details.style.height = "";
        animation = null;
        scheduleScroll();
      };
    });
  });

  // Images or font swaps can change document height after the first frame.
  addEventListener("load", scheduleScroll, { once: true });
  document.fonts?.ready.then(scheduleScroll);
})();

// Continuous type runs only on screen, with hover, keyboard and manual pause.
(() => {
  const preference = matchMedia("(prefers-reduced-motion: reduce)");
  const marquees = [...document.querySelectorAll(".marquee-section")];
  const visible = new Set();
  const sync = () =>
    marquees.forEach((el) =>
      el.classList.toggle(
        "is-running",
        visible.has(el) && !document.hidden && !preference.matches,
      ),
    );
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) visible.add(e.target);
      else visible.delete(e.target);
    });
    sync();
  });
  marquees.forEach((el) => {
    observer.observe(el);
    const button = el.querySelector(".marquee-toggle");
    const label = button.getAttribute("aria-label").replace(/^Pause /, "");
    button.addEventListener("click", () => {
      const paused = el.classList.toggle("user-paused");
      button.setAttribute("aria-pressed", String(paused));
      button.setAttribute(
        "aria-label",
        `${paused ? "Resume" : "Pause"} ${label}`,
      );
      button.innerHTML = `${paused ? '<svg class="playback-icon" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M5 3.5 12 8l-7 4.5Z"/></svg>' : '<svg class="playback-icon" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><rect x="4" y="3" width="2.5" height="10" rx=".5"/><rect x="9.5" y="3" width="2.5" height="10" rx=".5"/></svg>'} <span>${paused ? "Resume motion" : "Pause motion"}</span>`;
    });
  });
  document.addEventListener("visibilitychange", sync);
  preference.addEventListener("change", sync);
  document.querySelectorAll(".closing").forEach((el) =>
    el.addEventListener(
      "pointermove",
      (event) => {
        if (
          preference.matches ||
          !matchMedia("(hover: hover) and (pointer: fine)").matches
        )
          return;
        const r = el.getBoundingClientRect();
        el.style.setProperty("--glow-x", `${event.clientX - r.left}px`);
        el.style.setProperty("--glow-y", `${event.clientY - r.top}px`);
      },
      { passive: true },
    ),
  );
})();

// Compare intrinsic column heights after media and fonts settle.
(() => {
  const groups = [
    ...document.querySelectorAll(
      ".intro, .service-layout, .industries, .faq, .contact-layout, .insights-feature, .team-note, .team-grid",
    ),
  ];
  const desktop = matchMedia("(min-width:1051px)");
  let pending = 0;
  groups.forEach((group) => group.classList.add("adaptive-columns"));
  function measure() {
    pending = 0;
    groups.forEach((group) => {
      const columns = [...group.children];
      columns.forEach((el) => {
        el.classList.remove("sticky-column");
        el.style.removeProperty("--column-top");
      });
      if (!desktop.matches || columns.length !== 2) return;
      const heights = columns.map((el) => el.getBoundingClientRect().height);
      const short = heights[0] <= heights[1] ? 0 : 1;
      if (Math.abs(heights[0] - heights[1]) < 80) return;
      const el = columns[short];
      el.style.setProperty(
        "--column-top",
        `${Math.min(104, innerHeight - heights[short] - 24)}px`,
      );
      el.classList.add("sticky-column");
    });
  }
  const schedule = () => {
    if (!pending) pending = requestAnimationFrame(measure);
  };
  const observer = new ResizeObserver(schedule);
  groups.forEach((group) =>
    [...group.children].forEach((el) => observer.observe(el)),
  );
  addEventListener("resize", schedule, { passive: true });
  addEventListener("load", schedule, { once: true });
  desktop.addEventListener("change", schedule);
  document.fonts?.ready.then(schedule);
  schedule();
})();

(() => {
  const preference = matchMedia("(prefers-reduced-motion: reduce)");
  const menu = document.querySelector(".editorial-menu");
  const preview = menu.querySelector(".menu-art-frame>img");
  let previewTicket = 0,
    previewAnimation;
  menu.querySelectorAll(".menu-links>a").forEach((link, i) => {
    link.style.setProperty("--menu-index", i);
    const update = () => {
      const ticket = ++previewTicket;
      const image = new Image();
      image.src = `assets/${link.dataset.preview}-xl.webp`;
      image
        .decode()
        .then(() => {
          if (ticket !== previewTicket) return;
          preview.src = image.src;
          menu.querySelector(".menu-art-caption>span").textContent =
            `0${i + 1} / 05`;
          menu.querySelector(".menu-art-caption>p").textContent =
            link.dataset.caption;
          if (previewAnimation) previewAnimation.cancel();
          if (!preference.matches)
            previewAnimation = preview.animate(
              [
                { opacity: 0.3, transform: "scale(1.04)" },
                { opacity: 1, transform: "scale(1)" },
              ],
              { duration: 650, easing: "cubic-bezier(.16,1,.3,1)" },
            );
        })
        .catch(() => {});
    };
    link.addEventListener("pointerenter", update);
    link.addEventListener("focus", update);
  });
  const iconGroups = [
    ...document.querySelectorAll(".service-row, .steps article"),
  ];
  const animations = new WeakMap();
  function draw(group) {
    if (preference.matches) return;
    (animations.get(group) || []).forEach((a) => a.cancel());
    const run = [];
    group.querySelectorAll(".editorial-icon>*").forEach((part, i) => {
      if (typeof part.getTotalLength !== "function") return;
      const length = part.getTotalLength();
      run.push(
        part.animate(
          [
            {
              strokeDasharray: `${length} ${length}`,
              strokeDashoffset: length,
              opacity: 0.3,
            },
            {
              strokeDasharray: `${length} ${length}`,
              strokeDashoffset: 0,
              opacity: 1,
            },
          ],
          {
            duration: 850,
            delay: i * 55,
            easing: "cubic-bezier(.16,1,.3,1)",
            fill: "backwards",
          },
        ),
      );
    });
    animations.set(group, run);
    group.classList.remove("icon-activated");
    requestAnimationFrame(() => group.classList.add("icon-activated"));
  }
  const observer = new IntersectionObserver(
    (entries) =>
      entries.forEach((e) => {
        if (e.isIntersecting) {
          draw(e.target);
          observer.unobserve(e.target);
        }
      }),
    { threshold: 0.5 },
  );
  iconGroups.forEach((group) => {
    observer.observe(group);
    group.addEventListener("pointerenter", () => draw(group));
    group.addEventListener("focus", () => draw(group));
  });
})();

// Animate only while the pointer is moving; stop the frame loop at rest.
(() => {
  const fine = matchMedia("(hover: hover) and (pointer: fine)");
  const reduced = matchMedia("(prefers-reduced-motion: reduce)");
  const body = document.body;
  const dot = document.createElement("div"),
    ring = document.createElement("div");
  dot.className = "premium-cursor cursor-dot";
  ring.className = "premium-cursor cursor-ring";
  ring.innerHTML =
    '<span></span><svg class="cursor-arrow" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 18 18 6M6 6h12v12"/></svg>';
  dot.setAttribute("aria-hidden", "true");
  ring.setAttribute("aria-hidden", "true");
  body.append(dot, ring);
  let x = 0,
    y = 0,
    rx = 0,
    ry = 0,
    frame = 0,
    seen = false;
  const enabled = () => fine.matches && !reduced.matches;
  function paint() {
    frame = 0;
    if (!enabled()) return;
    rx += (x - rx) * 0.24;
    ry += (y - ry) * 0.24;
    ring.style.transform = `translate3d(${rx.toFixed(2)}px,${ry.toFixed(2)}px,0)`;
    if (Math.abs(x - rx) + Math.abs(y - ry) > 0.15)
      frame = requestAnimationFrame(paint);
  }
  const setup = () => {
    body.classList.toggle("custom-cursor", enabled());
    if (!enabled()) {
      body.classList.remove(
        "cursor-visible",
        "cursor-link",
        "cursor-media",
        "cursor-input",
      );
      cancelAnimationFrame(frame);
      frame = 0;
      seen = false;
    }
  };
  fine.addEventListener("change", setup);
  reduced.addEventListener("change", setup);
  setup();
  addEventListener(
    "pointermove",
    (e) => {
      if (!enabled() || e.pointerType === "touch") return;
      x = e.clientX;
      y = e.clientY;
      if (!seen) {
        rx = x;
        ry = y;
        seen = true;
      }
      dot.style.transform = `translate3d(${x}px,${y}px,0)`;
      body.classList.add("cursor-visible");
      const target = e.target;
      const media = target.closest(".project-image, .journal-card");
      const input = target.closest("input,textarea,select,[contenteditable]");
      body.classList.toggle("cursor-input", !!input);
      body.classList.toggle("cursor-media", !!media);
      body.classList.toggle(
        "cursor-link",
        !media && !!target.closest("a,button,summary"),
      );
      ring.firstElementChild.textContent = media
        ? media.matches(".journal-card")
          ? "Read more"
          : media.closest(".project").dataset.category === "Animations"
            ? "Play film"
            : media.closest(".project").dataset.category === "VR 360°"
              ? "Explore 360°"
              : "View image"
        : "";
      if (!frame) frame = requestAnimationFrame(paint);
    },
    { passive: true },
  );
  document.documentElement.addEventListener("pointerleave", () => {
    body.classList.remove("cursor-visible");
    seen = false;
    cancelAnimationFrame(frame);
    frame = 0;
  });
  addEventListener("blur", () => {
    body.classList.remove("cursor-visible");
    seen = false;
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Tab") body.classList.remove("cursor-visible");
  });
})();
