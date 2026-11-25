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

      prevBtn?.addEventListener('click', () => {
        track.scrollBy({ left: -getStep(), behavior: 'smooth' });
      });

      nextBtn?.addEventListener('click', () => {
        track.scrollBy({ left: getStep(), behavior: 'smooth' });
      });

      track.addEventListener('scroll', syncButtons, { passive: true });
      window.addEventListener('resize', syncButtons, { passive: true });
      syncButtons();
    });
  }

  function initCatalogSearch() {
    document.querySelectorAll('[data-catalog-search]').forEach((input) => {
      const section = input.closest('.catalogSection');
      const list = section?.querySelector('[data-catalog-list]');
      if (!section || !list) return;

      const rows = Array.from(list.querySelectorAll('[data-catalog-item]'));
      const count = section.querySelector('[data-catalog-count], .catalogCount');
      const emptyState = section.querySelector('[data-catalog-empty]');
      const defaultEmptyMessage = emptyState?.textContent.trim();
      const form = input.closest('form');

      const normalize = (value) => value
        .toLocaleLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd');

      const filterRows = () => {
        const query = normalize(input.value.trim());
        let visibleCount = 0;

        rows.forEach((row) => {
          const text = normalize(row.getAttribute('data-search') || row.textContent || '');
          const matches = text.includes(query);
          row.hidden = !matches;
          if (matches) visibleCount++;
        });

        if (count) count.textContent = `${visibleCount} sản phẩm`;
        if (emptyState) {
          emptyState.hidden = visibleCount > 0;
          emptyState.textContent = query
            ? 'Không tìm thấy sản phẩm nào.'
            : (defaultEmptyMessage || 'Không tìm thấy sản phẩm nào.');
        }
      };

      input.addEventListener('input', () => {
        if (input.dataset.serverFiltered === '1' && input.value.trim() === '' && form) {
          form.submit();
          return;
        }
        filterRows();
      });

      input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && input.value.trim() !== '') {
          input.value = '';
          if (input.dataset.serverFiltered === '1' && form) {
            form.submit();
          } else {
            filterRows();
          }
        }
      });

      filterRows();
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
    initCatalogSearch();
    initAddToCart();
  });
})();

function formatVnd(amount) {
    const num = Number(amount) || 0;
    return `${num.toLocaleString('vi-VN')} đ`;
  }

  function readLocalCart() {
    try {
      const raw = window.localStorage.getItem('steam_project_cart');
      const parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (_) {
      return [];
    }
  }

  function writeLocalCart(items) {
    try {
      window.localStorage.setItem('steam_project_cart', JSON.stringify(items));
    } catch (_) {
      // Bỏ qua nếu không truy cập được localStorage
    }
  }

  function initCartPage() {
    const cartPage = document.querySelector('[data-cart-page]');
    if (!cartPage) return;

    const listEl = cartPage.querySelector('[data-cart-items]');
    const emptyEl = cartPage.querySelector('[data-cart-empty]');
    const footerEl = cartPage.querySelector('[data-cart-footer]');
    const totalEl = cartPage.querySelector('[data-cart-total]');
    const clearBtn = cartPage.querySelector('[data-cart-clear]');
    const checkoutBtn = cartPage.querySelector('[data-cart-checkout]');
    const msgEl = cartPage.querySelector('[data-cart-message]');
    const catalogMap = window.__CATALOG_MAP__ || {};

    const render = () => {
      const cart = readLocalCart();
      if (!listEl || !emptyEl || !footerEl || !totalEl) return;

      if (cart.length === 0) {
        listEl.innerHTML = '';
        emptyEl.hidden = false;
        footerEl.hidden = true;
        totalEl.textContent = formatVnd(0);
        return;
      }

      emptyEl.hidden = true;
      footerEl.hidden = false;

      let grandTotal = 0;
      listEl.innerHTML = cart
        .map((entry) => {
          const id = String(entry.id || '');
          const meta = catalogMap[id] || {};
          const name = String(meta.name || entry.name || 'Sản phẩm');
          const price = Number(meta.price ?? entry.price ?? 0) || 0;
          const qty = Math.max(1, Number(entry.qty) || 1);
          const lineTotal = price * qty;
          grandTotal += lineTotal;

          const imgHtml = meta.image
            ? `<img src="${String(meta.image)}" alt="${name}" class="catalogImg">`
            : `<div class="fcPlaceholder"></div>`;

          return `
            <article class="catalogRow cartRow" data-cart-row="${id}">
              <a href="/product?id=${encodeURIComponent(id)}" class="catalogThumbLink">
                <div class="catalogThumb">${imgHtml}</div>
              </a>
              <div class="catalogBody">
                <a href="/product?id=${encodeURIComponent(id)}" class="catalogTitle">${name}</a>
                <p class="catalogSummary">Đơn giá: ${formatVnd(price)}</p>
              </div>
              <div class="catalogAside">
                <span class="catalogPrice">${formatVnd(lineTotal)}</span>
                <div class="cartQtyGroup">
                  <button type="button" class="cartQtyBtn" data-cart-dec="${id}" aria-label="Giảm số lượng">−</button>
                  <span class="cartQtyValue">${qty}</span>
                  <button type="button" class="cartQtyBtn" data-cart-inc="${id}" aria-label="Tăng số lượng">+</button>
                  <button type="button" class="cartRemoveBtn" data-cart-remove="${id}">Xóa</button>
                </div>
              </div>
            </article>
          `;
        })
        .join('');

      totalEl.textContent = formatVnd(grandTotal);
    };

    cartPage.addEventListener('click', (event) => {
      const incId = event.target.closest('[data-cart-inc]')?.getAttribute('data-cart-inc');
      const decId = event.target.closest('[data-cart-dec]')?.getAttribute('data-cart-dec');
      const remId = event.target.closest('[data-cart-remove]')?.getAttribute('data-cart-remove');

      if (incId) {
        const cart = readLocalCart();
        const found = cart.find((item) => String(item.id) === String(incId));
        if (found) {
          found.qty = (Number(found.qty) || 1) + 1;
          writeLocalCart(cart);
          render();
        }
      } else if (decId) {
        const cart = readLocalCart().map((item) => {
          if (String(item.id) === String(decId)) {
            return { ...item, qty: (Number(item.qty) || 1) - 1 };
          }
          return item;
        }).filter((item) => (Number(item.qty) || 0) > 0);
        writeLocalCart(cart);
        render();
      } else if (remId) {
        const cart = readLocalCart().filter((item) => String(item.id) !== String(remId));
        writeLocalCart(cart);
        render();
      }
    });

    clearBtn?.addEventListener('click', () => {
      writeLocalCart([]);
      if (msgEl) msgEl.textContent = '';
      render();
    });

    checkoutBtn?.addEventListener('click', () => {
      if (!isUserLoggedIn()) {
        window.location.href = '/login';
        return;
      }
      if (msgEl) {
        msgEl.textContent = 'Đã ghi nhận yêu cầu thanh toán (bản demo).';
      }
    });

    render();
  }