// Demo Explorer: pick a demo from the list, show it in the frame with its source.
// The open demo is kept in the URL hash (index.php#card_view.php) so it can be shared.
(function () {
  'use strict';

  var MIN_FRAME_HEIGHT = 640;
  var THEME_KEY = 'pgDemoExplorerTheme';
  var COPIED_MS = 1600;

  var frame = document.getElementById('demo_frame');
  var title = document.getElementById('demo-title');
  var fileLabel = document.getElementById('demo-file');
  var openLink = document.getElementById('demo-open');
  var reloadBtn = document.getElementById('demo-reload');
  var sourceFile = document.getElementById('source-file');
  var sourceCode = document.getElementById('source-code');
  var copyBtn = document.getElementById('source-copy');
  var filter = document.getElementById('demo-filter');
  var empty = document.getElementById('demo-empty');
  var themeSelect = document.getElementById('theme-select');
  var themeNote = document.getElementById('theme-note');
  var links = Array.prototype.slice.call(document.querySelectorAll('#demo-list a[data-file]'));

  var current = null;
  var sourceRequest = 0;
  var frameObserver = null;

  function linkFor(file) {
    return links.filter(function (a) { return a.getAttribute('data-file') === file; })[0] || null;
  }

  function fileFromHash() {
    var file = decodeURIComponent(window.location.hash.slice(1));
    return linkFor(file) ? file : null;
  }

  // ---- choosing a demo ----

  function show(file, options) {
    var link = linkFor(file);
    if (!link || file === current) { return; }
    current = file;

    links.forEach(function (a) { a.removeAttribute('aria-current'); });
    link.setAttribute('aria-current', 'page');
    if (options.scrollList) { link.scrollIntoView({ block: 'nearest' }); }

    title.textContent = link.textContent;
    document.title = link.textContent + ' | phpGrid Demo Explorer';
    fileLabel.textContent = 'examples/' + file;
    openLink.href = file;
    sourceFile.textContent = file;

    if (options.loadFrame) { frame.src = file; }
    if (options.updateHash) { history.replaceState(null, '', '#' + encodeURIComponent(file)); }
    loadSource(file);
  }

  function loadSource(file) {
    var request = ++sourceRequest;
    sourceCode.classList.add('is-loading');
    sourceCode.textContent = 'Loading source…';

    fetch('index.php?file=' + encodeURIComponent(file))
      .then(function (res) {
        if (!res.ok) { throw new Error('HTTP ' + res.status); }
        return res.text();
      })
      .then(function (html) {
        if (request !== sourceRequest) { return; }   // a newer demo was picked meanwhile
        sourceCode.innerHTML = html;                  // highlight_string() output, escaped by PHP
        sourceCode.classList.remove('is-loading');
      })
      .catch(function () {
        if (request !== sourceRequest) { return; }
        sourceCode.textContent = 'The source could not be loaded. Open the demo in a new tab to see it.';
      });
  }

  links.forEach(function (a) {
    a.addEventListener('click', function (event) {
      event.preventDefault();
      show(a.getAttribute('data-file'), { loadFrame: true, updateHash: true });
      if (window.matchMedia('(max-width: 960px)').matches) {
        document.getElementById('demo-stage').scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  window.addEventListener('hashchange', function () {
    var file = fileFromHash();
    if (file) { show(file, { loadFrame: true, scrollList: true }); }
  });

  // ---- filter ----

  filter.addEventListener('input', function () {
    var words = filter.value.toLowerCase().split(/\s+/).filter(Boolean);
    var shown = 0;
    links.forEach(function (a) {
      var text = (a.textContent + ' ' + a.getAttribute('data-file')).toLowerCase();
      var match = words.every(function (w) { return text.indexOf(w) !== -1; });
      a.parentNode.hidden = !match;
      if (match) { shown++; }
    });
    empty.hidden = shown > 0;
  });

  // ---- frame: grow to the demo's height, apply the chosen theme ----

  function fitFrame() {
    var doc = frame.contentDocument;
    if (!doc || !doc.documentElement) { return; }
    // offsetHeight of <html> is the page's own height, not the frame's, so a grid that
    // sizes itself to the window (enable_autoheight) settles instead of growing forever
    var height = Math.max(MIN_FRAME_HEIGHT, Math.ceil(doc.documentElement.offsetHeight));
    if (Math.abs(frame.offsetHeight - height) > 1) { frame.style.height = height + 'px'; }
  }

  function onFrameLoad() {
    if (frameObserver) { frameObserver.disconnect(); frameObserver = null; }
    var doc = frame.contentDocument;
    if (!doc) { return; }   // not same-origin; keep the default height

    // the frame can also navigate on its own (a link inside a demo)
    var file = frame.contentWindow.location.pathname.split('/').pop();
    if (linkFor(file) && file !== current) { show(file, { updateHash: true }); }

    fitFrame();
    if ('ResizeObserver' in window) {
      frameObserver = new ResizeObserver(fitFrame);
      frameObserver.observe(doc.documentElement);
    }
    applyTheme();
  }
  frame.addEventListener('load', onFrameLoad);

  function readTheme() {
    try { return localStorage.getItem(THEME_KEY) || ''; } catch (e) { return ''; }
  }
  function saveTheme(value) {
    try { localStorage.setItem(THEME_KEY, value); } catch (e) { /* private mode: not remembered */ }
  }

  // grids on a jQuery UI theme load it as #theme-custom-style; the modern theme has none
  function applyTheme() {
    var doc = frame.contentDocument;
    var link = doc && doc.getElementById('theme-custom-style');
    themeSelect.disabled = !link;
    themeNote.hidden = !!link;
    if (!link) { return; }
    if (!link.hasAttribute('data-original')) { link.setAttribute('data-original', link.getAttribute('href')); }
    var theme = themeSelect.value;
    link.href = theme ? '../css/' + theme + '/jquery-ui.css' : link.getAttribute('data-original');
  }

  themeSelect.value = readTheme();
  if (themeSelect.selectedIndex < 0) { themeSelect.value = ''; }
  themeSelect.addEventListener('change', function () {
    saveTheme(themeSelect.value);
    applyTheme();
  });

  // ---- toolbar ----

  reloadBtn.addEventListener('click', function () {
    if (frame.contentWindow) { frame.contentWindow.location.reload(); }
  });

  copyBtn.addEventListener('click', function () {
    var text = sourceCode.textContent;
    if (!navigator.clipboard || sourceCode.classList.contains('is-loading')) { return; }
    navigator.clipboard.writeText(text).then(function () {
      copyBtn.textContent = 'Copied';
      setTimeout(function () { copyBtn.textContent = 'Copy'; }, COPIED_MS);
    }, function () {
      copyBtn.textContent = 'Copy failed';
      setTimeout(function () { copyBtn.textContent = 'Copy'; }, COPIED_MS);
    });
  });

  // ---- start: the demo in the URL, else the one the page rendered ----

  var initial = fileFromHash();
  var rendered = frame.getAttribute('src');
  if (initial && initial !== rendered) {
    show(initial, { loadFrame: true, scrollList: true });
  } else {
    show(rendered, { scrollList: !!initial });
    // the demo may have finished loading before this script ran
    var doc = frame.contentDocument;
    if (doc && doc.readyState === 'complete' && doc.location.href !== 'about:blank') { onFrameLoad(); }
  }
})();
