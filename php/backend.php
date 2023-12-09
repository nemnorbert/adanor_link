<?php
set_include_path( $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR );
date_default_timezone_set('Europe/Budapest');

$siteINFO = new stdClass();
$siteJSON = loadJSON('json/site.json');
$siteINFO -> langAvailable = $siteJSON["languages"];
$siteINFO -> langUser = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : "?";

// Test Server?
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $preURL = '/redcat_link';
    $siteINFO -> test = true;
    $siteINFO -> mainPath = $siteJSON["mainPath"]["test"];
    $siteINFO -> redcatPath = $siteJSON["redcatPath"]["test"];
} else {
    error_reporting(0);
    ini_set('display_errors', '0');

    $preURL = '';
    $siteINFO -> test = false;
    $siteINFO -> mainPath = $siteJSON["mainPath"]["web"];
    $siteINFO -> redcatPath = $siteJSON["redcatPath"]["web"];
}

$requestURI = $_SERVER['REQUEST_URI'];
$siteINFO -> requestURI = $requestURI;
$requestURI = str_replace($preURL, '', $requestURI);
$parts = explode("?", $requestURI);
$parts = explode("/", $parts[0]);

$siteINFO->page = substr($parts[1], 0, 10);
$siteINFO -> langSite = in_array($siteINFO -> langUser, $siteINFO -> langAvailable) ? $siteINFO -> langUser : "en";
$langJSON = loadJSON('json/lang/'.$siteINFO -> langSite.'.json');

$browserLang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : "";

// Ready To Action
$apiData = [];

try {
    $pre = $siteINFO->test ? "http://localhost/redcat/api/" : "https://center.red-cat.hu/api/";
    $url = $pre."redcatLink?id=" . $siteINFO->page;
    $url .= $browserLang ? ("&lang=" . $browserLang) : "";
    $url .= "&time=" . time();
    $apiData = loadJSON($url);
} catch (\Throwable $th) {
    $apiData["status"] = "error";
    $apiData["error"] = "error_api";
}
?>