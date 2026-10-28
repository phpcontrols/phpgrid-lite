<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Customizing Edit Control Width & Height</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders"); 

// enable edit
$dg -> enable_edit("FORM", "CRUD"); 

$dg->set_col_edit_dimension("status", 40);
$dg->set_col_edit_dimension("comments", 40, 10);
$dg->set_col_edit_dimension("customerNumber", 10);

$dg -> display();
?>

</body>
</html>