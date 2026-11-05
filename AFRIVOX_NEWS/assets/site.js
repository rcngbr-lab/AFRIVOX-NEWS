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
  const scheduleView = (card) => {
    const state = states.get(card); if (!state || state.ratio < 0.5 || document.hidden || state.timer) return;
    state.timer = window.setTimeout(() => { state.timer = null; if (state.ratio >= 0.5 && !document.hidden) { report(card, 'viewable'); observer.unobserve(card); } }, 1000);
  };
  const observer = new IntersectionObserver((entries) => entries.forEach((entry) => {
    const state = states.get(entry.target); state.ratio = entry.intersectionRatio;
    if (state.ratio < 0.5 && state.timer) { window.clearTimeout(state.timer); state.timer = null; }
    scheduleView(entry.target);
  }), { threshold: [0, 0.5, 1] });
  cards.forEach((card) => { states.set(card, { ratio: 0, timer: null }); report(card, 'served'); card.addEventListener('click', () => report(card, 'click'), { passive: true }); observer.observe(card); });
  document.addEventListener('visibilitychange', () => cards.forEach((card) => { const state = states.get(card); if (document.hidden && state.timer) { window.clearTimeout(state.timer); state.timer = null; } else scheduleView(card); }));
})();
