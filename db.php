<?php

$users = [];
$list = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/users"), function ($i) {
    return !str_starts_with($i, ".");
});

foreach ($list as $item) {
    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/users/" . $item), true);

    if (!$data["banned"] || isset($_GET["banned"])) {
        $users[substr($item, 0, -5)] = $data;
        $users[substr($item, 0, -5)]["id"] = substr($item, 0, -5);
    }
}

echo(json_encode($users));