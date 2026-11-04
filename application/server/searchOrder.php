
<?php

function showError() {
    http_response_code(404);
    header("Content-Type: text/html; charset=utf-8");

    echo "<h1>Error 404</h1>";
    echo "<p>Order not found or unable to open the orders file.</p>";
    echo "<button onclick=\"window.location.href='../client/viewOrders.html'\">Back to View Orders</button>";
    echo "<button onclick=\"window.location.href='../client/operations.html'\">Back to Operations</button>";
    echo "<button onclick=\"window.location.href='../client/index.html'\">Back to Home</button>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $orderId = $_GET["orderId"];

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

    $foundOrder = null;

    foreach ($orders as $order) {
        if ($order["orderId"] == $orderId) {
            $foundOrder = $order;
            break;
        }
    }

    if ($foundOrder === null) {
        showError();
    }

    http_response_code(200);
    header("Content-Type: text/html; charset=utf-8");

    echo "<h1>Order Details</h1>";
    echo "<table border='1'>";

    echo "<tr><th>Field</th><th>Information</th></tr>";

    echo "<tr><td>Order ID</td><td>" . htmlspecialchars($foundOrder["orderId"]) . "</td></tr>";
    echo "<tr><td>Customer Name</td><td>" . htmlspecialchars($foundOrder["customerName"]) . "</td></tr>";
    echo "<tr><td>Address</td><td>" . htmlspecialchars($foundOrder["address"]) . "</td></tr>";
    echo "<tr><td>Email</td><td>" . htmlspecialchars($foundOrder["email"]) . "</td></tr>";
    echo "<tr><td>Phone</td><td>" . htmlspecialchars($foundOrder["phone"]) . "</td></tr>";

    $productText = "";

    foreach ($foundOrder["products"] as $product) {
        $productText .= htmlspecialchars($product["name"]) . " x " .
            htmlspecialchars($product["quantity"]) . "<br>";
    }

    echo "<tr><td>Products</td><td>" . $productText . "</td></tr>";
    echo "<tr><td>Subtotal</td><td>" . number_format($foundOrder["subtotal"], 2) . " EUR</td></tr>";
    echo "<tr><td>VAT</td><td>" . number_format($foundOrder["vat"], 2) . " EUR</td></tr>";
    echo "<tr><td>Total Including VAT</td><td>" . number_format($foundOrder["totalWithVat"], 2) . " EUR</td></tr>";

    echo "</table>";

    echo "<br>";
    echo "<button onclick=\"window.location.href='../client/viewOrders.html'\">Back to View Orders</button>";
    echo "<button onclick=\"window.location.href='../client/operations.html'\">Back to Operations</button>";
    echo "<button onclick=\"window.location.href='../client/index.html'\">Back to Home</button>";
}

?>
