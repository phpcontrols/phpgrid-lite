<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Multiple Datagrids</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", 'orders');
$dg->enable_edit("FORM", "CRUD")->enable_autowidth(true);
$dg->display();

$dg2 = new C_DataGrid("select * from employees", "employeeNumber", "employees");
$dg2->enable_edit("FORM", "CRUD")->enable_autowidth(true);
$dg2->display();

$dg3 = new C_DataGrid("select * from offices", "officeCode", "offices");
$dg3->enable_edit("FORM", "CRUD")->enable_autowidth(true);
$dg3->display();

$dg4 = new C_DataGrid("select * from productlines", "productLine", "productlines");
$dg4->enable_edit("FORM", "CRUD")->enable_autowidth(true);
$dg4->set_theme('flick');
$dg4->display();
?>

<script>
	$(function(){
		 $('#orders').jqGrid('setGridHeight',$(window).innerHeight()/10);
		 $('#employees').jqGrid('setGridHeight',$(window).innerHeight()/10);
		 $('#offices').jqGrid('setGridHeight',$(window).innerHeight()/10);
		 $('#productlines').jqGrid('setGridHeight',$(window).innerHeight()/10);

	})
</script>
</body>
</html>