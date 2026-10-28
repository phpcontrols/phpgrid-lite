// Copied from https://phpgrid.com/assets/js/site.js (2026-10-08) with site.css; update it from there.
// phpGrid shared site chrome: mobile nav, Demos dropdown, promo-bar offset, header scroll state, current-section link.
(function () {
	'use strict';

	// Path prefix -> nav item that represents that section
	var SECTION_LINKS = [
		{ prefix: '/documentation', selector: '[aria-controls="nav-docs"]' },
		{ prefix: '/knowledgebase', selector: '[aria-controls="nav-docs"]' },
		{ prefix: '/contactus', selector: 'a[href="/contactus/"]' },
		{ prefix: '/screenshots', selector: 'a[href="/#gallery"]' },
		{ prefix: '/example', selector: '[aria-controls="nav-demos"]' }
	];

	function initNavToggle(header) {
		var toggle = header.querySelector('.nav-toggle');
		var nav = header.querySelector('.site-nav');
		if (!toggle || !nav) return;

		toggle.addEventListener('click', function () {
			var isOpen = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', String(!isOpen));
			nav.classList.toggle('is-open', !isOpen);
		});
		nav.addEventListener('click', function (event) {
			if (event.target.closest('a') && toggle.getAttribute('aria-expanded') === 'true') toggle.click();
		});
	}

	function initDropdowns(header) {
		var buttons = Array.prototype.slice.call(header.querySelectorAll('.site-nav__menu-btn'));

		function setOpen(button, isOpen) {
			var menu = document.getElementById(button.getAttribute('aria-controls'));
			button.setAttribute('aria-expanded', String(isOpen));
			if (menu) menu.classList.toggle('is-open', isOpen);
		}

		function closeAll(except) {
			buttons.forEach(function (b) { if (b !== except) setOpen(b, false); });
		}

		buttons.forEach(function (button) {
			button.addEventListener('click', function () {
				var isOpen = button.getAttribute('aria-expanded') === 'true';
				closeAll(button);
				setOpen(button, !isOpen);
			});
		});

		document.addEventListener('click', function (event) {
			if (!event.target.closest('.has-menu')) closeAll();
		});
		document.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape') return;
			var open = buttons.filter(function (b) { return b.getAttribute('aria-expanded') === 'true'; })[0];
			if (!open) return;
			closeAll();
			open.focus();
		});
	}

	// "Scrolled" once content slides under the sticky header. On the homepage the
	// hero is the reference; elsewhere any scroll past the header's resting spot.
	function initScrollState(header) {
		var hero = document.querySelector('.hero');
		var restingTop = header.getBoundingClientRect().top + window.pageYOffset;
		var frame = null;

		function update() {
			frame = null;
			var isScrolled = hero
				? hero.getBoundingClientRect().top < header.offsetHeight
				: window.pageYOffset > restingTop + 1;
			header.classList.toggle('is-scrolled', isScrolled);
		}

		window.addEventListener('scroll', function () {
			if (frame === null) frame = requestAnimationFrame(update);
		}, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	// Legacy pages can show a fixed promo bar (myStickymenu "welcome bar") at the
	// top of the viewport; stick the header just below it instead of underneath.
	function initPromoBarOffset(header) {
		var bar = document.querySelector('.mysticky-welcomebar-fixed');
		if (!bar) return;

		function update() {
			var cs = getComputedStyle(bar);
			// The plugin keeps the bar in place at opacity 0 until it fades in
			var isShown = cs.position === 'fixed' && bar.offsetHeight > 0 && parseFloat(cs.opacity) > 0;
			header.style.top = isShown ? bar.offsetHeight + 'px' : '';
		}

		if ('ResizeObserver' in window) new ResizeObserver(update).observe(bar);
		new MutationObserver(update).observe(bar, { attributes: true, attributeFilter: ['style', 'class'] });
		window.addEventListener('resize', update);
		update();
	}

	function markCurrentSection(header) {
		var path = window.location.pathname;
		SECTION_LINKS.some(function (entry) {
			if (path.indexOf(entry.prefix) !== 0) return false;
			var el = header.querySelector(entry.selector);
			if (!el) return false;
			if (el.tagName === 'A') el.setAttribute('aria-current', 'page');
			else el.classList.add('is-current');
			return true;
		});
	}

	var header = document.querySelector('.site-header');
	if (!header) return;
	initNavToggle(header);
	initDropdowns(header);
	initPromoBarOffset(header);
	initScrollState(header);
	markCurrentSection(header);
})();
