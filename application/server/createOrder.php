<?php

require_once __DIR__ . "/functions.php";

// Calculates the price of the selected products, with 21% VAT.
function calculateTotals($selected, $quantities) {
    $catalog = getCatalog();
    $orderProducts = array();
    $subtotal = 0;

    foreach ($selected as $id => $productName) {
        $quantity = isset($quantities[$id]) ? intval($quantities[$id]) : 0;

        if (isset($catalog[$id]) && $quantity > 0) {
            $price = $catalog[$id]["price"];
            $lineSubtotal = $price * $quantity;
            $subtotal = $subtotal + $lineSubtotal;

            $orderProducts[] = array(
                "name" => $catalog[$id]["name"],
                "quantity" => $quantity,
                "unitPrice" => $price,
                "subtotal" => $lineSubtotal
            );
        }
    }

    $vat = round($subtotal * VAT_RATE, 2);

    return array(
        "products" => $orderProducts,
        "subtotal" => $subtotal,
        "vat" => $vat,
        "totalWithVat" => $subtotal + $vat
    );
}

// Adds the order to orders.json (the file is created with the first order).
function saveOrder($order) {
    if (!file_exists(ORDERS_FILE)) {
        if (@file_put_contents(ORDERS_FILE, json_encode(array())) === false) {
            showError("The orders file (orders.json) could not be created.");
        }
    }

    $orders = readOrders();
    $orders[] = $order;

    $saved = @file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT), LOCK_EX);
    if ($saved === false) {
        showError("The orders file (orders.json) could not be saved.");
    }
}

// Creates the order, saves it and sends only the total price with VAT (200 OK, plain text).
function createOrder() {
    $selected = isset($_POST["products"]) ? $_POST["products"] : array();
    $quantities = isset($_POST["quantities"]) ? $_POST["quantities"] : array();

    $totals = calculateTotals($selected, $quantities);

    $order = array(
        "orderId" => trim($_POST["orderId"]),
        "customerName" => trim($_POST["customerName"]),
        "address" => trim($_POST["address"]),
        "email" => trim($_POST["email"]),
        "phone" => trim($_POST["phone"]),
        "products" => $totals["products"],
        "subtotal" => $totals["subtotal"],
        "vat" => $totals["vat"],
        "totalWithVat" => $totals["totalWithVat"]
    );

    saveOrder($order);

    http_response_code(200);
    header("Content-Type: text/plain; charset=utf-8");
    echo number_format($totals["totalWithVat"], 2, ".", "") . " EUR";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    createOrder();
} else {
    http_response_code(405);
}