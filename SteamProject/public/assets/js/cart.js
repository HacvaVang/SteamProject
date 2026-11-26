(() => {
  'use strict';

  const storageKey = 'steam_project_cart';

  function formatVnd(amount) {
    const value = Number(amount) || 0;
    return `${value.toLocaleString('vi-VN')} đ`;
  }

  function readLocalCart(onError) {
    try {
      const raw = window.localStorage.getItem(storageKey);
      const parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      onError('Không thể đọc giỏ hàng đã lưu trong trình duyệt.');
      console.error('Unable to read local cart:', error);
      return [];
    }
  }

  function writeLocalCart(items, onError) {
    try {
      window.localStorage.setItem(storageKey, JSON.stringify(items));
      return true;
    } catch (error) {
      onError('Trình duyệt không cho phép lưu bản sao giỏ hàng.');
      console.error('Unable to save local cart:', error);
      return false;
    }
  }

  function mergeCartItems(serverItems, localItems) {
    const quantities = new Map();
    [...serverItems, ...localItems].forEach((item) => {
      const id = String(item.id || '');
      const qty = Math.max(1, Number(item.qty) || 1);
      if (id) quantities.set(id, Math.max(quantities.get(id) || 0, qty));
    });
    return Array.from(quantities, ([id, qty]) => ({ id, qty }));
  }

  function initCartPage() {
    const cartPage = document.querySelector('[data-cart-page]');
    if (!cartPage) return;

    const listEl = cartPage.querySelector('[data-cart-items]');
    const emptyEl = cartPage.querySelector('[data-cart-empty]');
    const totalEls = Array.from(cartPage.querySelectorAll('[data-cart-total]'));
    const clearBtn = cartPage.querySelector('[data-cart-clear]');
    const checkoutBtn = cartPage.querySelector('[data-cart-checkout]');
    const msgEl = cartPage.querySelector('[data-cart-message]');
    const syncMsgEl = cartPage.querySelector('[data-cart-sync-message]');
    const catalogMap = window.__CATALOG_MAP__ || {};
    const initialCart = window.__STORE_CART__ || {};
    const syncUrl = cartPage.getAttribute('data-cart-sync-url') || '/cart/sync';
    const checkoutUrl = cartPage.getAttribute('data-cart-checkout-url') || '/cart/checkout';
    let syncQueue = Promise.resolve();

    const setSyncMessage = (message, isError = false) => {
      if (!syncMsgEl) return;
      syncMsgEl.textContent = message;
      syncMsgEl.classList.toggle('is-error', isError);
    };

    const showMessage = (message, isError = false) => {
      if (!msgEl) return;
      msgEl.textContent = message;
      msgEl.classList.toggle('is-error', isError);
    };

    const requestJson = async (url, payload) => {
      const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
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
        throw new Error(result.message || 'Không thể cập nhật giỏ hàng.');
      }
      return result;
    };

    const syncToServer = (items) => {
      const request = syncQueue
        .catch(() => undefined)
        .then(() => requestJson(syncUrl, {
          items: items.map((item) => ({ id: item.id, qty: item.qty })),
        }));
      syncQueue = request;
      return request;
    };

    const render = () => {
      if (!listEl || !emptyEl) return;
      const cart = readLocalCart(setSyncMessage);
      const hasItems = cart.length > 0;
      listEl.replaceChildren();
      emptyEl.hidden = hasItems;
      if (checkoutBtn) checkoutBtn.disabled = !hasItems;
      if (clearBtn) clearBtn.disabled = !hasItems;

      let grandTotal = 0;
      cart.forEach((entry) => {
        const id = String(entry.id || '');
        const meta = catalogMap[id] || {};
        const name = String(meta.name || entry.name || 'Sản phẩm');
        const price = Number(meta.price ?? entry.price ?? 0) || 0;
        const qty = Math.max(1, Number(entry.qty) || 1);
        const lineTotal = price * qty;
        grandTotal += lineTotal;

        const row = document.createElement('article');
        row.className = 'cartRow';
        row.dataset.cartRow = id;

        const imageLink = document.createElement('a');
        imageLink.className = 'cartProductImage';
        imageLink.href = `/product?id=${encodeURIComponent(id)}`;
        if (meta.image) {
          const image = document.createElement('img');
          image.src = String(meta.image);
          image.alt = '';
          image.loading = 'lazy';
          imageLink.append(image);
        } else {
          imageLink.textContent = name.slice(0, 1).toUpperCase();
          imageLink.setAttribute('aria-hidden', 'true');
        }

        const details = document.createElement('div');
        details.className = 'cartItemDetails';
        const productLink = document.createElement('a');
        productLink.className = 'cartItemName';
        productLink.href = `/product?id=${encodeURIComponent(id)}`;
        productLink.textContent = name;
        const unitPrice = document.createElement('span');
        unitPrice.className = 'cartItemUnitPrice';
        unitPrice.textContent = `Đơn giá: ${formatVnd(price)}`;
        details.append(productLink, unitPrice);

        const controls = document.createElement('div');
        controls.className = 'cartItemControls';
        const linePrice = document.createElement('strong');
        linePrice.className = 'cartItemPrice';
        linePrice.textContent = formatVnd(lineTotal);
        const quantityGroup = document.createElement('div');
        quantityGroup.className = 'cartQtyGroup';

        const decrementButton = document.createElement('button');
        decrementButton.type = 'button';
        decrementButton.className = 'cartQtyBtn';
        decrementButton.dataset.cartDec = id;
        decrementButton.setAttribute('aria-label', `Giảm số lượng ${name}`);
        decrementButton.textContent = '−';

        const quantity = document.createElement('span');
        quantity.className = 'cartQtyValue';
        quantity.textContent = String(qty);
        quantity.setAttribute('aria-label', 'Số lượng');

        const incrementButton = document.createElement('button');
        incrementButton.type = 'button';
        incrementButton.className = 'cartQtyBtn';
        incrementButton.dataset.cartInc = id;
        incrementButton.setAttribute('aria-label', `Tăng số lượng ${name}`);
        incrementButton.textContent = '+';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'cartRemoveBtn';
        removeButton.dataset.cartRemove = id;
        removeButton.textContent = 'Xóa';

        quantityGroup.append(decrementButton, quantity, incrementButton, removeButton);
        controls.append(linePrice, quantityGroup);
        row.append(imageLink, details, controls);
        listEl.append(row);
      });

      totalEls.forEach((element) => {
        element.textContent = formatVnd(grandTotal);
      });
    };

    const saveAndSync = (items) => {
      writeLocalCart(items, setSyncMessage);
      render();
      showMessage('');
      setSyncMessage('Đang đồng bộ giỏ hàng...');
      syncToServer(items)
        .then(() => setSyncMessage('Giỏ hàng đã được lưu.'))
        .catch((error) => {
          setSyncMessage(error.message || 'Không thể lưu giỏ hàng.', true);
        });
    };

    const serverItems = Array.isArray(initialCart.items) ? initialCart.items : [];
    const localItems = readLocalCart(setSyncMessage);
    if (initialCart.status === 'converted') {
      writeLocalCart([], setSyncMessage);
      showMessage(`Giỏ hàng này đã được chuyển thành đơn hàng #${initialCart.converted_order_id}.`);
    } else {
      const mergedItems = mergeCartItems(serverItems, localItems);
      writeLocalCart(mergedItems, setSyncMessage);
      render();
      if (mergedItems.length > 0) {
        setSyncMessage('Đang đồng bộ giỏ hàng...');
        syncToServer(mergedItems)
          .then(() => setSyncMessage('Giỏ hàng đã được lưu.'))
          .catch((error) => setSyncMessage(error.message || 'Không thể lưu giỏ hàng.', true));
      }
    }
    render();

    cartPage.addEventListener('click', (event) => {
      if (cartPage.dataset.checkoutPending === '1') return;
      const target = event.target instanceof Element ? event.target : null;
      if (!target) return;

      const incId = target.closest('[data-cart-inc]')?.getAttribute('data-cart-inc');
      const decId = target.closest('[data-cart-dec]')?.getAttribute('data-cart-dec');
      const remId = target.closest('[data-cart-remove]')?.getAttribute('data-cart-remove');

      if (incId) {
        const cart = readLocalCart(setSyncMessage);
        const found = cart.find((item) => String(item.id) === String(incId));
        if (found) {
          const quantity = Number(found.qty) || 1;
          if (quantity >= 99) {
            showMessage('Mỗi sản phẩm chỉ được đặt tối đa 99 bản.', true);
            return;
          }
          found.qty = quantity + 1;
          saveAndSync(cart);
        }
      } else if (decId) {
        const cart = readLocalCart(setSyncMessage).map((item) => {
          if (String(item.id) === String(decId)) {
            return { ...item, qty: (Number(item.qty) || 1) - 1 };
          }
          return item;
        }).filter((item) => (Number(item.qty) || 0) > 0);
        saveAndSync(cart);
      } else if (remId) {
        const cart = readLocalCart(setSyncMessage).filter((item) => String(item.id) !== String(remId));
        saveAndSync(cart);
      }
    });

    clearBtn?.addEventListener('click', () => {
      writeLocalCart([], setSyncMessage);
      saveAndSync([]);
    });

    checkoutBtn?.addEventListener('click', async () => {
      if (cartPage.getAttribute('data-logged-in') !== '1') {
        window.location.href = '/login';
        return;
      }

      const cart = readLocalCart(setSyncMessage);
      if (cart.length === 0) return;

      cartPage.dataset.checkoutPending = '1';
      checkoutBtn.disabled = true;
      if (clearBtn) clearBtn.disabled = true;
      showMessage('Đang tạo đơn hàng...');
      try {
        await syncQueue;
        const result = await requestJson(checkoutUrl, {});
        writeLocalCart([], setSyncMessage);
        render();
        showMessage(`Đã tạo đơn hàng #${result.order_id}. Trạng thái: chờ xử lý.`);
        setSyncMessage('Đơn hàng đã được lưu trong hệ thống.');
      } catch (error) {
        showMessage(error.message || 'Không thể tạo đơn hàng.', true);
      } finally {
        delete cartPage.dataset.checkoutPending;
        render();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', initCartPage);
})();
