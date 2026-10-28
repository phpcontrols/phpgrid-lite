<?php
// Demo Explorer: every example page in this folder, shown in a frame with its source.
// Styled to match phpgrid.com; the shared header comes from explorer/site.css + site.js.

// first one present is shown on load (the Lite build ships without coinmarket_clone.php)
const PREFERRED_DEMOS = array('coinmarket_clone.php', 'basic_phpgrid.php');

// Example pages only: no folders, no helper/AJAX targets, nothing hidden.
function listDemos($dir) {
    $ignore = array('index.php', 'save_selected_rows.php', 'save_virtual_column.php', 'save_local_array.php', 'tabbed_grid_loader.php');
    $demos = array();
    foreach (scandir($dir) as $file) {
        if ($file[0] === '.' || is_dir($dir . DIRECTORY_SEPARATOR . $file)) { continue; }
        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'php') { continue; }
        if (in_array(strtolower($file), $ignore, true)) { continue; }
        $demos[] = $file;
    }
    return $demos;
}

// "coinmarket_clone.php" -> "Coinmarket Clone"
function demoTitle($file) {
    $special = array('wysiwyg.php' => 'WYSIWYG Editor');
    if (isset($special[$file])) { return $special[$file]; }
    $name = ucwords(str_replace(array('_', '-'), ' ', basename($file, '.php')));
    return str_replace('Phpgrid', 'phpGrid', $name);
}

// jQuery UI themes a demo's grid can be switched to (css/<name>/jquery-ui.css)
function listThemes($dir) {
    $themes = array();
    foreach (scandir($dir) as $name) {
        if ($name[0] !== '.' && is_file($dir . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'jquery-ui.css')) {
            $themes[] = $name;
        }
    }
    return $themes;
}

$demos = listDemos(__DIR__);
$preferred = array_values(array_intersect(PREFERRED_DEMOS, $demos));
define('DEFAULT_DEMO', $preferred ? $preferred[0] : $demos[0]);

// ?file=<demo>: the demo's highlighted source. Only files from the list above are served.
if (isset($_GET['file'])) {
    $file = basename((string)$_GET['file']);
    if (!in_array($file, $demos, true)) {
        http_response_code(404);
        die('Unknown demo.');
    }
    $code = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file);

    // never show connection details
    $code = preg_replace('/("hostname"\s*=>\s*")[^"]+"/i', '$1HOSTNAME"', $code);
    $code = preg_replace('/("username"\s*=>\s*")[^"]+"/i', '$1USERNAME"', $code);
    $code = preg_replace('/("password"\s*=>\s*")[^"]*"/i', '$1PASSWORD"', $code);

    // colors for the dark code window
    ini_set('highlight.default', '#e8e9ee');
    ini_set('highlight.keyword', '#a9acf2');
    ini_set('highlight.string',  '#9fd8a8');
    ini_set('highlight.comment', '#8a8f9e');
    ini_set('highlight.html',    '#9a9eab');
    highlight_string($code);
    exit;
}

// The other phpControls component demos, shown as tabs above the demo title
const COMPONENT_DEMOS_URL = 'https://demo.phpcontrols.com/public/demo/';
$components = array(
    array('label' => 'Datagrid',    'href' => 'index.php', 'current' => true),
    array('label' => 'Chart',       'href' => COMPONENT_DEMOS_URL . '?ctrl=chart'),
    array('label' => 'Form',        'href' => COMPONENT_DEMOS_URL . '?ctrl=form'),
    array('label' => 'Calendar',    'href' => 'https://www.quickproductdemo.com/phpeventcal/', 'external' => true),
    array('label' => 'File',        'href' => COMPONENT_DEMOS_URL . '?ctrl=file'),
    array('label' => 'Layout',      'href' => COMPONENT_DEMOS_URL . '?ctrl=layout'),
    array('label' => 'Sample apps', 'href' => COMPONENT_DEMOS_URL . '?ctrl=apps'),
);

$themes = listThemes(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'css');
$h = function ($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); };
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Demo Explorer | phpGrid</title>
<meta name="description" content="Live phpGrid examples with their PHP source: editing, search, export, master/detail, subgrids, themes and more.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600&display=swap">
<link rel="stylesheet" href="explorer/site.css">
<link rel="stylesheet" href="explorer/explorer.css">
<script defer src="explorer/site.js"></script>
<script defer src="explorer/explorer.js"></script>
</head>
<body>
<a class="skip-link" href="#demo-stage">Skip to the demo</a>

<header class="site-header">
	<div class="site-header__inner">
		<a class="brand" href="https://phpgrid.com/" aria-label="phpGrid home">
			<svg class="brand__mark" viewBox="0 0 32 32" aria-hidden="true"><rect width="32" height="32" rx="8"/><rect x="8" y="8" width="7" height="7" rx="1.5"/><rect x="17" y="8" width="7" height="7" rx="1.5" opacity=".7"/><rect x="8" y="17" width="7" height="7" rx="1.5" opacity=".7"/><rect x="17" y="17" width="7" height="7" rx="1.5"/></svg>
			<span>phpGrid</span>
		</a>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
			<span class="sr-only">Menu</span>
			<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
		</button>
		<nav id="site-nav" class="site-nav" aria-label="Main navigation">
			<ul class="site-nav__links">
				<li><a href="https://phpgrid.com/#features">Features</a></li>
				<li class="has-menu">
					<button class="site-nav__menu-btn is-current" type="button" aria-expanded="false" aria-controls="nav-demos">
						Demos
						<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<ul id="nav-demos" class="site-nav__menu">
						<li><a href="index.php" aria-current="page">Demo Explorer</a></li>
						<li><a href="https://phpgrid.com/example/">Examples &amp; tutorials</a></li>
						<li><a href="https://www.loom.com/share/folder/6831fd6e97274cc6b7b36249b1fef156">Video tutorials</a></li>
					</ul>
				</li>
				<li class="has-menu">
					<button class="site-nav__menu-btn" type="button" aria-expanded="false" aria-controls="nav-docs">
						Docs
						<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
					</button>
					<ul id="nav-docs" class="site-nav__menu">
						<li><a href="https://phpgrid.com/documentation/">Documentation</a></li>
						<li><a href="https://phpgrid.com/knowledgebase/knowledgebase.html">Knowledge Base</a></li>
					</ul>
				</li>
				<li><a href="https://phpgrid.com/#gallery">Gallery</a></li>
				<li><a href="https://phpgrid.com/contactus/">Contact</a></li>
			</ul>
			<div class="site-nav__cta">
				<a class="nav-btn nav-btn--outline" href="https://phpgrid.com/download/">Free Lite</a>
				<a class="nav-btn nav-btn--primary" href="https://phpgrid.com/download/index3b85.html?ref=demo">Download 7.7</a>
			</div>
		</nav>
	</div>
