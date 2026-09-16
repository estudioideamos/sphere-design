(() => {
  'use strict';
  const motionPreference = matchMedia('(prefers-reduced-motion: reduce)');
  const finePointer = matchMedia('(hover: hover) and (pointer: fine)');
  const canMove = () => !motionPreference.matches;
  const header = document.querySelector('.site-header');

  // Split only authored line breaks, preserving each heading's semantic text.
  document.querySelectorAll('h1, .intro h2, .section-heading h2, .why>h2, .faq h2, .closing h2, .cinema h2, .insights-feature h2').forEach(heading => {
    const lines = heading.innerHTML.split(/<br\s*\/?\s*>/i);
    heading.innerHTML = lines.map((line, i) => `<span class="motion-line" style="--line:${i}"><span>${line}</span></span>`).join('');
    heading.dataset.title = '';
  });

  const revealTargets = '.project, .steps article, .why-grid article, .team-grid article, .intro-bottom, .prose, .team-note, .contact-info, #contact-form, .service-visual, .service-row, .industry-list>a, .faq details, .closing>.circle-link, .footer-signature, .section-heading>.eyebrow, .intro>.eyebrow';
  document.querySelectorAll(revealTargets).forEach(el => el.dataset.enter = '');
  document.querySelectorAll('.steps, .why-grid, .service-list, .team-grid').forEach(group => {
    [...group.children].forEach((el, i) => el.style.setProperty('--stagger', `${Math.min(i * 65, 260)}ms`));
  });
  const observed = [...document.querySelectorAll('[data-title], [data-enter]')];
  if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver(entries => {
      entries.forEach(({ target, isIntersecting }) => {
        if (isIntersecting) {
          target.classList.add('entered');
          revealObserver.unobserve(target);
        }
      });
    }, { threshold: .06, rootMargin: '0px 0px -20px 0px' });
    observed.forEach(el => revealObserver.observe(el));
    document.body.classList.add('motion-ready');
  } else observed.forEach(el => el.classList.add('entered'));

  const progress = document.createElement('div');
  progress.className = 'reading-progress';
  progress.setAttribute('aria-hidden', 'true');
  document.body.append(progress);

  let chapterLinks = [];
  if (document.body.classList.contains('home')) {
    const chapters = [['intro', '01 — Perspective'], ['selected-work', '02 — Selected work'], ['expertise', '03 — Expertise'], ['process', '04 — Process'], ['conversation', '05 — Let’s talk']];
    const rail = document.createElement('nav');
    rail.className = 'chapter-nav';
    rail.setAttribute('aria-label', 'Page chapters');
    rail.innerHTML = chapters.map(([id, label]) => `<a href="#${id}" aria-label="${label}"><span>${label}</span></a>`).join('');
    document.body.append(rail);
    chapterLinks = [...rail.children].map(a => ({ a, section: document.querySelector(a.hash) }));
  }

  const location = document.querySelector('.location');
  let clockTimer;
  if (location) {
    const time = document.createElement('time');
    time.setAttribute('aria-label', 'Current time in Miami');
    location.replaceChildren();
    const dot = document.createElement('i');
    dot.setAttribute('aria-hidden', 'true');
    location.append(dot, document.createTextNode('MIAMI, FL · '), time);
    const formatter = new Intl.DateTimeFormat('en-US', { timeZone: 'America/New_York', hour: '2-digit', minute: '2-digit', hour12: false });
    const tick = () => { const now = new Date(); time.dateTime = now.toISOString(); time.textContent = formatter.format(now); };
    tick(); clockTimer = setInterval(tick, 60000);
  }

  // Keep scroll work to visible images; schedule only when input changes.
  const activeImages = new Set();
  const imageObserver = new IntersectionObserver(entries => entries.forEach(e => {
    if (e.isIntersecting) activeImages.add(e.target); else activeImages.delete(e.target);
  }), { rootMargin: '80px' });
  document.querySelectorAll('.project-image').forEach((el, i) => {
    imageObserver.observe(el);
    const index = document.createElement('span');
    index.className = 'project-index'; index.setAttribute('aria-hidden', 'true');
    index.textContent = `${String(i + 1).padStart(2, '0')} / SPHERE`;
    el.append(index);
    el.addEventListener('pointermove', e => {
      if (!finePointer.matches || !canMove()) return;
      const rect = el.getBoundingClientRect();
      el.style.setProperty('--pointer-x', `${e.clientX - rect.left}px`);
      el.style.setProperty('--pointer-y', `${e.clientY - rect.top}px`);
    }, { passive: true });
  });
  const heroMedia = document.querySelector('.hero-media');
  const cinema = document.querySelector('.cinema-frame');
  const orbit = document.querySelector('.orbit');
  const signature = document.querySelector('.footer-signature');
  let frame = 0;
  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
  function updateScroll() {
    frame = 0;
    const y = scrollY, vh = innerHeight;
    const total = document.documentElement.scrollHeight - vh;
    header.classList.toggle('scrolled', y > 70);
    progress.style.transform = `scaleX(${total > 0 ? clamp(y / total, 0, 1) : 0})`;
    if (chapterLinks.length) {
      document.querySelector('.chapter-nav').classList.toggle('shown', y > vh * .65);
      let active = chapterLinks[0];
      chapterLinks.forEach(item => { if (item.section.getBoundingClientRect().top < vh * .48) active = item; });
      chapterLinks.forEach(item => {
        if (item === active) item.a.setAttribute('aria-current', 'location'); else item.a.removeAttribute('aria-current');
      });
    }
    if (!canMove()) return;
    if (heroMedia && y < vh * 1.4) heroMedia.style.transform = `translateY(${Math.min(y * .16, 160)}px)`;
    if (innerWidth > 700) activeImages.forEach(el => {
      const r = el.getBoundingClientRect();
      const offset = clamp(((vh / 2 - r.top - r.height / 2) / vh) * 36, -22, 22);
      el.style.setProperty('--drift', `${offset.toFixed(2)}px`);
    });
    if (cinema) {
      const r = cinema.getBoundingClientRect();
      if (r.bottom > 0 && r.top < vh) {
        const position = clamp((vh - r.top) / (vh + r.height), 0, 1);
        cinema.style.setProperty('--cinema-inset', `${(4 * (1 - Math.min(position * 2, 1))).toFixed(2)}%`);
        cinema.style.setProperty('--cinema-scale', `${(1.12 - position * .12).toFixed(3)}`);
      }
    }
    if (orbit) { const r = orbit.parentElement.getBoundingClientRect(); if (r.top < vh && r.bottom > 0) orbit.style.setProperty('--orbit-turn', `${((vh - r.top) * .035).toFixed(1)}deg`); }
    if (signature && innerWidth > 700) { const r = signature.getBoundingClientRect(); if (r.top < vh) signature.style.setProperty('--signature-shift', `${clamp((vh - r.top) * .012 - 10, -10, 8)}px`); }
  }
  const scheduleScroll = () => { if (!frame) frame = requestAnimationFrame(updateScroll); };
  addEventListener('scroll', scheduleScroll, { passive: true });
  addEventListener('resize', scheduleScroll, { passive: true });
  addEventListener('pageshow', scheduleScroll);
  motionPreference.addEventListener('change', () => {
    if (!canMove()) {
      observed.forEach(el => el.classList.add('entered'));
      document.getAnimations().forEach(a => { if (a.effect?.getComputedTiming().endTime !== Infinity) { try { a.finish(); } catch {} } });
    }
    scheduleScroll();
  });
  scheduleScroll();

  document.querySelectorAll('.circle-link>span').forEach(el => {
    el.addEventListener('pointermove', e => {
      if (!finePointer.matches || !canMove()) return;
      const r = el.getBoundingClientRect();
      el.style.translate = `${clamp((e.clientX - r.left - r.width / 2) * .22, -8, 8)}px ${clamp((e.clientY - r.top - r.height / 2) * .22, -8, 8)}px`;
    });
    el.addEventListener('pointerleave', () => el.style.translate = '0px 0px');
  });

  // Editorial crossfades reuse media already supplied for the portfolio.
  const serviceVisual = document.querySelector('.service-visual');
  if (serviceVisual) {
    const preview = new Image();
    preview.className = 'service-preview'; preview.alt = ''; preview.setAttribute('aria-hidden', 'true');
    serviceVisual.append(preview);
    const serviceImages = ['hero-poster.jpg', 'interior1.webp', 'exterior2.webp', 'vr-sm.webp', 'floorplan.webp', 'product.webp', 'exterior1.webp'];
    const serviceLabels = ['ARCHITECTURE IN MOTION.', 'PRECISION IN EVERY DETAIL.', 'ARCHITECTURE. IN CONTEXT.', 'A NEW POINT OF VIEW.', 'CLARITY AT EVERY LEVEL.', 'MATERIAL. FORM. DETAIL.', 'THE FOUNDATION OF THE VISION.'];
    let ticket = 0;
    document.querySelectorAll('.service-row').forEach((row, i) => {
      const change = () => {
        if (!finePointer.matches) return;
        const stamp = ++ticket;
        const img = new Image(); img.src = `assets/${serviceImages[i]}`;
        img.decode().then(() => {
          if (stamp !== ticket) return;
          preview.src = img.src; preview.classList.add('active');
          if (canMove()) preview.animate([{ opacity: .25, transform: 'scale(1.035)' }, { opacity: 1, transform: 'scale(1)' }], { duration: 600, easing: 'cubic-bezier(.16,1,.3,1)' });
          serviceVisual.querySelector('span').textContent = serviceLabels[i];
        }).catch(() => {});
      };
      row.addEventListener('pointerenter', change); row.addEventListener('focus', change);
    });
  }

  document.querySelectorAll('[data-filter]').forEach(button => button.addEventListener('click', () => {
    const visible = [...document.querySelectorAll('.portfolio-grid .project:not([hidden])')];
    visible.forEach((card, i) => {
      card.classList.add('entered');
      card.getAnimations().forEach(a => a.cancel());
      if (canMove()) card.animate([{ opacity: 0, transform: 'translateY(22px)' }, { opacity: 1, transform: 'translateY(0)' }], { duration: 550, delay: Math.min(i * 45, 200), easing: 'cubic-bezier(.16,1,.3,1)', fill: 'backwards' });
    });
    scheduleScroll();
  }));

  // Animate the native accordion without losing keyboard or no-JS behavior.
  document.querySelectorAll('.faq details').forEach(details => {
    const summary = details.querySelector('summary');
    let animation, expanded = details.open;
    details.addEventListener('toggle', () => { if (!animation) expanded = details.open; });
    summary.addEventListener('click', e => {
      if (!canMove()) return;
      e.preventDefault();
      const from = details.getBoundingClientRect().height;
      expanded = !expanded;
      if (animation) { animation.onfinish = null; animation.cancel(); }
      details.style.height = '';
      details.open = true;
      const to = expanded ? details.getBoundingClientRect().height : summary.getBoundingClientRect().height + 1;
      animation = details.animate({ height: [`${from}px`, `${to}px`] }, { duration: 360, easing: 'cubic-bezier(.16,1,.3,1)' });
      animation.onfinish = () => { details.open = expanded; details.style.height = ''; animation = null; scheduleScroll(); };
    });
  });

  // Images or font swaps can change document height after the first frame.
  addEventListener('load', scheduleScroll, { once: true });
  document.fonts?.ready.then(scheduleScroll);
})();

