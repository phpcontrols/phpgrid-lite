<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0"/>
<title>A Basic PHP Datagrid</title>
</head>

<body>
<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");
$dg -> display();
?>


</body>
</html>