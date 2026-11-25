(() => {
  'use strict';

  const storageKey = 'steam_project_cart';

  function formatVnd(amount) {
    const num = Number(amount) || 0;
    return `${num.toLocaleString('vi-VN')} đ`;
  }

  function readLocalCart() {
    try {
      const raw = window.localStorage.getItem(storageKey);
      const parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (_) {
      return [];
    }
  }

  function writeLocalCart(items) {
    try {
      window.localStorage.setItem(storageKey, JSON.stringify(items));
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
            : '<div class="fcPlaceholder"></div>';

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
      if (cartPage.getAttribute('data-logged-in') !== '1') {
        window.location.href = '/login';
        return;
      }
      if (msgEl) {
        msgEl.textContent = 'Đã ghi nhận yêu cầu thanh toán (bản demo).';
      }
    });

    render();
  }

  document.addEventListener('DOMContentLoaded', initCartPage);
})();
