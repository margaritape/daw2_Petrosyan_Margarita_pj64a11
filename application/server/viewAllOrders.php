<?php

require_once __DIR__ . "/functions.php";

// Reads all the orders and sends them in an HTML table (200 OK).
function viewAllOrders() {
    $orders = readOrders();

    http_response_code(200);
    header("Content-Type: text/html; charset=utf-8");

    startPage("All Orders");
    showOrdersTable($orders);
    endPage();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    viewAllOrders();
} else {
    http_response_code(405);
}