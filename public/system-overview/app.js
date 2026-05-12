document.addEventListener('DOMContentLoaded', () => {
  // ─── Tab Navigation ───
  const btns = document.querySelectorAll('.tab-btn');
  const panels = document.querySelectorAll('.tab-panel');

  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      btns.forEach(b => b.classList.remove('active'));
      panels.forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById(btn.dataset.tab).classList.add('active');
    });
  });

  // ─── Flow step animation on scroll ───
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        entry.target.style.animationDelay = `${i * 0.1}s`;
        entry.target.classList.add('visible');
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.flow-step, .card, .stat-card, .rec-item, .decision-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(12px)';
    el.style.transition = 'opacity .5s ease, transform .5s ease';
    observer.observe(el);
  });

  // Intersection callback to reveal
  const reveal = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, { threshold: 0.05 });

  document.querySelectorAll('.flow-step, .card, .stat-card, .rec-item, .decision-item').forEach(el => reveal.observe(el));

  // ─── Animated counters ───
  document.querySelectorAll('.stat-value[data-count]').forEach(el => {
    const target = parseFloat(el.dataset.count);
    const isFloat = target % 1 !== 0;
    let current = 0;
    const step = target / 40;
    const interval = setInterval(() => {
      current += step;
      if (current >= target) { current = target; clearInterval(interval); }
      el.textContent = isFloat ? current.toFixed(1) : Math.round(current);
      if (el.dataset.suffix) el.textContent += el.dataset.suffix;
    }, 30);
  });

  // ─── Search / filter for file tree ───
  const searchInput = document.getElementById('fileSearch');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('.file-tree span').forEach(span => {
        const match = span.textContent.toLowerCase().includes(q);
        span.style.display = (!q || match) ? '' : 'none';
      });
    });
  }
});
