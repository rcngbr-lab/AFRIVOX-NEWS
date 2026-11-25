document.addEventListener('click', async (event) => {
  const menuButton = event.target.closest('.av-menu-toggle');
  if (menuButton) {
    const navigation = document.getElementById('primary-navigation');
    if (!navigation) return;
    const open = navigation.classList.toggle('is-open');
    menuButton.setAttribute('aria-expanded', String(open));
    return;
  }

  const navigationLink = event.target.closest('#primary-navigation a');
  if (navigationLink) {
    const navigation = document.getElementById('primary-navigation');
    const menu = document.querySelector('.av-menu-toggle');
    if (navigation && menu) {
      navigation.classList.remove('is-open');
      menu.setAttribute('aria-expanded', 'false');
    }
  }

  const btn = event.target.closest('[data-copy-url]');
  if (!btn) return;
  try {
    await navigator.clipboard.writeText(window.location.href);
    const old = btn.textContent;
    btn.textContent = document.documentElement.lang === 'en' ? 'LINK COPIED ✓' : 'LINK YAKOPIWE ✓';
    setTimeout(() => { btn.textContent = old; }, 1700);
  } catch (e) { btn.textContent = document.documentElement.lang === 'en' ? 'COPY THE URL FROM YOUR BROWSER' : 'KOPIYA URL MURI BROWSER'; }
});

document.querySelectorAll('[data-current-date]').forEach((element) => {
  const locale = document.documentElement.lang === 'rw' ? 'rw-RW' : document.documentElement.lang === 'fr' ? 'fr-FR' : 'en-GB';
  element.textContent = `▣ ${new Intl.DateTimeFormat(locale, { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }).format(new Date())}`;
});

document.querySelectorAll('[data-av-headline-slider]').forEach((slider) => {
  const hero = slider.closest('.av-hero') || slider;
  const slides = Array.from(slider.querySelectorAll('[data-av-slide]'));
  const dots = Array.from(slider.querySelectorAll('.av-slider-dots [data-av-slide-to]'));
  const thumbs = Array.from(hero.querySelectorAll('[data-av-hero-thumb]'));
  const selectors = Array.from(hero.querySelectorAll('.av-secondary-selector[data-av-slide-to]'));
  const status = slider.querySelector('[data-av-slide-status]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let current = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
  let timer = null;

  if (slides.length < 2) return;

  const announce = () => {
    if (!status) return;
    const heading = slides[current].querySelector('h1, h2');
    const label = document.documentElement.lang === 'en' ? 'Headline' : 'Inkuru';
    status.textContent = `${label} ${current + 1} / ${slides.length}: ${heading ? heading.textContent.trim() : ''}`;
  };

  const show = (requested, shouldAnnounce = false) => {
    current = (requested + slides.length) % slides.length;
    slides.forEach((slide, index) => {
      const active = index === current;
      slide.classList.toggle('is-active', active);
      slide.setAttribute('aria-hidden', String(!active));
      const link = slide.querySelector('.av-lead-image');
      if (link) link.tabIndex = active ? 0 : -1;
    });
    dots.forEach((dot, index) => dot.setAttribute('aria-selected', String(index === current)));
    thumbs.forEach((thumb) => thumb.classList.toggle('is-active', Number(thumb.dataset.avHeroThumb) === current));
    selectors.forEach((selector) => selector.setAttribute('aria-current', String(Number(selector.dataset.avSlideTo) === current)));
    if (shouldAnnounce) announce();
  };

  const stop = () => {
    if (timer !== null) window.clearInterval(timer);
    timer = null;
  };

  const start = () => {
    stop();
    if (reducedMotion.matches || document.hidden) return;
    timer = window.setInterval(() => show(current + 1), 6000);
  };

  hero.addEventListener('click', (event) => {
    const target = event.target.closest('[data-av-slide-prev], [data-av-slide-next], [data-av-slide-to]');
    if (!target) return;
    event.preventDefault();
    if (target.hasAttribute('data-av-slide-prev')) show(current - 1, true);
    else if (target.hasAttribute('data-av-slide-next')) show(current + 1, true);
    else show(Number(target.dataset.avSlideTo), true);
    start();
  });

  slider.addEventListener('keydown', (event) => {
    if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
    event.preventDefault();
    if (event.key === 'ArrowLeft') show(current - 1, true);
    if (event.key === 'ArrowRight') show(current + 1, true);
    if (event.key === 'Home') show(0, true);
    if (event.key === 'End') show(slides.length - 1, true);
    start();
  });

  hero.addEventListener('mouseenter', stop);
  hero.addEventListener('mouseleave', start);
  hero.addEventListener('focusin', stop);
  hero.addEventListener('focusout', (event) => {
    if (!hero.contains(event.relatedTarget)) start();
  });
  reducedMotion.addEventListener('change', start);
  document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());

  show(current);
  start();
});

