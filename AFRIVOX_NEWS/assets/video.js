'use strict';
document.addEventListener('click', function(event) {
  const button=event.target.closest('[data-video-play]');
  if (!button) return;
  const host=button.closest('[data-video]');
  const id=host && host.getAttribute('data-video');
  if (!id || !/^[a-zA-Z0-9_-]{11}$/.test(id)) return;
  const frame=document.createElement('iframe');
  frame.src='https://www.youtube-nocookie.com/embed/'+encodeURIComponent(id)+'?autoplay=1&rel=0';
  frame.title=button.getAttribute('aria-label')||'AFRIVOX NEWS video';
  frame.setAttribute('allow','accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; web-share');
  frame.setAttribute('referrerpolicy','strict-origin-when-cross-origin');
  frame.setAttribute('allowfullscreen','');
  host.replaceChildren(frame);
});
