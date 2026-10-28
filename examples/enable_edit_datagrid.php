<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Enable Datagrid Edit</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("SELECT `orderNumber`, `orderDate`, `requiredDate`, `shippedDate`, `status`, `comments`, `customerNumber` FROM `orders`", "`orderNumber`", "orders");

// change column titles
$dg -> set_col_title("orderNumber", "Order No.");
$dg -> set_col_title("orderDate", "Order Date");
$dg -> set_col_title("shippedDate", "Shipped Date");
$dg -> set_col_title("customerNumber", "Customer No.");

// change date format of a column
//$dg -> set_col_date("orderDate", "Y-m-d", "n/j/Y", "yy-mm-dd");

// change a date field to regular text field (no datepicker)
// $dg->set_col_property('orderDate', array('editoptions'=>array('dataInit'=>'')));

$dg -> enable_edit("FORM", 'U');

//$dg->before_script_end = '$.extend($.jgrid.edit, {closeAfterEdit: false}); ';
$dg -> display();
?>

<script type="text/javascript">
// Uncomment to keep edit form staying open after save
/*
jQuery(document).ready(function($){
	var grid = $('#orders')[0];
	$(grid).jqGrid('setGridParam',{
			ondblClickRow: function(rowid) {
			    $(this).jqGrid('editGridRow', rowid,
			        {   recreateForm:true,
			            closeAfterEdit:false,
			            closeOnEscape:true,
			            reloadAfterSubmit:false})
			}
		});
});
*/
</script>
</body>
</html>