import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/* =========================================================
   Micro-interacciones (tabs + repeaters + preview + dirty)
   ========================================================= */
document.addEventListener('DOMContentLoaded', () => {
  // Tabs
  const tabsWrap = document.querySelector('[data-tabs]');
  if (tabsWrap) {
    const tabBtns = [...tabsWrap.querySelectorAll('[data-tab]')];
    const panels  = [...document.querySelectorAll('[data-panel]')];

    const setTab = (key) => {
      tabBtns.forEach(b => b.classList.toggle('chip-accent', b.dataset.tab === key));
      panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== key));
    };

    tabBtns.forEach(b => b.addEventListener('click', () => setTab(b.dataset.tab)));
    setTab(tabBtns[0]?.dataset.tab || 'basic');
  }

  // Repeaters
  document.querySelectorAll('[data-repeater]').forEach((wrap) => {
    const btnAdd = wrap.querySelector('[data-add]');
    const list   = wrap.querySelector('[data-list]');
    if (!btnAdd || !list) return;

    const addRow = (value = '') => {
      const row = document.createElement('div');
      row.className = 'flex gap-2';
      row.innerHTML = `
        <input class="input w-full" name="${list.querySelector('input')?.name || 'items[]'}" value="${value}">
        <button type="button" class="chip" data-remove>×</button>
      `;
      list.appendChild(row);
      row.querySelector('[data-remove]').addEventListener('click', () => row.remove());
    };

    btnAdd.addEventListener('click', () => addRow());
    list.querySelectorAll('[data-remove]').forEach(btn =>
      btn.addEventListener('click', () => btn.closest('div')?.remove())
    );
  });

  // Preview portada
  const coverInput  = document.querySelector('[data-cover-input]');
  const coverImg    = document.querySelector('[data-cover-preview]');
  const coverEmpty  = document.querySelector('[data-cover-empty]');
  if (coverInput && coverImg && coverEmpty) {
    coverInput.addEventListener('change', () => {
      const f = coverInput.files?.[0];
      if (!f) return;
      const url = URL.createObjectURL(f);
      coverImg.src = url;
      coverImg.classList.remove('hidden');
      coverEmpty.classList.add('hidden');
    });
  }

  // Cambios sin guardar
  const form = document.querySelector('[data-editor-form]');
  const unsaved = document.querySelector('[data-unsaved]');
  if (form && unsaved) {
    let dirty = false;
    const mark = () => { dirty = true; unsaved.classList.remove('hidden'); };
    form.querySelectorAll('input, textarea, select').forEach(el => {
      el.addEventListener('input', mark, { passive: true });
      el.addEventListener('change', mark, { passive: true });
    });
    form.addEventListener('submit', () => { dirty = false; });
    window.addEventListener('beforeunload', (e) => {
      if (!dirty) return;
      e.preventDefault();
      e.returnValue = '';
    });
  }

  // ✅ Preloader
  initPreloader();
});

/* =========================================================
   Preloader (solo 1 vez por sesión/pestaña)
   ========================================================= */
function initPreloader() {
  // ✅ si ya está marcado skip-preloader, elimina el nodo y ya
  if (document.documentElement.classList.contains('skip-preloader')) {
    const el0 = document.getElementById('app-preloader');
    if (el0) el0.remove();
    return;
  }

  const el = document.getElementById('app-preloader');
  if (!el) return;

  // ✅ marca como mostrado (para que no salga en navegación)
  try { sessionStorage.setItem('preloader_shown', '1'); } catch (e) {}

  const fill = el.querySelector('.preloader-bar-fill');
  const bar  = el.querySelector('.preloader-bar');
  const pct  = el.querySelector('[data-preloader-percent]');
  const waveWrap = el.querySelector('[data-wave]');
  const particlesWrap = el.querySelector('.preloader-particles');

  const MIN_SHOW_MS = parseInt(el.dataset.min || '1600', 10);
  const FADE_MS     = parseInt(el.dataset.fade || '520', 10);
  el.style.setProperty('--fade-ms', `${FADE_MS}ms`);

  // Bloquear scroll
  document.body.classList.add('preloading');

  // ✅ Construir wave una sola vez
  if (waveWrap && !waveWrap.dataset.built) {
    waveWrap.dataset.built = '1';

    const text = (waveWrap.textContent || 'Bienvenido').trim();
    waveWrap.textContent = '';

    const center = (text.length - 1) / 2;

    [...text].forEach((ch, i) => {
      const s = document.createElement('span');
      s.textContent = (ch === ' ') ? '\u00A0' : ch;

      // delay desde el centro hacia afuera (onda “hacia adentro” elegante)
      const dist = Math.abs(i - center);
      const delay = Math.round(dist * 42); // 42ms por paso (suave)
      s.style.setProperty('--d', `${delay}ms`);

      waveWrap.appendChild(s);
    });
  }

  // Partículas
  if (particlesWrap && !particlesWrap.hasChildNodes()) {
    const n = 18;
    for (let i = 0; i < n; i++) {
      const p = document.createElement('span');
      p.className = 'preloader-particle';
      p.style.setProperty('--x', String(rand(6, 94)));
      p.style.setProperty('--y', String(rand(8, 92)));
      p.style.setProperty('--s', String(rand(10, 18)));
      p.style.setProperty('--d', String(rand(2.6, 5.2)));
      particlesWrap.appendChild(p);
    }
  }

  let progress = 0;
  let rafId = null;
  let done = false;
  const startedAt = performance.now();

  const setProgress = (p) => {
    progress = clamp(p, 0, 1);
    if (fill) fill.style.transform = `scaleX(${progress})`;
    const nowPct = Math.round(progress * 100);
    if (bar) bar.setAttribute('aria-valuenow', String(nowPct));
    if (pct) pct.textContent = `${nowPct}%`;
  };

  // Progreso fake hasta 88%
  const tick = (t) => {
    const elapsed = (t - startedAt) / 1000;
    const target = Math.min(0.88, 1 - Math.exp(-elapsed * 1.25));
    if (!done) setProgress(Math.max(progress, target));
    rafId = requestAnimationFrame(tick);
  };
  rafId = requestAnimationFrame(tick);

  const finish = () => {
    if (done) return;
    done = true;

    setProgress(1);

    const elapsedMs = performance.now() - startedAt;
    const waitMore = Math.max(0, MIN_SHOW_MS - elapsedMs);

    window.setTimeout(() => {
      el.classList.add('is-hiding');

      window.setTimeout(() => {
        document.body.classList.remove('preloading');
        el.remove();
        if (rafId) cancelAnimationFrame(rafId);
      }, FADE_MS + 60);
    }, waitMore);
  };

  const whenReady = async () => {
    try {
      if (document.fonts && document.fonts.ready) {
        await Promise.race([
          document.fonts.ready,
          new Promise(res => setTimeout(res, 900))
        ]);
      }
    } catch (_) {}
    finish();
  };

  // hard fallback
  window.setTimeout(() => finish(), 9000);

  if (document.readyState === 'complete') whenReady();
  else window.addEventListener('load', whenReady, { once: true });
}

function rand(min, max){ return (Math.random() * (max - min) + min).toFixed(2); }
function clamp(v, a, b){ return Math.max(a, Math.min(b, v)); }
