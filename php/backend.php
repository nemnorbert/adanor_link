<?php
$siteINFO = new stdClass();
$siteJSON = loadJSON('json/site.json');
$siteINFO -> langAvailable = $siteJSON["languages"]["site"];
$siteINFO -> langUser = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : false;
$siteINFO -> redirect = true;

// Test Server?
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    $preURL = '/redcat_link';
    $siteINFO -> test = true;
    $siteINFO -> mainPath = $siteJSON["mainPath"]["test"];
    $siteINFO -> redcatPath = $siteJSON["redcatPath"]["test"];
} else {
    $preURL = '';
    $siteINFO -> test = false;
    $siteINFO -> mainPath = $siteJSON["mainPath"]["web"];
    $siteINFO -> redcatPath = $siteJSON["redcatPath"]["web"];
}

$requestURI = $_SERVER['REQUEST_URI'];
$requestURI = str_replace($preURL, '', $requestURI);
$parts = explode("?", $requestURI);
$parts = explode("/", $parts[0]);

$siteINFO -> page = $parts[1];
$siteINFO -> langSite = in_array($siteINFO -> langUser, $siteINFO -> langAvailable) ? $siteINFO -> langUser : "en";

// Ready To Action
if ($siteINFO -> redirect) {
    $sql = "SELECT l.id, l.url, l.rapid, s.url, l.redirect FROM links AS l RIGHT JOIN services AS s ON s.id = l.service_id WHERE l.url = ?";
    manageDatabase($siteINFO, $sql);
}

echo "<pre>";
var_dump($siteINFO);
echo "<pre>";
?>