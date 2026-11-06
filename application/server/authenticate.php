<?php

// Returns true if the user and the password match a user of users.json.
function authenticateUser($username, $password) {
    $file = __DIR__ . "/../../users/users.json";

    if (!file_exists($file)) {
        return false;
    }

    $users = json_decode(file_get_contents($file), true);
    if (!is_array($users)) {
        return false;
    }

    foreach ($users as $user) {
        if ($user["user"] === $username && password_verify($password, $user["password"])) {
            return true;
        }
    }

    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST["username"]) ? $_POST["username"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    header("Content-Type: text/plain; charset=utf-8");

    if (authenticateUser($username, $password)) {
        http_response_code(200);
        echo "Authentication successful";
    } else {
        http_response_code(401);
        echo "The user could not be validated";
    }
} else {
    http_response_code(405);
}