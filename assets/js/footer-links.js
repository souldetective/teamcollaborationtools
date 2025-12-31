// Optional helper to add or remove footer links dynamically on the client side.
// Usage example:
//   window.aichatbotfreeFooterLinks.add('about', 'Contact', '/contact');
//   window.aichatbotfreeFooterLinks.removeByText('guides', 'Old Link');
(function () {
  function getColumn(columnKey) {
    return document.querySelector('[data-footer-column="' + columnKey + '"]');
  }

  function ensureList(column) {
    if (!column) return null;
    let list = column.querySelector('ul');
    if (!list) {
      list = document.createElement('ul');
      column.appendChild(list);
    }
    return list;
  }

  function add(columnKey, label, url) {
    const column = getColumn(columnKey);
    const list = ensureList(column);
    if (!list || !label || !url) return;

    const li = document.createElement('li');
    const anchor = document.createElement('a');
    anchor.href = url;
    anchor.textContent = label;
    anchor.rel = 'noopener noreferrer';
    anchor.target = '_self';
    li.appendChild(anchor);
    list.appendChild(li);
  }

  function removeByText(columnKey, label) {
    const column = getColumn(columnKey);
    if (!column) return;
    const items = column.querySelectorAll('li');
    items.forEach((item) => {
      if (item.textContent.trim() === label) {
        item.remove();
      }
    });
  }

  window.aichatbotfreeFooterLinks = {
    add,
    removeByText,
  };
})();
