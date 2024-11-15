<?php

function return_json($status_int, $status_string, $html)
{
    $data = array(
        "result" => array(
            "value" => $status_int,
            "message" => $status_string
        ),
        "html" => $html
    );
    echo json_encode($data);
    exit();
}

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = "itilucarelli-biblio";

try {
    $conn = mysqli_connect($servername, $username, $password, $dbname);
} catch (\Throwable $th) {
    return_json(1, 'error', '<p class="text-danger mb-0">Errore con il server</p>');
}
