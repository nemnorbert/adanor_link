<?php
function loadJSON($url) {
    try {
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => 'Content-Type: application/json',
            ],
        ];
        $context = stream_context_create($options);
        $json = file_get_contents($url, false, $context);
        if ($json === false) {
            throw new Exception('Error reading JSON file: ' . error_get_last()['message']);
        }
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error decoding JSON: ' . json_last_error_msg());
        }
        return $data;
    } catch (Exception $e) {
        errorHandler("json_error", "json", $e->getMessage());
        return null;
    }
}
function errorHandler($code, $text, $details) {
    $test = $_SERVER['SERVER_NAME'] === 'localhost';
    $link = $test ? "http://localhost/redcat_home/error" : "https://red-cat.hu/error";
    $link .= '?id='.$code ?? "400";
    $link .= '&url='.urlencode($_SERVER['REQUEST_URI']) ?? "";
    header("HTTP/1.1 303 See Other");
    header("Location: ".$link);
    exit();
}
?>