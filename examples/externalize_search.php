<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Externalize Search</title>
<style>
/* page and search bar share the grid's "modern" theme tokens so they read as one surface */
:root {
  --page-bg: #f1f5f9;
  --page-text: #0f172a;
  --page-muted: #64748b;
  --page-surface: #ffffff;
  --page-border: #e2e8f0;
  --page-border-strong: #cbd5e1;
  --page-accent: #4f46e5;
  --page-accent-hover: #4338ca;
  --page-accent-ring: rgb(79 70 229 / 28%);
  --page-radius: 12px;
  --page-radius-control: 8px;
  --page-font: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --page-duration: 140ms;
}

html body {
  margin: 0;
  padding: 24px 16px;
  background: var(--page-bg);
  color: var(--page-text);
  font-family: var(--page-font);
}

.search-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  gap: 12px;
  margin: 0 0 16px;
  padding: 16px;
  border: 1px solid var(--page-border);
  border-radius: var(--page-radius);
  background: var(--page-surface);
  box-shadow: 0 1px 2px rgb(15 23 42 / 5%);
}
.search-bar h1 {
  flex-basis: 100%;
  margin: 0 0 4px;
  font-size: 16px;
  font-weight: 600;
  letter-spacing: -0.01em;
}
.search-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1 1 200px;
}
.search-field--compact {
  flex: 0 0 auto;
}
.search-field label {
  color: var(--page-muted);
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.03em;
  text-transform: uppercase;
}
.search-field input,
.search-field select {
  box-sizing: border-box;
  height: 36px;
  padding: 0 10px;
  border: 1px solid var(--page-border-strong);
  border-radius: var(--page-radius-control);
  background: var(--page-surface);
  color: var(--page-text);
  font: inherit;
  font-size: 14px;
  transition: border-color var(--page-duration), box-shadow var(--page-duration);
}
.search-field input:focus,
.search-field select:focus {
  border-color: var(--page-accent);
  box-shadow: 0 0 0 3px var(--page-accent-ring);
  outline: 0;
}
.search-actions {
  display: flex;
  gap: 8px;
}
.search-actions button {
  height: 36px;
  padding: 0 16px;
  border: 1px solid var(--page-border-strong);
  border-radius: var(--page-radius-control);
  background: var(--page-surface);
  color: var(--page-text);
  font: inherit;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color var(--page-duration), border-color var(--page-duration);
}
.search-actions button:hover {
  border-color: var(--page-muted);
  background: var(--page-bg);
}
.search-actions button[type="submit"] {
  border-color: var(--page-accent);
  background: var(--page-accent);
  color: #fff;
}
.search-actions button[type="submit"]:hover {
  border-color: var(--page-accent-hover);
  background: var(--page-accent-hover);
}
.search-actions button:focus-visible {
  outline: 2px solid var(--page-accent);
  outline-offset: 2px;
}

@media (max-width: 600px) {
  .search-field--compact,
  .search-actions {
    flex: 1 1 100%;
  }
  .search-actions button {
    flex: 1;
  }
}
@media (prefers-reduced-motion: reduce) {
  .search-field input,
  .search-field select,
  .search-actions button {
    transition: none;
  }
}
</style>
</head>
<body>

<form class="search-bar" id="SearchPanel" role="search" aria-labelledby="search-title">
  <h1 id="search-title">Search orders</h1>
  <div class="search-field">
    <label for="status">Status contains</label>
    <input type="text" id="status" placeholder="e.g. Shipped" autocomplete="off" />
  </div>
  <div class="search-field search-field--compact">
    <label for="op">Match</label>
    <select id="op">
      <option value="AND">All (AND)</option>
      <option value="OR">Any (OR)</option>
    </select>
  </div>
  <div class="search-field">
    <label for="customerNumber">Customer # contains</label>
    <input type="text" id="customerNumber" placeholder="e.g. 141" inputmode="numeric" autocomplete="off" />
  </div>
  <div class="search-actions">
    <button type="submit" id="searchbtn">Search</button>
    <button type="reset" id="clearbtn">Clear</button>
  </div>
</form>

<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");

// change column titles
$dg -> set_col_title("orderNumber", "Order No.");
$dg -> set_col_title("orderDate", "Order Date");
$dg -> set_col_title("shippedDate", "Shipped Date");
$dg -> set_col_title("customerNumber",  "Customer No.");

// hide a column
$dg -> set_col_hidden("requiredDate");

// change default caption
$dg -> set_caption("Orders List");

$dg->enable_edit('FORM', 'CRUD');
$dg->set_col_edittype('status', 'select', 'Open:Open;Shipped:Shipped;Cancelled:Cancelled;Disputed:Disputed;On Hold:On Hold');

$dg-> set_col_readonly("orderNumber");
$dg->enable_kb_nav(true);
$dg->enable_autowidth(true);   // fill the page so columns aren't shrunk to fit a fixed width
$dg->set_col_width('comments', 320);   // the free-text column gets the widest share

$dg -> display();
?>

<!-- after display(): the grid's includes load jQuery -->
<script>
function searchGrid(){
	// skip empty boxes so "Shipped AND <blank>" still matches; JSON.stringify escapes quotes in user input
	var rules = [
		{field: "status", op: "cn", data: $("#status").val().trim()},
		{field: "customerNumber", op: "cn", data: $("#customerNumber").val().trim()}
	].filter(function (rule) { return rule.data !== ""; });

	phpGrid_orders.setGridParam({
		postData: {
			filters: JSON.stringify({groupOp: $("#op").val(), rules: rules})
		},
		search: rules.length > 0,
		page: 1
	}).trigger("reloadGrid");
}

jQuery(function ($) {
	$("#SearchPanel")
		.on("submit", function (e) {
			e.preventDefault();
			searchGrid();
		})
		// reset clears the boxes first, then the grid reloads with no filter
		.on("reset", function () {
			setTimeout(searchGrid, 0);
		});
});
</script>

</body>
</html>
