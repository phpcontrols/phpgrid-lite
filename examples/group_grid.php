<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Simple Grouping with data</title>
</head>
<body>

<?php
$dg = new C_DataGrid('SELECT customerNumber,customerName, city, state, creditLimit
FROM customers', 'customerNumber', 'customers'); // Basic
// var_dump($dg);

$dg -> set_sortname('state','ASC'); // Equivalent to "Order By"
$dg -> set_group_properties('state', false, true, true, false);
//$dg -> set_group_summary('state','count');
$dg -> set_group_summary('creditLimit','sum');
$dg -> set_caption('Test Grouping');


$dg -> set_query_filter("state is not null"); // Where clause

$dg -> set_pagesize(60)->enable_autoheight(true); // display up to 60 rows in a page

$dg -> display();

?>
<style>
    tr[jqfootlevel="0"]  td[aria-describedby=customers_state]{
       color:transparent;
    }
</style>
</body>
</html>