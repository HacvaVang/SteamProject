(() => {
  'use strict';

  function initCatalogLists() {
    document.querySelectorAll('[data-catalog-list]').forEach((list) => {
      const section = list.closest('.catalogSection');
      if (!section) return;

      const rows = Array.from(list.querySelectorAll('[data-catalog-item]'));
      const input = section.querySelector('[data-catalog-search]');
      const count = section.querySelector('[data-catalog-count], .catalogCount');
      const emptyState = section.querySelector('[data-catalog-empty]');
      const defaultEmptyMessage = emptyState?.textContent.trim();
      const pagination = section.querySelector('[data-catalog-pagination]');
      const pageStatus = pagination?.querySelector('[data-catalog-page-status]');
      const showMore = pagination?.querySelector('[data-catalog-show-more]');
      const collapse = pagination?.querySelector('[data-catalog-collapse]');
      const pageSize = Math.max(1, Number(section.dataset.pageSize) || 12);
      let limit = pageSize;
      let matchingRows = rows;

      const normalize = (value) => String(value)
        .toLocaleLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/[^a-z0-9]+/g, ' ')
        .trim();

      const render = () => {
        const visibleRows = new Set(matchingRows.slice(0, limit));
        rows.forEach((row) => {
          row.hidden = !visibleRows.has(row);
        });

        if (count) count.textContent = `${matchingRows.length} sản phẩm`;
        if (emptyState) {
          emptyState.hidden = matchingRows.length > 0;
          emptyState.textContent = matchingRows.length === 0 && input?.value.trim()
            ? 'Không tìm thấy sản phẩm nào.'
            : (defaultEmptyMessage || 'Không tìm thấy sản phẩm nào.');
        }

        if (pagination) {
          pagination.hidden = matchingRows.length <= pageSize;
          if (pageStatus) {
            pageStatus.textContent = `Đang hiển thị ${Math.min(limit, matchingRows.length)} / ${matchingRows.length} sản phẩm`;
          }
          if (showMore) {
            const remaining = Math.max(0, matchingRows.length - limit);
            showMore.hidden = remaining === 0;
            showMore.textContent = `Xem thêm ${Math.min(pageSize, remaining)}`;
          }
          if (collapse) collapse.hidden = limit <= pageSize;
        }
      };

      const filterRows = () => {
        const tokens = normalize(input?.value.trim() || '').split(/\s+/).filter(Boolean);
        matchingRows = rows.filter((row) => {
          const text = normalize(row.getAttribute('data-search') || row.textContent || '');
          return tokens.every((token) => text.includes(token));
        });
        limit = pageSize;
        render();
      };

      input?.addEventListener('input', filterRows);
      input?.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && input.value !== '') {
          input.value = '';
          filterRows();
        }
      });

      showMore?.addEventListener('click', () => {
        limit = Math.min(matchingRows.length, limit + pageSize);
        render();
      });

      collapse?.addEventListener('click', () => {
        limit = pageSize;
        render();
      });

      filterRows();
    });
  }

  document.addEventListener('DOMContentLoaded', initCatalogLists);
})();
