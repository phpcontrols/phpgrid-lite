<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP Datagrid - Locale Setting</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");
$dg->set_locale('es');        
$dg->enable_edit('FORM');

$dg -> set_col_property("requiredDate", 
                             array("formatter"=>"date",
                                   "formatoptions"=>array("srcformat"=>"Y-m-d","newformat"=>"m/d/Y"),
                                   "editoptions"=>array(
                                        "dataInit"=>"function(el) {
                                        	$(el).datepicker( $.datepicker.regional[ 'es' ] )
                                        }")));
$dg -> display();  
?>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/i18n/jquery-ui-i18n.min.js" type="text/javascript"></script>
</body>
</html>