(() => {
  'use strict';

  function initFeaturedCarousel() {
    const carousels = document.querySelectorAll('[data-carousel]');

    carousels.forEach((carousel) => {
      const track = carousel.querySelector('[data-carousel-track]');
      const prevBtn = carousel.querySelector('[data-carousel-prev]');
      const nextBtn = carousel.querySelector('[data-carousel-next]');

      if (!track) return;

      const getStep = () => {
        const firstCard = track.querySelector('.fcItem');
        if (!firstCard) return Math.max(240, track.clientWidth);

        const styles = window.getComputedStyle(track);
        const gap = parseFloat(styles.columnGap || styles.gap) || 0;
        return firstCard.getBoundingClientRect().width + gap;
      };

      const syncButtons = () => {
        const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth - 2);
        if (prevBtn) prevBtn.disabled = track.scrollLeft <= 2;
        if (nextBtn) nextBtn.disabled = track.scrollLeft >= maxScroll;
      };

      const scrollOneStep = (direction) => {
        const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
        const target = Math.min(maxScroll, Math.max(0, track.scrollLeft + direction * getStep()));
        track.scrollTo({ left: target, behavior: 'smooth' });
      };

      prevBtn?.addEventListener('click', () => {
        scrollOneStep(-1);
      });

      nextBtn?.addEventListener('click', () => {
        scrollOneStep(1);
      });

      track.addEventListener('scroll', syncButtons, { passive: true });
      window.addEventListener('resize', syncButtons, { passive: true });
      syncButtons();
    });
  }

  function isUserLoggedIn() {
    if (typeof window.__IS_LOGGED_IN__ === 'boolean') {
      return window.__IS_LOGGED_IN__;
    }

    const catalog = document.querySelector('.storeCatalog[data-logged-in]');
    if (catalog) {
      return catalog.getAttribute('data-logged-in') === '1';
    }

    const authNav = document.querySelector('[data-auth-nav]');
    return authNav?.getAttribute('data-logged-in') === '1';
  }

  function addItemToLocalCart(productId, productName) {
    const storageKey = 'steam_project_cart';
    let cart = [];

    try {
      const raw = window.localStorage.getItem(storageKey);
      const parsed = raw ? JSON.parse(raw) : [];
      if (Array.isArray(parsed)) {
        cart = parsed;
      }
    } catch (_) {
      cart = [];
    }

    const existing = cart.find((item) => String(item.id) === String(productId));
    if (existing) {
      existing.qty = (Number(existing.qty) || 1) + 1;
    } else {
      cart.push({
        id: productId,
        name: productName,
        qty: 1,
      });
    }

    try {
      window.localStorage.setItem(storageKey, JSON.stringify(cart));
    } catch (_) {
      // Bỏ qua nếu trình duyệt chặn localStorage
    }
  }

  function initAddToCart() {
    const catalog = document.querySelector('.storeCatalog') || document;
    const loginUrl = document.querySelector('.storeCatalog')?.getAttribute('data-login-url') || '/login';

    catalog.addEventListener('click', (event) => {
      const btn = event.target.closest('[data-add-to-cart]');
      if (!btn) return;

      if (!isUserLoggedIn()) {
        event.preventDefault();
        window.location.href = loginUrl;
        return;
      }

      const productId = btn.getAttribute('data-product-id') || '';
      const productName = btn.getAttribute('data-product-name') || 'Sản phẩm';
      if (!productId) return;

      addItemToLocalCart(productId, productName);

      const originalText = btn.textContent;
      btn.textContent = 'Đã thêm';
      btn.disabled = true;

      window.setTimeout(() => {
        btn.textContent = originalText || 'Thêm vào giỏ';
        btn.disabled = false;
      }, 900);
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initFeaturedCarousel();
    initAddToCart();
  });
})();