// Continuous type runs only on screen, with hover, keyboard and manual pause.
(() => {
  const preference = matchMedia('(prefers-reduced-motion: reduce)');
  const marquees = [...document.querySelectorAll('.marquee-section')];
  const visible = new Set();
  const sync = () => marquees.forEach(el => el.classList.toggle('is-running', visible.has(el) && !document.hidden && !preference.matches));
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) visible.add(e.target); else visible.delete(e.target); });
    sync();
  });
  marquees.forEach(el => {
    observer.observe(el);
    const button = el.querySelector('.marquee-toggle');
    const label = button.getAttribute('aria-label').replace(/^Pause /, '');
    button.addEventListener('click', () => {
      const paused = el.classList.toggle('user-paused');
      button.setAttribute('aria-pressed', String(paused));
      button.setAttribute('aria-label', `${paused ? 'Resume' : 'Pause'} ${label}`);
      button.innerHTML = `${paused ? '▶' : 'Ⅱ'} <span>${paused ? 'Resume motion' : 'Pause motion'}</span>`;
    });
  });
  document.addEventListener('visibilitychange', sync);
  preference.addEventListener('change', sync);
  document.querySelectorAll('.closing').forEach(el => el.addEventListener('pointermove', event => {
    if (preference.matches || !matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    const r = el.getBoundingClientRect();
    el.style.setProperty('--glow-x', `${event.clientX-r.left}px`);
    el.style.setProperty('--glow-y', `${event.clientY-r.top}px`);
  }, { passive: true }));
})();
