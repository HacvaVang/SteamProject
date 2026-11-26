(() => {
  'use strict';

  let cartSyncQueue = Promise.resolve();

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
    } catch (error) {
      console.error('Unable to read local cart:', error);
      throw new Error('Không thể đọc giỏ hàng hiện tại.');
    }

    const existing = cart.find((item) => String(item.id) === String(productId));
    if (existing) {
      const quantity = Number(existing.qty) || 1;
      if (quantity >= 99) {
        throw new Error('Mỗi sản phẩm chỉ được đặt tối đa 99 bản.');
      }
      existing.qty = quantity + 1;
    } else {
      cart.push({
        id: productId,
        name: productName,
        qty: 1,
      });
    }

    try {
      window.localStorage.setItem(storageKey, JSON.stringify(cart));
    } catch (error) {
      console.error('Unable to save local cart:', error);
      throw new Error('Trình duyệt không cho phép lưu giỏ hàng.');
    }
    return cart;
  }

  function syncCartToServer(items, syncUrl) {
    const request = cartSyncQueue
      .catch(() => undefined)
      .then(async () => {
        const response = await fetch(syncUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            items: items.map((item) => ({ id: item.id, qty: item.qty })),
          }),
          credentials: 'same-origin',
        });
        const responseText = await response.text();
        let result;
        try {
          result = JSON.parse(responseText);
        } catch (error) {
          throw new Error('Máy chủ trả về phản hồi không hợp lệ.');
        }
        if (!response.ok || !result.ok) {
          throw new Error(result.message || 'Không thể lưu giỏ hàng.');
        }
      });
    cartSyncQueue = request;
    return request;
  }

  function initAddToCart() {
    const catalog = document.querySelector('.storeCatalog') || document;
    const storeCatalog = document.querySelector('.storeCatalog');
    const loginUrl = storeCatalog?.getAttribute('data-login-url') || '/login';
    const syncUrl = storeCatalog?.getAttribute('data-cart-sync-url') || '/cart/sync';

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

      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Đang lưu...';
      btn.removeAttribute('title');

      try {
        const cart = addItemToLocalCart(productId, productName);
        syncCartToServer(cart, syncUrl)
          .then(() => {
            btn.textContent = 'Đã thêm';
          })
          .catch((error) => {
            console.error('Unable to sync cart:', error);
            btn.textContent = 'Lỗi lưu giỏ';
            btn.title = error.message || 'Không thể lưu giỏ hàng.';
          })
          .finally(() => {
            window.setTimeout(() => {
              btn.textContent = originalText || 'Thêm vào giỏ';
              btn.disabled = false;
            }, 1200);
          });
      } catch (error) {
        btn.textContent = error.message || 'Không thể thêm';
        btn.title = error.message || 'Không thể thêm sản phẩm vào giỏ.';
        window.setTimeout(() => {
          btn.textContent = originalText || 'Thêm vào giỏ';
          btn.disabled = false;
        }, 1800);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initFeaturedCarousel();
    initAddToCart();
  });
})();