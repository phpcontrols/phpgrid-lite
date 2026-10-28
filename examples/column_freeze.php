<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>phpGrid - Column Freeze with Cell Edit</title>
</head>
<body>
<style type="text/css">
    .ui-jqgrid tr.jqgrow td {
        white-space:nowrap;
    }
</style>
<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");
$dg->set_dimension(500, 400, false);
$dg->set_col_frozen('orderNumber');
$dg->enable_search(true);
$dg->enable_autoresizeOnLoad(false);
$dg->enable_edit('CELL', 'CRUD');
$dg->enable_search(true);
$dg -> display();
?>

<script type="text/javascript">
$(function() {
    var grid = jQuery("#orders");
    grid[0].toggleToolbar();

	setTimeout(function(e){
	    //Set the page to the last page
		$("#orders").trigger("reloadGrid",[{page:$("#orders").getGridParam('lastpage')}]);
	  }, 500);

	$('#addNew').on('click', function(){
		$("#orders").jqGrid('editGridRow', "new", {url:'../edit.php?dt=json&gn=orders&oper=add'} );
	})	
});


</script>

<button id="addNew">Add New</button>
	
</body>
</html>