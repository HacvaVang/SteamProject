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
    const form = document.querySelector('.filterForm');
    const input = form?.querySelector('.filterInput');
    if (!form || !input) return;

    input.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && input.value.trim() !== '') {
        input.value = '';
        form.submit();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initFeaturedCarousel();
    initCatalogSearch();
  });
})();