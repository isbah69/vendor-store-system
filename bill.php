<?php
include_once "connection.php";

if (!isset($_GET['sale_id'])) {
    echo json_encode(['error' => 'No sale ID provided']);
    exit;
}

$sale_id = intval($_GET['sale_id']);

// 🔹 Fetch sale info + company name
$saleQuery = mysqli_query($con, "
    SELECT s.*, c.name AS company_name 
    FROM sales s 
    LEFT JOIN companies c ON s.company_id = c.company_id 
    WHERE s.sale_id = '$sale_id'
");
$sale = mysqli_fetch_assoc($saleQuery);

// 🔹 Fetch sale items
$itemsQuery = mysqli_query($con, "
    SELECT si.product_id, si.quantity, si.unit_price, si.discount, si.tax, p.product_name
    FROM sales_item si
    JOIN products p ON si.product_id = p.product_id
    WHERE si.sale_id = '$sale_id'
");

$items = [];
$totalBeforeTax = 0;
$totalDiscount = 0;
$totalTax = 0;
$grandTotal = 0;

while ($row = mysqli_fetch_assoc($itemsQuery)) {
    $price = (float)$row['unit_price'];
    $qty = (int)$row['quantity'];
    $discountPercent = (float)$row['discount'];
    $taxPercent = (float)$row['tax'];

    // 🧮 Per-item calculations
    $subtotal = $price * $qty;
    $discountAmount = ($subtotal * $discountPercent) / 100;
    $afterDiscount = $subtotal - $discountAmount;
    $taxAmount = ($afterDiscount * $taxPercent) / 100;
    $finalTotal = $afterDiscount + $taxAmount;

    $totalBeforeTax += $subtotal;
    $totalDiscount += $discountAmount;
    $totalTax += $taxAmount;
    $grandTotal += $finalTotal;

    $items[] = [
        'name' => $row['product_name'],
        'price' => round($price, 2),
        'qty' => $qty,
        'discountPercent' => $discountPercent,
        'discountAmount' => round($discountAmount, 2),
        'taxPercent' => $taxPercent,
        'taxAmount' => round($taxAmount, 2),
        'total' => round($finalTotal, 2)
    ];
}

echo json_encode([
    'sale' => $sale,
    'items' => $items,
    'summary' => [
        'subtotal' => round($totalBeforeTax, 2),
        'total_discount' => round($totalDiscount, 2),
        'total_tax' => round($totalTax, 2),
        'grand_total' => round($grandTotal, 2)
    ]
]);
?>
