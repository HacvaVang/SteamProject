<div class="dbar" data-module="toolbar">
  <span class="dlabel">Lọc theo:</span>
  <select id="filterCategory" class="dsel" aria-label="Danh mục">
    <option value="all">Tất cả</option>
    <option value="products">Sản phẩm</option>
    <option value="news">Tin tức</option>
    <option value="faq">FAQ</option>
  </select>

  <form id="toolbarSearchForm" class="dsearch" role="search">
    <input id="toolbarSearchInput" type="search" placeholder="Tìm kiếm..." aria-label="Tìm kiếm">
    <button type="submit" aria-label="Tìm">
      <span class="ic"><svg viewBox="0 0 24 24"><path d="M10 2a8 8 0 105.29 14l4.7 4.7 1.42-1.42-4.7-4.7A8 8 0 0010 2zm0 2a6 6 0 110 12 6 6 0 010-12z"/></svg></span>
    </button>
  </form>

  <a href="/products?action=create" class="btn-dlink">Tạo mới</a>
</div>