<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Data Formatting</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("select * from products", "productCode", "products");
$dg -> set_col_title("productCode", "Product Code");
$dg -> set_col_title("productName", "Product Name");
$dg -> set_col_title("productLine", "Product Line");

// display static Url
$dg -> set_col_link("productUrl");                                             

// display dynamic url. e.g.http://www.example.com/?productCode=101&foo=bar
$dg -> set_col_dynalink("productCode", "http://www.example.com/", "productCode", '&foo=bar');                                                                   

// column format
$dg -> set_col_currency("buyPrice");
$dg -> set_col_format("quantityInStock", "integer", array("thousandsSeparator" => ",",
                                                          "defaultValue" => "0"));   
// Below is equivalent to the following helper function
//$dg -> set_col_currency("MSRP", "$", '', ".",",", "2", "0.00");
$dg -> set_col_format("MSRP", 'currency', array("prefix" => "$",
                                                "suffix" => '',
                                                "thousandsSeparator" => ".",
                                                "decimalSeparator" => ",",
                                                "decimalPlaces" => 0,
                                                "defaultValue" => '0,000'));


/* Brazilian currency hack - replace .. with ,*/
$onLoadComplete = <<<ONLOADCOMPLETE
function(data){
        $("td[aria-describedby=products_MSRP]").each(function() {
            var text = $(this).text();
            text = text.replace("..", ",");
            $(this).text(text);
        });
    }
ONLOADCOMPLETE;
$dg->add_event('jqGridLoadComplete', $onLoadComplete);

// A hack to allow some countries that use a comma (,) instead of a decimal to indicate that separation
// This replace "," with "." as soon as "," is entered
$afterShowForm = <<<AFTERSHOWFORM
function (){
    $("#MSRP").attr("onkeyup","replacekomma()");
}
AFTERSHOWFORM;
$dg->add_event("jqGridAddEditAfterShowForm", $afterShowForm);


$dg->enable_edit();
$dg -> display();
?>

<script>

function replacekomma() {
    $("#MSRP").val($("#MSRP").val().replace(',','.'));
}
</script>


</body>
</html>
