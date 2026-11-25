document.addEventListener('DOMContentLoaded', function () {
  const menu = document.getElementById('navbar-menu');

  if (!menu) {
    return;
  }

  const pages = [
    ['home', '/', 'Home'],
    ['products', '/products', 'Store'],
    ['about', '/about', 'About'],
    ['forums', '/forums', 'Forums'],
    ['faq', '/faq', 'FAQs'],
    ['qa', '/qa', 'Discussions'],
    ['contact', '/contact', 'Support']
  ];
  const items = document.createDocumentFragment();

  pages.forEach(function (page) {
    const item = document.createElement('li');
    const link = document.createElement('a');

    item.className = 'navbar-item';
    link.className = 'navbar-link';
    link.href = page[1];
    link.textContent = page[2];

    if (page[0] === menu.dataset.current ||
        (page[0] === 'products' && menu.dataset.current === 'product')) {
      item.classList.add('active');
      link.classList.add('active', 'is-active');
      link.setAttribute('aria-current', 'page');
    }

    item.appendChild(link);
    items.appendChild(item);
  });

  menu.prepend(items);
});