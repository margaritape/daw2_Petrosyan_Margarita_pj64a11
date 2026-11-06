<?php

define("ORDERS_FILE", __DIR__ . "/../../orders/orders.json");
define("VAT_RATE", 0.21);

// Product catalog: associative multidimensional array (id => name and price).
function getCatalog() {
    return array(
        1 => array("name" => "Eyeglasses",        "price" => 100),
        2 => array("name" => "Sunglasses",        "price" => 80),
        3 => array("name" => "Contact Lenses",    "price" => 30),
        4 => array("name" => "Lens Cleaning Kit", "price" => 10)
    );
}

// Prints the beginning of an HTML document.
function startPage($title) {
    echo "<!DOCTYPE html>";
    echo "<html lang='en'><head><meta charset='UTF-8'>";
    echo "<title>OptiOrder - " . htmlspecialchars($title) . "</title>";
    echo "<link rel='stylesheet' href='../client/style.css'>";
    echo "</head><body>";
    echo "<h1>" . htmlspecialchars($title) . "</h1>";
}

// Prints the three return buttons and closes the HTML document.
function endPage() {
    echo "<br>";
    echo "<button type='button' onclick=\"window.location.href='../client/viewOrders.html'\">Back to View Orders</button>";
    echo "<button type='button' onclick=\"window.location.href='../client/operations.html'\">Back to Operations</button>";
    echo "<button type='button' onclick=\"window.location.href='../client/index.html'\">Back to Home</button>";
    echo "</body></html>";
}

// Sends a 404 Not Found response with an HTML error message and stops.
function showError($message) {
    http_response_code(404);
    header("Content-Type: text/html; charset=utf-8");
    startPage("Error 404");
    echo "<p class='error'>" . htmlspecialchars($message) . "</p>";
    endPage();
    exit;
}

// Reads orders.json and returns the orders array. Sends a 404 error if the file cannot be opened.
function readOrders() {
    if (!file_exists(ORDERS_FILE)) {
        showError("The orders file (orders.json) was not found.");
    }

    $content = @file_get_contents(ORDERS_FILE);
    if ($content === false) {
        showError("The orders file (orders.json) could not be opened.");
    }

    $orders = json_decode($content, true);
    if (!is_array($orders)) {
        showError("The orders file (orders.json) could not be read.");
    }

    return $orders;
}

// Prints a table (one row per order) with meaningful column names.
function showOrdersTable($orders) {
    echo "<table>";
    echo "<tr>";
    echo "<th>Order ID</th><th>Customer Name</th><th>Address</th><th>Email</th><th>Phone</th>";
    echo "<th>Products (quantity x unit price)</th><th>Subtotal</th><th>VAT (21%)</th><th>Total Including VAT</th>";
    echo "</tr>";

    foreach ($orders as $order) {
        $productText = "";
        foreach ($order["products"] as $product) {
            $productText .= htmlspecialchars($product["name"]) . ": " .
                htmlspecialchars($product["quantity"]) . " x " .
                number_format($product["unitPrice"], 2) . " EUR<br>";
        }

        echo "<tr>";
        echo "<td>" . htmlspecialchars($order["orderId"]) . "</td>";
        echo "<td>" . htmlspecialchars($order["customerName"]) . "</td>";
        echo "<td>" . htmlspecialchars($order["address"]) . "</td>";
        echo "<td>" . htmlspecialchars($order["email"]) . "</td>";
        echo "<td>" . htmlspecialchars($order["phone"]) . "</td>";
        echo "<td>" . $productText . "</td>";
        echo "<td>" . number_format($order["subtotal"], 2) . " EUR</td>";
        echo "<td>" . number_format($order["vat"], 2) . " EUR</td>";
        echo "<td>" . number_format($order["totalWithVat"], 2) . " EUR</td>";
        echo "</tr>";
    }

    echo "</table>";
}

// Returns the order with the given identifier, or null if it does not exist.
function findOrder($orders, $orderId) {
    foreach ($orders as $order) {
        if ($order["orderId"] === $orderId) {
            return $order;
        }
    }
    return null;
}