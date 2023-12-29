<?php
set_include_path( $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR );
date_default_timezone_set('Europe/Budapest');
$siteINFO = new stdClass();

// Test Server?
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    $preURL = '/redcat_link';
    $siteINFO -> test = true;
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    $preURL = '';
    $siteINFO -> test = false;
}

$requestURI = $_SERVER['REQUEST_URI'];
$siteINFO -> requestURI = $requestURI;
$requestURI = str_replace($preURL, '', $requestURI);
$parts = explode("?", $requestURI);
$parts = explode("/", $parts[0]);
$siteINFO->page = substr($parts[1], 0, 10);

// Ready To Action
$apiData = [];
try {
    $pre = $siteINFO->test ? "http://localhost/redcat_api/" : "https://api.red-cat.hu/";
    $url = $pre."redcatLink?id=" . $siteINFO->page;
    $url .= "&time=" . time();
    $apiData = loadJSON($url);
} catch (\Throwable $th) {
    $apiData["status"] = "error";
    $apiData["error"] = "500";
    errorHandler($apiData["error"], "api", "");
}
if (isset($apiData["error"])) {
    if ($apiData["error"] === "404") {errorHandler($apiData["error"], $siteINFO -> requestURI, "");}
    if ($apiData["error"] === "901") {errorHandler($apiData["error"], "", "");}
}

// Ready to Redirect
header("HTTP/1.1 303 See Other");
header('Location: ' . $apiData["redirect_to"]);
exit();
?>