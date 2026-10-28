<?php
use phpCtrl\C_DataGrid;

require_once("../conf.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Image Display</title>
<style>
html body {
    margin: 0;
    padding: 32px 24px;
    color: #0f172a;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.page-header h1 {
    margin: 0 0 4px;
    font-size: 24px;
    letter-spacing: -0.02em;
}
.page-header p {
    margin: 0 0 20px;
    color: #64748b;
    font-size: 14px;
}

/* thumbnails: a fixed square so rows line up whatever the image's size; contain shows the whole image */
#products td[aria-describedby="products_Image"] img {
    display: block;
    width: 56px;
    height: 56px;
    border-radius: 8px;
    background: #f1f5f9;
    object-fit: contain;
}
#products tr.jqgrow {
    height: 72px;
}
</style>
</head>
<body>

<header class="page-header">
    <h1>Image display</h1>
    <p>The <code>Image</code> column stores a file path; <code>set_col_img()</code> renders it as a thumbnail.</p>
</header>

<?php
$dg = new C_DataGrid("SELECT Image, productCode, productName, productLine, productVendor, quantityInStock, MSRP FROM products", "productCode", "products");

// the column holds paths relative to this page, e.g. SampleImages/motorcycle.jpg
$dg->set_col_img("Image");
// images stored in the database as base64 work the same way with a data: URL prefix:
// $dg->set_col_img("picture", "data:image/png;base64,");

$dg->set_col_title("Image", "Image");
$dg->set_col_title("productCode", "Code");
$dg->set_col_title("productName", "Product");
$dg->set_col_title("productLine", "Line");
$dg->set_col_title("productVendor", "Vendor");
$dg->set_col_title("quantityInStock", "In stock");
$dg->set_col_width("Image", 80);
$dg->set_col_width("productName", 260);
$dg->set_col_align("quantityInStock", "right");
$dg->set_col_align("MSRP", "right");
$dg->set_col_currency("MSRP", "$");
$dg->set_caption("Products");
$dg->enable_autowidth(true);

$dg->display();
?>

</body>
</html>
