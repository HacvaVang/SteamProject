(() => {
  'use strict';

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

  document.addEventListener('DOMContentLoaded', initCatalogSearch);
})();
