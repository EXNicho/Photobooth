// Realtime optional: Echo/Pusher imports removed to keep build minimal.
// If you need realtime updates, install dependencies and restore setup:
//   npm i laravel-echo pusher-js --save-dev
// Then initialize Echo here.

window.subscribePhotos = (gridSelector) => {
  if (!window.Echo) return;
  const grid = document.querySelector(gridSelector);
  if (!grid) return;
  window.Echo.channel('photos')
    .listen('.created', (e) => {
      if (!e?.thumb_url && !e?.public_url) return;
      const div = document.createElement('a');
      div.className = 'card';
      const href = `/p/${e.qr_token}`;
      const img = e.thumb_url || e.public_url;
      div.href = href;
      div.innerHTML = `<img loading="lazy" src="${img}" alt="foto baru" class="img-thumb">`;
      grid.prepend(div);
    });
};

// Minimal lightbox for gallery images
window.enableLightbox = (gridSelector) => {
  const grid = document.querySelector(gridSelector);
  if (!grid) return;
  let overlay;
  const close = () => { if (overlay) { overlay.remove(); overlay=null; } };
  grid.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    // Only intercept when linking to an image thumb
    const img = a.querySelector('img');
    if (!img) return;
    e.preventDefault();
    overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:9999;display:flex;align-items:center;justify-content:center;';
    const big = new Image();
    big.src = img.src;
    big.style.cssText = 'max-width:90vw;max-height:90vh;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.4);';
    overlay.appendChild(big);
    overlay.addEventListener('click', close);
    document.body.appendChild(overlay);
    document.addEventListener('keydown', (ev)=>{ if(ev.key==='Escape') close(); }, { once:true });
  });
};
