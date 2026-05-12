// ---------------------------------------------------------
// Laravel Vite Entry File — BleakHospital
// ---------------------------------------------------------
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// ── Scroll Reveal ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-visible');
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal-hidden').forEach(el => observer.observe(el));

    // ── Active nav-link highlighting ─────────────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('[data-nav-href]').forEach(link => {
        const href = link.getAttribute('data-nav-href');
        if (href && (currentPath === href || currentPath.startsWith(href + '/'))) {
            link.classList.add('active');
        }
    });

    // ── Auto-dismiss flash alerts ────────────────────────
    document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
        const delay = parseInt(el.getAttribute('data-auto-dismiss')) || 4000;
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(() => el.remove(), 400);
        }, delay);
    });

    // ── Table row click navigation ───────────────────────
    document.querySelectorAll('tr[data-href]').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', () => {
            window.location.href = row.getAttribute('data-href');
        });
    });

    // ── Sticky top-bar scroll shadow ─────────────────────
    const topbar = document.getElementById('topbar');
    if (topbar) {
        window.addEventListener('scroll', () => {
            topbar.classList.toggle('shadow-md', window.scrollY > 4);
        }, { passive: true });
    }

    // ── Smooth progress counter for stat cards ───────────
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseInt(el.getAttribute('data-count')) || 0;
        if (target === 0) return;
        let current = 0;
        const step = Math.ceil(target / 30);
        const interval = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(interval);
        }, 30);
    });
});
