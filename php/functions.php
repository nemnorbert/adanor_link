<?php
function loadJSON($filePath) {
    try {
        $json = file_get_contents($filePath);
        if ($json === false) {
            throw new Exception('Error reading JSON file: ' . error_get_last()['message']);
        }
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error decoding JSON: ' . json_last_error_msg());
        }
        return $data;
    } catch (Exception $e) {
        errorHandler("json_error", $e->getMessage());
        return null;
    }
}
function errorHandler($code, $text) {
    $link = $_SERVER['SERVER_NAME'] === 'localhost' ? "/redcat_home/" : "https://red-cat.hu/";
    $link .= "error?id=" . $code;
    if ($text !== "") {$link .= "&desc=".$text;}
    header("HTTP/1.1 303 See Other");
    header("Location: ".$link);
    exit();
}
?>