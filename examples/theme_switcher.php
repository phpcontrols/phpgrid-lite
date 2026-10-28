<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");

$themes = array(
    'modern'     => 'Modern',
    'bootstrap5' => 'Bootstrap 5',
    'bootstrap4' => 'Bootstrap 4',
    'bootstrap'  => 'Bootstrap 3',
);
$theme = isset($_GET['theme']) && isset($themes[$_GET['theme']]) ? $_GET['theme'] : 'modern';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Theme Switcher</title>
<style>
:root { --page-bg: #f1f5f9; --page-text: #0f172a; }
:root[data-bs-theme="dark"] { --page-bg: #020617; --page-text: #e2e8f0; }
html body { margin: 0; padding: 24px 16px; background: var(--page-bg) !important; color: var(--page-text); font-family: system-ui, -apple-system, "Segoe UI", sans-serif; }
.theme-picker { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 16px; margin-bottom: 16px; font-size: 14px; }
.theme-picker select { padding: 6px 10px; font: inherit; }
</style>
</head>
<body>

<form class="theme-picker" method="get" action="theme_switcher.php">
  <label for="theme">Theme</label>
  <select id="theme" name="theme" onchange="this.form.submit()">
    <?php foreach ($themes as $value => $label): ?>
    <option value="<?php echo htmlspecialchars($value); ?>"<?php echo $value === $theme ? ' selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
    <?php endforeach; ?>
  </select>
  <noscript><button type="submit">Apply</button></noscript>
  <?php if ($theme === 'modern'): ?>
  <label><input type="checkbox" id="dark-toggle"> Dark mode</label>
  <?php endif; ?>
</form>

<script>
// the modern theme reads dark tokens from data-bs-theme="dark" on any ancestor
(function () {
  var toggle = document.getElementById("dark-toggle");
  if (!toggle) { return; }
  var KEY = "phpgrid-theme-switcher-dark";
  function apply(isDark) {
    document.documentElement.setAttribute("data-bs-theme", isDark ? "dark" : "light");
  }
  try { toggle.checked = localStorage.getItem(KEY) === "1"; } catch (e) { /* storage unavailable */ }
  apply(toggle.checked);
  toggle.addEventListener("change", function () {
    apply(toggle.checked);
    try { localStorage.setItem(KEY, toggle.checked ? "1" : "0"); } catch (e) { /* storage unavailable */ }
  });
}());
</script>

<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");
$dg->set_theme($theme);
$dg->set_caption("Orders");
$dg->set_col_title("orderNumber", "Order No.");
$dg->set_col_title("orderDate", "Order Date");
$dg->set_col_title("requiredDate", "Required");
$dg->set_col_title("shippedDate", "Shipped");
$dg->set_col_title("customerNumber", "Customer No.");
$dg->set_col_hidden("contractDateTime")->set_col_hidden("logTime");
$dg->enable_edit('FORM', 'CRUD');
$dg->enable_search(true);
$dg->enable_export('CSV');
$dg->set_multiselect(true);
$dg->set_pagesize(15);
$dg->enable_autowidth(true);
$dg->display();
?>

</body>
</html>
