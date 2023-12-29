<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');
//$maintence = true;
require_once "php/functions.php";
if (isset($maintence) && $maintence) {errorHandler("503", "", "");}
require_once "php/backend.php";
?>