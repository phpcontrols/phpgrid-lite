<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Datagrid Export</title>
</head>
<body> 

<?php

echo '<h2>EXCEL Export</h2>';

$dg2 = new C_DataGrid("select * from customers", "customerNumber", "Customers");
// PDF export
$dg2->enable_export('EXCEL');
$dg2->display();


?>
<script>
</script>
</body>
</html>