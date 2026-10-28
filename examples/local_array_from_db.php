<?php
use phpCtrl\C_DataGrid;
use phpCtrl\C_Database;

require_once("../conf.php");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0"/>
<title>Local Array from Database</title>
<body>
<?php

$sql = 'select
s.supplierCode, s.supplierZip, s.supplierPhonenumber,
spl.productLineNo, spl.productLine,
p.productName, p.MSRP
from suppliers s
inner join supplierproductlines spl on s.supplierName = spl.supplierName
inner join products p on s.supplierZip = p.supplierZip';

$db = new C_DataBase(PHPGRID_DB_HOSTNAME, PHPGRID_DB_USERNAME, PHPGRID_DB_PASSWORD, PHPGRID_DB_NAME, PHPGRID_DB_TYPE,PHPGRID_DB_CHARSET);
$results = $db->db_query($sql);
$data1 = array();
$count = 0;

while($row = $db->fetch_array_assoc($results)) {
	$data_row = array();
	for($i = 0; $i < $db->num_fields($results); $i++) {
		$col_name = $db->field_name($results, $i);
		$data1[$count][$col_name] = $row[$col_name];
	}
	$count++;
}

$dg = new C_DataGrid($data1, "id", "data1");
$dg->enable_edit('INLINE');
$dg->display();
?>
</body>
</html>