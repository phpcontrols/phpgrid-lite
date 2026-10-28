<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP Datagrid - Advanced Search</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("SELECT * FROM orders", "orderNumber", "orders");

$dg -> set_col_property("orderDate",
                        array("formatter"=>"date",
                            "sorttype"=>"date",
                            "searchoptions"=>
                                array("dataInit"=>"###datePick###")));

$dg->enable_advanced_search(true);
$dg -> set_col_property("customerNumber",
                        array("formatter"=>"integer",
                            "sorttype"=>"integer"));

$dg->set_col_hidden('comments');
$dg -> set_col_property("comments", array("searchoptions"=> array("searchhidden" => true)));


$dg->enable_export('EXCEL');
$dg -> display();  
?>

<script>
    datePick = function(elem)
    {
        $(elem).datepicker({
            dateFormat: 'yy-mm-dd',
            autoSize: true,
            changeYear: true,
            changeMonth: true,
            showButtonPanel: true,
            showWeek: true
        });
    }
</script>

</body>
</html>