<script>
  (function () {
    const btn = document.getElementById('js-menu-btn');
    const overlay = document.getElementById('js-overlay');
    const sidebar = document.querySelector('.sidebar');
    if (!btn || !overlay || !sidebar) return;

    const open = () => {
      sidebar.classList.add('is-open');
      overlay.classList.add('open');
      overlay.removeAttribute('aria-hidden');
      btn.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    };
    const close = () => {
      sidebar.classList.remove('is-open');
      overlay.classList.remove('open');
      overlay.setAttribute('aria-hidden', 'true');
      btn.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    };

    btn.addEventListener('click', open);
    overlay.addEventListener('click', close);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
    sidebar.querySelectorAll('.nav-item').forEach((link) => {
      link.addEventListener('click', () => { if (window.innerWidth <= 800) close(); });
    });
  })();
</script>
