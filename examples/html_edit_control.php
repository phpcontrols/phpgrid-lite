<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HTML Edit Controls</title>
</head>
<body> 

<?php
$dg = new C_DataGrid("select * from employees", "employeeNumber", "employees");
$dg -> set_col_title("employeeNumber", "Emp No.");
$dg -> set_col_title("lastName", "Last Name");
$dg -> set_col_title("firstName", "First Name");
$dg -> set_col_title("isActive", "Active?");
$dg -> set_col_format("email", "email");
$dg -> set_col_format("isActive", "checkbox");
$dg -> enable_edit("FORM", "CRUD");
$dg -> set_row_color("","");
$dg -> set_col_hidden('employeeNumber',true);
$dg->enable_advanced_search(true);
$dg -> set_col_edittype("isActive", "checkbox","1:0");
$dg -> set_col_edittype("reportsTo", "select", "Select employeeNumber, concat(firstName, ' ', lastName) from employees", true);
// autocomplete that looks cities up with ajax (sample-select-dataUrl.php) as you type,
// the way you would for a table too large to send to the page in full
$dg -> set_col_edittype("officeCode", "autocomplete", 
	array('sql' => 'Select officeCode,city from offices', 
		  'ajaxDataUrl' => 'sample-select-dataUrl.php'), false);

// - or -
//$dg -> set_col_edittype("officeCode", "autocomplete", "1:San Francisco;2:Boston;3:NYC;4:Paris;5:Tokyo;6:Sydney;7:London");

$dg->enable_search(true);

$sizeUpSelect = <<<BEFORESHOWFORM
function(status, rowid)
{
	$("#reportsTo").attr("size", 10);
}
BEFORESHOWFORM;
$dg->add_event('jqGridAddEditBeforeShowForm', $sizeUpSelect);


$dg->set_grid_property(array("footerrow"=>true));
$loadComplete = <<<LOADCOMPLETE
function ()
{
    var colSum = $('#employees').jqGrid('getCol', 'isActive', false, 'sum');
    $('#employees').jqGrid('footerData', 'set', { firstName: 'Active Count:', 'isActive': colSum });
    $('tr.footrow td[aria-describedby=employees_isActive]').text(colSum);
}
LOADCOMPLETE;
$dg->add_event("jqGridLoadComplete", $loadComplete);


$dg->set_col_property('jobTitle', array('editable'=>false,'hidedlg'=>true));

$dg -> display();
?>

<script type="text/javascript">
    $(function() {
        var grid = jQuery("#employees");
        grid[0].toggleToolbar();
    });
</script>

<style>
.ui-widget-content.footrow.footrow-ltr{
	font-size:10px;
}
</style>
</body>
</html>