</header>

<main class="explorer" id="main">
	<section class="explorer-hero" aria-labelledby="demo-title">
		<nav class="explorer-wrap component-bar" aria-label="phpControls component demos">
			<ul class="component-bar__list">
				<?php foreach ($components as $c): ?>
				<li><a href="<?php echo $h($c['href']); ?>"<?php
					echo !empty($c['current']) ? ' aria-current="page"' : '';
					echo !empty($c['external']) ? ' target="_blank" rel="noopener"' : '';
				?>><?php echo $h($c['label']); ?><?php if (!empty($c['external'])): ?><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 5h5v5M19 5l-8 8M17 14v5H5V7h5"/></svg><span class="sr-only"> (opens in a new tab)</span><?php endif; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<div class="explorer-wrap explorer-hero__inner">
			<div>
				<p class="eyebrow">Demo Explorer <span aria-hidden="true">&middot;</span> <?php echo count($demos); ?> live examples</p>
				<h1 id="demo-title" class="explorer-title"><?php echo $h(demoTitle(DEFAULT_DEMO)); ?></h1>
				<p class="explorer-lede">Every demo is a plain PHP page running on the sample database. Its source is right below it.</p>
			</div>
			<div class="explorer-hero__tools">
				<label class="theme-picker">
					<span class="theme-picker__label">Grid theme</span>
					<select id="theme-select" aria-describedby="theme-note">
						<option value="">As in the demo</option>
						<?php foreach ($themes as $t): ?>
						<option value="<?php echo $h($t); ?>"><?php echo $h(ucwords(str_replace('-', ' ', $t))); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<p class="theme-picker__note" id="theme-note" hidden>This demo uses the modern theme, which has no jQuery UI theme to switch.</p>
			</div>
		</div>
	</section>

	<div class="explorer-wrap explorer-body">
		<aside class="demo-nav" aria-label="Demos">
			<div class="demo-nav__search">
				<label class="sr-only" for="demo-filter">Filter demos</label>
				<svg class="demo-nav__search-ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>
				<input id="demo-filter" type="search" placeholder="Filter <?php echo count($demos); ?> demos" autocomplete="off">
			</div>
			<ul class="demo-nav__list" id="demo-list">
				<?php foreach ($demos as $file): ?>
				<li><a href="<?php echo $h($file); ?>" target="demo_frame" data-file="<?php echo $h($file); ?>"<?php echo $file === DEFAULT_DEMO ? ' aria-current="page"' : ''; ?>><?php echo $h(demoTitle($file)); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<p class="demo-nav__empty" id="demo-empty" hidden>No demos match.</p>
		</aside>

		<div class="explorer-main">
			<section class="stage" id="demo-stage" aria-label="Demo">
				<div class="stage__bar">
					<span class="stage__dots" aria-hidden="true"><span></span><span></span><span></span></span>
					<code class="stage__file" id="demo-file">examples/<?php echo $h(DEFAULT_DEMO); ?></code>
					<button class="stage__btn" type="button" id="demo-reload" title="Reload the demo">
						<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11a8 8 0 1 0-2.3 5.7M20 4v7h-7"/></svg>
						<span class="sr-only">Reload the demo</span>
					</button>
					<a class="stage__btn stage__btn--text" id="demo-open" href="<?php echo $h(DEFAULT_DEMO); ?>" target="_blank" rel="noopener">
						Open in new tab
						<svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 5h5v5M19 5l-8 8M17 14v5H5V7h5"/></svg>
					</a>
				</div>
				<iframe class="stage__frame" id="demo_frame" name="demo_frame" title="Demo" src="<?php echo $h(DEFAULT_DEMO); ?>"></iframe>
			</section>

			<section class="source" aria-labelledby="source-title">
				<div class="source__bar">
					<span class="stage__dots" aria-hidden="true"><span></span><span></span><span></span></span>
					<h2 id="source-title" class="source__title">Source <code id="source-file"><?php echo $h(DEFAULT_DEMO); ?></code></h2>
					<button class="source__copy" type="button" id="source-copy">Copy</button>
				</div>
				<div class="source__code" id="source-code" aria-live="polite"></div>
			</section>
		</div>
	</div>
</main>

<footer class="explorer-footer">
	<div class="explorer-wrap explorer-footer__inner">
		<p>&copy; <?php echo date('Y'); ?> Kayson Group Inc.</p>
		<ul>
			<li><a href="https://phpgrid.com/">phpgrid.com</a></li>
			<li><a href="https://phpgrid.com/documentation/">Documentation</a></li>
			<li><a href="https://phpgrid.com/contactus/">Contact</a></li>
		</ul>
	</div>
</footer>
</body>
</html>
