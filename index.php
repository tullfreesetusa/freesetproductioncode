<?php
require_once("header.php");
?>
<html>
<head>
<title>
Freeset Order Management
</title>
<link rel="stylesheet" type="text/css" href="/css/standard.css">
</head>
<body>
<img src="img/logo.png">
<div class="front_div">
<h1>Customer Care</h1>
<ul>
<li><a href="customercare/">Customer Care System</a></li>
</ul>
</div>
<div class="front_div">
<h1>Printing</h1>
<ul>
<li><a href="printing/">Update Printing</a></li>
</ul>
</div>
<div class="front_div">
<h1>Production</h1>
<ul>
<li><a href="production/">Production Sheet</a></li>
<li><a href="production/addCuttingData.php">Cutting Data Entry</a></li>
<li><a href="production/addPrintingData.php">Printing Data Entry</a></li>
<li><a href="production/addSewingData.php">Sewing Data Entry</a></li>
</ul>
</div>
<div class="front_div">
<h1>Admin</h1>
<ul>
<li><a href="admin/listCatalog.php">Add/Update Catalog</a></li>
</ul>
</div>
</body>
</html>
<?php
require_once("footer.php");
?>