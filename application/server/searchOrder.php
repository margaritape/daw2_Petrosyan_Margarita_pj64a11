<?php

require_once __DIR__ . "/functions.php";

// Looks for one order by its identifier and sends it in an HTML table (200 OK).
function searchOrder($orderId) {
    $orders = readOrders();
    $order = findOrder($orders, $orderId);

    if ($order === null) {
        showError("No order was found with the ID \"" . $orderId . "\".");
    }

    http_response_code(200);
    header("Content-Type: text/html; charset=utf-8");

    startPage("Order Details");
    showOrdersTable(array($order));
    endPage();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $orderId = isset($_GET["orderId"]) ? trim($_GET["orderId"]) : "";
    searchOrder($orderId);
} else {
    http_response_code(405);
}