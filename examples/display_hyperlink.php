<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Display Static Hyperlink</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("select * from products", "productCode", "productcode");
$dg -> set_col_title("productCode", "Product Code");
$dg -> set_col_title("productName", "Product Name");
$dg -> set_col_title("productLine", "Product Line");

// display static Url
$dg -> set_col_link("productUrl");                                             
                                                                                     
$dg -> display();
?>

</body>
</html>