document.querySelectorAll('[data-av-header-ad]').forEach((carousel) => {
  const slides = Array.from(carousel.querySelectorAll('[data-header-ad-slide]'));
  const dots = Array.from(carousel.querySelectorAll('[data-header-ad-to]'));
  const pauseButton = carousel.querySelector('[data-header-ad-pause]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let current = 0;
  let timer = null;
  let manuallyPaused = false;
  if (slides.length < 1) return;

  const show = (requested, announce = false) => {
    current = (requested + slides.length) % slides.length;
    slides.forEach((slide, index) => {
      const active = index === current;
      slide.classList.toggle('is-active', active);
      slide.setAttribute('aria-hidden', String(!active));
      if (slide.matches('a')) slide.tabIndex = active ? 0 : -1;
      if (active) slide.dispatchEvent(new CustomEvent('av-ad-visible'));
    });
    dots.forEach((dot, index) => dot.setAttribute('aria-selected', String(index === current)));
    if (announce) carousel.setAttribute('aria-label', `${document.documentElement.lang === 'en' ? 'Advertisement' : 'Kwamamaza'} ${current + 1} / ${slides.length}`);
  };
  const stop = () => { if (timer !== null) window.clearInterval(timer); timer = null; };
  const start = () => {
    stop();
    if (manuallyPaused || reducedMotion.matches || document.hidden || slides.length < 2) return;
    timer = window.setInterval(() => show(current + 1), 5500);
  };
  carousel.addEventListener('click', (event) => {
    const previous = event.target.closest('[data-header-ad-prev]');
    const next = event.target.closest('[data-header-ad-next]');
    const dot = event.target.closest('[data-header-ad-to]');
    const pause = event.target.closest('[data-header-ad-pause]');
    if (previous || next || dot) {
      event.preventDefault();
      show(dot ? Number(dot.dataset.headerAdTo) : current + (previous ? -1 : 1), true);
      start();
    }
    if (pause) {
      event.preventDefault();
      manuallyPaused = !manuallyPaused;
      pause.setAttribute('aria-pressed', String(manuallyPaused));
      pause.textContent = manuallyPaused ? '▶' : 'Ⅱ';
      pause.setAttribute('aria-label', manuallyPaused ? 'Resume autoplay' : 'Pause autoplay');
      manuallyPaused ? stop() : start();
    }
  });
  carousel.addEventListener('mouseenter', stop);
  carousel.addEventListener('mouseleave', start);
  carousel.addEventListener('focusin', stop);
  carousel.addEventListener('focusout', (event) => { if (!carousel.contains(event.relatedTarget)) start(); });
  document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
  reducedMotion.addEventListener('change', start);
  show(0);
  start();
});

document.querySelectorAll('[data-av-ad-carousel]').forEach((carousel) => {
  const viewport = carousel.querySelector('[data-av-ad-viewport]');
  const track = carousel.querySelector('[data-av-ad-track]');
  const slides = Array.from(carousel.querySelectorAll('[data-av-ad-slide]'));
  const dots = Array.from(carousel.querySelectorAll('[data-av-ad-page]'));
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let page = 0;
  let timer = null;
  if (!viewport || !track || slides.length < 1) return;

  const updateVideos = () => {
    const visible = new Set([page * 2, page * 2 + 1]);
    slides.forEach((slide, index) => {
      const video = slide.querySelector('[data-ad-video]');
      const button = slide.querySelector('[data-ad-video-toggle]');
      if (!video || visible.has(index)) return;
      video.pause();
      if (button) {
        button.querySelector('span').textContent = '▶';
        button.setAttribute('aria-label', button.dataset.playLabel || 'Play advertisement video');
      }
    });
  };

  const show = (requested, smooth = true) => {
    const pages = Math.max(1, Math.ceil(slides.length / 2));
    page = (requested + pages) % pages;
    slides.forEach((slide, index) => { slide.hidden = index < page * 2 || index >= page * 2 + 2; });
    dots.forEach((dot, index) => dot.setAttribute('aria-selected', String(index === page)));
    updateVideos();
  };
  const stop = () => { if (timer !== null) window.clearInterval(timer); timer = null; };
  const start = () => {
    stop();
    if (reducedMotion.matches || document.hidden || slides.length <= 2) return;
    timer = window.setInterval(() => show(page + 1), 8000);
  };

  carousel.addEventListener('click', (event) => {
    const previous = event.target.closest('[data-av-ad-prev]');
    const next = event.target.closest('[data-av-ad-next]');
    const dot = event.target.closest('[data-av-ad-page]');
    const videoButton = event.target.closest('[data-ad-video-toggle]');
    if (previous || next || dot) {
      event.preventDefault();
      show(dot ? Number(dot.dataset.avAdPage) : page + (previous ? -1 : 1));
      start();
    }
    if (videoButton) {
      event.preventDefault();
      const video = videoButton.closest('[data-av-ad-slide]')?.querySelector('[data-ad-video]');
      if (!video) return;
      if (video.paused) {
        video.play().then(() => {
          videoButton.querySelector('span').textContent = 'Ⅱ';
          videoButton.setAttribute('aria-label', videoButton.dataset.pauseLabel || 'Pause advertisement video');
        }).catch(() => {});
      } else {
        video.pause();
        videoButton.querySelector('span').textContent = '▶';
        videoButton.setAttribute('aria-label', videoButton.dataset.playLabel || 'Play advertisement video');
      }
    }
  });
  carousel.addEventListener('mouseenter', stop);
  carousel.addEventListener('mouseleave', start);
  carousel.addEventListener('focusin', stop);
  carousel.addEventListener('focusout', (event) => { if (!carousel.contains(event.relatedTarget)) start(); });
  document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
  reducedMotion.addEventListener('change', start);
  show(0, false);
  start();
});

(() => {
  const cards = Array.from(document.querySelectorAll('[data-ad-placement][data-ad-event-url]'));
  if (!cards.length) return;
  const pageToken = globalThis.crypto?.randomUUID?.() || `${Date.now()}-${Math.random().toString(36).slice(2)}`;
  const sent = new WeakMap();
  const report = (card, eventType) => {
    const events = sent.get(card) || new Set();
    if (events.has(eventType)) return;
    events.add(eventType); sent.set(card, events);
    const body = new URLSearchParams({ placement_id: card.dataset.adPlacement, event_type: eventType, token: `${pageToken}-${card.dataset.adPlacement}` });
    if (navigator.sendBeacon) navigator.sendBeacon(card.dataset.adEventUrl, new Blob([body.toString()], { type: 'application/x-www-form-urlencoded;charset=UTF-8' }));
    else fetch(card.dataset.adEventUrl, { method: 'POST', body, credentials: 'same-origin', keepalive: true }).catch(() => {});
  };
  const states = new Map();
  const isEligible = (card) => card.getAttribute('aria-hidden') !== 'true';
  const scheduleView = (card) => {
    const state = states.get(card); if (!state || state.ratio < 0.5 || !isEligible(card) || document.hidden || state.timer) return;
    state.timer = window.setTimeout(() => { state.timer = null; if (state.ratio >= 0.5 && isEligible(card) && !document.hidden) { report(card, 'viewable'); observer.unobserve(card); } }, 1000);
  };
  const observer = new IntersectionObserver((entries) => entries.forEach((entry) => {
    const state = states.get(entry.target); state.ratio = entry.intersectionRatio;
    if (state.ratio > 0 && isEligible(entry.target)) report(entry.target, 'served');
    if (state.ratio < 0.5 && state.timer) { window.clearTimeout(state.timer); state.timer = null; }
    scheduleView(entry.target);
  }), { threshold: [0, 0.5, 1] });
  cards.forEach((card) => { states.set(card, { ratio: 0, timer: null }); card.addEventListener('click', (event) => { if (event.target.closest('[data-ad-destination]')) report(card, 'click'); }, { passive: true }); card.addEventListener('av-ad-visible', () => { const state=states.get(card); if(state.ratio>0)report(card,'served'); scheduleView(card); }); observer.observe(card); });
  document.addEventListener('visibilitychange', () => cards.forEach((card) => { const state = states.get(card); if (document.hidden && state.timer) { window.clearTimeout(state.timer); state.timer = null; } else scheduleView(card); }));
})();

// AFRIVOX NEWS tools calculate locally. No health input is submitted or persisted.
document.querySelectorAll('[data-bmi-tool]').forEach((tool) => {
  tool.querySelector('form').addEventListener('submit', (event) => {
    event.preventDefault();
    const data = new FormData(event.currentTarget);
    const height = Number(data.get('height')) / 100;
    const weight = Number(data.get('weight'));
    if (!(height >= 0.8 && height <= 2.5 && weight >= 20 && weight <= 400)) return;
    const bmi = weight / (height * height);
    const lang = tool.dataset.lang;
    const band = bmi < 18.5 ? ['Below the usual adult range','Munsi y’igipimo gisanzwe cy’abakuru','Sous la plage habituelle adulte'] : bmi < 25 ? ['Within the usual adult range','Mu gipimo gisanzwe cy’abakuru','Dans la plage habituelle adulte'] : bmi < 30 ? ['Above the usual adult range','Hejuru y’igipimo gisanzwe cy’abakuru','Au-dessus de la plage habituelle adulte'] : ['Well above the usual adult range','Hejuru cyane y’igipimo gisanzwe cy’abakuru','Nettement au-dessus de la plage habituelle adulte'];
    const index = lang === 'rw' ? 1 : (lang === 'fr' ? 2 : 0);
    tool.querySelector('[data-bmi-value]').textContent = bmi.toFixed(1);
    tool.querySelector('[data-bmi-message]').textContent = `${band[index]}. BMI is a screening measure, not a diagnosis.`;
    tool.querySelector('.uz-tool-result').hidden = false;
  });
});

document.querySelectorAll('[data-pregnancy-tool]').forEach((tool) => {
  tool.querySelector('form').addEventListener('submit', (event) => {
    event.preventDefault();
    const raw = new FormData(event.currentTarget).get('lmp');
    const lmp = new Date(`${raw}T12:00:00`);
    if (Number.isNaN(lmp.getTime())) return;
    const now = new Date();
    const due = new Date(lmp); due.setDate(due.getDate() + 280);
    const elapsed = Math.floor((now - lmp) / 86400000);
    const locale = tool.dataset.lang === 'rw' ? 'rw-RW' : tool.dataset.lang === 'fr' ? 'fr-FR' : 'en-GB';
    tool.querySelector('[data-due-date]').textContent = due.toLocaleDateString(locale, { dateStyle: 'long' });
    tool.querySelector('[data-gestational-age]').textContent = elapsed >= 0 && elapsed <= 294 ? `${Math.floor(elapsed / 7)} weeks, ${elapsed % 7} days` : 'Outside the usual calculation window';
    tool.querySelector('.uz-tool-result').hidden = false;
  });
});

document.querySelectorAll('[data-growth-tool]').forEach((tool) => {
  let standards = null;
  const measurementDate = tool.querySelector('input[name="measured"]');
  if (measurementDate) measurementDate.value = new Date().toISOString().slice(0, 10);
  fetch(tool.dataset.source, { credentials: 'same-origin' }).then((response) => response.ok ? response.json() : Promise.reject()).then((data) => { standards = data; }).catch(() => {});
  tool.querySelector('form').addEventListener('submit', (event) => {
    event.preventDefault();
    if (!standards) return;
    const data = new FormData(event.currentTarget);
    const dob = new Date(`${data.get('dob')}T12:00:00`), measured = new Date(`${data.get('measured')}T12:00:00`), weight = Number(data.get('weight'));
    if (Number.isNaN(dob.getTime()) || Number.isNaN(measured.getTime()) || measured < dob || !(weight >= 1 && weight <= 50)) return;
    const months = Math.round((measured - dob) / 2629800000);
    if (months < 0 || months > 60) { tool.querySelector('[data-growth-message]').textContent = 'This standard covers birth through 60 months only.'; tool.querySelector('[data-growth-z]').textContent = '—'; tool.querySelector('.uz-tool-result').hidden = false; return; }
    const row = standards[data.get('sex')].find((item) => Number(item.Month) === months);
    if (!row) return;
    const L = Number(row.L), M = Number(row.M), S = Number(row.S);
    const z = L === 0 ? Math.log(weight / M) / S : (Math.pow(weight / M, L) - 1) / (L * S);
    const bounded = Math.max(-3, Math.min(3, z));
    tool.querySelector('[data-growth-z]').textContent = `z = ${z.toFixed(2)} (${months} months)`;
    tool.querySelector('[data-growth-message]').textContent = Math.abs(z) <= 2 ? 'Within the central WHO reference band. Trends over time and clinical context matter.' : 'Outside the central WHO reference band. Please discuss the measurement with a qualified child-health professional.';
    tool.querySelector('[data-growth-marker]').style.left = `${((bounded + 3) / 6) * 100}%`;
    tool.querySelector('.uz-tool-result').hidden = false;
  });
});

// One health-resource index powers both PHP hosting and the static Pages build.
document.querySelectorAll('[data-health-search]').forEach((search) => {
  const form = search.querySelector('.uz-client-search');
  const input = search.querySelector('[data-health-search-input]');
  const items = Array.from(search.querySelectorAll('[data-health-search-item]'));
  const count = search.querySelector('[data-health-search-count]');
  const empty = search.querySelector('[data-health-search-empty]');
  if (!form || !input) return;

  const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase().trim();
  const filter = () => {
    const terms = normalize(input.value).split(/\s+/).filter(Boolean);
    let visible = 0;
    items.forEach((item) => {
      const match = terms.every((term) => normalize(item.dataset.searchText).includes(term));
      item.hidden = !match;
      if (match) visible += 1;
    });
    if (count) count.textContent = String(visible);
    if (empty) empty.hidden = visible !== 0;
    const url = new URL(window.location.href);
    input.value.trim() ? url.searchParams.set('q', input.value.trim()) : url.searchParams.delete('q');
    window.history.replaceState({}, '', url);
  };
  form.addEventListener('submit', (event) => { event.preventDefault(); filter(); });
  input.addEventListener('input', filter);
  filter();
});
