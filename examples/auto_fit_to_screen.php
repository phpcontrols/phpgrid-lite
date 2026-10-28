<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auto-resize PHP Datagrid Like Excel Spreadsheet</title>
</head>
<body>


<div id="mydiv" style="width:100%">
<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");
$dg->enable_autowidth(true)->enable_autoheight(true);
$dg->set_pagesize(100); // need to be a large number
$dg->enable_edit('INLINE');
/* // uncomment to set width to parent DIV instead
$dg->before_script_end .= 'setTimeout(function(){$(window).bind("resize", function() {
        phpGrid_orders.setGridWidth($("#mydiv").width());
    }).trigger("resize");}, 0)';
*/
$dg -> display();
?>
<style>
    /* optional. removes page margin */
    body{margin:0px;}
</style>

<script>
// hide horizontal scroll bar 
$(function($){
	$('.ui-jqgrid .ui-jqgrid-bdiv').css('overflow-x', 'hidden');
});
</script>

</div>

</body>
</html>