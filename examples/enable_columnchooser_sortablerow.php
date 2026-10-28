<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>phpGrid Ajax File Upload (Form edit only)</title>
</head>
<body>

<?php
$dg = new C_DataGrid("select * from products", "productCode", "products");

$dg->enable_columnchooser(true);
$dg->set_sortablerow(true);

$dg -> display();
?>

<script>
// additional code snippet that saves selected columns and posts them to server side.
$(function($){
	jQuery("#products").jqGrid('columnChooser', {
	   done : function (perm) {
	      if (perm) {
	          // "OK" button are clicked	          
	          cols = $('#products').jqGrid('getGridParam', "colModel");
	          colChosen = [];
	          for (i = 0; i < cols.length; i++) { 
	          	 if(!cols[i]['hidden']){
	          	 	colChosen.push(cols[i]);
	          	 }
	          }

	          $.ajax({
				  url: 'http://example.com/save_column_chosen.php',
				  data: {selectedCols: colChosen},
				  type: 'POST',
				  dataType: 'JSON'
				});

	      } else {
	          // we can do some action in case of "Cancel" button clicked
	      }
	   }
	});
	
})
</script>

</body>
</html>