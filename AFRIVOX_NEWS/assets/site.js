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
    btn.textContent = 'LINK YAKOPIWE ✓';
    setTimeout(() => { btn.textContent = old; }, 1700);
  } catch (e) { btn.textContent = 'KOPIYA URL MURI BROWSER'; }
});
