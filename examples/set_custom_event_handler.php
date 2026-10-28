<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");      
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Custom Event Handler | PHP Datagrid</title>
</head>
<body> 
<?php
$dg = new C_DataGrid("SELECT * FROM shippers", "ShipperID", "shippers");
$dg->set_col_hidden('ShipperID', false);



$onSelectRow1 = <<<ONSELECTROW
function(status, rowid)
{
	alert('ONSELECTROW: event 1');
	console.log(rowid);
	console.log(status);

	/* demonstrate URL redirect after selecting a row */
	// orderNumber = $('#orders').jqGrid('getCell',rowid,'orderNumber');
    // customerNumber = $('#orders').jqGrid('getCell',rowid,'customerNumber');
	// window.location = encodeURI("http://example.com/" + "?" + "orderNumber=" + orderNumber + "&customerNumber="+customerNumber);
}
ONSELECTROW;




$onSelectRow2 = <<<ONSELECTROW2
function(status, rowid)
{
	alert('ONSELECTROW: event 2');
}
ONSELECTROW2;




$rowattr = <<<ROWATTR
function (rowData, inputRowData) 
{
	return rowData.status === "OnHold" ? {style: "background-color:blue"} : {};
}
ROWATTR;




// post data another page after submit
$beforeSubmit = <<<BEFORESUBMIT
function (event, postData)
{
	console.log(event);		
	console.log(postData);	


	alert('BEFORESUBMIT: post customerNumber ' + postData.CompanyName + ' to another page through AJAX call');

    $.ajax({ url: 'test.php',
        data: {custNum: postData.CompanyName}, // replace CompanyName with your own 
        type: 'post',
        success: function(output) {
                    alert(output);
                }
        });
}
BEFORESUBMIT;





// post data another page after submit
$afterSubmit = <<<AFTERSUBMIT
function (event, status, postData)
{
	console.log(event);		
	console.log(status);	// e.g. {readytate: 4, responseText: {id:8}, status: 200, statusText: OK}
	console.log(postData);	// e.g. {column1: value, column2: value, ...}
    alert('AFTERSUBIT: post CompanyName ' + postData.CompanyName + ' to another page through AJAX call');

    $.ajax({ url: '/my/site',
        data: {custNum: postData.CompanyName}, // replace customerNumber with your own 
        type: 'post',
        success: function(output) {
                    alert(output);
                }
        });
}
AFTERSUBMIT;



// delete event
$jqGridAfterDelRow  = <<<AFTERDELROW
function (event, rowid)
{
    console.log(rowid)
    alert('AFTERDELROW:');

    $.ajax({ url: '/my/delete_other_child_rows.php',
        data: { rowid }, // r
        type: 'post',
        success: function(output) {
                    alert(output);
                }
        });
}
AFTERDELROW;



$dg->add_event("jqGridSelectRow", $onSelectRow1);
$dg->add_event("jqGridSelectRow", $onSelectRow2);
$dg->add_event("jqGridrowattr", $rowattr);
$dg->add_event("jqGridAddEditBeforeSubmit",$beforeSubmit);
$dg->add_event("jqGridAddEditAfterSubmit", $afterSubmit);
$dg->add_event("jqGridAfterDelRow", $jqGridAfterDelRow);
$dg->enable_edit('FORM')->set_dimension(600);
$dg->display();
?>


<script type="text/javascript">
$(function() {
        var grid = jQuery("#orders");

        grid.bind('jqGridAddEditAfterShowForm', () => {
            document.getElementById('status')?.addEventListener('change',
                () =>  console.log(this)
            );
        })
    });
</script>

</body>
</html>