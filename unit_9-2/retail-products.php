<?php
// Fetch products from the database before rendering HTML
// Use the provided connection file; suppress its echo output if any
ob_start();
require_once __DIR__ . '/dbConnect1.php';
ob_end_clean();
//1. Your program should pull all rows from the table.
//Sort in Descending order by product name. 
$products = [];
try {
    if (isset($pdo) && $pdo instanceof PDO) {
        $sql = "SELECT * FROM wdv341_products ORDER BY product_name DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    // In a classroom setting, it's fine to keep this simple
    $products = [];
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>DMACC Electronics Store</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *,:after,:before{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}body{font:normal 15px/25px 'Open Sans',Arial,Helvetica,sans-serif;color:#444;text-align:left}h1,h2,h3{font-weight:400}h1{font:normal 40px/120px 'Open Sans',Arial,Helvetica,sans-serif;text-align:center;color:#444;margin:0}h1 span{color:#484c9b}h2{font-size:25px;line-height:30px;color:#484c9b;margin:50px 0 10px}h3{font-size:18px;line-height:35px;margin:50px 0 0}a{color:#484c9b;text-decoration:none}a:focus,a:hover{text-decoration:underline}p{margin:0 0 2rem}p span{color:#aaa}header{width:98%;margin:40px auto 0;border-bottom:1px solid #ddd;padding-bottom:40px;text-align:center}header p{margin:0}section{width:95%;max-width:910px;margin:40px auto}pre{background:#f9f9f9;padding:10px;font-size:12px;border:1px solid #eee;white-space:pre-wrap;border-radius:10px}table{border:1px solid #eee;background:#f9f9f9;width:100%;border-collapse:collapse;border-spacing:0;margin-bottom:3rem}thead{background:#5965af;color:#fff}tbody tr td,thead td{padding:.5rem .75rem}tbody tr:nth-child(even){background:#efefef}tbody tr td:first-child{padding-left:1.25rem}tbody tr td:first-child,tbody tr td:nth-child(3),thead td:first-child,thead td:nth-child(3){width:15%}tbody tr td:nth-child(2),thead td:nth-child(2){width:20%}tbody tr td:last-child,thead td:last-child{width:50%}@media only screen and (min-width:768px){body{font-size:20px;line-height:30px}h2{font-size:30px;line-height:45px}h3{font-size:22px;line-height:45px;margin-top:50px}p{margin-bottom:2rem}h1{font-size:60px}pre{padding:20px;font-size:15px}}
        section {display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem; max-width: 1200px;}
        .productBlock{width:calc(100% / 6 - 1rem);display:inline-block;border:none;padding:1rem;background:#efefef;border-radius:10px;font-size:.875rem;line-height:1.5}
        .productImage img{display:block;margin-left:auto;margin-right:auto;width:100%;height:auto}
        .productName{font-size:large;margin:1rem 0 .5rem;text-align:left}
        .productDesc{margin-left:10px;margin-right:10px;margin:0}
        .productPrice{font-size:larger;color:#00f;margin:.5rem 0;text-align:left}
        .productStatus{font-weight:bolder;color:#2f4f4f;margin:.5rem 0;text-align:left}
        .productInventory{margin:.5rem 0;text-align:left}
        .productLowInventory{color:red}
    </style>
</head>

<div class="container">
  <!-- Top link to return to the course home page -->
    <div style="margin:0 0 12px 0;"><a class="toplink" href="http://kickshunter.com/WDV341/wdv341.php" target="_blank" rel="noopener">&larr; Back to WDV341 Home</a></div>
    <div style="margin:0 0 12px 0;"><a class="toplink" href="https://github.com/huntlovan/php_341/tree/main/unit_9-1" target="_blank" rel="noopener">&larr; PHP Formatted Dynamic Output github repo</a></div>
<h2>Event Listings</h2>

<body>
    <header>
        <h1>DMACC Electronics Store!</h1>
        <p>Products for your Home and School Office</p>
    </header>
    <section>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $row): ?>
                <?php
                    $name = htmlspecialchars($row['product_name'] ?? '');
                    $desc = htmlspecialchars($row['product_description'] ?? '');
                    $price = isset($row['product_price']) ? number_format((float)$row['product_price'], 2) : '0.00';
                    $image = htmlspecialchars($row['product_image'] ?? '');
                    $status = trim((string)($row['product_status'] ?? ''));
                    $inStock = (int)($row['product_inStock'] ?? 0);
                    $invClass = 'productInventory' . ($inStock < 10 ? ' productLowInventory' : '');
                ?>
                <div class="productBlock">
                    <div class="productImage">
                        <img src="<?='productImages/' . rawurlencode($image)?>" alt="<?=$name?>">
                    </div>
                    <p class="productName"><?=$name?></p>
                    <p class="productDesc"><?=$desc?></p>
                    <p class="productPrice">$<?=$price?></p>
                    <?php if ($status !== ''): ?>
                        <p class="productStatus"><?=htmlspecialchars($status)?></p>
                    <?php endif; ?>
                    <p class="<?=$invClass?>"><?="{$inStock} In Stock!"?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </section>

</body>

</html>
