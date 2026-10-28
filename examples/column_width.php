<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP Datagrid - Modify Column Width</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");

$dg->set_col_width("orderNumber", 100);
$dg->set_col_width("comments",500);

$dg -> display();
?>

</body>
</html>