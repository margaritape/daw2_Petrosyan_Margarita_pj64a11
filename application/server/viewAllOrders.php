
<?php

function showError() {
    http_response_code(404);
    header("Content-Type: text/html; charset=utf-8");

    echo "<h1>Error 404</h1>";
    echo "<p>Unable to open the orders file.</p>";
    echo "<button onclick=\"window.location.href='../client/viewOrders.html'\">Back to View Orders</button>";
    echo "<button onclick=\"window.location.href='../client/operations.html'\">Back to Operations</button>";
    echo "<button onclick=\"window.location.href='../client/index.html'\">Back to Home</button>";
    exit;
}

$file = __DIR__ . "/../../orders/orders.json";

if (!file_exists($file)) {
    showError();
}

$content = file_get_contents($file);

if ($content === false) {
    showError();
}

$orders = json_decode($content, true);

if (!is_array($orders)) {
    showError();
}

http_response_code(200);
header("Content-Type: text/html; charset=utf-8");

echo "<h1>All Orders</h1>";

if (count($orders) == 0) {
    echo "<p>There are no orders saved.</p>";
} else {
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>Order ID</th>";
    echo "<th>Customer Name</th>";
    echo "<th>Address</th>";
    echo "<th>Email</th>";
    echo "<th>Phone</th>";
    echo "<th>Products</th>";
    echo "<th>Subtotal</th>";
    echo "<th>VAT</th>";
    echo "<th>Total Including VAT</th>";
    echo "</tr>";

    foreach ($orders as $order) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($order["orderId"]) . "</td>";
        echo "<td>" . htmlspecialchars($order["customerName"]) . "</td>";
        echo "<td>" . htmlspecialchars($order["address"]) . "</td>";
        echo "<td>" . htmlspecialchars($order["email"]) . "</td>";
        echo "<td>" . htmlspecialchars($order["phone"]) . "</td>";

        $productText = "";

        foreach ($order["products"] as $product) {
            $productText .= $product["name"] . " x " .
                $product["quantity"] . "<br>";
        }

        echo "<td>" . $productText . "</td>";
        echo "<td>" . number_format($order["subtotal"], 2) . " EUR</td>";
        echo "<td>" . number_format($order["vat"], 2) . " EUR</td>";
        echo "<td>" . number_format($order["totalWithVat"], 2) . " EUR</td>";
        echo "</tr>";
    }

    echo "</table>";
}

echo "<br>";
echo "<button onclick=\"window.location.href='../client/viewOrders.html'\">Back to View Orders</button>";
echo "<button onclick=\"window.location.href='../client/operations.html'\">Back to Operations</button>";
echo "<button onclick=\"window.location.href='../client/index.html'\">Back to Home</button>";

?>
