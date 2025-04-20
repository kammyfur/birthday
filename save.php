<?php

$request_raw = file_get_contents('php://input');
$json_object = json_decode($request_raw, true);

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    if (str_contains($id, ".") || str_contains($id, "/")) die();
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/users/" . $id . ".json", json_encode($json_object, JSON_PRETTY_PRINT));
} else if (isset($_GET["display"])) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/display.json", json_encode($json_object, JSON_PRETTY_PRINT));
}