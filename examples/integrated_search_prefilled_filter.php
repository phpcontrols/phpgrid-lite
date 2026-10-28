<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>phpGrid - Integrated Search with Prefilled Filter</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>

<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");
$dg->set_col_edittype('status', 'select', 'Open:Open;Shipped:Shipped;Cancelled:Cancelled;Disputed:Disputed;On Hold:On Hold');
$dg->enable_search(true);
$dg->display();
?>

<script>
// Prefill the search toolbar, then run the search exactly as if the user had typed it.
// Toolbar fields are named gs_<grid name>_<column>. Wait until the first load has
// finished: jqGrid ignores a search started while it is still loading.
$(function(){
    var grid = $("#orders");

    grid.one("jqGridLoadComplete", function(){
        setTimeout(function(){
            grid[0].toggleToolbar();    // the toolbar is hidden by default; show the prefilled values
            $("#gs_orders_status").val("Shipped");
            $("#gs_orders_comments").val("Customer");
            grid[0].triggerToolbar();
        }, 0);
    });
});
</script>   

</body>
</html>