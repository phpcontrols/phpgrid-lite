<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Employee Edit</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("select * from employees ", "employeeNumber", "employees");
$dg->enable_edit("INLINE", "CRUD");         
$dg->display();
?>
</body>
</html>
